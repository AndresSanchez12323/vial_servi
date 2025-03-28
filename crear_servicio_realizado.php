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
        /* Mismos estilos base con ajustes para el formulario */
        body {
            font-family: Arial, sans-serif;
            background-image: url('Imagenes/CrearServicioRealizado.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .sidebar {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #333;
            padding: 20px 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .sidebar a {
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            background-color: #007bff;
            margin-right: 10px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #0056b3;
        }

        .logo-container {
            margin-left: 50px;
            border-radius: 10px;
            width: 100px;
            height: 100px;
        }

        .logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(2px 4px 6px white);
            border-radius: 10px;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 180px auto;
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
        <button type="submit" class="btn btn-primary mt-3">Agregar Registro</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
