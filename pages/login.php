<?php
/**
 * Login Page
 * ----------
 * Unified login for both employee and admin roles.
 * Authenticates against the database.
 */

require_once __DIR__ . '/../includes/functions.php';
startAppSession();

// Already logged in? Redirect to the appropriate dashboard.
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'employee') {
        redirectTo(BASE_URL . '/pages/employee/dashboard.php');
    } elseif ($_SESSION['role'] === 'admin') {
        redirectTo(BASE_URL . '/pages/admin/console.php');
    }
}

$error        = $_GET['error'] ?? '';
$emailValue   = $_GET['email'] ?? '';
$selectedRole = $_GET['role']  ?? 'employee';
$notice       = '';

if (isset($_GET['logout'])) {
    $notice = 'You have been logged out successfully.';
}

$pageTitle = 'Sign In';
require_once __DIR__ . '/../includes/header.php';
?>

    <div class="page-wrapper">
        <div class="card">
            <h1>Sign In</h1>

            <?php if ($notice): ?>
                <div class="alert success"><?php echo e($notice); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form id="loginForm" action="<?php echo BASE_URL; ?>/actions/auth.php" method="post" novalidate>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo e($emailValue); ?>"
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

                <a class="forgot-link" href="<?php echo BASE_URL; ?>/pages/forgot_password.php">Forgot password?</a>
            </form>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>/public/js/validation.js"></script>
    <script>initLoginValidation();</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
