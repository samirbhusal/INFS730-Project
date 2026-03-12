<?php
/**
 * Activate Account — Set Password Form
 * -------------------------------------
 * Employees arrive here via an invitation link.
 */

require_once __DIR__ . '/../includes/functions.php';

$token = trim($_GET['token'] ?? '');
$error = trim($_GET['error'] ?? '');

$invitation = fetchValidInvitationByToken($token);

if (!$invitation) {
    redirectTo(BASE_URL . '/activation/invalid_link.php');
}

$employeeEmail = $invitation['email'];
$employeeName  = $invitation['full_name'];

$pageTitle = 'Activate Account';
require_once __DIR__ . '/../includes/header.php';
?>

    <div class="page-wrapper">
        <div class="card activation-card">
            <h1>Welcome to ABC Gas Station</h1>
            <p class="subtitle centered">Your account was created by Admin. Set your password to activate.</p>

            <?php if ($error): ?>
                <div class="alert error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form id="activationForm" action="<?php echo BASE_URL; ?>/actions/activate_account.php" method="post" novalidate>
                <input type="hidden" name="token" value="<?php echo e($token); ?>">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo e($employeeEmail); ?>"
                        readonly
                        class="readonly-input"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Create password</label>
                    <input type="password" id="password" name="password" required>
                    <small id="passwordRules" class="field-note">
                        Must be at least 8 characters, with 1 uppercase, 1 number, and 1 special character.
                    </small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <small id="matchMessage" class="field-note"></small>
                </div>

                <button type="submit" id="activateBtn" class="primary-btn" disabled>Activate Account</button>
            </form>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>/public/js/validation.js"></script>
    <script>initActivationValidation();</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
