<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required';
    } else {
        $stmt = db()->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$name, $email, $subject, $message])) {
            $success = 'Message sent successfully! We will get back to you soon.';
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';

$contactCards = [
    ['icon' => 'bi-geo-alt', 'title' => 'Address', 'value' => 'Main Boulevard, Gulberg III, Lahore, Pakistan'],
    ['icon' => 'bi-envelope', 'title' => 'Email', 'value' => 'hello@mehmaanhub.pk'],
    ['icon' => 'bi-phone', 'title' => 'Phone', 'value' => '+92 300 1234567'],
    ['icon' => 'bi-clock', 'title' => 'Hours', 'value' => 'Mon - Sat: 9AM - 9PM'],
];
?>

<main class="pt-nav">
    <section class="gradient-primary-light py-5">
        <div class="container-app">
            <h1 class="fw-bold">Contact Us</h1>
            <p style="color:var(--slate-600);">We'd love to hear from you</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container-app">
            <!-- Contact Info Cards -->
            <div class="row g-4 mb-5">
                <?php foreach ($contactCards as $card): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card p-4 text-center h-100">
                            <div class="d-flex align-items-center justify-content-center mx-auto mb-3" style="width:56px;height:56px;border-radius:14px;background:var(--primary-100);">
                                <i class="bi <?php echo e($card['icon']); ?>" style="font-size:1.5rem;color:var(--primary-600);"></i>
                            </div>
                            <h6 style="font-weight:700;"><?php echo e($card['title']); ?></h6>
                            <p style="color:var(--slate-500);font-size:0.85rem;margin:0;"><?php echo e($card['value']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row g-4">
                <!-- Map -->
                <div class="col-lg-6">
                    <div class="card p-0 overflow-hidden h-100">
                        <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=74.3429%2C31.5204%2C74.3529%2C31.5304&layer=mapnik" style="width:100%;height:100%;min-height:450px;border:none;"></iframe>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-6">
                    <div class="card p-4 h-100">
                        <h4 class="fw-bold mb-3">Send Us a Message</h4>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo e($success); ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo e($error); ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="label">Your Name</label>
                                <input type="text" name="name" class="input" placeholder="John Doe" required>
                            </div>
                            <div class="mb-3">
                                <label class="label">Email Address</label>
                                <input type="email" name="email" class="input" placeholder="you@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="label">Subject</label>
                                <input type="text" name="subject" class="input" placeholder="How can we help?" required>
                            </div>
                            <div class="mb-3">
                                <label class="label">Message</label>
                                <textarea name="message" class="input" rows="5" placeholder="Your message..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-send"></i> Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
