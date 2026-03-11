<?php
/**
 * Application Settings
 * --------------------
 * Central configuration for the entire project.
 */

date_default_timezone_set('America/Chicago');

define('BASE_URL', 'http://localhost/INFS730-Project');
define('ADMIN_CONTACT_EMAIL', 'admin@abcgas.com');

/*
    Set to true only if your MAMP/XAMPP SMTP is configured.
    When false the system still generates the invitation link
    and saves it to the invite_log.txt for testing.
*/
define('MAIL_ENABLED', false);
