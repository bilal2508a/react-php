<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'tenant';
    if ($role !== 'tenant' && $role !== 'owner') {
        $role = 'tenant';
    }
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (signUp($email, $password, $fullName, $role, $username)) {
        $user = currentUser();
        redirect(dashboardUrlForRole($user['role']));
    } else {
        $error = 'Email or username already registered. Please use different credentials.';
    }
}

require_once __DIR__ . '/includes/header-minimal.php';
?>

<main style="min-height:100vh;">
    <div class="container-fluid">
        <div class="row" style="min-height:100vh;">
            <!-- Left Form -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-5 order-lg-1 order-2">
                <div style="width:100%;max-width:400px;">
                    <a href="<?php echo url('/index.php'); ?>" class="d-flex align-items-center gap-2 text-decoration-none mb-4">
                        <div class="d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#0ea5e9,#14b8a6);color:#fff;font-size:1.25rem;">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <span style="font-size:1.25rem;font-weight:800;color:#0f172a;">Mehmaan<span style="color:#0ea5e9;">Hub</span></span>
                    </a>

                    <h2 style="font-weight:800;">Create Account</h2>
                    <p style="color:var(--slate-500);">Join Mehmaan Hub and start your journey</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger mt-3"><?php echo e($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" class="mt-4" autocomplete="off">
                        <!-- Role Selection -->
                        <label class="label">I want to:</label>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="d-flex align-items-center gap-2 p-3 rounded" style="border:2px solid var(--primary-200);cursor:pointer;">
                                    <input type="radio" name="role" value="tenant" checked style="accent-color:var(--primary-600);">
                                    <div>
                                        <i class="bi bi-person" style="font-size:1.25rem;color:var(--primary-600);"></i>
                                        <span style="font-weight:700;font-size:0.9rem;">Book Stays</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="d-flex align-items-center gap-2 p-3 rounded" style="border:2px solid var(--slate-200);cursor:pointer;">
                                    <input type="radio" name="role" value="owner" style="accent-color:var(--primary-600);">
                                    <div>
                                        <i class="bi bi-building" style="font-size:1.25rem;color:var(--secondary-600);"></i>
                                        <span style="font-weight:700;font-size:0.9rem;">List Properties</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="label">Full Name</label>
                            <input type="text" name="full_name" class="input" placeholder="John Doe" required>
                        </div>
                        <div class="mb-3">
                            <label class="label">Username</label>
                            <input type="text" name="username" class="input" placeholder="johndoe" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label class="label">Email Address</label>
                            <input type="email" name="email" class="input" placeholder="you@example.com" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label class="label">Password</label>
                            <div class="position-relative">
                                <input type="password" name="password" id="passwordField" class="input" placeholder="At least 6 characters" required autocomplete="new-password" style="padding-right:44px;">
                                <button type="button" onclick="togglePassword('passwordField', 'pwIcon')" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--slate-400);padding:4px;">
                                    <i class="bi bi-eye" id="pwIcon" style="font-size:1.1rem;"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Create Account</button>
                    </form>

                    <p class="text-center mt-4" style="color:var(--slate-500);">Already have an account? <a href="<?php echo url('/login.php'); ?>" style="color:var(--primary-600);font-weight:600;">Sign In</a></p>
                </div>
            </div>

            <!-- Right Brand Image -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center p-5 order-lg-2 order-1" style="background:linear-gradient(135deg,rgba(20,184,166,0.8),rgba(14,165,233,0.8)),url('https://images.pexels.com/photos/2422259/pexels-photo-2422259.jpeg') center/cover;">
                <div style="max-width:450px;color:#fff;">
                    <h1 style="font-size:2.5rem;font-weight:800;">Start Your Journey with Mehmaan Hub</h1>
                    <p style="font-size:1.1rem;opacity:0.9;margin-top:1rem;">Join thousands of travelers and property owners across Pakistan. Experience the best stays with AI-powered recommendations.</p>
                    <div class="d-flex gap-4 mt-4">
                        <div>
                            <h3 style="font-weight:800;">6</h3>
                            <p style="opacity:0.9;">Cities</p>
                        </div>
                        <div>
                            <h3 style="font-weight:800;">27</h3>
                            <p style="opacity:0.9;">Amenities</p>
                        </div>
                        <div>
                            <h3 style="font-weight:800;">100%</h3>
                            <p style="opacity:0.9;">Verified</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function togglePassword(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

<?php require __DIR__ . '/includes/footer-minimal.php'; ?>
