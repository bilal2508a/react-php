<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';

$team = [
    ['name' => 'Ahmed Raza', 'role' => 'Founder & CEO', 'icon' => 'bi-person-badge'],
    ['name' => 'Sana Khan', 'role' => 'Head of Operations', 'icon' => 'bi-gear'],
    ['name' => 'Usman Ali', 'role' => 'Lead Developer', 'icon' => 'bi-code-slash'],
    ['name' => 'Zainab Malik', 'role' => 'Customer Success', 'icon' => 'bi-headset'],
];
?>

<main class="pt-nav">
    <!-- Banner -->
    <section class="hero-section" style="background-image:url('https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg');min-height:400px;">
        <div class="container-app">
            <div style="max-width:600px;">
                <h1 class="hero-title">About Mehmaan Hub</h1>
                <p class="hero-subtitle">Pakistan's premier property booking platform, connecting travelers with authentic stays across the country.</p>
            </div>
        </div>
    </section>

    <!-- Story -->
    <section class="py-5">
        <div class="container-app" style="max-width:800px;">
            <h2 class="section-title">Our Story</h2>
            <p style="color:var(--slate-600);line-height:1.8;font-size:1.05rem;">
                Founded in 2024, Mehmaan Hub was born from a simple idea: make finding and booking properties in Pakistan effortless. 
                We noticed that travelers struggled to find quality accommodations that matched their needs, and property owners lacked a 
                reliable platform to showcase their spaces.
            </p>
            <p style="color:var(--slate-600);line-height:1.8;font-size:1.05rem;margin-top:1rem;">
                Today, Mehmaan Hub serves thousands of guests across 6 major Pakistani cities, from the beaches of Karachi to the 
                mountains of Hunza. Our AI-powered recommendations ensure every guest finds their perfect stay.
            </p>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-5 bg-slate">
        <div class="container-app">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;border-radius:14px;background:var(--primary-100);">
                            <i class="bi bi-bullseye" style="font-size:1.5rem;color:var(--primary-600);"></i>
                        </div>
                        <h4 class="fw-bold">Our Mission</h4>
                        <p style="color:var(--slate-600);margin-top:0.5rem;">
                            To make travel in Pakistan accessible, affordable, and authentic by connecting guests with quality 
                            accommodations and local hosts across the country.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;border-radius:14px;background:var(--accent-100);">
                            <i class="bi bi-eye" style="font-size:1.5rem;color:var(--accent-600);"></i>
                        </div>
                        <h4 class="fw-bold">Our Vision</h4>
                        <p style="color:var(--slate-600);margin-top:0.5rem;">
                            To become Pakistan's most trusted travel platform, empowering local communities and showcasing the 
                            beauty and hospitality of our country to the world.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-5 gradient-primary-accent">
        <div class="container-app">
            <div class="row g-4 text-center text-white">
                <div class="col-6 col-lg-3">
                    <h2 style="font-size:2.5rem;font-weight:800;">500+</h2>
                    <p style="opacity:0.9;">Properties</p>
                </div>
                <div class="col-6 col-lg-3">
                    <h2 style="font-size:2.5rem;font-weight:800;">10K+</h2>
                    <p style="opacity:0.9;">Happy Guests</p>
                </div>
                <div class="col-6 col-lg-3">
                    <h2 style="font-size:2.5rem;font-weight:800;">6</h2>
                    <p style="opacity:0.9;">Cities</p>
                </div>
                <div class="col-6 col-lg-3">
                    <h2 style="font-size:2.5rem;font-weight:800;">200+</h2>
                    <p style="opacity:0.9;">Property Owners</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="py-5">
        <div class="container-app">
            <h2 class="section-title text-center">Meet Our Team</h2>
            <p class="section-subtitle text-center">The people behind Mehmaan Hub</p>
            <div class="row g-4">
                <?php foreach ($team as $member): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card p-4 text-center h-100 animate-fadeInUp">
                            <div class="d-flex align-items-center justify-content-center mx-auto mb-3" style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--primary-500),var(--accent-500));color:#fff;font-size:2rem;">
                                <i class="bi <?php echo e($member['icon']); ?>"></i>
                            </div>
                            <h5 style="font-weight:700;"><?php echo e($member['name']); ?></h5>
                            <p style="color:var(--primary-600);font-size:0.85rem;font-weight:600;"><?php echo e($member['role']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5">
        <div class="container-app">
            <div class="card p-5 text-center gradient-primary-accent" style="border:none;">
                <h2 style="font-size:2rem;font-weight:800;color:#fff;">Join Our Journey</h2>
                <p style="color:rgba(255,255,255,0.9);font-size:1.1rem;margin-top:0.5rem;">Become part of Pakistan's growing travel community</p>
                <div class="d-flex gap-3 justify-content-center mt-3 flex-wrap">
                    <a href="<?php echo url('/register.php'); ?>" class="btn btn-light btn-lg">Get Started</a>
                    <a href="<?php echo url('/contact.php'); ?>" class="btn btn-outline-light btn-lg">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
