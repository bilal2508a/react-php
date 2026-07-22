<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
requireRole('owner');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $city = $_POST['city'] ?? '';
    $area = $_POST['area'] ?? '';
    $propertyType = $_POST['property_type'] ?? 'guest_house';
    $pricePerNight = (float)($_POST['price_per_night'] ?? 0);
    $bedrooms = (int)($_POST['bedrooms'] ?? 1);
    $bathrooms = (int)($_POST['bathrooms'] ?? 1);
    $maxGuests = (int)($_POST['max_guests'] ?? 2);
    $description = $_POST['description'] ?? '';
    $amenities = $_POST['amenities'] ?? [];
    $images = $_POST['images_hidden'] ?? '';

    if (empty($title) || empty($city) || empty($area) || $pricePerNight <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        $amenitiesStr = implode(',', $amenities);
        $stmt = db()->prepare('INSERT INTO properties (title, description, city, area, property_type, price_per_night, bedrooms, bathrooms, max_guests, amenities, images, owner_id, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)');
        if ($stmt->execute([$title, $description, $city, $area, $propertyType, $pricePerNight, $bedrooms, $bathrooms, $maxGuests, $amenitiesStr, $images, $_SESSION['user_id']])) {
            $success = 'Property added successfully!';
        } else {
            $error = 'Failed to add property';
        }
    }
}

$cities = getCities();
$propertyTypes = getPropertyTypes();
$allAmenities = getAllAmenities();

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-nav">
    <section class="gradient-primary-light py-5">
        <div class="container-app">
            <h1 class="fw-bold">Add New Property</h1>
            <p style="color:var(--slate-600);">List your property and start earning</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container-app" style="max-width:900px;">
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo e($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>

            <div class="card p-4">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="label">Property Title *</label>
                            <input type="text" name="title" id="title" class="input" placeholder="Luxury Sea View Apartment" required>
                        </div>
                        <div class="col-md-4">
                            <label class="label">City *</label>
                            <select name="city" id="city" class="input" required>
                                <?php foreach ($cities as $c): ?>
                                    <option value="<?php echo e($c['name']); ?>"><?php echo e($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="label">Area *</label>
                            <input type="text" name="area" class="input" placeholder="Clifton" required>
                        </div>
                        <div class="col-md-4">
                            <label class="label">Property Type *</label>
                            <select name="property_type" id="propertyType" class="input" required>
                                <?php foreach ($propertyTypes as $pt): ?>
                                    <option value="<?php echo e($pt['value']); ?>"><?php echo e($pt['label']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="label">Price per Night (PKR) *</label>
                            <input type="number" name="price_per_night" class="input" placeholder="15000" required>
                        </div>
                        <div class="col-md-4">
                            <label class="label">Bedrooms</label>
                            <input type="number" name="bedrooms" id="bedrooms" class="input" value="1" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="label">Bathrooms</label>
                            <input type="number" name="bathrooms" class="input" value="1" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="label">Max Guests</label>
                            <input type="number" name="max_guests" id="maxGuests" class="input" value="2" min="1">
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="label mb-0">Description</label>
                                <button type="button" onclick="generateDescription()" class="btn btn-accent btn-sm"><i class="bi bi-robot"></i> AI Generate</button>
                            </div>
                            <textarea name="description" id="description" class="input" rows="4" placeholder="Describe your property..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="label">Amenities</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($allAmenities as $am): ?>
                                    <label class="d-flex align-items-center gap-1" style="padding:6px 12px;border:1px solid var(--slate-200);border-radius:20px;cursor:pointer;font-size:0.8rem;">
                                        <input type="checkbox" name="amenities[]" value="<?php echo e($am); ?>" style="accent-color:var(--primary-600);">
                                        <?php echo e($am); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="label">Image URLs</label>
                            <div class="d-flex gap-2 mb-2">
                                <input type="text" id="imageUrl" class="input" placeholder="https://images.pexels.com/...">
                                <button type="button" onclick="addImagePreview()" class="btn btn-ghost"><i class="bi bi-plus-circle"></i> Add</button>
                            </div>
                            <div id="imagePreview" class="d-flex flex-wrap gap-2 mb-2"></div>
                            <input type="hidden" name="images_hidden" id="imagesHidden" value="">
                            <small style="color:var(--slate-400);">Add multiple image URLs for your property</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-3"><i class="bi bi-check-circle"></i> Add Property</button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
