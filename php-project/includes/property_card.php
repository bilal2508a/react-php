<?php
// Mehmaan Hub - Property Card Component
// Expects $p variable with property data
$images = !empty($p['images']) ? explode(',', $p['images']) : [];
$firstImage = !empty($images) ? trim($images[0]) : 'https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg';
$propertyTypes = getPropertyTypes();
$typeLabel = $p['property_type'];
foreach ($propertyTypes as $pt) {
    if ($pt['value'] === $p['property_type']) {
        $typeLabel = $pt['label'];
        break;
    }
}
?>
<a href="<?php echo url('/property-details.php?id=' . (int)$p['id']); ?>" class="property-card">
    <div class="property-card-image">
        <img src="<?php echo e($firstImage); ?>" alt="<?php echo e($p['title']); ?>" loading="lazy">
        <?php if (!empty($p['is_featured'])): ?>
            <span class="badge badge-featured"><i class="bi bi-star-fill"></i> Featured</span>
        <?php endif; ?>
        <span class="badge badge-type"><?php echo e($typeLabel); ?></span>
        <span class="badge badge-price glass"><?php echo formatPKR($p['price_per_night']); ?><span style="font-size:0.7rem;font-weight:400;">/night</span></span>
    </div>
    <div class="property-card-body">
        <h5 class="property-card-title line-clamp-1"><?php echo e($p['title']); ?></h5>
        <div class="d-flex align-items-center gap-1 mb-2">
            <i class="bi bi-star-fill" style="color:#f59e0b;font-size:0.85rem;"></i>
            <span style="font-weight:700;font-size:0.9rem;color:#0f172a;"><?php echo e(number_format($p['rating'], 1)); ?></span>
            <span style="color:#64748b;font-size:0.8rem;">(<?php echo (int)$p['review_count']; ?> reviews)</span>
        </div>
        <p class="property-card-location line-clamp-1">
            <i class="bi bi-geo-alt"></i> <?php echo e($p['city'] . ', ' . $p['area']); ?>
        </p>
        <div class="property-card-info">
            <span><i class="bi bi-house"></i> <?php echo (int)$p['bedrooms']; ?> Beds</span>
            <span><i class="bi bi-droplet"></i> <?php echo (int)$p['bathrooms']; ?> Baths</span>
            <span><i class="bi bi-people"></i> <?php echo (int)$p['max_guests']; ?> Guests</span>
        </div>
    </div>
</a>
