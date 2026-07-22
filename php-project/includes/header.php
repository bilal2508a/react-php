<?php
// Mehmaan Hub - Header / Navbar
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mehmaan Hub - Find Your Perfect Stay in Pakistan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav id="navbar" class="navbar-mh">
    <div class="container-app d-flex align-items-center justify-content-between" style="height: 72px;">
        <a href="/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#0ea5e9,#14b8a6);color:#fff;font-size:1.25rem;">
                <i class="bi bi-buildings"></i>
            </div>
            <span style="font-size:1.25rem;font-weight:800;color:#0f172a;">Mehmaan<span style="color:#0ea5e9;">Hub</span></span>
        </a>

        <div class="d-none d-lg-flex align-items-center gap-1">
            <a href="/index.php" class="nav-link-mh <?php echo isActive('/') ? 'active' : ''; ?>">Home</a>
            <a href="/properties.php" class="nav-link-mh <?php echo isActive('/properties.php') ? 'active' : ''; ?>">Properties</a>
            <a href="/about.php" class="nav-link-mh <?php echo isActive('/about.php') ? 'active' : ''; ?>">About</a>
            <a href="/contact.php" class="nav-link-mh <?php echo isActive('/contact.php') ? 'active' : ''; ?>">Contact</a>
        </div>

        <div class="d-none d-lg-flex align-items-center gap-2">
            <?php if ($user): ?>
                <div class="position-relative">
                    <button onclick="toggleUserMenu()" class="btn btn-ghost d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center" style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#14b8a6);color:#fff;font-weight:600;">
                            <?php echo e(strtoupper(substr($user['full_name'], 0, 1))); ?>
                        </div>
                        <span style="font-weight:600;font-size:0.9rem;color:#0f172a;"><?php echo e(explode(' ', $user['full_name'])[0]); ?></span>
                        <i class="bi bi-chevron-down" style="font-size:0.75rem;color:#64748b;"></i>
                    </button>
                    <div id="userMenu" class="user-menu">
                        <a href="<?php echo e(dashboardUrlForRole($user['role'])); ?>" class="dropdown-item-mh">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <?php if ($user['role'] === 'tenant'): ?>
                            <a href="/bookings.php" class="dropdown-item-mh">
                                <i class="bi bi-calendar-check"></i> My Bookings
                            </a>
                            <a href="/wishlist.php" class="dropdown-item-mh">
                                <i class="bi bi-heart"></i> Wishlist
                            </a>
                        <?php endif; ?>
                        <?php if ($user['role'] === 'owner'): ?>
                            <a href="/add-property.php" class="dropdown-item-mh">
                                <i class="bi bi-plus-circle"></i> Add Property
                            </a>
                        <?php endif; ?>
                        <a href="/profile.php" class="dropdown-item-mh">
                            <i class="bi bi-person"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="/logout.php" class="dropdown-item-mh text-error">
                            <i class="bi bi-box-arrow-right"></i> Sign Out
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login.php" class="btn btn-ghost">Sign In</a>
                <a href="/register.php" class="btn btn-primary">Get Started</a>
            <?php endif; ?>
        </div>

        <button onclick="toggleMobileMenu()" class="btn btn-ghost d-lg-none" aria-label="Menu">
            <i class="bi bi-list" style="font-size:1.5rem;"></i>
        </button>
    </div>

    <div id="mobileMenu" class="mobile-menu d-lg-none">
        <a href="/index.php" class="mobile-nav-link">Home</a>
        <a href="/properties.php" class="mobile-nav-link">Properties</a>
        <a href="/about.php" class="mobile-nav-link">About</a>
        <a href="/contact.php" class="mobile-nav-link">Contact</a>
        <?php if ($user): ?>
            <a href="<?php echo e(dashboardUrlForRole($user['role'])); ?>" class="mobile-nav-link">Dashboard</a>
            <a href="/bookings.php" class="mobile-nav-link">My Bookings</a>
            <a href="/wishlist.php" class="mobile-nav-link">Wishlist</a>
            <a href="/profile.php" class="mobile-nav-link">Profile</a>
            <a href="/logout.php" class="mobile-nav-link text-error">Sign Out</a>
        <?php else: ?>
            <a href="/login.php" class="mobile-nav-link">Sign In</a>
            <a href="/register.php" class="mobile-nav-link">Get Started</a>
        <?php endif; ?>
    </div>
</nav>
