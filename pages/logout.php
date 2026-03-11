<?php
/**
 * Logout
 * ------
 * Destroy session and redirect to login page.
 */

require_once __DIR__ . '/../includes/functions.php';
startAppSession();

session_unset();
session_destroy();

redirectTo(BASE_URL . '/pages/login.php?logout=1');
