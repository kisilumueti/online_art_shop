<?php
/**
 * db.php
 * Centralized database connection handler using PDO.
 * Supports UTF-8MB4, persistent connections, and error logging.
 */

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'online_art_shop');
define('DB_USER', 'root');
define('DB_PASS', '1234'); // replace with strong password if set
define('DB_CHARSET', 'utf8mb4');

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // throw exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch assoc arrays
    PDO::ATTR_PERSISTENT         => true,                   // persistent connections
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
];

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check logs.");
}
?>
