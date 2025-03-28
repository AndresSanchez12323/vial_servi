<?php
// session.php
session_start();

// Destruir la sesión si se recibe un logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout']) && $_POST['logout'] === 'true') {
    session_destroy();
    session_unset();
    echo 'Sesión eliminada';
    exit;
}

// Verificar si la sesión sigue activa
if (!isset($_SESSION['loggedin'])) {
    echo 'No active session';
    exit;
}

echo 'Session active';
