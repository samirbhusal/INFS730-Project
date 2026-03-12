<?php
/**
 * Forgot Password
 * ----------------
 * User enters their email to request a password reset link.
 */

require_once __DIR__ . '/../includes/functions.php';
startAppSession();

$error    = '';
$message  = '';
$mailInfo = '';
$resetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Look up user
        $stmt = db()->prepare("
            SELECT id, full_name, email, user_status
            FROM users
            WHERE email = :email
              AND user_status = 'Active'
            LIMIT 1
        ");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            // Don't reveal whether the email exists or not
            $message = 'If an account with that email exists, a reset link has been generated.';
        } else {
            try {
                $pdo = db();
                $pdo->beginTransaction();

                // Invalidate any previous reset tokens
                invalidateAllResetTokensForUser((int) $user['id']);

                // Generate new token
                [$plainToken, $tokenHash] = generateResetToken();
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $insertToken = $pdo->prepare("
                    INSERT INTO password_reset_tokens (user_id, token_hash, expires_at)
                    VALUES (:user_id, :token_hash, :expires_at)
                ");
                $insertToken->execute([
                    'user_id'    => $user['id'],
                    'token_hash' => $tokenHash,
                    'expires_at' => $expiresAt,
                ]);

                $pdo->commit();

                $resetLink = BASE_URL . '/pages/reset_password.php?token=' . urlencode($plainToken);

                $mailSent = sendResetEmail($user['full_name'], $user['email'], $resetLink, $expiresAt);
                saveResetLinkToLog($user['email'], $resetLink, $expiresAt);

                $message  = 'If an account with that email exists, a reset link has been generated.';
                $mailInfo = $mailSent
                    ? 'A reset email has been sent.'
                    : 'SMTP is not configured, so the reset link was saved to invite_log.txt and is shown below for testing.';
            } catch (Exception $ex) {
                if (isset($pdo) && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}

$pageTitle = 'Forgot Password';
require_once __DIR__ . '/../includes/header.php';
?>

    <div class="page-wrapper">
        <div class="card">
            <h1>Forgot Password</h1>
            <p class="subtitle centered">Enter your email address and we'll send you a link to reset your password.</p>

            <?php if ($error): ?>
                <div class="alert error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert success"><?php echo e($message); ?></div>
            <?php endif; ?>

            <?php if ($mailInfo): ?>
                <div class="alert info"><?php echo e($mailInfo); ?></div>
            <?php endif; ?>

            <form id="forgotForm" method="post" action="" novalidate>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        autocomplete="email"
                        required
                    >
                    <small class="field-error" id="emailError"></small>
                </div>

                <button type="submit" id="resetBtn" class="primary-btn" disabled>Send Reset Link</button>
            </form>

            <?php if ($resetLink): ?>
                <div class="link-box">
                    <strong>Testing Link:</strong><br>
                    <a href="<?php echo e($resetLink); ?>"><?php echo e($resetLink); ?></a>
                </div>
            <?php endif; ?>

            <div class="small-links">
                <a href="<?php echo BASE_URL; ?>/pages/login.php">Back to Sign In</a>
            </div>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>/public/js/validation.js"></script>
    <script>initForgotPasswordValidation();</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
