<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$bookingId = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT b.*, p.title as property_title, p.city, p.area, p.images, p.price_per_night FROM bookings b JOIN properties p ON b.property_id = p.id WHERE b.id = ? AND b.user_id = ?');
$stmt->execute([$bookingId, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect('/bookings.php');
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-nav">
    <section class="py-5">
        <div class="container-app" style="max-width:800px;">
            <h1 class="fw-bold mb-4">Payment</h1>

            <div class="row">
                <div class="col-lg-7">
                    <!-- Payment Methods -->
                    <div class="card p-4 mb-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-credit-card"></i> Payment Method</h4>
                        <form method="POST" action="<?php echo url('/api/process-payment.php'); ?>">
                            <input type="hidden" name="booking_id" value="<?php echo (int)$booking['id']; ?>">
                            <input type="hidden" name="total_price" id="hiddenTotal" value="<?php echo e($booking['total_price']); ?>">
                            <div class="d-flex flex-column gap-3">
                                <label class="d-flex align-items-center gap-3 p-3 rounded" style="border:2px solid var(--primary-200);cursor:pointer;">
                                    <input type="radio" name="payment_method" value="card" checked style="accent-color:var(--primary-600);">
                                    <i class="bi bi-credit-card" style="font-size:1.5rem;color:var(--primary-600);"></i>
                                    <div>
                                        <h6 style="font-weight:700;margin:0;">Credit/Debit Card</h6>
                                        <small style="color:var(--slate-500);">Visa, Mastercard, Union Pay</small>
                                    </div>
                                </label>
                                <label class="d-flex align-items-center gap-3 p-3 rounded" style="border:2px solid var(--slate-200);cursor:pointer;">
                                    <input type="radio" name="payment_method" value="wallet" style="accent-color:var(--primary-600);">
                                    <i class="bi bi-wallet2" style="font-size:1.5rem;color:var(--accent-600);"></i>
                                    <div>
                                        <h6 style="font-weight:700;margin:0;">Digital Wallet</h6>
                                        <small style="color:var(--slate-500);">JazzCash, EasyPaisa, SadaPay</small>
                                    </div>
                                </label>
                                <label class="d-flex align-items-center gap-3 p-3 rounded" style="border:2px solid var(--slate-200);cursor:pointer;">
                                    <input type="radio" name="payment_method" value="bank" style="accent-color:var(--primary-600);">
                                    <i class="bi bi-bank" style="font-size:1.5rem;color:var(--secondary-600);"></i>
                                    <div>
                                        <h6 style="font-weight:700;margin:0;">Bank Transfer</h6>
                                        <small style="color:var(--slate-500);">Direct bank deposit</small>
                                    </div>
                                </label>
                            </div>

                            <!-- Coupon Input -->
                            <div class="mt-4">
                                <label class="label">Have a coupon code?</label>
                                <div class="d-flex gap-2">
                                    <input type="text" id="couponCode" class="input" placeholder="Enter coupon code">
                                    <button type="button" onclick="applyCoupon()" class="btn btn-accent">Apply</button>
                                </div>
                                <p id="couponMessage" style="font-size:0.85rem;margin-top:0.5rem;"></p>
                                <small style="color:var(--slate-500);">Available: EARLY20, STAY7, FAMILY4, WELCOME10</small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">
                                <i class="bi bi-lock"></i> Pay <?php echo formatPKR($booking['total_price']); ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="col-lg-5">
                    <div class="card p-4" style="position:sticky;top:90px;">
                        <h4 class="fw-bold mb-3">Booking Summary</h4>
                        <img src="<?php echo e(trim(explode(',', $booking['images'])[0])); ?>" alt="<?php echo e($booking['property_title']); ?>" style="width:100%;height:160px;object-fit:cover;border-radius:10px;margin-bottom:1rem;">
                        <h6 style="font-weight:700;"><?php echo e($booking['property_title']); ?></h6>
                        <p style="color:var(--slate-500);font-size:0.85rem;"><i class="bi bi-geo-alt"></i> <?php echo e($booking['city'] . ', ' . $booking['area']); ?></p>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--slate-600);">Check-in</span>
                            <span style="font-weight:600;"><?php echo formatDate($booking['check_in']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--slate-600);">Check-out</span>
                            <span style="font-weight:600;"><?php echo formatDate($booking['check_out']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--slate-600);">Guests</span>
                            <span style="font-weight:600;"><?php echo (int)$booking['guests']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--slate-600);">Nights</span>
                            <span style="font-weight:600;"><?php echo nightsBetween($booking['check_in'], $booking['check_out']); ?></span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--slate-600);">Subtotal</span>
                            <span id="subtotalAmount" style="font-weight:600;"><?php echo formatPKR($booking['total_price'] / 1.05); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--slate-600);">Service fee (5%)</span>
                            <span id="serviceFeeAmount" style="font-weight:600;"><?php echo formatPKR($booking['total_price'] * 0.05 / 1.05); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" id="discountRow" style="display:none !important;">
                            <span style="color:var(--success-600);">Discount</span>
                            <span id="discountAmount" style="font-weight:600;color:var(--success-600);">PKR 0</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span style="font-weight:700;">Total</span>
                            <span id="totalAmount" style="font-weight:800;color:var(--primary-600);"><?php echo formatPKR($booking['total_price']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
