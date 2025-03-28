<?php

// Verificar si ya hay una sesión activa antes de configurar las cookies
if (session_status() === PHP_SESSION_NONE) {
    // Configurar las cookies de sesión antes de iniciar la sesión
    session_set_cookie_params([
        'lifetime' => 0,                  // La sesión se destruye al cerrar el navegador
        'path' => '/',                    // Disponible en todo el sitio
        'httponly' => true,               // No accesible por JavaScript (más seguro)
        'secure' => isset($_SERVER['HTTPS']), // Solo se envía en HTTPS si está activo
        'samesite' => 'Lax'               // Protección contra ataques CSRF
    ]);

    // Iniciar la sesión solo si no está iniciada
    session_start();
}

// Datos de conexión
$servername = "localhost";
$username = "root";
$password = ""; // Tu contraseña
$dbname = "vial_servi";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
