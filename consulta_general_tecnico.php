<?php
session_start();

// Validar que el usuario haya iniciado sesión y tenga rol de técnico (2)
if (!isset($_SESSION['cedula']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 2) {
    echo "<p>No autorizado. Debe iniciar sesión como técnico.</p>";
    exit;
}

$cedula_tecnico = $_SESSION['cedula'];

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$database = "vial_servi";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicializar variables de filtro (solo se aplicarán dentro de los servicios del técnico)
$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$filtro_placa = isset($_GET['placa']) ? $_GET['placa'] : '';
$filtro_servicio = isset($_GET['servicio']) ? $_GET['servicio'] : '';
$filtro_municipio = isset($_GET['municipio']) ? $_GET['municipio'] : '';

// Configuración de paginación
$registros_por_pagina = 10; // Número de registros por página
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Base de la consulta SQL para contar registros
$sql_count = "
    SELECT COUNT(*) as total
    FROM servicios_realizados sr
    LEFT JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
    LEFT JOIN municipios m ON sr.municipio = m.id
    WHERE sr.Cedula_Empleado_id_Servicios_Realizados = ?
";

// Consulta principal base
$sql = "
    SELECT 
        sr.Servicio_Realizado_id,
        s.Nombre_Servicio,
        s.Descripción,
        sr.Fecha,
        sr.Ubicación,
        m.nombre AS Municipio,
        sr.Vehiculo_id_Servicios_Realizados AS Placa,
        sr.Novedades,
        sr.Detalle_Servicio,
        sr.Custodia_Servicio
    FROM servicios_realizados sr
    LEFT JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
    LEFT JOIN municipios m ON sr.municipio = m.id
    WHERE sr.Cedula_Empleado_id_Servicios_Realizados = ?
";

// Añadir condiciones adicionales según los filtros
$params = [$cedula_tecnico];
$param_types = "i";

if (!empty($filtro_fecha)) {
    $sql .= " AND DATE_FORMAT(sr.Fecha, '%Y-%m') = ?";
    $sql_count .= " AND DATE_FORMAT(sr.Fecha, '%Y-%m') = ?";
    $params[] = $filtro_fecha;
    $param_types .= "s";
}

if (!empty($filtro_placa)) {
    $sql .= " AND sr.Vehiculo_id_Servicios_Realizados LIKE ?";
    $sql_count .= " AND sr.Vehiculo_id_Servicios_Realizados LIKE ?";
    $params[] = "%$filtro_placa%";
    $param_types .= "s";
}

if (!empty($filtro_servicio)) {
    $sql .= " AND s.Servicio_id = ?";
    $sql_count .= " AND s.Servicio_id = ?";
    $params[] = $filtro_servicio;
    $param_types .= "i";
}

if (!empty($filtro_municipio)) {
    $sql .= " AND m.id = ?";
    $sql_count .= " AND m.id = ?";
    $params[] = $filtro_municipio;
    $param_types .= "i";
}

// Ejecutar consulta para obtener total de registros
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param($param_types, ...$params);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$total_registros = $row_count['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Añadir ordenamiento y límites a la consulta principal
$sql .= " ORDER BY sr.Fecha DESC LIMIT ? OFFSET ?";
$params[] = $registros_por_pagina;
$params[] = $offset;
$param_types .= "ii";

// Ejecutar la consulta principal
$stmt = $conn->prepare($sql);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Obtener la lista de servicios para el filtro
$sql_servicios = "SELECT Servicio_id, Nombre_Servicio FROM servicios";
$result_servicios = $conn->query($sql_servicios);

// Obtener la lista de municipios para el filtro
$sql_municipios = "SELECT id, nombre FROM municipios";
$result_municipios = $conn->query($sql_municipios);

// Función para construir la URL de paginación
function construirUrlPaginacion($pagina) {
    global $filtro_fecha, $filtro_placa, $filtro_servicio, $filtro_municipio;
    
    $url = $_SERVER['PHP_SELF'] . '?pagina=' . $pagina;
    
    if (!empty($filtro_fecha)) $url .= '&fecha=' . $filtro_fecha;
    if (!empty($filtro_placa)) $url .= '&placa=' . $filtro_placa;
    if (!empty($filtro_servicio)) $url .= '&servicio=' . $filtro_servicio;
    if (!empty($filtro_municipio)) $url .= '&municipio=' . $filtro_municipio;
    
    return $url;
}

// Si se solicita exportar a Excel
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    // Configurar encabezados para la descarga de Excel
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header("Content-Disposition: attachment; filename=servicios_tecnico_{$cedula_tecnico}.xls");
    header('Cache-Control: max-age=0');
    
    // Asegurar zona horaria correcta para Colombia
    date_default_timezone_set('America/Bogota');
    
    // Recuperar el nombre del técnico
    $nombre_tecnico = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : $cedula_tecnico;
    
    // Iniciar documento HTML/Excel
    echo '<!DOCTYPE html>';
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<!--[if gte mso 9]>';
    echo '<xml>';
    echo '<x:ExcelWorkbook>';
    echo '<x:ExcelWorksheets>';
    echo '<x:ExcelWorksheet>';
    echo '<x:Name>Servicios Realizados</x:Name>';
    echo '<x:WorksheetOptions>';
    echo '<x:DisplayGridlines/>';
    echo '<x:Print>';
    echo '<x:ValidPrinterInfo/>';
    echo '</x:Print>';
    // Agregar protección de hoja
    echo '<x:ProtectContents>True</x:ProtectContents>';
    echo '<x:ProtectObjects>True</x:ProtectObjects>';
    echo '<x:ProtectScenarios>True</x:ProtectScenarios>';
    echo '</x:WorksheetOptions>';
    echo '</x:ExcelWorksheet>';
    echo '</x:ExcelWorksheets>';
    echo '</x:ExcelWorkbook>';
    echo '</xml>';
    echo '<![endif]-->';
    
    // Estilos CSS mejorados (igual al diseño anterior)
    echo '<style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th {
            background-color: #4F6228;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            border: 1px solid #000000;
        }
        td {
            padding: 6px;
            border: 1px solid #CCCCCC;
        }
        .section-title {
            background-color: #C6E0B4;
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border: 1px solid #000000;
        }
        .sub-title {
            background-color: #D6EECD;
            font-size: 14pt;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #000000;
        }
        .info-row td {
            font-weight: bold;
            background-color: #F2F2F2;
        }
        .total-row td {
            font-weight: bold;
            background-color: #E2EFDA;
            text-align: right;
        }
        .cell-right {
            text-align: right;
        }
    </style>';
    echo '</head>';
    echo '<body>';
    
    // ENCABEZADO DEL DOCUMENTO
    echo '<table>';
    echo '<tr><td colspan="10" class="section-title">VIALSERVI - REPORTE DE SERVICIOS DE ' . strtoupper(htmlspecialchars($nombre_tecnico)) . '</td></tr>';
    echo '<tr><td colspan="10" class="sub-title">INFORMACIÓN GENERAL</td></tr>';
    
    // Datos del informe
    echo '<tr class="info-row">';
    echo '<td>Técnico:</td>';
    echo '<td colspan="9">' . htmlspecialchars($nombre_tecnico) . '</td>';
    echo '</tr>';
    
    echo '<tr class="info-row">';
    echo '<td>Fecha de generación:</td>';
    echo '<td colspan="9">' . date('d/m/Y H:i:s') . '</td>';
    echo '</tr>';
    
    // Filtros aplicados
    if (!empty($filtro_fecha) || !empty($filtro_placa) || !empty($filtro_servicio) || !empty($filtro_municipio)) {
        echo '<tr class="info-row">';
        echo '<td>Filtros aplicados:</td>';
        echo '<td colspan="9">';
        
        $filtros_texto = [];
        if (!empty($filtro_fecha)) $filtros_texto[] = "Fecha: $filtro_fecha";
        if (!empty($filtro_placa)) $filtros_texto[] = "Placa: $filtro_placa";
        
        if (!empty($filtro_servicio)) {
            // Obtener el nombre del servicio
            $stmt_servicio = $conn->prepare("SELECT Nombre_Servicio FROM servicios WHERE Servicio_id = ?");
            $stmt_servicio->bind_param("i", $filtro_servicio);
            $stmt_servicio->execute();
            $result_servicio = $stmt_servicio->get_result();
            if ($row_servicio = $result_servicio->fetch_assoc()) {
                $filtros_texto[] = "Servicio: " . $row_servicio['Nombre_Servicio'];
            }
        }
        
        if (!empty($filtro_municipio)) {
            // Obtener el nombre del municipio
            $stmt_municipio = $conn->prepare("SELECT nombre FROM municipios WHERE id = ?");
            $stmt_municipio->bind_param("i", $filtro_municipio);
            $stmt_municipio->execute();
            $result_municipio = $stmt_municipio->get_result();
            if ($row_municipio = $result_municipio->fetch_assoc()) {
                $filtros_texto[] = "Municipio: " . $row_municipio['nombre'];
            }
        }
        
        echo implode(" | ", $filtros_texto);
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    
    // TABLA PRINCIPAL DE SERVICIOS
    echo '<table>';
    echo '<tr><td colspan="10" class="sub-title">DETALLE DE SERVICIOS REALIZADOS</td></tr>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Nombre del Servicio</th>';
    echo '<th>Descripción</th>';
    echo '<th>Mes</th>'; // Cambiar "Fecha" por "Mes"
    echo '<th>Placa</th>';
    echo '<th>Ubicación</th>';
    echo '<th>Municipio</th>';
    echo '<th>Novedades</th>';
    echo '<th>Detalle</th>';
    echo '<th>Custodia</th>';
    echo '</tr>';
    
    // Recuperar los datos sin límite para exportar todos
    $sql_export = str_replace("LIMIT ? OFFSET ?", "", $sql);
    $param_types_export = substr($param_types, 0, -2); // Quitar los últimos dos caracteres (ii)
    $params_export = array_slice($params, 0, -2); // Quitar los últimos dos elementos del array
    
    $stmt_export = $conn->prepare($sql_export);
    $stmt_export->bind_param($param_types_export, ...$params_export);
    $stmt_export->execute();
    $result_export = $stmt_export->get_result();
    
    $total_records = 0;
    
    // Modificar las celdas para tener atributo readonly (no es completamente efectivo pero añade una capa más)
    while ($row = $result_export->fetch_assoc()) {
        // Formatear la fecha para mostrar solo mes y año
        $fecha_completa = new DateTime($row['Fecha']);
        $mes_anio = $fecha_completa->format('Y-m'); // Formato YYYY-MM
        $mes_nombre = $fecha_completa->format('F Y'); // Nombre del mes y año
        
        echo '<tr>';
        echo '<td readonly="true">' . htmlspecialchars($row['Servicio_Realizado_id']) . '</td>';
        echo '<td readonly="true">' . htmlspecialchars($row['Nombre_Servicio']) . '</td>';
        echo '<td readonly="true">' . htmlspecialchars($row['Descripción']) . '</td>';
        echo '<td readonly="true">' . $mes_nombre . '</td>'; // Mostrar nombre del mes y año
        echo '<td readonly="true">' . htmlspecialchars($row['Placa']) . '</td>';
        echo '<td readonly="true">' . htmlspecialchars($row['Ubicación']) . '</td>';
        echo '<td readonly="true">' . htmlspecialchars($row['Municipio']) . '</td>';
        echo '<td readonly="true">' . htmlspecialchars($row['Novedades']) . '</td>';
        echo '<td readonly="true">' . htmlspecialchars($row['Detalle_Servicio']) . '</td>';
        echo '<td readonly="true">' . htmlspecialchars($row['Custodia_Servicio']) . '</td>';
        echo '</tr>';
        $total_records++;
    }
    
    // Agregar fila con el total
    echo '<tr class="total-row">';
    echo '<td colspan="9">Total de servicios:</td>';
    echo '<td>' . $total_records . '</td>';
    echo '</tr>';
    
    echo '</table>';
    
    // RESUMEN ESTADÍSTICO
    echo '<table>';
    echo '<tr><td colspan="3" class="sub-title">RESUMEN DE SERVICIOS</td></tr>';
    echo '<tr>';
    echo '<th>Tipo de Servicio</th>';
    echo '<th>Cantidad</th>';
    echo '<th>Porcentaje</th>';
    echo '</tr>';
    
    // Consulta para obtener conteo por tipo de servicio
    $sql_resumen = "
        SELECT 
            s.Nombre_Servicio, 
            COUNT(*) as cantidad
        FROM servicios_realizados sr
        LEFT JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
        WHERE sr.Cedula_Empleado_id_Servicios_Realizados = ?
    ";
    
    // Aplicar los mismos filtros que la consulta principal
    if (!empty($filtro_fecha)) {
        $sql_resumen .= " AND sr.Fecha = ?";
    }
    if (!empty($filtro_placa)) {
        $sql_resumen .= " AND sr.Vehiculo_id_Servicios_Realizados LIKE ?";
    }
    if (!empty($filtro_servicio)) {
        $sql_resumen .= " AND s.Servicio_id = ?";
    }
    if (!empty($filtro_municipio)) {
        $sql_resumen .= " AND sr.municipio = ?";
    }
    
    $sql_resumen .= " GROUP BY s.Nombre_Servicio ORDER BY cantidad DESC";
    
    $stmt_resumen = $conn->prepare($sql_resumen);
    $stmt_resumen->bind_param($param_types_export, ...$params_export);
    $stmt_resumen->execute();
    $result_resumen = $stmt_resumen->get_result();
    
    $datos_resumen = [];
    $total_servicios = 0;
    
    // Recopilar datos para el resumen
    while ($row = $result_resumen->fetch_assoc()) {
        $datos_resumen[] = $row;
        $total_servicios += $row['cantidad'];
    }
    
    // Mostrar datos con porcentajes
    foreach ($datos_resumen as $servicio) {
        $porcentaje = ($total_servicios > 0) ? round(($servicio['cantidad'] / $total_servicios) * 100, 1) . '%' : '0%';
        echo '<tr>';
        echo '<td>' . htmlspecialchars($servicio['Nombre_Servicio']) . '</td>';
        echo '<td class="cell-right">' . $servicio['cantidad'] . '</td>';
        echo '<td class="cell-right">' . $porcentaje . '</td>';
        echo '</tr>';
    }
    
    // Agregar fila total
    echo '<tr class="total-row">';
    echo '<td>TOTAL</td>';
    echo '<td class="cell-right">' . $total_servicios . '</td>';
    echo '<td class="cell-right">100%</td>';
    echo '</tr>';
    
    echo '</table>';

    // RESUMEN POR MES
    echo '<table>';
    echo '<tr><td colspan="3" class="sub-title">RESUMEN POR MES</td></tr>';
    echo '<tr>';
    echo '<th>Mes</th>';
    echo '<th>Cantidad de Servicios</th>';
    echo '<th>Porcentaje</th>';
    echo '</tr>';

    // Consulta para obtener conteo por mes
    $sql_meses = "
        SELECT 
            DATE_FORMAT(sr.Fecha, '%Y-%m') as mes,
            DATE_FORMAT(sr.Fecha, '%M %Y') as mes_nombre,
            COUNT(*) as cantidad
        FROM servicios_realizados sr
        WHERE sr.Cedula_Empleado_id_Servicios_Realizados = ?
    ";

    // Aplicar filtros similares pero excluyendo el filtro de fecha
    $params_meses = [$cedula_tecnico];
    $param_types_meses = "i";

    if (!empty($filtro_placa)) {
        $sql_meses .= " AND sr.Vehiculo_id_Servicios_Realizados LIKE ?";
        $params_meses[] = "%$filtro_placa%";
        $param_types_meses .= "s";
    }

    if (!empty($filtro_servicio)) {
        $sql_meses .= " AND sr.Servicio_id_Servicios_Realizados = ?";
        $params_meses[] = $filtro_servicio;
        $param_types_meses .= "i";
    }

    if (!empty($filtro_municipio)) {
        $sql_meses .= " AND sr.municipio = ?";
        $params_meses[] = $filtro_municipio;
        $param_types_meses .= "i";
    }

    $sql_meses .= " GROUP BY mes ORDER BY mes DESC";

    $stmt_meses = $conn->prepare($sql_meses);
    $stmt_meses->bind_param($param_types_meses, ...$params_meses);
    $stmt_meses->execute();
    $result_meses = $stmt_meses->get_result();

    $total_por_meses = 0;
    $datos_meses = [];

    while ($row = $result_meses->fetch_assoc()) {
        $datos_meses[] = $row;
        $total_por_meses += $row['cantidad'];
    }

    foreach ($datos_meses as $mes_data) {
        $porcentaje = ($total_por_meses > 0) ? round(($mes_data['cantidad'] / $total_por_meses) * 100, 1) . '%' : '0%';
        echo '<tr>';
        echo '<td>' . htmlspecialchars($mes_data['mes_nombre']) . '</td>';
        echo '<td class="cell-right">' . $mes_data['cantidad'] . '</td>';
        echo '<td class="cell-right">' . $porcentaje . '</td>';
        echo '</tr>';
    }

    echo '<tr class="total-row">';
    echo '<td>TOTAL</td>';
    echo '<td class="cell-right">' . $total_por_meses . '</td>';
    echo '<td class="cell-right">100%</td>';
    echo '</tr>';

    echo '</table>';
    
    // PIE DEL DOCUMENTO
    echo '<table>';
    echo '<tr><td colspan="10" class="section-title">DOCUMENTO GENERADO EL ' . date('d/m/Y H:i:s') . '</td></tr>';
    echo '</table>';
    
    echo '<div style="margin-top: 20px; color: #cc0000; font-weight: bold; text-align: center;">';
    echo 'DOCUMENTO DE SOLO LECTURA - NO MODIFICAR';
    echo '</div>';
    
    echo '</body>';
    echo '</html>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios Realizados | VialServi</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #2c3e50;
            --text-color: #333;
            --light-text: #ffffff;
            --card-bg: #fff;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/Administrador.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            padding-top: 20px;
            padding-bottom: 40px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .dashboard-header {
            margin-bottom: 25px;
            position: relative;
        }
        
        .dashboard-title {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
            margin-bottom: 5px;
        }
        
        .dashboard-subtitle {
            font-size: 16px;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }
        
        .dashboard-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-bottom: 25px;
            border-top: 4px solid var(--primary-color);
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: var(--secondary-color);
        }
        
        .dashboard-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .dashboard-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            color: white;
        }
        
        .dashboard-btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        .dashboard-btn-secondary {
            background-color: #6c757d;
        }
        
        .dashboard-btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .filter-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .filter-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--secondary-color);
        }
        
        .form-group label {
            font-weight: 600;
            font-size: 14px;
            color: #495057;
        }
        
        .table-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid #dee2e6;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e9ecef;
        }
        
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
        
        .page-link {
            color: var(--primary-color);
            border-radius: 4px;
            margin: 0 3px;
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .actions-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .actions-bar .btn-group {
            margin-bottom: 10px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .actions-bar {
                flex-direction: column;
            }
            
            .dashboard-btn {
                margin-bottom: 10px;
                width: 100%;
                justify-content: center;
            }
            
            .filter-container .row {
                margin-right: -5px;
                margin-left: -5px;
            }
            
            .filter-container [class*="col-"] {
                padding-right: 5px;
                padding-left: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Nuevo contenedor para el título principal -->
        <div class="dashboard-card mb-4">
            <div class="dashboard-header text-center">
                <h1 class="dashboard-title" style="color: var(--secondary-color); text-shadow: none;">
                    <i class="fas fa-user-cog mr-2"></i>Panel de Técnico
                </h1>
                <p class="dashboard-subtitle" style="color: var(--secondary-color); text-shadow: none;">
                    Gestión de servicios realizados
                </p>
            </div>
        </div>
        
        <!-- El resto del contenido permanece igual -->
        <div class="dashboard-card">
            <h5 class="card-title">
                <i class="fas fa-tools mr-2"></i>Mis Servicios Realizados
            </h5>
            
            <!-- Formulario de filtros -->
            <div class="filter-container">
                <h6 class="filter-title"><i class="fas fa-filter mr-2"></i>Filtros de búsqueda</h6>
                <form class="form" action="" method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="fecha"><i class="far fa-calendar-alt mr-1"></i>Mes:</label>
                                <input type="month" id="fecha" name="fecha" value="<?= htmlspecialchars($filtro_fecha); ?>" class="form-control auto-submit">
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="placa"><i class="fas fa-car mr-1"></i>Placa:</label>
                                <input type="text" id="placa" name="placa" value="<?= htmlspecialchars($filtro_placa); ?>" placeholder="Ej. ABC123" class="form-control auto-submit">
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="servicio"><i class="fas fa-wrench mr-1"></i>Servicio:</label>
                                <select name="servicio" id="servicio" class="form-control auto-submit">
                                    <option value="">Todos los servicios</option>
                                    <?php while ($s = $result_servicios->fetch_assoc()): ?>
                                        <option value="<?= $s['Servicio_id']; ?>" <?= ($s['Servicio_id'] == $filtro_servicio ? 'selected' : '') ?>>
                                            <?= htmlspecialchars($s['Nombre_Servicio']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="municipio"><i class="fas fa-map-marker-alt mr-1"></i>Municipio:</label>
                                <select name="municipio" id="municipio" class="form-control auto-submit">
                                    <option value="">Todos los municipios</option>
                                    <?php while ($m = $result_municipios->fetch_assoc()): ?>
                                        <option value="<?= $m['id']; ?>" <?= ($m['id'] == $filtro_municipio ? 'selected' : '') ?>>
                                            <?= htmlspecialchars($m['nombre']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Barra de acciones -->
            <div class="actions-bar">
                <div>
                    <?php if ($total_registros > 0): ?>
                    <span class="text-muted">
                        <i class="fas fa-clipboard-list mr-1"></i> Mostrando <?= min($registros_por_pagina, $result->num_rows) ?> de <?= $total_registros ?> registros
                    </span>
                    <?php endif; ?>
                </div>
                
                <div class="btn-group">
                    <a href="?export=excel<?= (!empty($filtro_fecha) ? '&fecha='.$filtro_fecha : '') ?><?= (!empty($filtro_placa) ? '&placa='.$filtro_placa : '') ?><?= (!empty($filtro_servicio) ? '&servicio='.$filtro_servicio : '') ?><?= (!empty($filtro_municipio) ? '&municipio='.$filtro_municipio : '') ?>" class="dashboard-btn">
                        <i class="fas fa-file-excel"></i> Descargar en Excel
                    </a>
                    <a href="dashboard.php" class="dashboard-btn dashboard-btn-secondary">
                        <i class="fas fa-tachometer-alt"></i> Volver al Dashboard
                    </a>
                    <a href="consulta_general_tecnico.php" class="dashboard-btn dashboard-btn-secondary">
                        <i class="fas fa-sync-alt"></i> Limpiar Filtros
                    </a>
                </div>
            </div>
            
            <!-- Tabla de datos -->
            <div class="table-container table-responsive">
                <?php
                if ($result->num_rows > 0) {
                    echo "<table class='table table-hover'>";
                    echo "<thead><tr>
                            <th>ID</th>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Placa</th>
                            <th>Ubicación</th>
                            <th>Municipio</th>
                            <th>Novedades</th>
                            </tr></thead><tbody>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['Servicio_Realizado_id']) . "</td>
                                <td>" . htmlspecialchars($row['Nombre_Servicio']) . "</td>
                                <td>" . htmlspecialchars($row['Fecha']) . "</td>
                                <td>" . htmlspecialchars($row['Placa']) . "</td>
                                <td>" . htmlspecialchars($row['Ubicación']) . "</td>
                                <td>" . htmlspecialchars($row['Municipio']) . "</td>
                                <td>" . htmlspecialchars($row['Novedades']) . "</td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<div class='no-results'>
                            <i class='fas fa-search fa-3x mb-3'></i>
                            <p>No se encontraron servicios realizados con los filtros aplicados.</p>
                            <a href='consulta_general_tecnico.php' class='btn btn-outline-secondary mt-3'><i class='fas fa-sync-alt mr-2'></i>Limpiar filtros</a>
                          </div>";
                }
                ?>
            </div>
            
            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
            <nav aria-label="Navegación de páginas">
                <ul class="pagination">
                    <!-- Botón Anterior -->
                    <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= construirUrlPaginacion($pagina_actual - 1) ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php
                    $rango = 2;
                    $inicio_rango = max(1, $pagina_actual - $rango);
                    $fin_rango = min($total_paginas, $pagina_actual + $rango);
                    
                    if ($inicio_rango > 1) {
                        echo '<li class="page-item"><a class="page-link" href="' . construirUrlPaginacion(1) . '">1</a></li>';
                        if ($inicio_rango > 2) {
                            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                        }
                    }
                    
                    for ($i = $inicio_rango; $i <= $fin_rango; $i++) {
                        echo '<li class="page-item ' . ($i == $pagina_actual ? 'active' : '') . '">
                                <a class="page-link" href="' . construirUrlPaginacion($i) . '">' . $i . '</a>
                              </li>';
                    }
                    
                    if ($fin_rango < $total_paginas) {
                        if ($fin_rango < $total_paginas - 1) {
                            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="' . construirUrlPaginacion($total_paginas) . '">' . $total_paginas . '</a></li>';
                    }
                    ?>
                    
                    <!-- Botón Siguiente -->
                    <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= construirUrlPaginacion($pagina_actual + 1) ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript para el envío automático de filtros -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los elementos con la clase auto-submit
            const autoSubmitElements = document.querySelectorAll('.auto-submit');
            
            // Agregar listener de eventos para cada elemento
            autoSubmitElements.forEach(element => {
                element.addEventListener('change', function() {
                    // Pequeño retraso para mejor experiencia de usuario
                    setTimeout(() => {
                        document.getElementById('filterForm').submit();
                    }, 300);
                });
            });
            
            // Para el campo de texto de placa, aplicar un retraso después de que el usuario deje de escribir
            const placaInput = document.getElementById('placa');
            let typingTimer;
            
            placaInput.addEventListener('input', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 1000); // Esperar 1 segundo después de que el usuario deje de escribir
            });
        });
    </script>
</body>
</html>
<?php