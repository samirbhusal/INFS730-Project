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
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="password-toggle" data-pw-toggle="password" aria-label="Show password">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    <small id="passwordRules" class="field-note">
                        Must be at least 6 characters.
                    </small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="password-toggle" data-pw-toggle="confirm_password" aria-label="Show password">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
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
