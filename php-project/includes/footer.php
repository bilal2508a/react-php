<?php
// Mehmaan Hub - Footer
?>
<footer class="footer-mh">
    <div class="container-app">
        <div class="row g-4 py-5">
            <div class="col-lg-4">
                <a href="<?php echo url('/index.php'); ?>" class="d-flex align-items-center gap-2 text-decoration-none mb-3">
                    <div class="d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#0ea5e9,#14b8a6);color:#fff;font-size:1.25rem;">
                        <i class="bi bi-buildings"></i>
                    </div>
                    <span style="font-size:1.25rem;font-weight:800;color:#fff;">Mehmaan<span style="color:#0ea5e9;">Hub</span></span>
                </a>
                <p style="color:#94a3b8;font-size:0.9rem;line-height:1.6;">
                    Pakistan's premier property booking platform. Find your perfect stay with AI-powered recommendations and verified listings.
                </p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h5 class="footer-title">Company</h5>
                <a href="<?php echo url('/about.php'); ?>" class="footer-link">About</a>
                <a href="<?php echo url('/contact.php'); ?>" class="footer-link">Contact</a>
                <a href="<?php echo url('/faq.php'); ?>" class="footer-link">FAQ</a>
            </div>

            <div class="col-6 col-lg-3">
                <h5 class="footer-title">Properties</h5>
                <a href="<?php echo url('/properties.php'); ?>" class="footer-link">All Properties</a>
                <a href="<?php echo url('/properties.php'); ?>" class="footer-link">Property List</a>
                <a href="<?php echo url('/properties.php?featured=1'); ?>" class="footer-link">Featured</a>
            </div>

            <div class="col-6 col-lg-3">
                <h5 class="footer-title">Account</h5>
                <a href="<?php echo url('/login.php'); ?>" class="footer-link">Sign In</a>
                <a href="<?php echo url('/register.php'); ?>" class="footer-link">Register</a>
                <a href="<?php echo url('/dashboard.php'); ?>" class="footer-link">Dashboard</a>
                <a href="<?php echo url('/bookings.php'); ?>" class="footer-link">My Bookings</a>
            </div>
        </div>

        <hr style="border-color:#1e293b;">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center py-3 gap-2">
            <p style="color:#64748b;font-size:0.85rem;margin:0;">&copy; <?php echo date('Y'); ?> Mehmaan Hub. All rights reserved.</p>
            <div class="d-flex gap-3">
                <span style="color:#94a3b8;font-size:0.85rem;"><i class="bi bi-envelope"></i> hello@mehmaanhub.pk</span>
                <span style="color:#94a3b8;font-size:0.85rem;"><i class="bi bi-phone"></i> +92 300 1234567</span>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo url('/assets/js/main.js'); ?>"></script>
</body>
</html>
