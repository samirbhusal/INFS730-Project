<?php
/**
 * Root Entry Point
 * ----------------
 * Redirects to the login page.
 */

require_once __DIR__ . '/config/app.php';

header('Location: ' . BASE_URL . '/pages/login.php');
exit();
