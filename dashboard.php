<?php
// session.php
session_start();

// Destruir la sesión si se recibe el logout
if (isset($_POST['logout']) && $_POST['logout'] === 'true') {
    session_destroy();
    session_unset();
    echo 'Sesión eliminada';
    exit;
}

// Redirigir si la sesión no está activa
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['cedula'];
$permisoRequerido = 'leer_servicio';

// Incluir validación de permisos
include('validPermissions.php');

if (!usuarioTienePermiso($usuarioId, $permisoRequerido, $conn)) {
    die("❌ Acceso denegado: No tienes permiso para ver esta página.");
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/Dashboard.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            text-align: center;
            margin-top: 100px;
            transition: all 0.3s ease;
        }

        h2 {
            margin-bottom: 20px;
            color: #680c39;
            font-size: 24px;
            font-weight: 600;
        }

        p {
            color: #555;
            font-size: 16px;
        }

        a {
            padding: 12px 20px;
            background-color: #2d0f2a;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            display: inline-block;
            margin: 10px 5px;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        a:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        a:active {
            background-color: #680c39;
            transform: translateY(0);
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Bienvenido al Dashboard</h2>
        <p>Aquí podrás acceder a las diferentes funciones del sistema.</p>
        <a href="consulta_general.php" data-no-warning>Ir a Consulta General</a>
        <a href="consulta_identificacion.php" data-no-warning>Consulta por Identificación</a>
        <a href="gestionar_servicios.php" data-no-warning>Crud servicios</a>
    </div>


    <script>
        const checkSession = () => {
            fetch('session.php')
                .then(response => response.text())
                .then(data => {
                    if (data.includes('No active session')) {
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => console.error('Error al validar la sesión:', error));
        };

        checkSession();

        const beforeUnloadHandler = (event) => {
            if (event.target.activeElement?.hasAttribute('data-no-warning')) return;
            navigator.sendBeacon('session.php', new URLSearchParams({ logout: 'true' }));
        };

        window.addEventListener('beforeunload', beforeUnloadHandler);
    </script>

</body>

</html>

