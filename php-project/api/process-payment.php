<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/bookings.php');
}

$bookingId = (int)($_POST['booking_id'] ?? 0);
$paymentMethod = $_POST['payment_method'] ?? 'card';
$totalPrice = (float)($_POST['total_price'] ?? 0);

// Validate
if ($bookingId <= 0) {
    redirect('/bookings.php');
}

// Verify booking belongs to user
$stmt = db()->prepare('SELECT * FROM bookings WHERE id = ? AND user_id = ?');
$stmt->execute([$bookingId, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect('/bookings.php');
}

// Update booking payment status
$stmt = db()->prepare('UPDATE bookings SET payment_status = ?, payment_method = ?, status = ? WHERE id = ?');
$stmt->execute(['paid', $paymentMethod, 'confirmed', $bookingId]);

redirect('/bookings.php');
