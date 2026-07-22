<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';

$faqs = [
    ['category' => 'Booking', 'q' => 'How do I book a property on Mehmaan Hub?', 'a' => 'Simply browse properties, select your dates, fill in guest details, and proceed to payment. Your booking will be confirmed once payment is processed.'],
    ['category' => 'Booking', 'q' => 'Can I modify my booking after confirmation?', 'a' => 'Yes, you can modify your booking by contacting the property owner or our support team. Changes are subject to availability and property policies.'],
    ['category' => 'Booking', 'q' => 'What is the maximum number of guests allowed?', 'a' => 'Each property has its own maximum guest capacity. You can see this on the property details page before booking.'],
    ['category' => 'Payment', 'q' => 'What payment methods are accepted?', 'a' => 'We accept credit/debit cards, digital wallets (JazzCash, EasyPaisa), and bank transfers. All payments are processed securely.'],
    ['category' => 'Payment', 'q' => 'Is my payment information secure?', 'a' => 'Yes, we use encrypted payment gateways and never store your card details. Your financial information is always protected.'],
    ['category' => 'Payment', 'q' => 'Are there any hidden fees?', 'a' => 'No hidden fees. We charge a transparent 5% service fee on each booking, which is clearly shown before you confirm.'],
    ['category' => 'Cancellation', 'q' => 'What is the cancellation policy?', 'a' => 'Free cancellation is available up to 48 hours before check-in for most properties. Some properties may have different policies.'],
    ['category' => 'Cancellation', 'q' => 'How do I get a refund?', 'a' => 'Refunds are processed to your original payment method within 5-7 business days after cancellation approval.'],
    ['category' => 'Cancellation', 'q' => 'Can I cancel a non-refundable booking?', 'a' => 'Non-refundable bookings cannot be cancelled for a refund, but you may still be able to modify dates depending on the property.'],
    ['category' => 'Features', 'q' => 'How does the AI recommendation system work?', 'a' => 'Our AI analyzes your search preferences, budget, group size, and ratings to suggest properties that best match your needs.'],
    ['category' => 'Features', 'q' => 'What is the Smart Travel Checklist?', 'a' => 'It is a helpful feature that provides a checklist of items to prepare for your trip, ensuring you do not forget anything important.'],
    ['category' => 'Hosting', 'q' => 'How do I list my property?', 'a' => 'Register as an owner, click Add Property, fill in your property details, add images and amenities, and your listing goes live immediately.'],
];

$categories = ['all', 'Booking', 'Payment', 'Cancellation', 'Features', 'Hosting'];
?>

<main class="pt-nav">
    <section class="gradient-primary-light py-5">
        <div class="container-app">
            <h1 class="fw-bold">Frequently Asked Questions</h1>
            <p style="color:var(--slate-600);">Find answers to common questions</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container-app" style="max-width:800px;">
            <!-- Search -->
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-slate"></i></span>
                    <input type="text" id="faqSearch" class="input border-start-0" placeholder="Search FAQs..." onkeyup="searchFAQ()">
                </div>
            </div>

            <!-- Category Filter -->
            <div class="d-flex gap-2 mb-4 flex-wrap">
                <?php foreach ($categories as $cat): ?>
                    <button class="faq-category-btn tab-btn <?php echo $cat === 'all' ? 'active' : ''; ?>" onclick="filterFAQ('<?php echo e($cat); ?>')">
                        <?php echo $cat === 'all' ? 'All' : e($cat); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- FAQ Items -->
            <div class="d-flex flex-column gap-3">
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item card p-0 overflow-hidden" data-category="<?php echo e($faq['category']); ?>">
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

            <!-- Contact Support CTA -->
            <div class="card p-5 text-center mt-5 gradient-primary-light">
                <h3 class="fw-bold">Still Have Questions?</h3>
                <p style="color:var(--slate-600);">Our support team is here to help you 24/7</p>
                <div class="d-flex gap-3 justify-content-center mt-3 flex-wrap">
                    <a href="<?php echo url('/contact.php'); ?>" class="btn btn-primary btn-lg"><i class="bi bi-headset"></i> Contact Support</a>
                    <a href="mailto:hello@mehmaanhub.pk" class="btn btn-ghost btn-lg"><i class="bi bi-envelope"></i> Email Us</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
