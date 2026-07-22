<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
requireRole('owner');

$user = currentUser();

// Stats
$stmt = db()->prepare('SELECT COUNT(*) FROM properties WHERE owner_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$propertyCount = $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM bookings b JOIN properties p ON b.property_id = p.id WHERE p.owner_id = ? AND b.owner_status = ?');
$stmt->execute([$_SESSION['user_id'], 'pending']);
$pendingRequests = $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM bookings b JOIN properties p ON b.property_id = p.id WHERE p.owner_id = ? AND b.owner_status = ?');
$stmt->execute([$_SESSION['user_id'], 'approved']);
$approvedBookings = $stmt->fetchColumn();

$stmt = db()->prepare('SELECT SUM(total_price) FROM bookings b JOIN properties p ON b.property_id = p.id WHERE p.owner_id = ? AND b.payment_status = ?');
$stmt->execute([$_SESSION['user_id'], 'paid']);
$earnings = (float)$stmt->fetchColumn();
$commission = $earnings * 0.05;
$netEarnings = $earnings - $commission;

// Fetch booking requests
$stmt = db()->prepare('SELECT b.*, p.title as property_title, p.city, p.area FROM bookings b JOIN properties p ON b.property_id = p.id WHERE p.owner_id = ? ORDER BY b.created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

// Fetch my properties
$stmt = db()->prepare('SELECT * FROM properties WHERE owner_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$myProperties = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-nav">
    <section class="gradient-primary-light py-5">
        <div class="container-app">
            <h1 class="fw-bold">Owner Dashboard</h1>
            <p style="color:var(--slate-600);">Manage your properties and bookings</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container-app">
            <!-- Stats -->
            <div class="row g-4 mb-5">
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--primary-100);">
                            <i class="bi bi-building" style="font-size:1.25rem;color:var(--primary-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$propertyCount; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Properties</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--warning-100);">
                            <i class="bi bi-clock" style="font-size:1.25rem;color:var(--warning-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$pendingRequests; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Pending Requests</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--success-100);">
                            <i class="bi bi-check-circle" style="font-size:1.25rem;color:var(--success-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$approvedBookings; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Approved Bookings</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--accent-100);">
                            <i class="bi bi-cash-coin" style="font-size:1.25rem;color:var(--accent-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo formatPKR($netEarnings); ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Net Earnings</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="d-flex gap-2 mb-4 border-bottom">
                <button class="tab-btn active" onclick="switchTab('requests')">Booking Requests</button>
                <button class="tab-btn" onclick="switchTab('properties')">My Properties</button>
                <button class="tab-btn" onclick="switchTab('earnings')">Earnings</button>
            </div>

            <!-- Booking Requests Tab -->
            <div id="tab-requests">
                <?php if (empty($bookings)): ?>
                    <div class="card p-5 text-center">
                        <i class="bi bi-calendar-x" style="font-size:2.5rem;color:var(--slate-300);"></i>
                        <h5 class="mt-3">No booking requests yet</h5>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($bookings as $b): ?>
                            <div class="card p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <h6 style="font-weight:700;"><?php echo e($b['property_title']); ?></h6>
                                        <p style="color:var(--slate-500);font-size:0.85rem;margin:0;"><i class="bi bi-geo-alt"></i> <?php echo e($b['city'] . ', ' . $b['area']); ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p style="font-size:0.85rem;margin:0;"><?php echo formatDate($b['check_in']); ?> - <?php echo formatDate($b['check_out']); ?></p>
                                        <p style="font-size:0.85rem;color:var(--slate-500);margin:0;"><?php echo e($b['guest_name']); ?> (<?php echo (int)$b['guests']; ?> guests)</p>
                                    </div>
                                    <div class="col-md-2 text-md-center">
                                        <p style="font-weight:800;color:var(--primary-600);margin:0;"><?php echo formatPKR($b['total_price']); ?></p>
                                        <span class="badge badge-<?php echo $b['owner_status'] === 'approved' ? 'success' : ($b['owner_status'] === 'rejected' ? 'error' : 'warning'); ?>"><?php echo e(ucfirst($b['owner_status'])); ?></span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <?php if ($b['owner_status'] === 'pending'): ?>
                                            <a href="/api/booking-action.php?id=<?php echo (int)$b['id']; ?>&action=approve" class="btn btn-success btn-sm"><i class="bi bi-check"></i></a>
                                            <a href="/api/booking-action.php?id=<?php echo (int)$b['id']; ?>&action=reject" class="btn btn-error btn-sm"><i class="bi bi-x"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- My Properties Tab -->
            <div id="tab-properties" style="display:none;">
                <div class="mb-3">
                    <a href="/add-property.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New Property</a>
                </div>
                <?php if (empty($myProperties)): ?>
                    <div class="card p-5 text-center">
                        <i class="bi bi-building" style="font-size:2.5rem;color:var(--slate-300);"></i>
                        <h5 class="mt-3">No properties yet</h5>
                        <p style="color:var(--slate-500);">Add your first property to start earning!</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($myProperties as $p): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card p-3">
                                    <img src="<?php echo e(trim(explode(',', $p['images'])[0])); ?>" style="width:100%;height:160px;object-fit:cover;border-radius:10px;">
                                    <h6 style="font-weight:700;margin-top:0.75rem;"><?php echo e($p['title']); ?></h6>
                                    <p style="color:var(--slate-500);font-size:0.85rem;"><i class="bi bi-geo-alt"></i> <?php echo e($p['city'] . ', ' . $p['area']); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="font-weight:700;color:var(--primary-600);"><?php echo formatPKR($p['price_per_night']); ?>/night</span>
                                        <?php if ($p['is_featured']): ?>
                                            <span class="badge badge-featured">Featured</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Earnings Tab -->
            <div id="tab-earnings" style="display:none;">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card p-4">
                            <p style="color:var(--slate-500);font-size:0.85rem;">Gross Earnings</p>
                            <h3 style="font-weight:800;color:var(--primary-600);"><?php echo formatPKR($earnings); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4">
                            <p style="color:var(--slate-500);font-size:0.85rem;">Platform Commission (5%)</p>
                            <h3 style="font-weight:800;color:var(--error-600);">- <?php echo formatPKR($commission); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4">
                            <p style="color:var(--slate-500);font-size:0.85rem;">Net Earnings</p>
                            <h3 style="font-weight:800;color:var(--success-600);"><?php echo formatPKR($netEarnings); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    event.target.classList.add('active');
    document.getElementById('tab-requests').style.display = tab === 'requests' ? '' : 'none';
    document.getElementById('tab-properties').style.display = tab === 'properties' ? '' : 'none';
    document.getElementById('tab-earnings').style.display = tab === 'earnings' ? '' : 'none';
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
