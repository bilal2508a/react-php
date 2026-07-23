<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$user = currentUser();
if ($user['role'] === 'owner') {
    redirect('/owner-dashboard.php');
}
if ($user['role'] === 'admin') {
    redirect('/admin.php');
}
if ($user['role'] === 'tenant') {
    redirect('/profile.php');
}

// Stats
$stmt = db()->prepare('SELECT COUNT(*) FROM bookings WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$totalTrips = $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM bookings WHERE user_id = ? AND status = ?');
$stmt->execute([$_SESSION['user_id'], 'completed']);
$completedTrips = $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM wishlist WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$wishlistCount = $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM reviews WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$reviewCount = $stmt->fetchColumn();

// Travel Analytics
$stmt = db()->prepare('SELECT SUM(total_price) FROM bookings WHERE user_id = ? AND payment_status = ?');
$stmt->execute([$_SESSION['user_id'], 'paid']);
$totalSpent = (float)$stmt->fetchColumn();

$avgTripCost = $totalTrips > 0 ? $totalSpent / $totalTrips : 0;
$savings = $totalSpent * 0.12;

// Top Destinations
$stmt = db()->prepare('SELECT p.city, COUNT(*) as cnt FROM bookings b JOIN properties p ON b.property_id = p.id WHERE b.user_id = ? GROUP BY p.city ORDER BY cnt DESC LIMIT 5');
$stmt->execute([$_SESSION['user_id']]);
$destinations = $stmt->fetchAll();
$maxDestCount = !empty($destinations) ? max(array_column($destinations, 'cnt')) : 1;

// Recent bookings
$stmt = db()->prepare('SELECT b.*, p.title as property_title, p.city, p.area, p.images FROM bookings b JOIN properties p ON b.property_id = p.id WHERE b.user_id = ? ORDER BY b.created_at DESC LIMIT 5');
$stmt->execute([$_SESSION['user_id']]);
$recentBookings = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-nav">
    <section class="gradient-primary-light py-5">
        <div class="container-app">
            <h1 class="fw-bold">Welcome, <?php echo e(explode(' ', $user['full_name'])[0]); ?>!</h1>
            <p style="color:var(--slate-600);">Here's your travel dashboard overview</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container-app">
            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--primary-100);">
                            <i class="bi bi-airplane" style="font-size:1.25rem;color:var(--primary-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$totalTrips; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Total Trips</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--success-100);">
                            <i class="bi bi-check-circle" style="font-size:1.25rem;color:var(--success-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$completedTrips; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Completed</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--error-100);">
                            <i class="bi bi-heart" style="font-size:1.25rem;color:var(--error-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$wishlistCount; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Wishlist</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--accent-100);">
                            <i class="bi bi-star" style="font-size:1.25rem;color:var(--accent-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$reviewCount; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Reviews</p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Travel Analytics -->
                <div class="col-lg-5">
                    <div class="card p-4 h-100">
                        <h5 class="fw-bold mb-3"><i class="bi bi-graph-up"></i> Travel Analytics</h5>
                        <div class="mb-3">
                            <p style="color:var(--slate-500);font-size:0.85rem;margin-bottom:0.25rem;">Total Spent</p>
                            <h4 style="font-weight:800;color:var(--primary-600);"><?php echo formatPKR($totalSpent); ?></h4>
                        </div>
                        <div class="mb-3">
                            <p style="color:var(--slate-500);font-size:0.85rem;margin-bottom:0.25rem;">Estimated Savings (12%)</p>
                            <h4 style="font-weight:800;color:var(--success-600);"><?php echo formatPKR($savings); ?></h4>
                        </div>
                        <div class="mb-3">
                            <p style="color:var(--slate-500);font-size:0.85rem;margin-bottom:0.25rem;">Average Trip Cost</p>
                            <h4 style="font-weight:800;color:var(--accent-600);"><?php echo formatPKR($avgTripCost); ?></h4>
                        </div>
                    </div>
                </div>

                <!-- Top Destinations -->
                <div class="col-lg-7">
                    <div class="card p-4 h-100">
                        <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt"></i> Top Destinations</h5>
                        <?php if (empty($destinations)): ?>
                            <p style="color:var(--slate-500);">No destinations yet. Start booking to see your travel patterns!</p>
                        <?php else: ?>
                            <?php foreach ($destinations as $dest): ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="font-weight:600;font-size:0.9rem;"><?php echo e($dest['city']); ?></span>
                                        <span style="color:var(--slate-500);font-size:0.85rem;"><?php echo (int)$dest['cnt']; ?> trips</span>
                                    </div>
                                    <div style="height:8px;background:var(--slate-100);border-radius:4px;overflow:hidden;">
                                        <div class="gradient-primary-accent" style="height:100%;width:<?php echo (int)(($dest['cnt'] / $maxDestCount) * 100); ?>%;border-radius:4px;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="mt-5">
                <h4 class="fw-bold mb-3">Recent Bookings</h4>
                <?php if (empty($recentBookings)): ?>
                    <div class="card p-5 text-center">
                        <i class="bi bi-calendar-x" style="font-size:2.5rem;color:var(--slate-300);"></i>
                        <h5 class="mt-3">No bookings yet</h5>
                        <p style="color:var(--slate-500);">Start exploring properties and book your first stay!</p>
                        <a href="<?php echo url('/properties.php'); ?>" class="btn btn-primary">Browse Properties</a>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($recentBookings as $b): ?>
                            <div class="card p-3 d-flex align-items-center gap-3 flex-row">
                                <img src="<?php echo e(trim(explode(',', $b['images'])[0])); ?>" style="width:80px;height:80px;object-fit:cover;border-radius:10px;">
                                <div class="flex-grow-1">
                                    <h6 style="font-weight:700;"><?php echo e($b['property_title']); ?></h6>
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0;"><i class="bi bi-geo-alt"></i> <?php echo e($b['city'] . ', ' . $b['area']); ?></p>
                                    <p style="font-size:0.85rem;margin:0;"><?php echo formatDate($b['check_in']); ?> - <?php echo formatDate($b['check_out']); ?></p>
                                </div>
                                <div class="text-end">
                                    <p style="font-weight:800;color:var(--primary-600);margin:0;"><?php echo formatPKR($b['total_price']); ?></p>
                                    <span class="badge badge-<?php echo $b['status'] === 'confirmed' ? 'success' : ($b['status'] === 'pending' ? 'warning' : ($b['status'] === 'completed' ? 'primary' : 'error')); ?>"><?php echo e(ucfirst($b['status'])); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
