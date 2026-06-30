<?php

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']),
        'samesite' => 'Lax'
    ]);

    session_start();
}

// Railway MySQL connection via DATABASE_URL or individual env vars
$mysqlUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');
if ($mysqlUrl) {
    $parts = parse_url($mysqlUrl);
    $servername = $parts['host'] ?? 'localhost';
    $username = $parts['user'] ?? 'root';
    $password = $parts['pass'] ?? '';
    $dbname = ltrim($parts['path'] ?? '/vial_servi', '/');
    $port = $parts['port'] ?? 3306;
} else {
    $servername = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: 'localhost';
    $username = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'root';
    $password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '';
    $dbname = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'vial_servi';
    $port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: 3306;
}

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
