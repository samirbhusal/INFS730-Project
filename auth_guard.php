<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin($requiredRole = null) {
    if (!isset($_SESSION['role'])) {
        header("Location: login.php?error=" . urlencode("Please sign in first."));
        exit();
    }

    if ($requiredRole !== null && $_SESSION['role'] !== $requiredRole) {
        header("Location: login.php?error=" . urlencode("Unauthorized access."));
        exit();
    }
}