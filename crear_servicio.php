<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

require_once 'config.php';
include('validPermissions.php');
include('header.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $nombreServicio = $_POST['Nombre_Servicio']?? '';
    $Descripción = $_POST['Descripción']?? '';
    
    $sql = "INSERT INTO servicios (Nombre_Servicio, Descripción) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombreServicio, $Descripción);

    if ($stmt->execute()) { 
        echo "Servicio creado exitosamente.";
        header("refresh:2;url=gestionar_servicios.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta General</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/EditarServicioRealizado.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            padding: 20px;
            margin-top: 120px;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #680c39;
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
        }

        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: auto;
        }

        th {
            background-color: #2d0f2a;
            color: white;
            font-weight: bold;
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td img {
            width: 100px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container mt-5"> 
    <h2>Crear Servicio</h2>
    <form method="POST" action="">
        
        <div class="form-group">
            <label for="Nombre_Servicio">Nombre del Servicio</label>
            <input type="text" name="Nombre_Servicio" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="Descripción">Descripción del Servicio</label>
            <input type="text" name="Descripción" class="form-control" required>
        </div>
        
        
        <button type="submit" class="btn btn-primary mt-3" data-no-warning>Crear Servicios</button>
    </form>
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

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
