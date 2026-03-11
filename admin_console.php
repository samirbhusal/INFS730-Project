<?php
require_once 'auth_guard.php';
requireLogin('admin');

$userName = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-page">
        <div class="dashboard-card">
            <h1>Admin Console</h1>
            <p>Welcome, <?php echo htmlspecialchars($userName); ?>.</p>
            <p>You have successfully signed in as an Admin.</p>
            <a class="logout-btn" href="logout.php">Log Out</a>
        </div>
    </div>
</body>
</html>