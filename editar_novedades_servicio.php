<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirigir si no está logueado
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}
// Luego incluir el archivo de permisos
include('validPermissions.php');

$usuarioId = $_SESSION['cedula'];
$permisoRequerido = 'actualizar_servicio';

if (!usuarioTienePermiso($usuarioId, $permisoRequerido, $conn)) {
    die("❌ Acceso denegado: No tienes permiso para modificar servicios.");
}

// Verificar si es técnico
$esTecnico = isset($_SESSION['rol']) && $_SESSION['rol'] == 2;

if (!$esTecnico) {
    die("❌ Acceso denegado: Esta función es solo para técnicos.");
}

// Procesar actualización de novedades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_novedades'])) {
    $servicioId = (int)$_POST['servicio_id'];
    $nuevasNovedades = trim($_POST['nuevas_novedades']);
    
    // Actualizar novedades verificando que el servicio pertenece al técnico
    $queryUpdate = "UPDATE servicios_realizados SET Novedades = ? 
                   WHERE Servicio_Realizado_id = ? 
                   AND Cedula_Empleado_id_Servicios_Realizados = ?";
    $stmtUpdate = mysqli_prepare($conn, $queryUpdate);
    
    if ($stmtUpdate === false) {
        error_log("Error en la preparación de la consulta actualizar: " . mysqli_error($conn));
        die("Error en la consulta de actualización: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmtUpdate, "sii", $nuevasNovedades, $servicioId, $usuarioId);
    
    if (mysqli_stmt_execute($stmtUpdate)) {
        // Verificar si realmente se actualizó alguna fila
        $filasAfectadas = mysqli_stmt_affected_rows($stmtUpdate);
        if ($filasAfectadas > 0) {
            $mensaje = "Novedades actualizadas correctamente para el servicio #$servicioId";
            $tipoMensaje = "success";
        } else {
            error_log("No se encontró el servicio o no hubo cambios. ID: $servicioId, Técnico: $usuarioId");
            $mensaje = "No se actualizaron las novedades. El servicio no existe o no está asignado a tu cuenta.";
            $tipoMensaje = "error";
        }
    } else {
        error_log("Error al actualizar novedades: " . mysqli_stmt_error($stmtUpdate));
        $mensaje = "Error al actualizar las novedades: " . mysqli_stmt_error($stmtUpdate);
        $tipoMensaje = "error";
    }
    mysqli_stmt_close($stmtUpdate);
}

// Inicializar variables de filtro
$filtroPlaca = '';
$filtroFechaInicio = '';
$filtroFechaFin = '';
$filtroServicio = ''; // Nuevo filtro para tipo de servicio

// Procesar filtros cuando se envía el formulario
if (isset($_POST['filtrar']) || isset($_POST['placa']) || isset($_POST['fecha_inicio']) || isset($_POST['fecha_fin']) || isset($_POST['tipo_servicio'])) {
    $filtroPlaca = isset($_POST['placa']) ? trim($_POST['placa']) : '';
    $filtroFechaInicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : '';
    $filtroFechaFin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : '';
    $filtroServicio = isset($_POST['tipo_servicio']) ? (int)$_POST['tipo_servicio'] : ''; // Nuevo filtro
    
    // Guardar filtros en la sesión
    $_SESSION['filtro_placa'] = $filtroPlaca;
    $_SESSION['filtro_fecha_inicio'] = $filtroFechaInicio;
    $_SESSION['filtro_fecha_fin'] = $filtroFechaFin;
    $_SESSION['filtro_servicio'] = $filtroServicio; // Guardar en sesión
} else {
    // Recuperar filtros de la sesión si existen
    $filtroPlaca = isset($_SESSION['filtro_placa']) ? $_SESSION['filtro_placa'] : '';
    $filtroFechaInicio = isset($_SESSION['filtro_fecha_inicio']) ? $_SESSION['filtro_fecha_inicio'] : '';
    $filtroFechaFin = isset($_SESSION['filtro_fecha_fin']) ? $_SESSION['filtro_fecha_fin'] : '';
    $filtroServicio = isset($_SESSION['filtro_servicio']) ? $_SESSION['filtro_servicio'] : ''; // Recuperar de sesión
}

// Limpiar filtros si se solicita
if (isset($_GET['limpiar_filtros'])) {
    unset($_SESSION['filtro_placa']);
    unset($_SESSION['filtro_fecha_inicio']);
    unset($_SESSION['filtro_fecha_fin']);
    unset($_SESSION['filtro_servicio']); // Limpiar filtro de servicio
    $filtroPlaca = '';
    $filtroFechaInicio = '';
    $filtroFechaFin = '';
    $filtroServicio = '';
    header("Location: editar_novedades_servicio.php");
    exit;
}

// ASEGURAR QUE SOLO SE MUESTRAN LOS SERVICIOS DEL TÉCNICO:
// Preparar la consulta con filtros y filtro obligatorio de cédula de técnico
$whereClause = 'sr.Cedula_Empleado_id_Servicios_Realizados = ?';
$params = [$usuarioId];
$types = 'i'; // Para el usuarioId

// Adicionar los demás filtros
if (!empty($filtroPlaca)) {
    $whereClause .= " AND sr.Vehiculo_id_Servicios_Realizados = ?";
    $params[] = $filtroPlaca;
    $types .= 's'; // string para placa
}

if (!empty($filtroFechaInicio)) {
    $whereClause .= " AND sr.Fecha >= ?";
    $params[] = $filtroFechaInicio;
    $types .= 's'; // string para fecha
}

if (!empty($filtroFechaFin)) {
    $whereClause .= " AND sr.Fecha <= ?";
    $params[] = $filtroFechaFin;
    $types .= 's'; // string para fecha
}

// Nuevo filtro por tipo de servicio
if (!empty($filtroServicio)) {
    $whereClause .= " AND sr.Servicio_id_Servicios_Realizados = ?";
    $params[] = $filtroServicio;
    $types .= 'i'; // integer para id de servicio
}

// Obtener todas las placas asociadas a servicios del técnico
$sqlPlacas = "SELECT DISTINCT sr.Vehiculo_id_Servicios_Realizados AS Placa 
               FROM servicios_realizados sr 
               WHERE sr.Cedula_Empleado_id_Servicios_Realizados = ? 
               ORDER BY sr.Vehiculo_id_Servicios_Realizados";
$stmtPlacas = mysqli_prepare($conn, $sqlPlacas);
mysqli_stmt_bind_param($stmtPlacas, "i", $usuarioId);
mysqli_stmt_execute($stmtPlacas);
$resultPlacas = mysqli_stmt_get_result($stmtPlacas);

// Obtener todos los tipos de servicios realizados por el técnico
$sqlServicios = "SELECT DISTINCT s.Servicio_id, s.Nombre_Servicio 
                FROM servicios_realizados sr 
                JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
                WHERE sr.Cedula_Empleado_id_Servicios_Realizados = ? 
                ORDER BY s.Nombre_Servicio";
$stmtServicios = mysqli_prepare($conn, $sqlServicios);
mysqli_stmt_bind_param($stmtServicios, "i", $usuarioId);
mysqli_stmt_execute($stmtServicios);
$resultTiposServicio = mysqli_stmt_get_result($stmtServicios);

// Configuración de paginación
$registrosPorPagina = 6; // Ajustar según necesidades
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $registrosPorPagina;

// Contar total de registros con filtros aplicados
$queryCount = "SELECT COUNT(*) AS total FROM servicios_realizados sr WHERE " . $whereClause;
$stmtCount = mysqli_prepare($conn, $queryCount);
mysqli_stmt_bind_param($stmtCount, $types, ...$params);
mysqli_stmt_execute($stmtCount);
$resultCount = mysqli_stmt_get_result($stmtCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalRegistros = $rowCount['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// CONSULTA CLARA Y EXPLÍCITA para servicios del técnico con filtros
$queryServicios = "
    SELECT 
        sr.Servicio_Realizado_id,
        sr.Fecha,
        sr.Novedades,
        sr.Ubicación,
        sr.Fotos,
        sr.Cedula_Empleado_id_Servicios_Realizados,
        sr.Servicio_id_Servicios_Realizados,
        s.Nombre_Servicio,
        v.Placa,
        m.nombre as nombre_municipio
    FROM servicios_realizados sr
    LEFT JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
    LEFT JOIN vehiculos v ON sr.Vehiculo_id_Servicios_Realizados = v.Placa
    LEFT JOIN municipios m ON sr.municipio = m.id
    WHERE " . $whereClause . "
    ORDER BY sr.Fecha DESC
    LIMIT ? OFFSET ?
";

$stmtConsultaServicios = mysqli_prepare($conn, $queryServicios);

if ($stmtConsultaServicios === false) {
    die("Error en la consulta principal: " . mysqli_error($conn));
}

// Añadir parámetros de paginación
$allParams = $params;
$allParams[] = $registrosPorPagina;
$allParams[] = $offset;
$allTypes = $types . 'ii'; // 'i' para limit y offset

mysqli_stmt_bind_param($stmtConsultaServicios, $allTypes, ...$allParams);
mysqli_stmt_execute($stmtConsultaServicios);
$resultServicios = mysqli_stmt_get_result($stmtConsultaServicios);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Novedades de Servicios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
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
            background-repeat: no-repeat;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        h1 {
            color: #680c39;
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #2d0f2a;
            color: white;
        }

        .btn-primary:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }
        
        .btn-filter {
            background-color: #0056b3;
            color: white;
        }
        
        .btn-filter:hover {
            background-color: #004094;
        }
        
        .btn-reset {
            background-color: #6c757d;
            color: white;
            margin-left: 10px;
        }

        .servicios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .servicio-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .servicio-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .servicio-info {
            margin-bottom: 20px;
        }

        .servicio-info h3 {
            color: #680c39;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .servicio-info p {
            margin: 8px 0;
            color: #555;
            font-size: 14px;
        }

        .servicio-info strong {
            color: #333;
        }

        .novedades-form {
            border-top: 2px solid #f0f0f0;
            padding-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
            transition: border-color 0.3s ease;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: #680c39;
            box-shadow: 0 0 0 3px rgba(104, 12, 57, 0.1);
        }

        .form-group select, .form-group input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group select:focus, .form-group input[type="date"]:focus {
            outline: none;
            border-color: #680c39;
            box-shadow: 0 0 0 3px rgba(104, 12, 57, 0.1);
        }

        .foto-servicio {
            width: 100%;
            max-width: 250px;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
            border: 2px solid #f0f0f0;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 600;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background-color: #e8f4fd;
            color: #0c5460;
            border: 1px solid #bee5eb;
            margin: 15px 0;
        }

        .novedades-actuales {
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #680c39;
        }

        .novedades-actuales h4 {
            color: #680c39;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .novedades-actuales p {
            color: #666;
            font-style: italic;
            margin: 0;
        }

        .char-counter {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .char-counter.warning {
            color: #e67e22;
        }

        .char-counter.danger {
            color: #e74c3c;
        }

        .no-servicios {
            text-align: center;
            padding: 40px;
            color: #666;
            grid-column: 1 / -1;
        }

        .no-servicios h3 {
            color: #680c39;
            margin-bottom: 15px;
        }
        
        /* Estilos para el filtro */
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .filter-title {
            color: #680c39;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .filter-buttons {
            display: flex;
            justify-content: center;
            margin-top: 15px;
            grid-column: 1 / -1;
        }
        
        /* Estilos para la paginación */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            background-color: white;
            color: #680c39;
            border: 1px solid #ddd;
            margin: 0 4px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .pagination a:hover {
            background-color: #f1f1f1;
        }
        
        .pagination .active {
            background-color: #680c39;
            color: white;
            border: 1px solid #680c39;
        }
        
        .pagination .disabled {
            color: #aaa;
            cursor: not-allowed;
        }
        
        .results-summary {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }
        
        .results-summary strong {
            color: #680c39;
        }

        /* Estilo para indicar que un campo está siendo filtrado */
        .is-filtered {
            border-color: #680c39 !important;
            background-color: rgba(104, 12, 57, 0.05) !important;
            box-shadow: 0 0 0 1px rgba(104, 12, 57, 0.25) !important;
        }

        /* Nuevos estilos para las etiquetas de filtro */
        .filter-badge {
            display: inline-block;
            background-color: #680c39;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 10px;
        }

        .filter-badge i {
            cursor: pointer;
            margin-left: 5px;
            opacity: 0.8;
        }

        .filter-badge i:hover {
            opacity: 1;
        }

        .active-filters {
            margin-bottom: 15px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .servicios-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 15px;
            }

            .servicio-card {
                padding: 20px;
            }
            
            .filter-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
\</head>
<body>
    <div class="container">
        <a data-no-warning  href="dashboard.php" class="btn btn-back">← Volver al Dashboard</a>
        
        <h1>Editar Novedades de Servicios - Técnico #<?php echo $usuarioId; ?></h1>

        <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipoMensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Sección de Filtros -->
        <div class="filter-section">
            <h2 class="filter-title">Filtrar Servicios</h2>
            
            <form method="post" action="" class="filter-form" id="filterForm">
                <div class="form-group">
                    <label for="placa">Placa del Vehículo</label>
                    <select data-no-warning  name="placa" id="placa" class="form-select auto-submit <?php echo !empty($filtroPlaca) ? 'is-filtered' : ''; ?>">
                        <option value="">Todas las placas</option>
                        <?php 
                        // Reset pointer al inicio de los resultados
                        mysqli_data_seek($resultPlacas, 0); 
                        while ($rowPlaca = mysqli_fetch_assoc($resultPlacas)): ?>
                            <option value="<?php echo htmlspecialchars($rowPlaca['Placa']); ?>" 
                                <?php echo ($filtroPlaca == $rowPlaca['Placa']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rowPlaca['Placa']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <!-- Nuevo filtro por tipo de servicio -->
                <div class="form-group">
                    <label for="tipo_servicio">Tipo de Servicio</label>
                    <select data-no-warning  name="tipo_servicio" id="tipo_servicio" class="form-select auto-submit <?php echo !empty($filtroServicio) ? 'is-filtered' : ''; ?>">
                        <option value="">Todos los servicios</option>
                        <?php 
                        mysqli_data_seek($resultTiposServicio, 0);
                        while ($rowServicio = mysqli_fetch_assoc($resultTiposServicio)): ?>
                            <option value="<?php echo $rowServicio['Servicio_id']; ?>" 
                                <?php echo ($filtroServicio == $rowServicio['Servicio_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rowServicio['Nombre_Servicio']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control auto-submit <?php echo !empty($filtroFechaInicio) ? 'is-filtered' : ''; ?>"
                        value="<?php echo htmlspecialchars($filtroFechaInicio); ?>">
                </div>
                
                <div class="form-group">
                    <label for="fecha_fin">Fecha Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control auto-submit <?php echo !empty($filtroFechaFin) ? 'is-filtered' : ''; ?>" 
                        value="<?php echo htmlspecialchars($filtroFechaFin); ?>">
                </div>
                
                <div class="filter-buttons">
                    <input type="hidden" name="filtrar" value="1">
                    <a data-no-warning  href="?limpiar_filtros=1" class="btn btn-reset">
                        Limpiar Filtros
                    </a>
                </div>
            </form>
            
            <!-- Mostrar filtros activos -->
            <?php if (!empty($filtroPlaca) || !empty($filtroFechaInicio) || !empty($filtroFechaFin) || !empty($filtroServicio)): ?>
                <div class="active-filters">
                    <div class="filter-badges">
                        <?php if (!empty($filtroPlaca)): ?>
                            <span class="filter-badge">
                                Placa: <?php echo htmlspecialchars($filtroPlaca); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($filtroServicio)): 
                            // Buscar el nombre del servicio
                            mysqli_data_seek($resultTiposServicio, 0);
                            $nombreServicio = "Servicio desconocido";
                            while ($rowServicio = mysqli_fetch_assoc($resultTiposServicio)) {
                                if ($rowServicio['Servicio_id'] == $filtroServicio) {
                                    $nombreServicio = $rowServicio['Nombre_Servicio'];
                                    break;
                                }
                            }
                        ?>
                            <span class="filter-badge">
                                Servicio: <?php echo htmlspecialchars($nombreServicio); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($filtroFechaInicio)): ?>
                            <span class="filter-badge">
                                Desde: <?php echo date('d/m/Y', strtotime($filtroFechaInicio)); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($filtroFechaFin)): ?>
                            <span class="filter-badge">
                                Hasta: <?php echo date('d/m/Y', strtotime($filtroFechaFin)); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Resumen de resultados -->
        <div class="results-summary">
            Mostrando <strong><?php echo min($totalRegistros, $registrosPorPagina); ?></strong> 
            de <strong><?php echo $totalRegistros; ?></strong> servicios
            <?php if (!empty($filtroPlaca) || !empty($filtroFechaInicio) || !empty($filtroFechaFin) || !empty($filtroServicio)): ?>
                (con filtros aplicados)
            <?php endif; ?>
        </div>

        <?php if ($totalRegistros > 0): ?>
            <div class="servicios-grid">
                <?php while ($servicio = mysqli_fetch_assoc($resultServicios)): ?>
                    <div class="servicio-card">
                        <div class="servicio-info">
                            <h3><?php echo htmlspecialchars($servicio['Nombre_Servicio'] ?? 'Sin servicio asignado'); ?></h3>
                            <p><strong>ID:</strong> #<?php echo $servicio['Servicio_Realizado_id']; ?></p>
                            <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($servicio['Fecha'])); ?></p>
                            <p><strong>Vehículo:</strong> <?php echo htmlspecialchars($servicio['Placa'] ?? 'Sin placa'); ?></p>
                            <p><strong>Municipio:</strong> <?php echo htmlspecialchars($servicio['nombre_municipio'] ?? 'Sin municipio'); ?></p>
                            <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($servicio['Ubicación'] ?? 'Sin ubicación'); ?></p>
                            
                            <?php if (!empty($servicio['Fotos'])): ?>
                                <img src="<?php echo htmlspecialchars($servicio['Fotos']); ?>" 
                                     alt="Foto del servicio" class="foto-servicio">
                            <?php endif; ?>
                        </div>

                        <div class="novedades-form">
                            <div class="novedades-actuales">
                                <h4>Novedades Actuales:</h4>
                                <p><?php echo !empty($servicio['Novedades']) ? htmlspecialchars($servicio['Novedades']) : 'Sin novedades registradas'; ?></p>
                            </div>

                            <!-- Formulario de actualización de novedades -->
                            <form method="POST" class="novedades-update-form" id="form_<?php echo $servicio['Servicio_Realizado_id']; ?>">
                                <input type="hidden" name="servicio_id" value="<?php echo $servicio['Servicio_Realizado_id']; ?>">
                                
                                <div class="form-group">
                                    <label for="novedades_<?php echo $servicio['Servicio_Realizado_id']; ?>">Nuevas Novedades:</label>
                                    <textarea 
                                        name="nuevas_novedades" 
                                        id="novedades_<?php echo $servicio['Servicio_Realizado_id']; ?>" 
                                        placeholder="Escribe aquí las nuevas novedades del servicio..."
                                        maxlength="1000"
                                        oninput="updateCharCounter(this)"
                                        required><?php echo htmlspecialchars($servicio['Novedades'] ?? ''); ?></textarea>
                                    <div class="char-counter" id="counter_<?php echo $servicio['Servicio_Realizado_id']; ?>">
                                        <span class="current">0</span>/1000 caracteres
                                    </div>
                                </div>
                                
                                <button type="button" 
                                        onclick="confirmarActualizacion(<?php echo $servicio['Servicio_Realizado_id']; ?>)" 
                                        class="btn btn-primary">
                                    Actualizar Novedades
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <div class="pagination">
                    <?php if ($paginaActual > 1): ?>
                        <a data-no-warning  href="?pagina=1&placa=<?php echo urlencode($filtroPlaca); ?>&tipo_servicio=<?php echo urlencode($filtroServicio); ?>&fecha_inicio=<?php echo urlencode($filtroFechaInicio); ?>&fecha_fin=<?php echo urlencode($filtroFechaFin); ?>" data-no-warning class="page-link">&laquo; Primera</a>
                        <a data-no-warning  href="?pagina=<?php echo $paginaActual - 1; ?>&placa=<?php echo urlencode($filtroPlaca); ?>&tipo_servicio=<?php echo urlencode($filtroServicio); ?>&fecha_inicio=<?php echo urlencode($filtroFechaInicio); ?>&fecha_fin=<?php echo urlencode($filtroFechaFin); ?>" data-no-warning class="page-link">&lsaquo; Anterior</a>
                    <?php else: ?>
                        <span class="page-link disabled">&laquo; Primera</span>
                        <span class="page-link disabled">&lsaquo; Anterior</span>
                    <?php endif; ?>
                    
                    <?php
                    $inicio = max(1, $paginaActual - 2);
                    $fin = min($inicio + 4, $totalPaginas);
                    if ($fin - $inicio < 4) $inicio = max(1, $fin - 4);
                    
                    for ($i = $inicio; $i <= $fin; $i++):
                    ?>
                        <a data-no-warning  href="?pagina=<?php echo $i; ?>&placa=<?php echo urlencode($filtroPlaca); ?>&tipo_servicio=<?php echo urlencode($filtroServicio); ?>&fecha_inicio=<?php echo urlencode($filtroFechaInicio); ?>&fecha_fin=<?php echo urlencode($filtroFechaFin); ?>" 
                           class="page-link <?php echo ($i == $paginaActual) ? 'active' : ''; ?>" data-no-warning>
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a data-no-warning  href="?pagina=<?php echo $paginaActual + 1; ?>&placa=<?php echo urlencode($filtroPlaca); ?>&tipo_servicio=<?php echo urlencode($filtroServicio); ?>&fecha_inicio=<?php echo urlencode($filtroFechaInicio); ?>&fecha_fin=<?php echo urlencode($filtroFechaFin); ?>" data-no-warning class="page-link">Siguiente &rsaquo;</a>
                        <a data-no-warning  href="?pagina=<?php echo $totalPaginas; ?>&placa=<?php echo urlencode($filtroPlaca); ?>&tipo_servicio=<?php echo urlencode($filtroServicio); ?>&fecha_inicio=<?php echo urlencode($filtroFechaInicio); ?>&fecha_fin=<?php echo urlencode($filtroFechaFin); ?>" data-no-warning class="page-link">Última &raquo;</a>
                    <?php else: ?>
                        <span class="page-link disabled">Siguiente &rsaquo;</span>
                        <span class="page-link disabled">Última &raquo;</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-servicios">
                <h3>No se encontraron servicios</h3>
                <p>No hay servicios asignados a tu cuenta con los criterios seleccionados.</p>
                <?php if (!empty($filtroPlaca) || !empty($filtroFechaInicio) || !empty($filtroFechaFin) || !empty($filtroServicio)): ?>
                    <a href="?limpiar_filtros=1" class="btn btn-primary mt-3" data-no-warning>Limpiar filtros</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function updateCharCounter(textarea) {
            const maxLength = 1000;
            const currentLength = textarea.value.length;
            const servicioId = textarea.id.split('_')[1];
            const counter = document.getElementById('counter_' + servicioId);
            const currentSpan = counter.querySelector('.current');
            
            currentSpan.textContent = currentLength;
            
            // Cambiar color según la cantidad de caracteres
            counter.classList.remove('warning', 'danger');
            if (currentLength > maxLength * 0.8) {
                counter.classList.add('warning');
            }
            if (currentLength > maxLength * 0.95) {
                counter.classList.add('danger');
            }
        }

        function confirmarActualizacion(servicioId) {
            const form = document.getElementById('form_' + servicioId);
            const nuevasNovedades = form.querySelector('textarea[name="nuevas_novedades"]').value.trim();
            
            if (!nuevasNovedades) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo requerido',
                    text: 'Por favor escribe las novedades del servicio.',
                });
                return;
            }

            if (nuevasNovedades.length < 10) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Novedades muy cortas',
                    text: 'Las novedades deben tener al menos 10 caracteres.',
                });
                return;
            }

            Swal.fire({
                title: '¿Confirmar actualización?',
                text: `¿Estás seguro de actualizar las novedades del servicio #${servicioId}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2d0f2a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar',
                didOpen: () => {
                    document.querySelector('.swal2-confirm').setAttribute('data-no-warning', '');
                    document.querySelector('.swal2-cancel').setAttribute('data-no-warning', '');
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Prevenir conflictos con el filtrado automático
                    const autoSubmitElements = document.querySelectorAll('.auto-submit');
                    autoSubmitElements.forEach(el => {
                        el.classList.add('submit-disabled');
                        el.disabled = true;
                    });
                    
                    // Añadir los filtros actuales al formulario para mantenerlos después de la actualización
                    const filtros = ['placa', 'fecha_inicio', 'fecha_fin', 'tipo_servicio'];
                    filtros.forEach(filtro => {
                        const elemento = document.getElementById(filtro);
                        if (elemento && elemento.value) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = filtro;
                            input.value = elemento.value;
                            form.appendChild(input);
                        }
                    });
                    
                    // Añadir el campo actualizar_novedades
                    const actualizarInput = document.createElement('input');
                    actualizarInput.type = 'hidden';
                    actualizarInput.name = 'actualizar_novedades';
                    actualizarInput.value = '1';
                    form.appendChild(actualizarInput);
                    
                    // Enviar el formulario
                    form.submit();
                }
            });
        }

        // Script para filtrado automático y otras funcionalidades
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los elementos con clase auto-submit
            const autoSubmitElements = document.querySelectorAll('.auto-submit');
            
            // Agregar listener de eventos para cada elemento
            autoSubmitElements.forEach(element => {
                element.addEventListener('change', function() {
                    if (!element.classList.contains('submit-disabled')) {
                        setTimeout(() => {
                            document.getElementById('filterForm').submit();
                        }, 300);
                    }
                });
            });
            
            // Inicializar contadores de caracteres al cargar la página
            const textareas = document.querySelectorAll('textarea[name="nuevas_novedades"]');
            textareas.forEach(textarea => {
                updateCharCounter(textarea);
            });
            
            // Validación de fechas
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaFin = document.getElementById('fecha_fin');
            
            document.querySelector('form.filter-form').addEventListener('submit', function(e) {
                if (fechaInicio.value && fechaFin.value) {
                    if (new Date(fechaInicio.value) > new Date(fechaFin.value)) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en fechas',
                            text: 'La fecha de inicio no puede ser posterior a la fecha de fin.',
                            didOpen: () => {
                                document.querySelector('.swal2-confirm').setAttribute('data-no-warning', '');
                            }
                        });
                    }
                }
            });
        });

        // Mostrar mensaje de éxito si existe
        <?php if (isset($mensaje) && $tipoMensaje === 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?php echo $mensaje; ?>',
                timer: 3000,
                showConfirmButton: false,
                didOpen: () => {
                    if (document.querySelector('.swal2-confirm')) {
                        document.querySelector('.swal2-confirm').setAttribute('data-no-warning', '');
                    }
                }
            });
        <?php endif; ?>

        // Mostrar mensaje de error si existe
        <?php if (isset($mensaje) && $tipoMensaje === 'error'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $mensaje; ?>',
                didOpen: () => {
                    document.querySelector('.swal2-confirm').setAttribute('data-no-warning', '');
                }
            });
        <?php endif; ?>
    </script>
</body>
</html>

<?php
// Cerrar declaraciones y conexión
mysqli_stmt_close($stmtConsultaServicios);
mysqli_stmt_close($stmtCount);
mysqli_stmt_close($stmtPlacas);
mysqli_stmt_close($stmtServicios);
mysqli_close($conn);
?>