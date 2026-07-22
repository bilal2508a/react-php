<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
requireRole('admin');

// Stats
$stmt = db()->query('SELECT COUNT(*) FROM users');
$userCount = $stmt->fetchColumn();

$stmt = db()->query('SELECT COUNT(*) FROM properties');
$propertyCount = $stmt->fetchColumn();

$stmt = db()->query('SELECT COUNT(*) FROM bookings');
$bookingCount = $stmt->fetchColumn();

$stmt = db()->query("SELECT SUM(total_price) FROM bookings WHERE payment_status = 'paid'");
$platformRevenue = (float)$stmt->fetchColumn() * 0.05;

// User breakdown
$stmt = db()->query("SELECT role, COUNT(*) as cnt FROM users GROUP BY role");
$userBreakdown = $stmt->fetchAll();

// Top rated properties
$stmt = db()->query('SELECT * FROM properties ORDER BY rating DESC LIMIT 5');
$topRated = $stmt->fetchAll();

// City distribution
$stmt = db()->query('SELECT city, COUNT(*) as cnt FROM properties GROUP BY city ORDER BY cnt DESC');
$cityData = $stmt->fetchAll();
$maxCityCount = !empty($cityData) ? max(array_column($cityData, 'cnt')) : 1;

// All users
$stmt = db()->query('SELECT * FROM users ORDER BY created_at DESC');
$allUsers = $stmt->fetchAll();

// All properties
$stmt = db()->query('SELECT * FROM properties ORDER BY created_at DESC');
$allProperties = $stmt->fetchAll();

// All bookings
$stmt = db()->query('SELECT b.*, p.title as property_title, u.full_name as user_name FROM bookings b JOIN properties p ON b.property_id = p.id JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC');
$allBookings = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-nav">
    <section class="gradient-primary-light py-5">
        <div class="container-app">
            <h1 class="fw-bold">Admin Dashboard</h1>
            <p style="color:var(--slate-600);">Platform overview and management</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container-app">
            <!-- Stats -->
            <div class="row g-4 mb-5">
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--primary-100);">
                            <i class="bi bi-people" style="font-size:1.25rem;color:var(--primary-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$userCount; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Users</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--accent-100);">
                            <i class="bi bi-building" style="font-size:1.25rem;color:var(--accent-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$propertyCount; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Properties</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--secondary-100);">
                            <i class="bi bi-calendar-check" style="font-size:1.25rem;color:var(--secondary-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$bookingCount; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Bookings</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--success-100);">
                            <i class="bi bi-cash-coin" style="font-size:1.25rem;color:var(--success-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo formatPKR($platformRevenue); ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Platform Revenue (5%)</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="d-flex gap-2 mb-4 border-bottom">
                <button class="tab-btn active" onclick="switchTab('overview')">Overview</button>
                <button class="tab-btn" onclick="switchTab('users')">Users</button>
                <button class="tab-btn" onclick="switchTab('properties')">Properties</button>
                <button class="tab-btn" onclick="switchTab('bookings')">Bookings</button>
            </div>

            <!-- Overview Tab -->
            <div id="tab-overview">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card p-4 h-100">
                            <h5 class="fw-bold mb-3">User Breakdown</h5>
                            <?php foreach ($userBreakdown as $ub): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span style="font-weight:600;text-transform:capitalize;"><?php echo e($ub['role']); ?>s</span>
                                    <span style="font-weight:700;color:var(--primary-600);"><?php echo (int)$ub['cnt']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card p-4 h-100">
                            <h5 class="fw-bold mb-3">Top Rated Properties</h5>
                            <?php foreach ($topRated as $p): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span style="font-size:0.85rem;" class="line-clamp-1"><?php echo e($p['title']); ?></span>
                                    <span style="font-weight:700;color:#f59e0b;font-size:0.85rem;"><i class="bi bi-star-fill"></i> <?php echo e(number_format($p['rating'], 1)); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card p-4 h-100">
                            <h5 class="fw-bold mb-3">City Distribution</h5>
                            <?php foreach ($cityData as $cd): ?>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="font-size:0.85rem;"><?php echo e($cd['city']); ?></span>
                                        <span style="font-size:0.85rem;color:var(--slate-500);"><?php echo (int)$cd['cnt']; ?></span>
                                    </div>
                                    <div style="height:6px;background:var(--slate-100);border-radius:3px;">
                                        <div class="gradient-primary-accent" style="height:100%;width:<?php echo (int)(($cd['cnt'] / $maxCityCount) * 100); ?>%;border-radius:3px;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Tab -->
            <div id="tab-users" style="display:none;">
                <div class="card p-0 overflow-hidden">
                    <table class="table mb-0">
                        <thead style="background:var(--slate-100);">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUsers as $u): ?>
                                <tr>
                                    <td><?php echo (int)$u['id']; ?></td>
                                    <td style="font-weight:600;"><?php echo e($u['full_name']); ?></td>
                                    <td><?php echo e($u['email']); ?></td>
                                    <td><span class="badge badge-primary"><?php echo e(ucfirst($u['role'])); ?></span></td>
                                    <td style="font-size:0.85rem;"><?php echo formatDate($u['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Properties Tab -->
            <div id="tab-properties" style="display:none;">
                <div class="row g-4">
                    <?php foreach ($allProperties as $p): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card p-3">
                                <img src="<?php echo e(trim(explode(',', $p['images'])[0])); ?>" style="width:100%;height:160px;object-fit:cover;border-radius:10px;">
                                <h6 style="font-weight:700;margin-top:0.75rem;"><?php echo e($p['title']); ?></h6>
                                <p style="color:var(--slate-500);font-size:0.85rem;"><i class="bi bi-geo-alt"></i> <?php echo e($p['city'] . ', ' . $p['area']); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="font-weight:700;color:var(--primary-600);"><?php echo formatPKR($p['price_per_night']); ?>/night</span>
                                    <a href="<?php echo url('/api/delete-property.php?id=' . (int)$p['id']); ?>" class="btn btn-error btn-sm" onclick="return confirm('Delete this property?')"><i class="bi bi-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Bookings Tab -->
            <div id="tab-bookings" style="display:none;">
                <div class="card p-0 overflow-hidden">
                    <table class="table mb-0">
                        <thead style="background:var(--slate-100);">
                            <tr>
                                <th>ID</th>
                                <th>Property</th>
                                <th>Guest</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allBookings as $b): ?>
                                <tr>
                                    <td><?php echo (int)$b['id']; ?></td>
                                    <td style="font-weight:600;"><?php echo e($b['property_title']); ?></td>
                                    <td><?php echo e($b['user_name']); ?></td>
                                    <td style="font-size:0.85rem;"><?php echo formatDate($b['check_in']); ?> - <?php echo formatDate($b['check_out']); ?></td>
                                    <td style="font-weight:600;"><?php echo formatPKR($b['total_price']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $b['payment_status'] === 'paid' ? 'success' : 'warning'; ?>"><?php echo e(ucfirst($b['payment_status'])); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    event.target.classList.add('active');
    ['overview', 'users', 'properties', 'bookings'].forEach(function(t) {
        document.getElementById('tab-' + t).style.display = t === tab ? '' : 'none';
    });
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
