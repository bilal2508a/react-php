<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM properties WHERE id = ?');
$stmt->execute([$id]);
$property = $stmt->fetch();

if (!$property) {
    redirect('/properties.php');
}

$images = !empty($property['images']) ? explode(',', $property['images']) : [];
$amenities = !empty($property['amenities']) ? explode(',', $property['amenities']) : [];

// Fetch reviews
$stmt = db()->prepare('SELECT r.*, u.full_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.property_id = ? ORDER BY r.created_at DESC');
$stmt->execute([$id]);
$reviews = $stmt->fetchAll();

// Fetch similar properties
$stmt = db()->prepare('SELECT * FROM properties WHERE city = ? AND id != ? LIMIT 3');
$stmt->execute([$property['city'], $id]);
$similar = $stmt->fetchAll();

// Compatibility score checks
$scoreChecks = [
    ['label' => 'Verified Listing', 'passed' => true],
    ['label' => 'High Rating', 'passed' => $property['rating'] >= 4.5],
    ['label' => 'Featured Property', 'passed' => (bool)$property['is_featured']],
    ['label' => 'Amenities Available', 'passed' => count($amenities) >= 5],
    ['label' => 'Good Reviews', 'passed' => $property['review_count'] >= 10],
    ['label' => 'Prime Location', 'passed' => true],
];
$scorePct = (count(array_filter($scoreChecks, fn($c) => $c['passed'])) / count($scoreChecks)) * 100;
$scoreDeg = ($scorePct / 100) * 360;
?>

<main class="pt-nav">
    <!-- Breadcrumb -->
    <div class="bg-slate py-3">
        <div class="container-app">
            <nav style="font-size:0.85rem;">
                <a href="/index.php" style="color:var(--slate-500);">Home</a> >
                <a href="/properties.php" style="color:var(--slate-500);">Properties</a> >
                <span style="color:var(--slate-700);"><?php echo e($property['title']); ?></span>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container-app">
            <!-- Title & Rating -->
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                <div>
                    <h1 style="font-size:1.75rem;font-weight:800;"><?php echo e($property['title']); ?></h1>
                    <div class="d-flex align-items-center gap-3 mt-2">
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-star-fill" style="color:#f59e0b;"></i>
                            <span style="font-weight:700;"><?php echo e(number_format($property['rating'], 1)); ?></span>
                            <span style="color:var(--slate-500);">(<?php echo (int)$property['review_count']; ?> reviews)</span>
                        </div>
                        <span style="color:var(--slate-500);"><i class="bi bi-geo-alt"></i> <?php echo e($property['city'] . ', ' . $property['area']); ?></span>
                    </div>
                </div>
                <a href="/booking.php?property_id=<?php echo (int)$property['id']; ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-calendar-check"></i> Book Now
                </a>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Image Gallery -->
                    <div class="card p-3 mb-4">
                        <img id="mainImage" src="<?php echo e(trim($images[0] ?? 'https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg')); ?>" alt="<?php echo e($property['title']); ?>" style="width:100%;height:400px;object-fit:cover;border-radius:12px;">
                        <div class="d-flex gap-2 mt-3 overflow-auto">
                            <?php foreach ($images as $img): ?>
                                <img src="<?php echo e(trim($img)); ?>" onclick="setActiveImage('<?php echo e(trim($img)); ?>')" class="gallery-thumb" style="width:100px;height:80px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid var(--slate-200);">
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="card p-4 mb-4">
                        <div class="row text-center">
                            <div class="col-6 col-md-3">
                                <i class="bi bi-house" style="font-size:1.5rem;color:var(--primary-600);"></i>
                                <h6 style="font-weight:700;margin-top:0.5rem;"><?php echo (int)$property['bedrooms']; ?> Bedrooms</h6>
                            </div>
                            <div class="col-6 col-md-3">
                                <i class="bi bi-droplet" style="font-size:1.5rem;color:var(--primary-600);"></i>
                                <h6 style="font-weight:700;margin-top:0.5rem;"><?php echo (int)$property['bathrooms']; ?> Bathrooms</h6>
                            </div>
                            <div class="col-6 col-md-3">
                                <i class="bi bi-people" style="font-size:1.5rem;color:var(--primary-600);"></i>
                                <h6 style="font-weight:700;margin-top:0.5rem;"><?php echo (int)$property['max_guests']; ?> Guests</h6>
                            </div>
                            <div class="col-6 col-md-3">
                                <i class="bi bi-star" style="font-size:1.5rem;color:var(--primary-600);"></i>
                                <h6 style="font-weight:700;margin-top:0.5rem;"><?php echo e(number_format($property['rating'], 1)); ?> Rating</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="card p-4 mb-4">
                        <h4 class="fw-bold mb-3">About this property</h4>
                        <p style="color:var(--slate-600);line-height:1.8;"><?php echo e($property['description']); ?></p>
                    </div>

                    <!-- Amenities -->
                    <div class="card p-4 mb-4">
                        <h4 class="fw-bold mb-3">Amenities</h4>
                        <div class="row g-3">
                            <?php foreach ($amenities as $am): ?>
                                <div class="col-6 col-md-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle" style="color:var(--success-500);"></i>
                                        <span style="font-size:0.9rem;color:var(--slate-700);"><?php echo e(trim($am)); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Compatibility Score -->
                    <div class="card p-4 mb-4">
                        <h4 class="fw-bold mb-3">Compatibility Score</h4>
                        <div class="d-flex align-items-center gap-4 flex-wrap">
                            <div class="score-circle">
                                <svg width="120" height="120">
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                                    <circle cx="60" cy="60" r="52" fill="none" stroke="url(#gradScore)" stroke-width="8" stroke-dasharray="<?php echo (int)(2 * M_PI * 52); ?>" stroke-dashoffset="<?php echo (int)(2 * M_PI * 52 * (1 - $scorePct / 100)); ?>" stroke-linecap="round"/>
                                    <defs>
                                        <linearGradient id="gradScore" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#0ea5e9"/>
                                            <stop offset="100%" stop-color="#14b8a6"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <span class="score-value"><?php echo (int)$scorePct; ?>%</span>
                            </div>
                            <div class="flex-grow-1">
                                <?php foreach ($scoreChecks as $check): ?>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi <?php echo $check['passed'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?>" style="color:<?php echo $check['passed'] ? 'var(--success-500)' : 'var(--slate-300)'; ?>;"></i>
                                        <span style="font-size:0.9rem;color:var(--slate-700);"><?php echo e($check['label']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div class="card p-4">
                        <h4 class="fw-bold mb-3">Guest Reviews (<?php echo count($reviews); ?>)</h4>
                        <?php if (empty($reviews)): ?>
                            <p style="color:var(--slate-500);">No reviews yet. Be the first to review!</p>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--primary-500),var(--accent-500));color:#fff;font-weight:700;">
                                            <?php echo e(strtoupper(substr($review['full_name'], 0, 1))); ?>
                                        </div>
                                        <div>
                                            <h6 style="font-weight:700;margin:0;"><?php echo e($review['full_name']); ?></h6>
                                            <div class="d-flex gap-1">
                                                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                                    <i class="bi bi-star-fill" style="color:#f59e0b;font-size:0.75rem;"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <p style="color:var(--slate-600);font-size:0.9rem;"><?php echo e($review['comment']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Booking Widget -->
                <div class="col-lg-4">
                    <div class="card p-4" style="position:sticky;top:90px;">
                        <h4 style="font-weight:800;color:var(--primary-600);"><?php echo formatPKR($property['price_per_night']); ?> <span style="font-size:0.9rem;color:var(--slate-500);font-weight:400;">/ night</span></h4>
                        <hr class="my-3">
                        <div class="mb-3">
                            <label class="label">Check-in</label>
                            <input type="date" id="checkIn" class="input" onchange="recalculateTotal()">
                        </div>
                        <div class="mb-3">
                            <label class="label">Check-out</label>
                            <input type="date" id="checkOut" class="input" onchange="recalculateTotal()">
                        </div>
                        <div class="mb-3">
                            <label class="label">Guests</label>
                            <select id="guests" class="input">
                                <?php for ($i = 1; $i <= $property['max_guests']; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> Guest<?php echo $i > 1 ? 's' : ''; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <input type="hidden" id="pricePerNight" value="<?php echo e($property['price_per_night']); ?>">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--slate-600);"><span id="nightsCount">0</span> nights</span>
                            <span id="subtotalAmount" style="font-weight:600;">PKR 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--slate-600);">Service fee (5%)</span>
                            <span id="serviceFeeAmount" style="font-weight:600;">PKR 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span style="font-weight:700;">Total</span>
                            <span id="totalAmount" style="font-weight:800;color:var(--primary-600);">PKR 0</span>
                        </div>
                        <a href="/booking.php?property_id=<?php echo (int)$property['id']; ?>" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-calendar-check"></i> Book Now
                        </a>
                        <p class="text-center mt-2" style="font-size:0.8rem;color:var(--slate-500);">You won't be charged yet</p>
                    </div>
                </div>
            </div>

            <!-- Similar Properties -->
            <?php if (!empty($similar)): ?>
            <div class="mt-5">
                <h3 class="fw-bold mb-4">Similar Properties in <?php echo e($property['city']); ?></h3>
                <div class="row g-4">
                    <?php foreach ($similar as $p): ?>
                        <div class="col-md-4">
                            <?php include __DIR__ . '/includes/property_card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
