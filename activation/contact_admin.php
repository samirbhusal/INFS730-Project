<?php
/**
 * Contact Admin
 */

require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Contact Admin';
require_once __DIR__ . '/../includes/header.php';
?>

    <div class="page-wrapper">
        <div class="card">
            <h1>Contact Admin</h1>
            <p class="subtitle centered">
                Please contact your administrator to request a new invitation link.
            </p>

            <a href="mailto:<?php echo e(ADMIN_CONTACT_EMAIL); ?>" class="primary-btn">Email Admin</a>

            <div class="small-links">
                <a href="<?php echo BASE_URL; ?>/pages/login.php">Back to Login</a>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
