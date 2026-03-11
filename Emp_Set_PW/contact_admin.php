<?php
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <h1>Contact Admin</h1>
            <p class="subtitle centered">
                Please contact your administrator to request a new invitation link.
            </p>

            <a href="mailto:<?php echo e(ADMIN_CONTACT_EMAIL); ?>" class="primary-btn">
                Email Admin
            </a>

            <div class="small-links">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>