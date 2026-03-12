<?php
/**
 * Database Configuration
 * ----------------------
 * Auto-detects MAMP (macOS) vs XAMPP (Windows) and connects accordingly.
 */

define('DB_NAME', 'project730_activation1');
define('DB_USER', 'root');

/*
    MAMP (macOS):  password = 'root', uses Unix socket
    XAMPP (Windows): password = '' (empty), uses TCP port 3306
*/

if (PHP_OS_FAMILY === 'Darwin' && file_exists('/Applications/MAMP/tmp/mysql/mysql.sock')) {
    // macOS + MAMP
    define('DB_PASS', 'root');
    define('DB_DSN', 'mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;dbname=' . DB_NAME . ';charset=utf8mb4');
} else {
    // Windows + XAMPP (or any standard MySQL on port 3306)
    define('DB_PASS', '');
    define('DB_DSN', 'mysql:host=127.0.0.1;dbname=' . DB_NAME . ';charset=utf8mb4');
}

/**
 * Return a shared PDO instance (created once, reused).
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, $options);
    }

    return $pdo;
}
