<?php
require_once 'functions.php';
startAppSession();

if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'employee') {
    redirectTo('employee_dashboard.php');
}

$error = '';
$notice = '';

if (isset($_GET['logout'])) {
    $notice = 'You have been logged out successfully.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password === '') {
        $error = 'Password is required.';
    } else {
        $stmt = db()->prepare("
            SELECT id, full_name, email, password_hash, role, user_status
            FROM users
            WHERE email = :email
              AND role = 'employee'
            LIMIT 1
        ");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || $user['user_status'] !== 'Active' || !password_verify($password, $user['password_hash'])) {
            $error = 'Incorrect email or password, or the account is not activated yet.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = 'employee';

            redirectTo('employee_dashboard.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <h1>Employee Login</h1>
            <p class="subtitle centered">Sign in after your account has been activated.</p>

            <?php if ($error): ?>
                <div class="alert error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <?php if ($notice): ?>
                <div class="alert success"><?php echo e($notice); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="primary-btn">Sign In</button>
            </form>

            <div class="small-links">
                <a href="admin_add_user.php">Admin Add Employee</a>
            </div>
        </div>
    </div>
</body>
</html>