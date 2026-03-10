<?php
require_once 'functions.php';
requireEmployeeLogin();

$activatedNow = isset($_GET['activated']) && $_GET['activated'] === '1';
$userName = $_SESSION['user_name'] ?? 'Employee';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <?php if ($activatedNow): ?>
                <div class="alert success">Success. Your account has been activated and you are now logged in.</div>
            <?php endif; ?>

            <h1>Employee Dashboard</h1>
            <p class="subtitle centered">Welcome, <?php echo e($userName); ?>.</p>

            <div class="dashboard-box">
                <p>Your employee account is active and authorized.</p>
            </div>

            <a href="logout.php" class="primary-btn">Log Out</a>
        </div>
    </div>
</body>
</html>