<?php
session_start();
require_once 'config.php';

// Verificar si el cliente está logueado
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login_cliente.php");
    exit();
}

// Obtener información del cliente
$sql_cliente = "SELECT * FROM clientes WHERE Cedula_Id = ?";
$stmt_cliente = $conn->prepare($sql_cliente);
$stmt_cliente->bind_param("i", $_SESSION['cliente_id']);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();
$cliente = $result_cliente->fetch_assoc();
$stmt_cliente->close();

// Obtener vehículos del cliente
$sql_vehiculos = "SELECT * FROM vehiculos WHERE Clientes_Vehiculos = ?";
$stmt_vehiculos = $conn->prepare($sql_vehiculos);
$stmt_vehiculos->bind_param("i", $_SESSION['cliente_id']);
$stmt_vehiculos->execute();
$vehiculos = $stmt_vehiculos->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_vehiculos->close();

// Obtener servicios realizados para los vehículos del cliente
$servicios = array();
if (!empty($vehiculos)) {
    $placas = array_column($vehiculos, 'Placa');
    $placeholders = implode(',', array_fill(0, count($placas), '?'));

    $sql_servicios = "SELECT sr.*, s.Nombre_Servicio, v.Placa 
                     FROM servicios_realizados sr
                     JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
                     JOIN vehiculos v ON sr.Vehiculo_id_Servicios_Realizados = v.Placa
                     WHERE sr.Vehiculo_id_Servicios_Realizados IN ($placeholders)
                     ORDER BY (sr.Fecha >= CURDATE()) DESC, sr.Fecha ASC";

    $stmt_servicios = $conn->prepare($sql_servicios);
    $types = str_repeat('s', count($placas));
    $stmt_servicios->bind_param($types, ...$placas);
    $stmt_servicios->execute();
    $servicios = $stmt_servicios->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_servicios->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - VialServi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/ClienteDasboard.jpg');
            background-color: #f5f5f5;
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

        .main-container {
            width: 90%;
            max-width: 1200px;
            margin: 120px auto 40px;
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2,
        h3 {
            color: #680c39;
            margin-bottom: 20px;
        }

        .welcome-message {
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 40px;
        }

        /* NUEVO GRID para las cards */
        .section-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .vehicle-card,
        .service-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .vehicle-card:hover,
        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-card h3 {
            margin-top: 0;
            color: #3498db;
        }

        .service-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .service-card.futuro {
            border: 2px solid #2ecc71;
            /* verde */
            background-color: #eafaf1;
        }

        .service-card.pasado {
            border: 2px solid #95a5a6;
            /* gris */
            background-color: #f4f6f7;
        }

        .estado-label {
            display: inline-block;
            padding: 3px 8px;
            font-size: 12px;
            color: white;
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .service-card.futuro .estado-label {
            background-color: #2ecc71;
        }

        .service-card.pasado .estado-label {
            background-color: #95a5a6;
        }


        .detail-item {
            margin-bottom: 5px;
        }

        .detail-label {
            font-weight: bold;
            color: #7f8c8d;
        }

        .logout-btn {
            background-color: #c70a3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background-color: #a00832;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo-container">
            <img src="Imagenes/Logo.jpg" alt="Logo VialServi" class="logo">
        </div>
        <div>
            <a href="crear_servicio_realizado_cliente.php">Pedir un Servicio</a>
        </div>
    </div>

    <div class="main-container">
        <div class="welcome-message">
            <h1>Bienvenido, <?php echo htmlspecialchars($cliente['Nombre']); ?></h1>
            <p>Desde aquí puedes gestionar y ver el historial de tus servicios.</p>
        </div>

        <div class="section">
            <h2>Mis Vehículos</h2>
            <div class="section-cards">
                <?php if (!empty($vehiculos)): ?>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                        <div class="vehicle-card">
                            <h3><?php echo htmlspecialchars($vehiculo['Marca'] . ' ' . $vehiculo['Modelo']); ?></h3>
                            <div class="service-details">
                                <div class="detail-item">
                                    <span class="detail-label">Placa:</span>
                                    <span><?php echo htmlspecialchars($vehiculo['Placa']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Color:</span>
                                    <span><?php echo htmlspecialchars($vehiculo['Color']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Objetos valiosos:</span>
                                    <span><?php echo htmlspecialchars($vehiculo['Objetos_Valiosos'] ?? 'Ninguno registrado'); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tienes vehículos registrados.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="section">
            <h2>Historial de Servicios</h2>
            <div class="section-cards">
                <?php if (!empty($servicios)): ?>
                    <?php foreach ($servicios as $servicio): ?>
                        <?php
                        $fechaServicio = strtotime($servicio['Fecha']);
                        $hoy = strtotime(date('Y-m-d'));
                        $esFuturo = $fechaServicio >= $hoy;
                        ?>
                        <div class="service-card <?php echo $esFuturo ? 'futuro' : 'pasado'; ?>">
                            <h3><?php echo htmlspecialchars($servicio['Nombre_Servicio']); ?></h3>
                            <span class="estado-label"><?php echo $esFuturo ? '📅 Programado' : '✅ Completado'; ?></span>

                            <p><strong>Vehículo:</strong> <?php echo htmlspecialchars($servicio['Placa']); ?></p>
                            <div class="service-details">
                                <div class="detail-item">
                                    <span class="detail-label">Fecha:</span>
                                    <span><?php echo htmlspecialchars($servicio['Fecha']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Ubicación:</span>
                                    <span><?php echo htmlspecialchars($servicio['Ubicación']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Novedades:</span>
                                    <span><?php echo htmlspecialchars($servicio['Novedades'] ?? 'Ninguna'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Detalle:</span>
                                    <span><?php echo htmlspecialchars($servicio['Detalle_Servicio']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No se encontraron servicios realizados.</p>
                <?php endif; ?>
            </div>
        </div>

        <form action="descargar_reporte_cliente.php" method="post">
            <button type="submit" class="logout-btn" style="background-color:#3498db;">Descargar Reporte</button>
        </form>

        <form action="logout_cliente.php" method="post">
            <button type="submit" class="logout-btn">Cerrar Sesión</button>
        </form>


    </div>
</body>

</html>