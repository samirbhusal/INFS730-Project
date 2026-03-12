<?php
/**
 * Password Recovery (placeholder)
 */

require_once __DIR__ . '/../includes/functions.php';
startAppSession();

$pageTitle = 'Password Recovery';
require_once __DIR__ . '/../includes/header.php';
?>

    <div class="page-wrapper">
        <div class="card">
            <h1>Password Recovery</h1>
            <p class="subtitle centered">This page represents the Password Recovery flow.</p>
            <p class="subtitle centered">You can replace this page later with your actual reset-password form.</p>
            <a class="primary-btn" href="<?php echo BASE_URL; ?>/pages/login.php">Back to Sign In</a>
        </div>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
