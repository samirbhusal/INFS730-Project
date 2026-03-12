<?php
/**
 * Reset Password
 * ---------------
 * User arrives here via a reset link. Sets a new password.
 */

require_once __DIR__ . '/../includes/functions.php';

$token = trim($_GET['token'] ?? '');
$error = trim($_GET['error'] ?? '');

$resetData = fetchValidResetToken($token);

if (!$resetData) {
    $pageTitle = 'Link Invalid';
    require_once __DIR__ . '/../includes/header.php';
?>

    <div class="page-wrapper">
        <div class="card">
            <h1>Link Invalid</h1>
            <p class="subtitle centered">
                This password reset link is invalid, expired, or has already been used.
            </p>

            <a href="<?php echo BASE_URL; ?>/pages/forgot_password.php" class="primary-btn">Request New Link</a>

            <div class="small-links">
                <a href="<?php echo BASE_URL; ?>/pages/login.php">Back to Sign In</a>
            </div>
        </div>
    </div>

<?php
    require_once __DIR__ . '/../includes/footer.php';
    exit();
}

$userEmail = $resetData['email'];
$userName  = $resetData['full_name'];

$pageTitle = 'Reset Password';
require_once __DIR__ . '/../includes/header.php';
?>

    <div class="page-wrapper">
        <div class="card activation-card">
            <h1>Reset Password</h1>
            <p class="subtitle centered">Set a new password for your account.</p>

            <?php if ($error): ?>
                <div class="alert error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form id="resetForm" action="<?php echo BASE_URL; ?>/actions/reset_password.php" method="post" novalidate>
                <input type="hidden" name="token" value="<?php echo e($token); ?>">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        value="<?php echo e($userEmail); ?>"
                        readonly
                        class="readonly-input"
                    >
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required>
                    <small id="passwordRules" class="field-note">
                        Must be at least 8 characters, with 1 uppercase, 1 number, and 1 special character.
                    </small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <small id="matchMessage" class="field-note"></small>
                </div>

                <button type="submit" id="activateBtn" class="primary-btn" disabled>Reset Password</button>
            </form>

            <div class="small-links">
                <a href="<?php echo BASE_URL; ?>/pages/login.php">Back to Sign In</a>
            </div>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>/public/js/validation.js"></script>
    <script>initActivationValidation();</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
