<?php
/**
 * Admin Console
 * -------------
 * Protected page — requires admin login.
 */

require_once __DIR__ . '/../../includes/auth_guard.php';
requireLogin('admin');

$userName = $_SESSION['user_name'] ?? 'Admin';

$pageTitle = 'Admin Console';
require_once __DIR__ . '/../../includes/header.php';
?>

    <div class="page-wrapper">
        <div class="card">
            <h1>Admin Console</h1>
            <p class="subtitle centered">Welcome, <?php echo e($userName); ?>.</p>
            <p class="subtitle centered">You have successfully signed in as an Admin.</p>

            <a href="<?php echo BASE_URL; ?>/pages/admin/add_user.php" class="primary-btn" style="margin-bottom: 12px;">Add Employee</a>
            <a href="<?php echo BASE_URL; ?>/pages/logout.php" class="primary-btn" style="background: #6b7280;">Log Out</a>
        </div>
    </div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
