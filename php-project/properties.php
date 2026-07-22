<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';

// Get filters from $_GET
$city = $_GET['city'] ?? '';
$type = $_GET['type'] ?? '';
$minPrice = $_GET['minPrice'] ?? '';
$maxPrice = $_GET['maxPrice'] ?? '';
$bedrooms = $_GET['bedrooms'] ?? '';
$guests = $_GET['guests'] ?? '';
$featured = $_GET['featured'] ?? '';
$amenitiesFilter = $_GET['amenities'] ?? '';

// Build WHERE clause
$where = [];
$params = [];

if ($city) {
    $where[] = 'city = ?';
    $params[] = $city;
}
if ($type) {
    $where[] = 'property_type = ?';
    $params[] = $type;
}
if ($minPrice !== '') {
    $where[] = 'price_per_night >= ?';
    $params[] = (float)$minPrice;
}
if ($maxPrice !== '') {
    $where[] = 'price_per_night <= ?';
    $params[] = (float)$maxPrice;
}
if ($bedrooms !== '') {
    $where[] = 'bedrooms >= ?';
    $params[] = (int)$bedrooms;
}
if ($guests !== '') {
    $where[] = 'max_guests >= ?';
    $params[] = (int)$guests;
}
if ($featured) {
    $where[] = 'is_featured = 1';
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = db()->prepare("SELECT * FROM properties $whereClause ORDER BY is_featured DESC, rating DESC");
$stmt->execute($params);
$properties = $stmt->fetchAll();

// Filter amenities in PHP
if ($amenitiesFilter) {
    $filterAmenities = explode(',', $amenitiesFilter);
    $properties = array_filter($properties, function($p) use ($filterAmenities) {
        $propAmenities = explode(',', $p['amenities']);
        foreach ($filterAmenities as $fa) {
            if (!in_array(trim($fa), $propAmenities)) {
                return false;
            }
        }
        return true;
    });
}

// AI Recommendations - score properties
$aiRecommendations = [];
foreach ($properties as $p) {
    $score = 50;
    if ($city && $p['city'] === $city) $score += 30;
    if ($maxPrice !== '' && $p['price_per_night'] <= (float)$maxPrice) $score += 25;
    if ($guests !== '' && $p['max_guests'] >= (int)$guests) $score += 20;
    if ($p['rating'] >= 4.5) $score += 15;
    if ($p['is_featured']) $score += 10;
    $aiRecommendations[] = ['property' => $p, 'score' => min($score, 100)];
}
usort($aiRecommendations, function($a, $b) {
    return $b['score'] - $a['score'];
});
$topRecommendations = array_slice($aiRecommendations, 0, 3);

$cities = getCities();
$propertyTypes = getPropertyTypes();
$allAmenities = getAllAmenities();
?>

<main class="pt-nav">
    <section class="gradient-primary-light py-5">
        <div class="container-app">
            <h1 class="section-title">Find Your Perfect Property</h1>
            <p class="section-subtitle mb-0">Discover amazing stays across Pakistan</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container-app">
            <div class="row">
                <!-- Filter Sidebar -->
                <div class="col-lg-3 mb-4">
                    <div class="card p-4" id="filterSidebar">
                        <h5 class="fw-bold mb-3"><i class="bi bi-funnel"></i> Filters</h5>
                        <form method="GET" action="/properties.php">
                            <div class="mb-3">
                                <label class="label">City</label>
                                <select name="city" class="input">
                                    <option value="">All Cities</option>
                                    <?php foreach ($cities as $c): ?>
                                        <option value="<?php echo e($c['name']); ?>" <?php echo $city === $c['name'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="label">Property Type</label>
                                <select name="type" class="input">
                                    <option value="">All Types</option>
                                    <?php foreach ($propertyTypes as $pt): ?>
                                        <option value="<?php echo e($pt['value']); ?>" <?php echo $type === $pt['value'] ? 'selected' : ''; ?>><?php echo e($pt['label']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="label">Min Price</label>
                                    <input type="number" name="minPrice" class="input" value="<?php echo e($minPrice); ?>" placeholder="0">
                                </div>
                                <div class="col-6">
                                    <label class="label">Max Price</label>
                                    <input type="number" name="maxPrice" class="input" value="<?php echo e($maxPrice); ?>" placeholder="50000">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="label">Min Bedrooms</label>
                                <select name="bedrooms" class="input">
                                    <option value="">Any</option>
                                    <option value="1" <?php echo $bedrooms === '1' ? 'selected' : ''; ?>>1+</option>
                                    <option value="2" <?php echo $bedrooms === '2' ? 'selected' : ''; ?>>2+</option>
                                    <option value="3" <?php echo $bedrooms === '3' ? 'selected' : ''; ?>>3+</option>
                                    <option value="4" <?php echo $bedrooms === '4' ? 'selected' : ''; ?>>4+</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="label">Min Guests</label>
                                <select name="guests" class="input">
                                    <option value="">Any</option>
                                    <option value="2" <?php echo $guests === '2' ? 'selected' : ''; ?>>2+</option>
                                    <option value="4" <?php echo $guests === '4' ? 'selected' : ''; ?>>4+</option>
                                    <option value="6" <?php echo $guests === '6' ? 'selected' : ''; ?>>6+</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="label">Featured Only</label>
                                <select name="featured" class="input">
                                    <option value="">All Properties</option>
                                    <option value="1" <?php echo $featured === '1' ? 'selected' : ''; ?>>Featured Only</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="label">Amenities</label>
                                <input type="hidden" name="amenities" id="amenitiesFilter" value="<?php echo e($amenitiesFilter); ?>">
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($allAmenities as $am): ?>
                                        <?php $selected = in_array($am, explode(',', $amenitiesFilter)); ?>
                                        <span class="amenity-chip <?php echo $selected ? 'selected' : ''; ?>" data-amenity="<?php echo e($am); ?>" onclick="toggleAmenity('<?php echo e($am); ?>', this)" style="padding:4px 10px;border-radius:20px;font-size:0.75rem;cursor:pointer;border:1px solid var(--slate-200);<?php echo $selected ? 'background:var(--primary-500);color:#fff;' : 'background:#fff;color:var(--slate-600);'; ?>">
                                            <?php echo e($am); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Apply Filters</button>
                            <a href="/properties.php" class="btn btn-ghost w-100 mt-2">Clear All</a>
                        </form>
                    </div>
                </div>

                <!-- Property Grid -->
                <div class="col-lg-9">
                    <!-- AI Recommendations -->
                    <?php if (!empty($topRecommendations) && ($city || $maxPrice !== '' || $guests !== '')): ?>
                    <div class="card p-4 mb-4 gradient-primary-light" style="border:2px solid var(--primary-200);">
                        <h5 class="fw-bold mb-3"><i class="bi bi-robot"></i> AI Recommendations for You</h5>
                        <div class="row g-3">
                            <?php foreach ($topRecommendations as $rec): ?>
                                <div class="col-md-4">
                                    <div class="card p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge badge-primary">Match: <?php echo (int)$rec['score']; ?>%</span>
                                        </div>
                                        <h6 style="font-weight:700;font-size:0.9rem;" class="line-clamp-1"><?php echo e($rec['property']['title']); ?></h6>
                                        <p style="font-size:0.8rem;color:var(--slate-500);" class="line-clamp-1"><i class="bi bi-geo-alt"></i> <?php echo e($rec['property']['city']); ?></p>
                                        <p style="font-weight:700;color:var(--primary-600);"><?php echo formatPKR($rec['property']['price_per_night']); ?></p>
                                        <a href="/property-details.php?id=<?php echo (int)$rec['property']['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0"><?php echo count($properties); ?> Properties Found</h5>
                        <button onclick="toggleFilters()" class="btn btn-ghost d-lg-none"><i class="bi bi-funnel"></i> Filters</button>
                    </div>

                    <?php if (empty($properties)): ?>
                        <div class="card p-5 text-center">
                            <i class="bi bi-search" style="font-size:3rem;color:var(--slate-300);"></i>
                            <h5 class="mt-3">No properties found</h5>
                            <p style="color:var(--slate-500);">Try adjusting your filters</p>
                            <a href="/properties.php" class="btn btn-primary">Clear Filters</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($properties as $p): ?>
                                <div class="col-md-6 col-xl-4">
                                    <?php include __DIR__ . '/includes/property_card.php'; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-4">
                            <button class="btn btn-ghost btn-lg" id="loadMoreBtn">Load More Properties</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
