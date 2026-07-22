<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

// Stats
$stmt = db()->prepare('SELECT COUNT(*) FROM wishlist WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$savedCount = $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM wishlist w JOIN properties p ON w.property_id = p.id WHERE w.user_id = ? AND p.price_per_night >= ?');
$stmt->execute([$_SESSION['user_id'], 15000]);
$luxuryCount = $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM wishlist w JOIN properties p ON w.property_id = p.id WHERE w.user_id = ? AND p.price_per_night < ?');
$stmt->execute([$_SESSION['user_id'], 10000]);
$budgetCount = $stmt->fetchColumn();

$stmt = db()->prepare('SELECT COUNT(*) FROM wishlist w JOIN properties p ON w.property_id = p.id WHERE w.user_id = ? AND p.max_guests >= ?');
$stmt->execute([$_SESSION['user_id'], 4]);
$familyCount = $stmt->fetchColumn();

// Fetch wishlist properties
$stmt = db()->prepare('SELECT p.* FROM wishlist w JOIN properties p ON w.property_id = p.id WHERE w.user_id = ? ORDER BY w.created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$wishlistProperties = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-nav">
    <section class="gradient-primary-light py-5">
        <div class="container-app">
            <h1 class="fw-bold">My Wishlist</h1>
            <p style="color:var(--slate-600);">Your saved properties for later</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container-app">
            <!-- Stats -->
            <div class="row g-4 mb-5">
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--error-100);">
                            <i class="bi bi-heart-fill" style="font-size:1.25rem;color:var(--error-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$savedCount; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Saved</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--secondary-100);">
                            <i class="bi bi-gem" style="font-size:1.25rem;color:var(--secondary-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$luxuryCount; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Luxury (PKR 15K+)</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--success-100);">
                            <i class="bi bi-piggy-bank" style="font-size:1.25rem;color:var(--success-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$budgetCount; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Budget (PKR &lt;10K)</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card p-4">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:var(--accent-100);">
                            <i class="bi bi-people" style="font-size:1.25rem;color:var(--accent-600);"></i>
                        </div>
                        <h3 style="font-weight:800;"><?php echo (int)$familyCount; ?></h3>
                        <p style="color:var(--slate-500);font-size:0.85rem;">Family (4+ guests)</p>
                    </div>
                </div>
            </div>

            <!-- Wishlist Grid -->
            <?php if (empty($wishlistProperties)): ?>
                <div class="card p-5 text-center">
                    <i class="bi bi-heart" style="font-size:3rem;color:var(--slate-300);"></i>
                    <h5 class="mt-3">Your wishlist is empty</h5>
                    <p style="color:var(--slate-500);">Save properties you love to find them here later</p>
                    <a href="<?php echo url('/properties.php'); ?>" class="btn btn-primary">Browse Properties</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($wishlistProperties as $p): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="property-card position-relative">
                                <div class="property-card-image">
                                    <img src="<?php echo e(trim(explode(',', $p['images'])[0])); ?>" alt="<?php echo e($p['title']); ?>">
                                    <span class="badge badge-price glass"><?php echo formatPKR($p['price_per_night']); ?><span style="font-size:0.7rem;font-weight:400;">/night</span></span>
                                </div>
                                <div class="property-card-body">
                                    <h5 class="property-card-title line-clamp-1"><?php echo e($p['title']); ?></h5>
                                    <p class="property-card-location line-clamp-1"><i class="bi bi-geo-alt"></i> <?php echo e($p['city'] . ', ' . $p['area']); ?></p>
                                    <div class="property-card-info">
                                        <span><i class="bi bi-house"></i> <?php echo (int)$p['bedrooms']; ?> Beds</span>
                                        <span><i class="bi bi-people"></i> <?php echo (int)$p['max_guests']; ?> Guests</span>
                                    </div>
                                    <div class="d-flex gap-2 mt-3">
                                        <a href="<?php echo url('/property-details.php?id=' . (int)$p['id']); ?>" class="btn btn-primary btn-sm flex-grow-1">View Details</a>
                                        <a href="<?php echo url('/api/toggle-wishlist.php?property_id=' . (int)$p['id']); ?>" class="btn btn-error btn-sm"><i class="bi bi-heart-fill"></i></a>
                                    </div>
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
