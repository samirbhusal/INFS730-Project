<?php
/**
 * Auth Guard
 * ----------
 * Include this file and call requireLogin() at the top
 * of any page that requires an authenticated user.
 */

require_once __DIR__ . '/functions.php';
startAppSession();

/**
 * Ensure the user is logged in.
 * Optionally require a specific role ('employee' or 'admin').
 */
function requireLogin(?string $requiredRole = null): void
{
    if (!isset($_SESSION['role'])) {
        redirectTo(BASE_URL . '/pages/login.php?error=' . urlencode('Please sign in first.'));
    }

    if ($requiredRole !== null && $_SESSION['role'] !== $requiredRole) {
        redirectTo(BASE_URL . '/pages/login.php?error=' . urlencode('Unauthorized access.'));
    }
}
