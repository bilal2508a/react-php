<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$user = currentUser();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $travelPersonality = $_POST['travel_personality'] ?? 'explorer';
    if ($travelPersonality !== 'adventurer' && $travelPersonality !== 'relaxer' && $travelPersonality !== 'explorer' && $travelPersonality !== 'foodie') {
        $travelPersonality = 'explorer';
    }
    if (empty($fullName)) {
        $error = 'Full name is required';
    } else {
        $stmt = db()->prepare('UPDATE users SET full_name = ?, phone = ?, travel_personality = ? WHERE id = ?');
        if ($stmt->execute([$fullName, $phone, $travelPersonality, $_SESSION['user_id']])) {
            $success = 'Profile updated successfully!';
            $user = currentUser();
        } else {
            $error = 'Failed to update profile';
        }
    }
}

// Owner stats if owner
$ownerStats = null;
if ($user['role'] === 'owner') {
    $stmt = db()->prepare('SELECT COUNT(*) FROM properties WHERE owner_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $ownerPropertyCount = $stmt->fetchColumn();
    $stmt = db()->prepare('SELECT SUM(total_price) FROM bookings b JOIN properties p ON b.property_id = p.id WHERE p.owner_id = ? AND b.payment_status = ?');
    $stmt->execute([$_SESSION['user_id'], 'paid']);
    $ownerEarnings = (float)$stmt->fetchColumn();
    $ownerStats = ['properties' => $ownerPropertyCount, 'earnings' => $ownerEarnings];
}

$personalities = [
    ['value' => 'adventurer', 'label' => 'Adventurer', 'icon' => 'bi-mountain', 'desc' => 'Love outdoor adventures and hiking'],
    ['value' => 'relaxer', 'label' => 'Relaxer', 'icon' => 'bi-umbrella', 'desc' => 'Prefer peaceful and relaxing getaways'],
    ['value' => 'explorer', 'label' => 'Explorer', 'icon' => 'bi-compass', 'desc' => 'Enjoy discovering new places and cultures'],
    ['value' => 'foodie', 'label' => 'Foodie', 'icon' => 'bi-cup-hot', 'desc' => 'Travel for culinary experiences'],
];

require_once __DIR__ . '/includes/header.php';
?>

<main class="pt-nav">
    <section class="gradient-primary-light py-5">
        <div class="container-app">
            <h1 class="fw-bold">My Profile</h1>
            <p style="color:var(--slate-600);">View and manage your account information</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container-app">
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo e($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Profile View (default) -->
                    <div class="card p-4 mb-4" id="profileView">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold mb-0"><i class="bi bi-person-circle"></i> Profile Information</h4>
                            <button onclick="showEditForm()" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit Profile</button>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background:var(--slate-50);">
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0 0 4px 0;">Full Name</p>
                                    <p style="font-weight:700;font-size:1.1rem;margin:0;"><?php echo e($user['full_name']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background:var(--slate-50);">
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0 0 4px 0;">Email Address</p>
                                    <p style="font-weight:700;font-size:1.1rem;margin:0;"><?php echo e($user['email']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background:var(--slate-50);">
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0 0 4px 0;">Username</p>
                                    <p style="font-weight:700;font-size:1.1rem;margin:0;"><?php echo e($user['username'] ?? 'Not set'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background:var(--slate-50);">
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0 0 4px 0;">Phone Number</p>
                                    <p style="font-weight:700;font-size:1.1rem;margin:0;"><?php echo e($user['phone'] ?? 'Not set'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background:var(--slate-50);">
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0 0 4px 0;">Role</p>
                                    <p style="font-weight:700;font-size:1.1rem;margin:0;">
                                        <span class="badge badge-primary" style="font-size:0.9rem;"><?php echo e(ucfirst($user['role'])); ?></span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded" style="background:var(--slate-50);">
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0 0 4px 0;">Member Since</p>
                                    <p style="font-weight:700;font-size:1.1rem;margin:0;"><?php echo formatDate($user['created_at']); ?></p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 rounded" style="background:var(--slate-50);">
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0 0 4px 0;">Travel Personality</p>
                                    <p style="font-weight:700;font-size:1.1rem;margin:0;text-transform:capitalize;">
                                        <?php echo e($user['travel_personality']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form (hidden by default) -->
                    <div class="card p-4 mb-4" id="editForm" style="display:none;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold mb-0"><i class="bi bi-pencil"></i> Edit Profile</h4>
                            <button onclick="cancelEdit()" class="btn btn-ghost"><i class="bi bi-x-lg"></i> Cancel</button>
                        </div>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="label">Full Name</label>
                                <input type="text" name="full_name" class="input" value="<?php echo e($user['full_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="label">Email Address</label>
                                <input type="email" class="input" value="<?php echo e($user['email']); ?>" disabled>
                                <small style="color:var(--slate-400);">Email cannot be changed</small>
                            </div>
                            <div class="mb-3">
                                <label class="label">Username</label>
                                <input type="text" class="input" value="<?php echo e($user['username'] ?? ''); ?>" disabled>
                                <small style="color:var(--slate-400);">Username cannot be changed</small>
                            </div>
                            <div class="mb-3">
                                <label class="label">Phone Number</label>
                                <input type="text" name="phone" class="input" value="<?php echo e($user['phone'] ?? ''); ?>" placeholder="+92 300 1234567">
                            </div>

                            <label class="label">Travel Personality</label>
                            <div class="row g-2 mb-3">
                                <?php foreach ($personalities as $p): ?>
                                    <div class="col-md-6">
                                        <label class="d-flex align-items-start gap-2 p-3 rounded" style="border:2px solid <?php echo $user['travel_personality'] === $p['value'] ? 'var(--primary-500)' : 'var(--slate-200)'; ?>;cursor:pointer;">
                                            <input type="radio" name="travel_personality" value="<?php echo e($p['value']); ?>" <?php echo $user['travel_personality'] === $p['value'] ? 'checked' : ''; ?> style="accent-color:var(--primary-600);margin-top:4px;">
                                            <div>
                                                <i class="bi <?php echo e($p['icon']); ?>" style="font-size:1.25rem;color:var(--primary-600);"></i>
                                                <span style="font-weight:700;font-size:0.9rem;display:block;"><?php echo e($p['label']); ?></span>
                                                <small style="color:var(--slate-500);"><?php echo e($p['desc']); ?></small>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Save Changes</button>
                        </form>
                    </div>

                    <?php if ($user['role'] === 'owner' && $ownerStats): ?>
                    <div class="card p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-building"></i> Owner Summary</h4>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card p-3" style="background:var(--primary-50);">
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0;">Total Properties</p>
                                    <h4 style="font-weight:800;color:var(--primary-600);"><?php echo (int)$ownerStats['properties']; ?></h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card p-3" style="background:var(--success-50);">
                                    <p style="color:var(--slate-500);font-size:0.85rem;margin:0;">Total Earnings</p>
                                    <h4 style="font-weight:800;color:var(--success-600);"><?php echo formatPKR($ownerStats['earnings']); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Account Info Sidebar -->
                <div class="col-lg-4">
                    <div class="card p-4" style="position:sticky;top:90px;">
                        <div class="text-center mb-3">
                            <div class="d-flex align-items-center justify-content-center mx-auto mb-2" style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--primary-500),var(--accent-500));color:#fff;font-size:2rem;font-weight:800;">
                                <?php echo e(strtoupper(substr($user['full_name'], 0, 1))); ?>
                            </div>
                            <h5 style="font-weight:700;"><?php echo e($user['full_name']); ?></h5>
                            <span class="badge badge-primary"><?php echo e(ucfirst($user['role'])); ?></span>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <p style="color:var(--slate-500);font-size:0.85rem;margin:0;">Email</p>
                            <p style="font-weight:600;font-size:0.9rem;"><?php echo e($user['email']); ?></p>
                        </div>
                        <div class="mb-2">
                            <p style="color:var(--slate-500);font-size:0.85rem;margin:0;">Username</p>
                            <p style="font-weight:600;font-size:0.9rem;"><?php echo e($user['username'] ?? 'Not set'); ?></p>
                        </div>
                        <div class="mb-2">
                            <p style="color:var(--slate-500);font-size:0.85rem;margin:0;">Phone</p>
                            <p style="font-weight:600;font-size:0.9rem;"><?php echo e($user['phone'] ?? 'Not set'); ?></p>
                        </div>
                        <div class="mb-2">
                            <p style="color:var(--slate-500);font-size:0.85rem;margin:0;">Member Since</p>
                            <p style="font-weight:600;font-size:0.9rem;"><?php echo formatDate($user['created_at']); ?></p>
                        </div>
                        <div class="mb-2">
                            <p style="color:var(--slate-500);font-size:0.85rem;margin:0;">Travel Personality</p>
                            <p style="font-weight:600;font-size:0.9rem;text-transform:capitalize;"><?php echo e($user['travel_personality']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function showEditForm() {
    document.getElementById('profileView').style.display = 'none';
    document.getElementById('editForm').style.display = '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelEdit() {
    document.getElementById('editForm').style.display = 'none';
    document.getElementById('profileView').style.display = '';
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
