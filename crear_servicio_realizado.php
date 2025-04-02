<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedulaEmpleado = $_POST['Cedula_Empleado_id_Servicios_Realizados'];
    $vehiculoId = $_POST['Vehiculo_id_Servicios_Realizados'];
    $servicioId = $_POST['Servicio_id_Servicios_Realizados'];
    $fecha = $_POST['Fecha'];
    $ubicacion = $_POST['Ubicacion'];
    $novedades = $_POST['Novedades'];
    $fotos = $_POST['Fotos'];
    $detalleServicio = $_POST['Detalle_Servicio'];
    $custodiaServicio = $_POST['Custodia_Servicio'];
    $facturacionSeparada = isset($_POST['Facturacion_Separada']) ? 1 : 0;

    // Consulta preparada para insertar los datos
    $stmt = $conn->prepare("INSERT INTO servicios_realizados (
        Cedula_Empleado_id_Servicios_Realizados, 
        Vehiculo_id_Servicios_Realizados, 
        Servicio_id_Servicios_Realizados, 
        Fecha, 
        Ubicación, 
        Novedades, 
        Fotos, 
        Detalle_Servicio, 
        Custodia_Servicio, 
        Facturación_Separada
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("isissssssi", $cedulaEmpleado, $vehiculoId, $servicioId, $fecha, $ubicacion, $novedades, $fotos, $detalleServicio, $custodiaServicio, $facturacionSeparada);

    if ($stmt->execute()) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Registro agregado exitosamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#ff6b6b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'consulta_general.php';
                        }
                    });
                });
              </script>";
    } else {
        echo "Error al agregar el registro: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Servicio Realizado</title>

    <!-- SweetAlert2 CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/CrearServicioRealizado.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: rgba(100, 67, 67, 0.7);
            padding: 15px 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            padding: 12px 25px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            background-color: #2d0f2a;
            margin-right: 15px;
            border-radius: 50px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        .logo-container {
            margin-left: 30px;
            border-radius: 50%;
            width: 90px;
            height: 90px;
            overflow: hidden;
        }

        .logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
            text-align: center;
            margin-top: 100px;
            transition: all 0.3s ease;
        }

        .container:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        h2 {
            margin-bottom: 30px;
            color: #680c39;
            font-size: 28px;
            font-weight: 600;
        }

        label {
            display: block;
            margin: 15px 0 8px;
            color: black;
            font-size: 16px;
            text-align: left;
        }

        input[type="text"], 
        input[type="date"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-sizing: border-box;
            background-color: #f9f9f9;
            font-size: 16px;
            color: #333;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, 
        input[type="date"]:focus,
        input[type="password"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2d0f2a;
            background-color: #fff;
        }

        .form-check {
            text-align: left;
            margin: 15px 0;
        }

        .form-check-label {
            margin-left: 10px;
            color: #333;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #2d0f2a;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-top: 20px;
        }

        button:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        button:active {
            background-color: #680c39;
            transform: translateY(0);
        }

        .form-message {
            margin-top: 20px;
            font-size: 14px;
            color: #c70a3c;
        }
    </style>
</head>
<body>

<!-- Barra superior -->
<div id="mySidebar" class="sidebar">
    <div class="logo-container">
        <img src="Imagenes/Logo.jpg" alt="Logo" class="logo">
    </div>
    <div>
        <a href="dashboard.php" data-no-warning>Dashboard</a>
        <a href="consulta_general.php" data-no-warning>Consulta General</a>
        <a href="consulta_identificacion.php" data-no-warning>Consulta por Identificación</a>
        <a href="logout.php" data-no-warning>Cerrar Sesión</a>
    </div>
</div>

<!-- Contenido principal -->
<div class="container">
    <h2>Agregar Servicio Realizado</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="Cedula_Empleado_id_Servicios_Realizados">Cédula del Empleado</label>
            <input type="text" name="Cedula_Empleado_id_Servicios_Realizados" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="Vehiculo_id_Servicios_Realizados">ID del Vehículo</label>
            <input type="text" name="Vehiculo_id_Servicios_Realizados" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="Servicio_id_Servicios_Realizados">ID del Servicio</label>
            <input type="text" name="Servicio_id_Servicios_Realizados" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="Fecha">Fecha</label>
            <input type="date" name="Fecha" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="Ubicacion">Ubicación</label>
            <input type="text" name="Ubicacion" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="Novedades">Novedades</label>
            <input type="text" name="Novedades" class="form-control">
        </div>
        <div class="form-group">
            <label for="Fotos">Fotos (nombre de archivo)</label>
            <input type="text" name="Fotos" class="form-control">
        </div>
        <div class="form-group">
            <label for="Detalle_Servicio">Detalle del Servicio</label>
            <input type="text" name="Detalle_Servicio" class="form-control">
        </div>
        <div class="form-group">
            <label for="Custodia_Servicio">Custodia del Servicio</label>
            <input type="text" name="Custodia_Servicio" class="form-control">
        </div>
        <div class="form-check">
            <input type="checkbox" name="Facturacion_Separada" class="form-check-input" value="1">
            <label for="Facturacion_Separada" class="form-check-label">¿Facturación Separada?</label>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Registro</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>