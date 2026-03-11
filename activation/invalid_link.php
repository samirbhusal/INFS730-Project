<?php
/**
 * Invalid / Expired Activation Link
 */

require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Link Invalid';
require_once __DIR__ . '/../includes/header.php';
?>

    <div class="page-wrapper">
        <div class="card">
            <h1>Link Invalid</h1>
            <p class="subtitle centered">
                This activation link is invalid, expired, or has already been used.
            </p>

            <a href="<?php echo BASE_URL; ?>/activation/contact_admin.php" class="primary-btn">Contact Admin</a>

            <div class="small-links">
                <a href="<?php echo BASE_URL; ?>/pages/login.php">Back to Login</a>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
