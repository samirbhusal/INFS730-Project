<?php
/**
 * Employee Dashboard
 * ------------------
 * Protected page — requires employee login.
 */

require_once __DIR__ . '/../../includes/auth_guard.php';
requireLogin('employee');

$activatedNow = isset($_GET['activated']) && $_GET['activated'] === '1';
$userName     = $_SESSION['user_name'] ?? 'Employee';

$pageTitle = 'Employee Dashboard';
require_once __DIR__ . '/../../includes/header.php';
?>

    <div class="page-wrapper">
        <div class="card">
            <?php if ($activatedNow): ?>
                <div class="alert success">Success. Your account has been activated and you are now logged in.</div>
            <?php endif; ?>

            <h1>Employee Dashboard</h1>
            <p class="subtitle centered">Welcome, <?php echo e($userName); ?>.</p>

            <div class="dashboard-box">
                <p>Your employee account is active and authorized.</p>
            </div>

            <a href="<?php echo BASE_URL; ?>/pages/logout.php" class="primary-btn">Log Out</a>
        </div>
    </div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
