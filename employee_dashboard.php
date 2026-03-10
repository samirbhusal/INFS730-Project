<?php
require_once 'auth_guard.php';
requireLogin('employee');

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
    <div class="dashboard-page">
        <div class="dashboard-card">
            <h1>Employee Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($userName); ?>.</p>
            <p>You have successfully signed in as an Employee.</p>
            <a class="logout-btn" href="logout.php">Log Out</a>
        </div>
    </div>
</body>
</html>