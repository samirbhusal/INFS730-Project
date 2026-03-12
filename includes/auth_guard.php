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
 * Ensure the user is logged in with a fully-initialized session.
 * Optionally require a specific role ('employee' or 'admin').
 */
function requireLogin(?string $requiredRole = null): void
{
    $allowedRoles = ['employee', 'admin'];

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles, true)) {
        redirectTo(BASE_URL . '/pages/login.php?error=' . urlencode('Please sign in first.'));
    }

    if ($requiredRole !== null && $_SESSION['role'] !== $requiredRole) {
        redirectTo(BASE_URL . '/pages/login.php?error=' . urlencode('Unauthorized access.'));
    }
}
