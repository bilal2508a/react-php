<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';

$cities = getCities();
$propertyTypes = getPropertyTypes();

// Fetch featured properties
$stmt = db()->prepare('SELECT * FROM properties WHERE is_featured = 1 LIMIT 6');
$stmt->execute();
$featuredProperties = $stmt->fetchAll();

// Fetch all properties for category counts
$stmt = db()->query('SELECT property_type, COUNT(*) as cnt FROM properties GROUP BY property_type');
$typeCounts = [];
foreach ($stmt->fetchAll() as $row) {
    $typeCounts[$row['property_type']] = $row['cnt'];
}

// Why Choose Us features
$features = [
    ['icon' => 'bi-robot', 'title' => 'AI Recommendations', 'desc' => 'Smart algorithms match you with perfect stays based on your preferences.', 'color' => 'primary'],
    ['icon' => 'bi-shield-check', 'title' => 'Verified Listings', 'desc' => 'Every property is verified for quality and authenticity.', 'color' => 'success'],
    ['icon' => 'bi-geo-alt', 'title' => 'Local Expertise', 'desc' => 'Discover hidden gems across Pakistan with local insights.', 'color' => 'accent'],
    ['icon' => 'bi-cash-coin', 'title' => 'Best Price Guarantee', 'desc' => 'Get the best rates with our price match promise.', 'color' => 'secondary'],
    ['icon' => 'bi-headset', 'title' => '24/7 Support', 'desc' => 'Round-the-clock customer support for peace of mind.', 'color' => 'primary'],
    ['icon' => 'bi-heart', 'title' => 'Wishlist & Save', 'desc' => 'Save your favorite properties and book when ready.', 'color' => 'error'],
];

// How It Works steps
$steps = [
    ['icon' => 'bi-search', 'title' => 'Search', 'desc' => 'Browse thousands of properties across Pakistan.'],
    ['icon' => 'bi-calendar-check', 'title' => 'Book', 'desc' => 'Select dates and book your perfect stay instantly.'],
    ['icon' => 'bi-airplane', 'title' => 'Enjoy', 'desc' => 'Check in and enjoy your memorable Pakistani experience!'],
];

// Special Offers
$offers = [
    ['code' => 'EARLY20', 'discount' => '20% OFF', 'desc' => 'Book 30 days in advance and save 20%'],
    ['code' => 'STAY7', 'discount' => '15% OFF', 'desc' => 'Stay 7+ nights and get 15% discount'],
    ['code' => 'FAMILY4', 'discount' => '10% OFF', 'desc' => 'Family booking of 4+ guests gets 10% off'],
];

// Testimonials
$testimonials = [
    ['name' => 'Ayesha Khan', 'location' => 'Karachi', 'text' => 'Mehmaan Hub made finding accommodation in Hunza so easy. The AI recommendations were spot on!', 'rating' => 5],
    ['name' => 'Bilal Ahmed', 'location' => 'Lahore', 'text' => 'Best booking platform in Pakistan. Found a beautiful villa in Murree at an amazing price.', 'rating' => 5],
    ['name' => 'Fatima Malik', 'location' => 'Islamabad', 'text' => 'The travel checklist feature helped me prepare perfectly for my Skardu trip. Highly recommended!', 'rating' => 5],
];

// FAQ items
$faqs = [
    ['q' => 'How does Mehmaan Hub work?', 'a' => 'Simply search for properties, select your dates, and book. Our AI helps you find the perfect stay based on your preferences.'],
    ['q' => 'Is my payment secure?', 'a' => 'Yes, we use secure payment gateways and your information is encrypted. We never store your card details.'],
    ['q' => 'Can I cancel my booking?', 'a' => 'Yes, you can cancel bookings based on the property cancellation policy. Free cancellation is available for most properties.'],
    ['q' => 'How do I become a property owner?', 'a' => 'Register as an owner, add your property details, and start earning. Our platform handles bookings and payments seamlessly.'],
];
?>

<main class="pt-nav">
    <!-- Hero Section -->
    <section class="hero-section" style="background-image:url('https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg');">
        <div class="container-app">
            <div style="max-width:640px;">
                <h1 class="hero-title animate-fadeInUp">Find Your Perfect Stay in Pakistan</h1>
                <p class="hero-subtitle animate-fadeInUp">Discover verified properties across Pakistan with AI-powered recommendations. From Hunza valleys to Karachi beaches, your perfect stay awaits.</p>
                <div class="d-flex gap-3 flex-wrap animate-fadeInUp">
                    <a href="/properties.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-search"></i> Explore Properties
                    </a>
                    <a href="/register.php" class="btn btn-light btn-lg glass">
                        <i class="bi bi-person-plus"></i> Get Started
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-5">
        <div class="container-app">
            <h2 class="section-title text-center">Why Choose Mehmaan Hub?</h2>
            <p class="section-subtitle text-center">Pakistan's most trusted property booking platform</p>
            <div class="row g-4">
                <?php foreach ($features as $f): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 animate-fadeInUp">
                            <div class="d-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,var(--primary-50),var(--accent-50));">
                                <i class="bi <?php echo e($f['icon']); ?>" style="font-size:1.5rem;color:var(--primary-600);"></i>
                            </div>
                            <h5 style="font-weight:700;color:var(--slate-900);"><?php echo e($f['title']); ?></h5>
                            <p style="color:var(--slate-500);font-size:0.9rem;margin-top:0.5rem;"><?php echo e($f['desc']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Popular Cities -->
    <section class="py-5 bg-slate">
        <div class="container-app">
            <h2 class="section-title text-center">Popular Cities</h2>
            <p class="section-subtitle text-center">Explore top destinations across Pakistan</p>
            <div class="row g-4">
                <?php foreach ($cities as $city): ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="/properties.php?city=<?php echo urlencode($city['name']); ?>" class="text-decoration-none">
                            <div class="card h-100 text-center p-3 animate-fadeInUp">
                                <img src="<?php echo e($city['image']); ?>" alt="<?php echo e($city['name']); ?>" style="width:100%;height:120px;object-fit:cover;border-radius:10px;">
                                <h6 style="font-weight:700;color:var(--slate-900);margin-top:0.75rem;"><?php echo e($city['name']); ?></h6>
                                <p style="color:var(--slate-500);font-size:0.8rem;"><?php echo (int)$city['count']; ?> Properties</p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Properties -->
    <section class="py-5">
        <div class="container-app">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="section-title">Featured Properties</h2>
                    <p class="section-subtitle mb-0">Handpicked stays for the best experience</p>
                </div>
                <a href="/properties.php" class="btn btn-ghost">View All <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach ($featuredProperties as $p): ?>
                    <div class="col-md-6 col-lg-4">
                        <?php include __DIR__ . '/includes/property_card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Property Categories -->
    <section class="py-5 bg-slate">
        <div class="container-app">
            <h2 class="section-title text-center">Browse by Category</h2>
            <p class="section-subtitle text-center">Find the perfect type of accommodation</p>
            <div class="row g-4">
                <?php foreach ($propertyTypes as $pt): ?>
                    <div class="col-6 col-lg-3">
                        <a href="/properties.php?type=<?php echo urlencode($pt['value']); ?>" class="text-decoration-none">
                            <div class="card h-100 text-center p-4 animate-fadeInUp">
                                <i class="bi <?php echo e($pt['icon']); ?>" style="font-size:2.5rem;color:var(--primary-600);"></i>
                                <h5 style="font-weight:700;color:var(--slate-900);margin-top:0.75rem;"><?php echo e($pt['label']); ?></h5>
                                <p style="color:var(--slate-500);font-size:0.85rem;"><?php echo (int)($typeCounts[$pt['value']] ?? 0); ?> Properties</p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-5">
        <div class="container-app">
            <h2 class="section-title text-center">How It Works</h2>
            <p class="section-subtitle text-center">Book your stay in 3 easy steps</p>
            <div class="row g-4">
                <?php foreach ($steps as $i => $step): ?>
                    <div class="col-md-4">
                        <div class="card h-100 text-center p-4 animate-fadeInUp">
                            <div class="d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--primary-500),var(--accent-500));color:#fff;font-size:1.5rem;font-weight:800;">
                                <?php echo $i + 1; ?>
                            </div>
                            <i class="bi <?php echo e($step['icon']); ?>" style="font-size:2rem;color:var(--primary-600);"></i>
                            <h5 style="font-weight:700;color:var(--slate-900);margin-top:0.75rem;"><?php echo e($step['title']); ?></h5>
                            <p style="color:var(--slate-500);font-size:0.9rem;margin-top:0.5rem;"><?php echo e($step['desc']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Special Offers -->
    <section class="py-5 bg-slate">
        <div class="container-app">
            <h2 class="section-title text-center">Special Offers</h2>
            <p class="section-subtitle text-center">Save more with our exclusive coupons</p>
            <div class="row g-4">
                <?php foreach ($offers as $offer): ?>
                    <div class="col-md-4">
                        <div class="card h-100 p-4 animate-fadeInUp" style="border:2px dashed var(--primary-300);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge badge-success" style="font-size:0.9rem;"><?php echo e($offer['discount']); ?></span>
                                <i class="bi bi-ticket-perforated" style="font-size:1.5rem;color:var(--primary-400);"></i>
                            </div>
                            <h5 style="font-weight:800;color:var(--primary-600);"><?php echo e($offer['code']); ?></h5>
                            <p style="color:var(--slate-500);font-size:0.85rem;"><?php echo e($offer['desc']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Counter -->
    <section class="py-5 gradient-primary-accent">
        <div class="container-app">
            <div class="row g-4 text-center text-white">
                <div class="col-6 col-lg-3">
                    <h2 style="font-size:2.5rem;font-weight:800;">500+</h2>
                    <p style="opacity:0.9;">Properties Listed</p>
                </div>
                <div class="col-6 col-lg-3">
                    <h2 style="font-size:2.5rem;font-weight:800;">10K+</h2>
                    <p style="opacity:0.9;">Happy Guests</p>
                </div>
                <div class="col-6 col-lg-3">
                    <h2 style="font-size:2.5rem;font-weight:800;">6</h2>
                    <p style="opacity:0.9;">Cities Covered</p>
                </div>
                <div class="col-6 col-lg-3">
                    <h2 style="font-size:2.5rem;font-weight:800;">4.8</h2>
                    <p style="opacity:0.9;">Average Rating</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5">
        <div class="container-app">
            <h2 class="section-title text-center">What Our Guests Say</h2>
            <p class="section-subtitle text-center">Trusted by thousands of travelers</p>
            <div class="row g-4">
                <?php foreach ($testimonials as $t): ?>
                    <div class="col-md-4">
                        <div class="card h-100 p-4 animate-fadeInUp">
                            <div class="d-flex gap-1 mb-3">
                                <?php for ($i = 0; $i < $t['rating']; $i++): ?>
                                    <i class="bi bi-star-fill" style="color:#f59e0b;"></i>
                                <?php endfor; ?>
                            </div>
                            <p style="color:var(--slate-700);font-size:0.95rem;">"<?php echo e($t['text']); ?>"</p>
                            <div class="d-flex align-items-center gap-2 mt-3">
                                <div class="d-flex align-items-center justify-content-center" style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--primary-500),var(--accent-500));color:#fff;font-weight:700;">
                                    <?php echo e(strtoupper(substr($t['name'], 0, 1))); ?>
                                </div>
                                <div>
                                    <h6 style="font-weight:700;color:var(--slate-900);margin:0;"><?php echo e($t['name']); ?></h6>
                                    <small style="color:var(--slate-500);"><?php echo e($t['location']); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-5 bg-slate">
        <div class="container-app" style="max-width:800px;">
            <h2 class="section-title text-center">Frequently Asked Questions</h2>
            <p class="section-subtitle text-center">Everything you need to know</p>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($faqs as $faq): ?>
                    <div class="card p-0 overflow-hidden">
                        <button class="faq-toggle btn btn-ghost w-100 text-start d-flex justify-content-between align-items-center p-3" style="border:none;border-radius:0;">
                            <span style="font-weight:700;color:var(--slate-900);"><?php echo e($faq['q']); ?></span>
                            <i class="bi bi-chevron-down faq-icon" style="color:var(--slate-500);"></i>
                        </button>
                        <div class="faq-answer">
                            <p style="color:var(--slate-600);"><?php echo e($faq['a']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Banner -->
    <section class="py-5">
        <div class="container-app">
            <div class="card p-5 text-center gradient-primary-accent" style="border:none;">
                <h2 style="font-size:2rem;font-weight:800;color:#fff;">Ready to Find Your Perfect Stay?</h2>
                <p style="color:rgba(255,255,255,0.9);font-size:1.1rem;margin-top:0.5rem;">Join thousands of happy travelers across Pakistan</p>
                <div class="d-flex gap-3 justify-content-center mt-3 flex-wrap">
                    <a href="/register.php" class="btn btn-light btn-lg"><i class="bi bi-person-plus"></i> Sign Up Now</a>
                    <a href="/properties.php" class="btn btn-outline-light btn-lg"><i class="bi bi-search"></i> Browse Properties</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
