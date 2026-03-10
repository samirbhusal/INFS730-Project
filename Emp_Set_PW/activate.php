<?php
require_once 'functions.php';

$token = trim($_GET['token'] ?? '');
$error = trim($_GET['error'] ?? '');

$invitation = fetchValidInvitationByToken($token);

if (!$invitation) {
    redirectTo('invalid_link.php');
}

$employeeEmail = $invitation['email'];
$employeeName = $invitation['full_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate Employee Account</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-wrapper">
        <div class="card activation-card">
            <h1>Welcome to ABC Gas Station</h1>
            <p class="subtitle centered">Your account was created by Admin. Set your password to activate.</p>

            <?php if ($error): ?>
                <div class="alert error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form id="activationForm" action="activate_account.php" method="post" novalidate>
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

    <script>
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        const passwordRules = document.getElementById('passwordRules');
        const matchMessage = document.getElementById('matchMessage');
        const activateBtn = document.getElementById('activateBtn');

        function isStrongPassword(password) {
            return /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(password);
        }

        function updateValidation() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;

            const strong = isStrongPassword(password);
            const matches = password !== '' && confirm !== '' && password === confirm;

            if (password === '') {
                passwordRules.textContent = 'Must be at least 8 characters, with 1 uppercase, 1 number, and 1 special character.';
                passwordRules.className = 'field-note';
            } else if (strong) {
                passwordRules.textContent = 'Password strength requirement satisfied.';
                passwordRules.className = 'field-note success-text';
            } else {
                passwordRules.textContent = 'Password does not yet meet all requirements.';
                passwordRules.className = 'field-note error-text';
            }

            if (confirm === '') {
                matchMessage.textContent = '';
                matchMessage.className = 'field-note';
            } else if (matches) {
                matchMessage.textContent = 'Passwords match.';
                matchMessage.className = 'field-note success-text';
            } else {
                matchMessage.textContent = 'Passwords do not match.';
                matchMessage.className = 'field-note error-text';
            }

            activateBtn.disabled = !(strong && matches);
        }

        passwordInput.addEventListener('input', updateValidation);
        confirmInput.addEventListener('input', updateValidation);
    </script>
</body>
</html>