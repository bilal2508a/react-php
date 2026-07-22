<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$bookingId = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

// Validate action
if ($action !== 'approve' && $action !== 'reject') {
    redirect('/owner-dashboard.php');
}

// Verify booking belongs to owner's property
$stmt = db()->prepare('SELECT b.* FROM bookings b JOIN properties p ON b.property_id = p.id WHERE b.id = ? AND p.owner_id = ?');
$stmt->execute([$bookingId, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect('/owner-dashboard.php');
}

// Update owner_status
$ownerStatus = $action === 'approve' ? 'approved' : 'rejected';
$status = $action === 'approve' ? 'confirmed' : 'cancelled';

$stmt = db()->prepare('UPDATE bookings SET owner_status = ?, status = ? WHERE id = ?');
$stmt->execute([$ownerStatus, $status, $bookingId]);

redirect('/owner-dashboard.php');
