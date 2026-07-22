<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (signIn($email, $password)) {
        $user = currentUser();
        redirect(dashboardUrlForRole($user['role']));
    } else {
        $error = 'Invalid email or password';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-nav" style="min-height:100vh;">
    <div class="container-fluid">
        <div class="row" style="min-height:calc(100vh - 72px);">
            <!-- Left Brand Image -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center p-5" style="background:linear-gradient(135deg,rgba(14,165,233,0.8),rgba(20,184,166,0.8)),url('https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg') center/cover;">
                <div style="max-width:450px;color:#fff;">
                    <h1 style="font-size:2.5rem;font-weight:800;">Welcome Back to Mehmaan Hub</h1>
                    <p style="font-size:1.1rem;opacity:0.9;margin-top:1rem;">Pakistan's premier property booking platform. Sign in to continue your journey.</p>
                    <div class="d-flex gap-4 mt-4">
                        <div>
                            <h3 style="font-weight:800;">500+</h3>
                            <p style="opacity:0.9;">Properties</p>
                        </div>
                        <div>
                            <h3 style="font-weight:800;">10K+</h3>
                            <p style="opacity:0.9;">Happy Guests</p>
                        </div>
                        <div>
                            <h3 style="font-weight:800;">4.8</h3>
                            <p style="opacity:0.9;">Avg Rating</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Form -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-5">
                <div style="width:100%;max-width:400px;">
                    <a href="<?php echo url('/index.php'); ?>" class="d-flex align-items-center gap-2 text-decoration-none mb-4">
                        <div class="d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#0ea5e9,#14b8a6);color:#fff;font-size:1.25rem;">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <span style="font-size:1.25rem;font-weight:800;color:#0f172a;">Mehmaan<span style="color:#0ea5e9;">Hub</span></span>
                    </a>

                    <h2 style="font-weight:800;">Sign In</h2>
                    <p style="color:var(--slate-500);">Enter your credentials to access your account</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger mt-3"><?php echo e($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label class="label">Email Address</label>
                            <input type="email" name="email" class="input" placeholder="you@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="label">Password</label>
                            <input type="password" name="password" class="input" placeholder="••••••••" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember" style="font-size:0.85rem;color:var(--slate-600);">Remember me</label>
                            </div>
                            <a href="#" style="font-size:0.85rem;color:var(--primary-600);">Forgot password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Sign In</button>
                    </form>

                    <p class="text-center mt-4" style="color:var(--slate-500);">Don't have an account? <a href="<?php echo url('/register.php'); ?>" style="color:var(--primary-600);font-weight:600;">Sign Up</a></p>

                    <div class="card p-3 mt-4" style="background:var(--slate-50);">
                        <p style="font-size:0.8rem;color:var(--slate-500);margin:0;"><strong>Demo Admin:</strong> admin@mehmaanhub.pk / admin123</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
