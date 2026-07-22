<?php
// Mehmaan Hub - Configuration File
// Database constants and helper functions

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'mehmaan_hub');
define('DB_USER', 'root');
define('DB_PASS', '');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Singleton PDO connection
function db() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    return $pdo;
}

// Format amount as PKR currency
function formatPKR($amount) {
    return 'PKR ' . number_format((float)$amount, 0, '.', ',');
}

// Format date string to 'M j, Y'
function formatDate($dateStr) {
    $date = new DateTime($dateStr);
    return $date->format('M j, Y');
}

// Calculate number of nights between two dates
function nightsBetween($checkIn, $checkOut) {
    $in = new DateTime($checkIn);
    $out = new DateTime($checkOut);
    $diff = $in->diff($out);
    return (int)$diff->days;
}

// Get list of Pakistani cities
function getCities() {
    return [
        ['name' => 'Karachi', 'image' => 'https://images.pexels.com/photos/2699348/pexels-photo-2699348.jpeg', 'count' => 24],
        ['name' => 'Lahore', 'image' => 'https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg', 'count' => 18],
        ['name' => 'Islamabad', 'image' => 'https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg', 'count' => 15],
        ['name' => 'Hunza', 'image' => 'https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg', 'count' => 12],
        ['name' => 'Murree', 'image' => 'https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg', 'count' => 9],
        ['name' => 'Skardu', 'image' => 'https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg', 'count' => 7],
    ];
}

// Get property types
function getPropertyTypes() {
    return [
        ['value' => 'guest_house', 'label' => 'Guest House', 'icon' => 'bi-house-door'],
        ['value' => 'apartment', 'label' => 'Apartment', 'icon' => 'bi-building'],
        ['value' => 'villa', 'label' => 'Villa', 'icon' => 'bi-house'],
        ['value' => 'hotel', 'label' => 'Hotel', 'icon' => 'bi-building-fill'],
    ];
}

// Get all amenities (27)
function getAllAmenities() {
    return [
        'WiFi',
        'Air Conditioning',
        'Heating',
        'Kitchen',
        'Free Parking',
        'Swimming Pool',
        'Gym',
        'Hot Tub',
        'Washing Machine',
        'Dryer',
        'TV',
        'Cable TV',
        'Netflix',
        'Workspace',
        'Fireplace',
        'BBQ Grill',
        'Garden',
        'Balcony',
        'Mountain View',
        'City View',
        'Sea View',
        'Breakfast',
        'Room Service',
        '24/7 Security',
        'Pet Friendly',
        'Family Friendly',
        'Wheelchair Accessible',
    ];
}

// Escape output for HTML safety
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Redirect to a path
function redirect($path) {
    header('Location: ' . $path);
    exit;
}

// Get current path
function currentPath() {
    return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
}

// Check if a nav link is active
function isActive($path) {
    $current = currentPath();
    if ($path === '/' && $current === '/') {
        return true;
    }
    if ($path !== '/' && strpos($current, $path) === 0) {
        return true;
    }
    return false;
}
