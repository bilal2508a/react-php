<?php
// Mehmaan Hub - Authentication Functions
// Requires config.php to be included first

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fetch current user from database
function currentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Require login - redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

// Require specific role
function requireRole($role) {
    $user = currentUser();
    if (!$user || $user['role'] !== $role) {
        redirect('/index.php');
    }
}

// Sign in user with email or username and password
function signIn($loginInput, $password) {
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? OR username = ?');
    $stmt->execute([$loginInput, $loginInput]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return true;
    }
    return false;
}

// Sign up new user
function signUp($email, $password, $fullName, $role, $username = '') {
    // Check for duplicate email
    $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return false;
    }
    // Check for duplicate username
    if ($username) {
        $stmt = db()->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return false;
        }
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('INSERT INTO users (full_name, email, password, role, username) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$fullName, $email, $hashed, $role, $username]);
    $_SESSION['user_id'] = db()->lastInsertId();
    return true;
}

// Sign out user
function signOut() {
    session_destroy();
}

// Get dashboard URL based on role
function dashboardUrlForRole($role) {
    switch ($role) {
        case 'admin':
            return '/admin.php';
        case 'owner':
            return '/owner-dashboard.php';
        default:
            return '/profile.php';
    }
}
