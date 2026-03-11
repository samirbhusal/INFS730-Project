<?php
date_default_timezone_set('America/Chicago');

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'project730_activation');
define('DB_USER', 'root');
define('DB_PASS', '');

define('BASE_URL', 'http://localhost/INFS730/Project730/Emp_Set_PW');
define('ADMIN_CONTACT_EMAIL', 'admin@abcgas.com');

/*
    Set this to true only if your XAMPP SMTP/mail is configured correctly.
    Otherwise leave false. The system will still generate the invitation link
    and save it to invite_log.txt for testing.
*/
define('MAIL_ENABLED', false);

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    return $pdo;
}