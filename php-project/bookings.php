<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$tab = $_GET['tab'] ?? 'upcoming';
if ($tab !== 'upcoming' && $tab !== 'completed' && $tab !== 'cancelled') {
    $tab = 'upcoming';
}

// Fetch bookings based on tab
$statusCondition = '';
switch ($tab) {
    case 'upcoming':
        $statusCondition = "b.status IN ('pending','confirmed')";
        break;
    case 'completed':
        $statusCondition = "b.status = 'completed'";
        break;
    case 'cancelled':
        $statusCondition = "b.status = 'cancelled'";
        break;
}

$stmt = db()->prepare("SELECT b.*, p.title as property_title, p.city, p.area, p.images FROM bookings b JOIN properties p ON b.property_id = p.id WHERE b.user_id = ? AND $statusCondition ORDER BY b.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-nav">
    <section class="gradient-primary-light py-5">
        <div class="container-app">
            <h1 class="fw-bold">My Bookings</h1>
            <p style="color:var(--slate-600);">Manage your travel reservations</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container-app">
            <!-- Tabs -->
            <div class="d-flex gap-2 mb-4 border-bottom">
                <a href="/bookings.php?tab=upcoming" class="tab-btn <?php echo $tab === 'upcoming' ? 'active' : ''; ?>">Upcoming</a>
                <a href="/bookings.php?tab=completed" class="tab-btn <?php echo $tab === 'completed' ? 'active' : ''; ?>">Completed</a>
                <a href="/bookings.php?tab=cancelled" class="tab-btn <?php echo $tab === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
            </div>

            <!-- Bookings -->
            <?php if (empty($bookings)): ?>
                <div class="card p-5 text-center">
                    <i class="bi bi-calendar-x" style="font-size:3rem;color:var(--slate-300);"></i>
                    <h5 class="mt-3">No <?php echo e($tab); ?> bookings</h5>
                    <p style="color:var(--slate-500);">
                        <?php if ($tab === 'upcoming'): ?>
                            Start planning your next trip!
                        <?php else: ?>
                            No bookings in this category.
                        <?php endif; ?>
                    </p>
                    <a href="/properties.php" class="btn btn-primary">Browse Properties</a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($bookings as $b): ?>
                        <div class="card p-3">
                            <div class="row align-items-center g-3">
                                <div class="col-md-2">
                                    <img src="<?php echo e(trim(explode(',', $b['images'])[0])); ?>" style="width:100%;height:100px;object-fit:cover;border-radius:10px;">
                                </div>
                                <div class="col-md-4">
                                    <h6 style="font-weight:700;"><?php echo e($b['property_title']); ?></h6>
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0;"><i class="bi bi-geo-alt"></i> <?php echo e($b['city'] . ', ' . $b['area']); ?></p>
                                </div>
                                <div class="col-md-2">
                                    <p style="font-size:0.8rem;color:var(--slate-500);margin:0;">Check-in</p>
                                    <p style="font-weight:600;font-size:0.85rem;"><?php echo formatDate($b['check_in']); ?></p>
                                    <p style="font-size:0.8rem;color:var(--slate-500);margin:0;">Check-out</p>
                                    <p style="font-weight:600;font-size:0.85rem;"><?php echo formatDate($b['check_out']); ?></p>
                                </div>
                                <div class="col-md-2 text-md-center">
                                    <p style="font-size:0.8rem;color:var(--slate-500);margin:0;">Guests</p>
                                    <p style="font-weight:600;font-size:0.85rem;"><?php echo (int)$b['guests']; ?> Guest<?php echo $b['guests'] > 1 ? 's' : ''; ?></p>
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <p style="font-weight:800;color:var(--primary-600);margin:0;"><?php echo formatPKR($b['total_price']); ?></p>
                                    <div class="d-flex gap-1 justify-content-md-end mt-1 flex-wrap">
                                        <span class="badge badge-<?php echo $b['status'] === 'confirmed' ? 'success' : ($b['status'] === 'pending' ? 'warning' : ($b['status'] === 'completed' ? 'primary' : 'error')); ?>"><?php echo e(ucfirst($b['status'])); ?></span>
                                        <span class="badge badge-<?php echo $b['payment_status'] === 'paid' ? 'success' : 'warning'; ?>"><?php echo e(ucfirst($b['payment_status'])); ?></span>
                                    </div>
                                    <?php if ($b['payment_status'] === 'unpaid' && $b['status'] !== 'cancelled'): ?>
                                        <a href="/payment.php?id=<?php echo (int)$b['id']; ?>" class="btn btn-primary btn-sm mt-2"><i class="bi bi-credit-card"></i> Pay Now</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
