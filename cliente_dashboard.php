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
$sql_vehiculos = "
    SELECT
      v.Placa,
      v.Marca,
      mo.nombre       AS Modelo,
      co.nombre_color AS Color,
      v.Objetos_Valiosos
    FROM vehiculos v
    LEFT JOIN modelos  mo ON v.modelo_id = mo.id
    LEFT JOIN colores  co ON v.color_id   = co.color_id
    WHERE v.Clientes_Vehiculos = ?
";
$stmt_vehiculos = $conn->prepare($sql_vehiculos);
if (!$stmt_vehiculos) {
    die("Error al preparar consulta vehículos: " . $conn->error);
}
$stmt_vehiculos->bind_param("i", $_SESSION['cliente_id']);
$stmt_vehiculos->execute();
$vehiculos = $stmt_vehiculos->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_vehiculos->close();

// Inicializar filtros
$filtro_placa = isset($_GET['placa']) ? $_GET['placa'] : '';
$filtro_servicio = isset($_GET['servicio']) ? $_GET['servicio'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_mes = isset($_GET['mes']) ? $_GET['mes'] : '';

// Configuración de paginación
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$registros_por_pagina = 10;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Obtener todos los tipos de servicios para el selector
$sql_tipos_servicios = "SELECT Servicio_id, Nombre_Servicio FROM servicios ORDER BY Nombre_Servicio";
$result_tipos_servicios = $conn->query($sql_tipos_servicios);
$tipos_servicios = $result_tipos_servicios->fetch_all(MYSQLI_ASSOC);

// Obtener servicios realizados para los vehículos del cliente con filtros
$servicios = array();
$total_registros = 0;
$total_paginas = 0;

if (!empty($vehiculos)) {
    $placas = array_column($vehiculos, 'Placa');
    $placeholders = implode(',', array_fill(0, count($placas), '?'));
    
    // Consulta base para CONTAR registros
    $sql_count = "SELECT COUNT(*) as total 
                 FROM servicios_realizados sr
                 JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
                 JOIN vehiculos v ON sr.Vehiculo_id_Servicios_Realizados = v.Placa
                 WHERE sr.Vehiculo_id_Servicios_Realizados IN ($placeholders)";
    
    $params_count = $placas;
    $types_count = str_repeat('s', count($placas));
    
    // Consulta principal
    $sql_servicios = "SELECT sr.*, s.Nombre_Servicio, v.Placa, s.Servicio_id  
                     FROM servicios_realizados sr
                     JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
                     JOIN vehiculos v ON sr.Vehiculo_id_Servicios_Realizados = v.Placa
                     WHERE sr.Vehiculo_id_Servicios_Realizados IN ($placeholders)";
    
    $params = $placas;
    $types = str_repeat('s', count($placas));
    
    // Aplicar filtro de placa a ambas consultas
    if (!empty($filtro_placa)) {
        $sql_servicios .= " AND sr.Vehiculo_id_Servicios_Realizados = ?";
        $sql_count .= " AND sr.Vehiculo_id_Servicios_Realizados = ?";
        $params[] = $filtro_placa;
        $types .= 's';
        $params_count[] = $filtro_placa;
        $types_count .= 's';
    }
    
    // Aplicar filtro de tipo de servicio a ambas consultas
    if (!empty($filtro_servicio)) {
        $sql_servicios .= " AND s.Servicio_id = ?";
        $sql_count .= " AND s.Servicio_id = ?";
        $params[] = $filtro_servicio;
        $types .= 'i';
        $params_count[] = $filtro_servicio;
        $types_count .= 'i';
    }
    
    // Aplicar filtro de estado a ambas consultas
    if ($filtro_estado == 'programado') {
        $sql_servicios .= " AND sr.Fecha >= CURDATE()";
        $sql_count .= " AND sr.Fecha >= CURDATE()";
    } elseif ($filtro_estado == 'completado') {
        $sql_servicios .= " AND sr.Fecha < CURDATE()";
        $sql_count .= " AND sr.Fecha < CURDATE()";
    }
    
    // Aplicar filtro de mes a ambas consultas
    if (!empty($filtro_mes)) {
        $mes_anio = explode('-', $filtro_mes);
        if (count($mes_anio) == 2) {
            $sql_servicios .= " AND MONTH(sr.Fecha) = ? AND YEAR(sr.Fecha) = ?";
            $sql_count .= " AND MONTH(sr.Fecha) = ? AND YEAR(sr.Fecha) = ?";
            $params[] = $mes_anio[1]; // Mes
            $params[] = $mes_anio[0]; // Año
            $types .= 'ii';
            $params_count[] = $mes_anio[1]; // Mes
            $params_count[] = $mes_anio[0]; // Año
            $types_count .= 'ii';
        }
    }
    
    // Ordenar primero por servicios futuros y luego por fecha
    $sql_servicios .= " ORDER BY (sr.Fecha >= CURDATE()) DESC, sr.Fecha DESC";
    
    // Agregar LIMIT y OFFSET para paginación
    $sql_servicios .= " LIMIT ? OFFSET ?";
    $params[] = $registros_por_pagina;
    $params[] = $offset;
    $types .= 'ii';
    
    // Ejecutar la consulta COUNT para obtener el total de registros
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param($types_count, ...$params_count);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_assoc();
    $total_registros = $row_count['total'];
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    $stmt_count->close();
    
    // Ejecutar la consulta principal con paginación
    $stmt_servicios = $conn->prepare($sql_servicios);
    $stmt_servicios->bind_param($types, ...$params);
    $stmt_servicios->execute();
    $servicios = $stmt_servicios->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_servicios->close();
}

// Función para construir URL de paginación manteniendo los filtros
function construirUrlPaginacion($pagina) {
    global $filtro_placa, $filtro_servicio, $filtro_estado, $filtro_mes;
    
    $url = 'cliente_dashboard.php?pagina=' . $pagina;
    
    if (!empty($filtro_placa)) {
        $url .= '&placa=' . urlencode($filtro_placa);
    }
    
    if (!empty($filtro_servicio)) {
        $url .= '&servicio=' . urlencode($filtro_servicio);
    }
    
    if (!empty($filtro_estado)) {
        $url .= '&estado=' . urlencode($filtro_estado);
    }
    
    if (!empty($filtro_mes)) {
        $url .= '&mes=' . urlencode($filtro_mes);
    }
    
    return $url;
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
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
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

        h1, h2, h3 {
            color: #680c39;
            margin-bottom: 20px;
        }

        .welcome-message {
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 40px;
        }

        /* Estilos para el formulario de filtrado */
        .filter-container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #444;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .filter-button {
            padding: 10px 15px;
            background-color: #680c39;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .filter-button:hover {
            background-color: #4a0b29;
        }

        .clear-button {
            background-color: #6c757d;
        }

        .clear-button:hover {
            background-color: #5a6268;
        }

        /* Grid para las cards */
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
            background-color: #eafaf1;
        }

        .service-card.pasado {
            border: 2px solid #95a5a6;
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

        .no-results {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 10px;
            color: #6c757d;
        }

        /* Estilos para paginación */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }
        
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .pagination li {
            margin: 0 2px;
        }
        
        .pagination li a {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #680c39;
            transition: all 0.3s ease;
        }
        
        .pagination li.active a {
            background-color: #680c39;
            color: #fff;
            border-color: #680c39;
        }
        
        .pagination li a:hover:not(.active) {
            background-color: #f5f5f5;
        }
        
        .pagination li.disabled a {
            color: #999;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .records-info {
            text-align: center;
            color: #777;
            font-size: 14px;
            margin: 15px 0;
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .filter-buttons {
                flex-direction: column;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo-container">
            <img src="Imagenes/Logo.jpg" alt="Logo VialServi" class="logo">
        </div>
        <div>
            <a data-no-warning href="crear_servicio_realizado_cliente.php">Pedir un Servicio</a>
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
            
            <!-- Formulario de filtrado -->
            <div class="filter-container">
                <form id="filterForm" class="filter-form" method="GET">
                    <input type="hidden" name="pagina" value="1">
                    <div class="filter-group">
                        <label for="placa">Placa:</label>
                        <select data-no-warning name="placa" id="placa" class="auto-submit">
                            <option value="">Todas las placas</option>
                            <?php foreach ($vehiculos as $vehiculo): ?>
                                <option value="<?php echo htmlspecialchars($vehiculo['Placa']); ?>" <?php echo ($filtro_placa == $vehiculo['Placa']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($vehiculo['Placa']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="servicio">Tipo de Servicio:</label>
                        <select data-no-warning name="servicio" id="servicio" class="auto-submit">
                            <option value="">Todos los servicios</option>
                            <?php foreach ($tipos_servicios as $servicio): ?>
                                <option value="<?php echo $servicio['Servicio_id']; ?>" <?php echo ($filtro_servicio == $servicio['Servicio_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($servicio['Nombre_Servicio']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="estado">Estado:</label>
                        <select data-no-warning name="estado" id="estado" class="auto-submit">
                            <option value="">Todos</option>
                            <option value="programado" <?php echo ($filtro_estado == 'programado') ? 'selected' : ''; ?>>Programados</option>
                            <option value="completado" <?php echo ($filtro_estado == 'completado') ? 'selected' : ''; ?>>Completados</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="mes">Mes:</label>
                        <input data-no-warning type="month" id="mes" name="mes" value="<?php echo $filtro_mes; ?>" class="auto-submit">
                    </div>
                    
                    <div class="filter-group">
                        <div class="filter-buttons">
                            <a data-no-warning href="cliente_dashboard.php" class="filter-button clear-button">Limpiar filtros</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Visualización de servicios -->
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
                    <div class="no-results">
                        <h3>No se encontraron servicios</h3>
                        <p>No hay servicios que coincidan con los filtros seleccionados.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- NUEVO: Paginación -->
            <?php if ($total_paginas > 1): ?>
            <div class="pagination-container">
                <ul class="pagination">
                    <!-- Botón Anterior -->
                    <li class="<?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                        <a href="<?= construirUrlPaginacion($pagina_actual - 1) ?>" data-no-warning>
                            &laquo; Anterior
                        </a>
                    </li>
                    
                    <?php
                    // Determinar el rango de páginas a mostrar
                    $rango = 2; // Mostrar 2 páginas antes y después de la actual
                    $inicio_rango = max(1, $pagina_actual - $rango);
                    $fin_rango = min($total_paginas, $pagina_actual + $rango);
                    
                    // Mostrar primera página si no está en el rango
                    if ($inicio_rango > 1) {
                        echo '<li><a href="' . construirUrlPaginacion(1) . '" data-no-warning>1</a></li>';
                        if ($inicio_rango > 2) {
                            echo '<li class="disabled"><a>...</a></li>';
                        }
                    }
                    
                    // Mostrar las páginas en el rango
                    for ($i = $inicio_rango; $i <= $fin_rango; $i++) {
                        echo '<li class="' . ($i == $pagina_actual ? 'active' : '') . '">
                                <a href="' . construirUrlPaginacion($i) . '" data-no-warning>' . $i . '</a>
                              </li>';
                    }
                    
                    // Mostrar última página si no está en el rango
                    if ($fin_rango < $total_paginas) {
                        if ($fin_rango < $total_paginas - 1) {
                            echo '<li class="disabled"><a>...</a></li>';
                        }
                        echo '<li><a href="' . construirUrlPaginacion($total_paginas) . '" data-no-warning>' . $total_paginas . '</a></li>';
                    }
                    ?>
                    
                    <!-- Botón Siguiente -->
                    <li class="<?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                        <a href="<?= construirUrlPaginacion($pagina_actual + 1) ?>" data-no-warning>
                            Siguiente &raquo;
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Información de paginación -->
            <div class="records-info">
                Mostrando <?= min(($pagina_actual - 1) * $registros_por_pagina + 1, $total_registros) ?> 
                a <?= min($pagina_actual * $registros_por_pagina, $total_registros) ?> 
                de <?= $total_registros ?> servicios
            </div>
            <?php endif; ?>
        </div>

        <!-- Formulario de descarga de reporte -->
        <form data-no-warning action="descargar_reporte_cliente.php" method="get">
            <!-- Pasar los filtros actuales como parámetros ocultos -->
            <input type="hidden" name="placa" value="<?php echo htmlspecialchars($filtro_placa); ?>">
            <input type="hidden" name="servicio" value="<?php echo htmlspecialchars($filtro_servicio); ?>">
            <input type="hidden" name="estado" value="<?php echo htmlspecialchars($filtro_estado); ?>">
            <input type="hidden" name="mes" value="<?php echo htmlspecialchars($filtro_mes); ?>">
            <button data-no-warning type="submit" class="logout-btn" style="background-color:#680c39;">Descargar Reporte Filtrado</button>
        </form>

        <form action="logout_cliente.php" method="post">
            <button type="submit" class="logout-btn" style="background-color:#680c39; transform: translateY(-2px);">Cerrar Sesión</button>
        </form>
    </div>

    <!-- Script para enviar el formulario automáticamente al cambiar los filtros -->
     <script src="js/session-check.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los elementos con clase auto-submit
            const autoSubmitElements = document.querySelectorAll('.auto-submit');
            
            // Agregar listener de eventos para cada elemento
            autoSubmitElements.forEach(element => {
                element.addEventListener('change', function() {
                    // Al cambiar un filtro, siempre volvemos a la página 1
                    document.querySelector('input[name="pagina"]').value = "1";
                    
                    // Pequeño retraso para mejor experiencia de usuario
                    setTimeout(() => {
                        document.getElementById('filterForm').submit();
                    }, 300);
                });
            });
        });
    </script>
</body>
</html>