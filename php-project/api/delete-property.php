<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
requireRole('admin');

$propertyId = (int)($_GET['id'] ?? 0);

if ($propertyId <= 0) {
    redirect('/admin.php');
}

$stmt = db()->prepare('DELETE FROM properties WHERE id = ?');
$stmt->execute([$propertyId]);

redirect('/admin.php');
