<?php
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'employee') {
        header("Location: employee_dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'admin') {
        header("Location: admin_console.php");
        exit();
    }
}

$error = $_GET['error'] ?? '';
$emailValue = $_GET['email'] ?? '';
$selectedRole = $_GET['role'] ?? 'employee';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Project730</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-wrapper">
        <div class="login-card">
            <h1>Sign In</h1>

            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="auth.php" method="post" novalidate>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($emailValue); ?>"
                        autocomplete="username"
                        required
                    >
                    <small class="field-error" id="emailError"></small>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="role-toggle">
                    <input
                        type="radio"
                        id="employee"
                        name="role"
                        value="employee"
                        <?php echo ($selectedRole === 'employee') ? 'checked' : ''; ?>
                    >
                    <label for="employee">Employee</label>

                    <input
                        type="radio"
                        id="admin"
                        name="role"
                        value="admin"
                        <?php echo ($selectedRole === 'admin') ? 'checked' : ''; ?>
                    >
                    <label for="admin">Admin</label>
                </div>

                <button type="submit" id="signInBtn" disabled>Sign In</button>

                <a class="forgot-link" href="forgot_password.php">Forgot password?</a>
            </form>
        </div>
    </div>

    <script>
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const signInBtn = document.getElementById('signInBtn');
        const emailError = document.getElementById('emailError');

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function updateButtonState() {
            const emailFilled = emailInput.value.trim() !== '';
            const passwordFilled = passwordInput.value.trim() !== '';
            signInBtn.disabled = !(emailFilled && passwordFilled);
        }

        emailInput.addEventListener('input', function () {
            const emailValue = emailInput.value.trim();

            if (emailValue !== '' && !isValidEmail(emailValue)) {
                emailError.textContent = 'Please enter a valid email address.';
            } else {
                emailError.textContent = '';
            }

            updateButtonState();
        });

        passwordInput.addEventListener('input', updateButtonState);

        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const emailValue = emailInput.value.trim();

            if (!isValidEmail(emailValue)) {
                emailError.textContent = 'Please enter a valid email address.';
                e.preventDefault();
            }
        });

        window.addEventListener('load', updateButtonState);
    </script>
</body>
</html>