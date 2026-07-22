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
                        <form method="GET" action="<?php echo url('/properties.php'); ?>" id="filterForm">
                            <div class="mb-3">
                                <label class="label">City</label>
                                <select name="city" class="input" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Cities</option>
                                    <?php foreach ($cities as $c): ?>
                                        <option value="<?php echo e($c['name']); ?>" <?php echo $city === $c['name'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="label">Property Type</label>
                                <select name="type" class="input" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Types</option>
                                    <?php foreach ($propertyTypes as $pt): ?>
                                        <option value="<?php echo e($pt['value']); ?>" <?php echo $type === $pt['value'] ? 'selected' : ''; ?>><?php echo e($pt['label']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="label">Min Price</label>
                                    <input type="number" name="minPrice" class="input" value="<?php echo e($minPrice); ?>" placeholder="0" onchange="document.getElementById('filterForm').submit()">
                                </div>
                                <div class="col-6">
                                    <label class="label">Max Price</label>
                                    <input type="number" name="maxPrice" class="input" value="<?php echo e($maxPrice); ?>" placeholder="50000" onchange="document.getElementById('filterForm').submit()">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="label">Min Bedrooms</label>
                                <select name="bedrooms" class="input" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">Any</option>
                                    <option value="1" <?php echo $bedrooms === '1' ? 'selected' : ''; ?>>1+</option>
                                    <option value="2" <?php echo $bedrooms === '2' ? 'selected' : ''; ?>>2+</option>
                                    <option value="3" <?php echo $bedrooms === '3' ? 'selected' : ''; ?>>3+</option>
                                    <option value="4" <?php echo $bedrooms === '4' ? 'selected' : ''; ?>>4+</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="label">Min Guests</label>
                                <select name="guests" class="input" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">Any</option>
                                    <option value="2" <?php echo $guests === '2' ? 'selected' : ''; ?>>2+</option>
                                    <option value="4" <?php echo $guests === '4' ? 'selected' : ''; ?>>4+</option>
                                    <option value="6" <?php echo $guests === '6' ? 'selected' : ''; ?>>6+</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="label">Featured Only</label>
                                <select name="featured" class="input" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Properties</option>
                                    <option value="1" <?php echo $featured === '1' ? 'selected' : ''; ?>>Featured Only</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Apply Filters</button>
                            <a href="<?php echo url('/properties.php'); ?>" class="btn btn-ghost w-100 mt-2">Clear All</a>
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
                                        <a href="<?php echo url('/property-details.php?id=' . (int)$rec['property']['id']); ?>" class="btn btn-primary btn-sm">View</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0"><span id="visibleCount">6</span> of <?php echo count($properties); ?> Properties Found</h5>
                        <button onclick="toggleFilters()" class="btn btn-ghost d-lg-none"><i class="bi bi-funnel"></i> Filters</button>
                    </div>

                    <?php if (empty($properties)): ?>
                        <div class="card p-5 text-center">
                            <i class="bi bi-search" style="font-size:3rem;color:var(--slate-300);"></i>
                            <h5 class="mt-3">No properties found</h5>
                            <p style="color:var(--slate-500);">Try adjusting your filters</p>
                            <a href="<?php echo url('/properties.php'); ?>" class="btn btn-primary">Clear Filters</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-4" id="propertyGrid">
                            <?php foreach ($properties as $i => $p): ?>
                                <div class="col-md-6 col-xl-4 property-item" data-index="<?php echo $i; ?>" <?php echo $i >= 6 ? 'style="display:none;"' : ''; ?>>
                                    <?php include __DIR__ . '/includes/property_card.php'; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($properties) > 6): ?>
                        <div class="text-center mt-4">
                            <button class="btn btn-primary btn-lg" id="loadMoreBtn" onclick="loadMore()">
                                <i class="bi bi-arrow-down-circle"></i> Load More Properties
                            </button>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
var visibleCount = 6;
var totalProperties = <?php echo count($properties); ?>;

function loadMore() {
    var items = document.querySelectorAll('.property-item');
    var newlyShown = 0;
    items.forEach(function(item) {
        if (item.style.display === 'none' && newlyShown < 6) {
            item.style.display = '';
            newlyShown++;
        }
    });
    visibleCount += newlyShown;
    document.getElementById('visibleCount').textContent = visibleCount;

    if (visibleCount >= totalProperties) {
        document.getElementById('loadMoreBtn').style.display = 'none';
    }
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
