<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$propertyId = (int)($_GET['property_id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM properties WHERE id = ?');
$stmt->execute([$propertyId]);
$property = $stmt->fetch();

if (!$property) {
    redirect('/properties.php');
}

$user = currentUser();

// Smart Travel Checklist items
$checklistItems = [
    'Valid ID/Passport',
    'Booking confirmation',
    'Weather-appropriate clothing',
    'Comfortable walking shoes',
    'Phone charger & power bank',
    'Medications & first aid',
    'Cash and cards',
    'Toiletries',
    'Travel insurance docs',
    'Emergency contacts',
    'Camera for memories',
    'Snacks for the journey',
];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checkIn = $_POST['check_in'] ?? '';
    $checkOut = $_POST['check_out'] ?? '';
    $guests = (int)($_POST['guests'] ?? 1);
    $guestName = $_POST['guest_name'] ?? '';
    $guestEmail = $_POST['guest_email'] ?? '';
    $guestPhone = $_POST['guest_phone'] ?? '';
    $specialRequests = $_POST['special_requests'] ?? '';

    // Validation
    if (empty($checkIn)) $errors[] = 'Check-in date is required';
    if (empty($checkOut)) $errors[] = 'Check-out date is required';
    if ($checkIn && $checkOut && $checkIn >= $checkOut) $errors[] = 'Check-out must be after check-in';
    if ($guests < 1) $errors[] = 'At least 1 guest required';
    if ($guests > $property['max_guests']) $errors[] = 'Maximum ' . $property['max_guests'] . ' guests allowed';
    if (empty($guestName)) $errors[] = 'Guest name is required';
    if (empty($guestEmail)) $errors[] = 'Guest email is required';
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-nav">
    <section class="py-5">
        <div class="container-app">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-4">Book Your Stay</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $err): ?>
                                    <li><?php echo e($err); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Guest Details Form -->
                    <div class="card p-4 mb-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-person"></i> Guest Details</h4>
                        <form method="POST" action="/api/create-booking.php">
                            <input type="hidden" name="property_id" value="<?php echo (int)$property['id']; ?>">
                            <input type="hidden" name="total_price" id="hiddenTotal" value="0">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="label">Check-in Date</label>
                                    <input type="date" name="check_in" id="checkIn" class="input" onchange="recalculateTotal()" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="label">Check-out Date</label>
                                    <input type="date" name="check_out" id="checkOut" class="input" onchange="recalculateTotal()" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="label">Number of Guests</label>
                                    <select name="guests" id="guests" class="input">
                                        <?php for ($i = 1; $i <= $property['max_guests']; $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> Guest<?php echo $i > 1 ? 's' : ''; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="label">Full Name</label>
                                    <input type="text" name="guest_name" class="input" value="<?php echo e($user['full_name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="label">Email</label>
                                    <input type="email" name="guest_email" class="input" value="<?php echo e($user['email']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="label">Phone</label>
                                    <input type="text" name="guest_phone" class="input" value="<?php echo e($user['phone'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="label">Special Requests (Optional)</label>
                                    <textarea name="special_requests" class="input" rows="3" placeholder="Any special requirements or requests..."></textarea>
                                </div>
                            </div>
                            <input type="hidden" id="pricePerNight" value="<?php echo e($property['price_per_night']); ?>">
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                                <i class="bi bi-arrow-right-circle"></i> Proceed to Payment
                            </button>
                        </form>
                    </div>

                    <!-- Smart Travel Checklist -->
                    <div class="card p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-list-check"></i> Smart Travel Checklist</h4>
                        <p style="color:var(--slate-500);font-size:0.9rem;">Check items as you prepare for your trip</p>
                        <div class="progress mb-3" style="height:8px;">
                            <div class="progress-bar gradient-primary-accent" id="checklistProgress" style="width:0%;transition:width 0.3s ease;"></div>
                        </div>
                        <p class="text-center mb-3" style="font-size:0.85rem;color:var(--slate-500);"><span id="checklistProgressText">0 / <?php echo count($checklistItems); ?></span> items checked</p>
                        <div class="row g-2">
                            <?php foreach ($checklistItems as $item): ?>
                                <div class="col-md-6">
                                    <div class="checklist-item d-flex align-items-center gap-2 p-2 rounded" style="cursor:pointer;border:1px solid var(--slate-200);" onclick="toggleChecklistItem(this)">
                                        <i class="bi bi-circle" style="color:var(--slate-400);"></i>
                                        <span style="font-size:0.9rem;color:var(--slate-700);"><?php echo e($item); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="col-lg-4">
                    <div class="card p-4" style="position:sticky;top:90px;">
                        <h4 class="fw-bold mb-3">Booking Summary</h4>
                        <img src="<?php echo e(trim(explode(',', $property['images'])[0])); ?>" alt="<?php echo e($property['title']); ?>" style="width:100%;height:180px;object-fit:cover;border-radius:10px;margin-bottom:1rem;">
                        <h6 style="font-weight:700;"><?php echo e($property['title']); ?></h6>
                        <p style="color:var(--slate-500);font-size:0.85rem;"><i class="bi bi-geo-alt"></i> <?php echo e($property['city'] . ', ' . $property['area']); ?></p>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--slate-600);"><?php echo formatPKR($property['price_per_night']); ?> x <span id="nightsCount">0</span> nights</span>
                            <span id="subtotalAmount" style="font-weight:600;">PKR 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--slate-600);">Service fee (5%)</span>
                            <span id="serviceFeeAmount" style="font-weight:600;">PKR 0</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span style="font-weight:700;">Total</span>
                            <span id="totalAmount" style="font-weight:800;color:var(--primary-600);">PKR 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
