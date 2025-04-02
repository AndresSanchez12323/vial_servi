<?php
session_start();
require_once 'config.php';

// Verificar si el cliente está logueado (solo con cliente_id)
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login_cliente.php");
    exit;
}

// Obtener datos del cliente de la sesión
$cliente_id = $_SESSION['cliente_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $vehiculoId = $_POST['vehiculo_id'];
    $servicioId = $_POST['servicio_id'];
    $fecha = $_POST['fecha'];
    $ubicacion = $_POST['ubicacion'];
    $detalleServicio = $_POST['detalle_servicio'];
    $contacto = $_POST['contacto'];
    
    // Consulta preparada para insertar en servicios_realizados
    $sql = "INSERT INTO servicios_realizados (
        Vehiculo_id_Servicios_Realizados, 
        Servicio_id_Servicios_Realizados, 
        Fecha, 
        Ubicación, 
        Detalle_Servicio,
        Custodia_Servicio,
        Facturación_Separada
    ) VALUES (?, ?, ?, ?, ?, ?, 0)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("sissss", $vehiculoId, $servicioId, $fecha, $ubicacion, $detalleServicio, $contacto);

    if ($stmt->execute()) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Solicitud enviada',
                        text: 'Tu solicitud de servicio ha sido registrada exitosamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#ff6b6b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'cliente_dashboard.php';
                        }
                    });
                });
              </script>";
    } else {
        echo "Error al registrar la solicitud: " . $stmt->error;
    }
    $stmt->close();
}

// Obtener vehículos del cliente
$vehiculos = [];
$stmt = $conn->prepare("SELECT Placa, Marca, Modelo FROM vehiculos WHERE Clientes_Vehiculos = ?");
if ($stmt) {
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $vehiculos[] = $row;
    }
    $stmt->close();
} else {
    die("Error al preparar la consulta de vehículos: " . $conn->error);
}

// Obtener servicios disponibles
$servicios = [];
$stmt = $conn->prepare("SELECT Servicio_id, Nombre_Servicio FROM servicios");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $servicios[] = $row;
    }
    $stmt->close();
} else {
    die("Error al preparar la consulta de servicios: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Servicio</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/CrearServicioRealizadoCliente.jpg');
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
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
            margin-top: 100px;
        }
        h2 {
            color: #680c39;
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-primary {
            background-color: #680c39;
            border-color: #680c39;
            width: 100%;
            padding: 10px;
            font-size: 18px;
        }
        .btn-primary:hover {
            background-color: #4d0929;
            border-color: #4d0929;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <img src="Imagenes/Logo.jpg" alt="Logo" class="logo">
    </div>
    <div>
        <a href="cliente_dashboard.php" data-no-warning>Dashboard</a>
        <a href="cliente_registrar_vehiculo.php" data-no-warning>Registrar Vehículo</a>
        <a href="logout_cliente.php" data-no-warning>Cerrar Sesión</a>
    </div>
</div>

<div class="container">
    <h2>Solicitar Servicio</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="vehiculo_id">Vehículo</label>
            <select name="vehiculo_id" class="form-control" required>
                <option value="">Seleccione un vehículo</option>
                <?php foreach ($vehiculos as $vehiculo): ?>
                    <option value="<?php echo $vehiculo['Placa']; ?>">
                        <?php echo htmlspecialchars($vehiculo['Marca'] . ' ' . $vehiculo['Modelo'] . ' - ' . $vehiculo['Placa']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="servicio_id">Servicio</label>
            <select name="servicio_id" class="form-control" required>
                <option value="">Seleccione un servicio</option>
                <?php foreach ($servicios as $servicio): ?>
                    <option value="<?php echo $servicio['Servicio_id']; ?>">
                        <?php echo htmlspecialchars($servicio['Nombre_Servicio']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="fecha">Fecha deseada para el servicio</label>
            <input type="date" name="fecha" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
        </div>
        
        <div class="form-group">
            <label for="ubicacion">Ubicación donde se realizará el servicio</label>
            <input type="text" name="ubicacion" class="form-control" required placeholder="Dirección exacta">
        </div>
        
        <div class="form-group">
            <label for="detalle_servicio">Detalles adicionales del servicio</label>
            <textarea name="detalle_servicio" class="form-control" rows="3" placeholder="Describa cualquier detalle importante sobre el servicio requerido"></textarea>
        </div>
        
        <div class="form-group">
            <label for="contacto">Teléfono de contacto</label>
            <input type="tel" name="contacto" class="form-control" required placeholder="Número donde podemos contactarle">
        </div>
        
        <button type="submit" class="btn btn-primary">Solicitar Servicio</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>