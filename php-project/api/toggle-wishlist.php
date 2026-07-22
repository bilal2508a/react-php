<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$propertyId = (int)($_GET['property_id'] ?? 0);
$userId = $_SESSION['user_id'];

// Check if already in wishlist
$stmt = db()->prepare('SELECT id FROM wishlist WHERE user_id = ? AND property_id = ?');
$stmt->execute([$userId, $propertyId]);
$existing = $stmt->fetch();

if ($existing) {
    // Remove from wishlist
    $stmt = db()->prepare('DELETE FROM wishlist WHERE user_id = ? AND property_id = ?');
    $stmt->execute([$userId, $propertyId]);
} else {
    // Add to wishlist
    $stmt = db()->prepare('INSERT INTO wishlist (user_id, property_id) VALUES (?, ?)');
    $stmt->execute([$userId, $propertyId]);
}

// Redirect back
$referer = $_SERVER['HTTP_REFERER'] ?? '/wishlist.php';
redirect($referer);
