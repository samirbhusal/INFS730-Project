<?php
/**
 * Application Settings
 * --------------------
 * Central configuration for the entire project.
 * Auto-detects MAMP (port 8888) vs XAMPP (port 80).
 */

date_default_timezone_set('America/Chicago');

/*
    MAMP (macOS):  Apache runs on port 8888
    XAMPP (Windows): Apache runs on port 80
*/
if (PHP_OS_FAMILY === 'Darwin') {
    define('BASE_URL', 'http://localhost:8888/INFS730-Project');
} else {
    define('BASE_URL', 'http://localhost/INFS730-Project');
}

define('ADMIN_CONTACT_EMAIL', 'admin@abcgas.com');

/*
 Set to true only if your MAMP/XAMPP SMTP is configured.
 When false the system still generates the invitation link
 and saves it to the invite_log.txt for testing.
 */
define('MAIL_ENABLED', false);