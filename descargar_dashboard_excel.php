<?php
// Configuración inicial
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';

// Verificar sesión
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

include('validPermissions.php');
$usuarioId = $_SESSION['cedula'];

// Verificar permisos
$verReportesAdministrador = usuarioTienePermiso($usuarioId, 'ver_reporte_administrador', $conn);
$verReportesTecnico = usuarioTienePermiso($usuarioId, 'ver_reporte_tecnico', $conn);

if (!$verReportesAdministrador && !$verReportesTecnico) {
    die("❌ Acceso denegado: No tienes permiso para descargar este reporte.");
}

// Obtener el mes seleccionado
$mesFiltro = isset($_POST['mes']) ? $_POST['mes'] : (isset($_GET['mes']) ? $_GET['mes'] : date('Y-m'));

if (!preg_match('/^\d{4}-\d{2}$/', $mesFiltro)) {
    die('El parámetro "mes" debe tener formato YYYY-MM');
}

// Convertir el mes a nombre para el título
$arr_fecha = explode('-', $mesFiltro);
$timestamp = mktime(0, 0, 0, $arr_fecha[1], 1, $arr_fecha[0]);
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'es');
$nombreMes = strftime('%B %Y', $timestamp);
$nombreMes = ucfirst($nombreMes);

// Nombre del archivo para descargar
$nombreArchivo = "Reporte_Dashboard_" . $mesFiltro . ".xls";

// Configurar cabeceras
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

// Preparar los datos para las consultas
$mesFiltroSQL = "'" . mysqli_real_escape_string($conn, $mesFiltro) . "'";
$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : (isset($_SESSION['username']) ? $_SESSION['username'] : $_SESSION['cedula']);

// Iniciar salida HTML Excel
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Dashboard VialServi</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
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
    </style>
</head>
<body>
    <!-- ENCABEZADO DEL DOCUMENTO -->
    <table>
        <tr>
            <td colspan="6" class="section-title">VIALSERVI - REPORTE DE DASHBOARD</td>
        </tr>
        <tr>
            <td colspan="6" class="sub-title">INFORMACIÓN GENERAL</td>
        </tr>
        <tr class="info-row">
            <td>Mes de reporte:</td>
            <td colspan="5"><?php echo $nombreMes; ?></td>
        </tr>
        <tr class="info-row">
            <td>Fecha de generación:</td>
            <td colspan="5"><?php echo date('d/m/Y H:i:s'); ?></td>
        </tr>
        <tr class="info-row">
            <td>Usuario:</td>
            <td colspan="5"><?php echo $nombreUsuario; ?></td>
        </tr>
    </table>

<?php
// SECCIÓN DE ADMINISTRADOR
if ($verReportesAdministrador) {
    // 1. SERVICIOS POR TIPO
    echo '<table>
            <tr>
                <td colspan="3" class="sub-title">ESTADÍSTICAS DE SERVICIOS REALIZADOS</td>
            </tr>
            <tr>
                <th>Tipo de Servicio</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>';
    
    // Consulta para servicios
    $query_servicios = "
        SELECT 
            s.Nombre_Servicio, 
            COUNT(sr.Servicio_id_Servicios_Realizados) AS cantidad
        FROM servicios_realizados sr
        JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
        WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
        GROUP BY s.Nombre_Servicio
        ORDER BY cantidad DESC
    ";
    
    $result_servicios = mysqli_query($conn, $query_servicios);
    
    // Calcular el total para porcentajes
    $total_servicios = 0;
    $datos_servicios = [];
    
    while ($row = mysqli_fetch_assoc($result_servicios)) {
        $total_servicios += $row['cantidad'];
        $datos_servicios[] = $row;
    }
    
    // Escribir datos con porcentajes
    foreach ($datos_servicios as $servicio) {
        $porcentaje = ($total_servicios > 0) ? round(($servicio['cantidad'] / $total_servicios) * 100, 1) . '%' : '0%';
        echo '<tr>
                <td>' . htmlspecialchars($servicio['Nombre_Servicio']) . '</td>
                <td class="cell-right">' . $servicio['cantidad'] . '</td>
                <td class="cell-right">' . $porcentaje . '</td>
              </tr>';
    }
    
    // Agregar fila total
    echo '<tr class="total-row">
            <td>TOTAL</td>
            <td>' . $total_servicios . '</td>
            <td>100%</td>
          </tr>';
    
    echo '</table>';

    // 2. SERVICIOS POR MUNICIPIO
    echo '<table>
            <tr>
                <td colspan="3" class="sub-title">SERVICIOS POR UBICACIÓN</td>
            </tr>
            <tr>
                <th>Municipio</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>';
    
    // Consulta para municipios
    $query_municipios = "
        SELECT 
            m.nombre, 
            COUNT(sr.Servicio_Realizado_id) AS cantidad
        FROM servicios_realizados sr
        JOIN municipios m ON sr.municipio = m.id
        WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
        GROUP BY m.nombre
        ORDER BY cantidad DESC
    ";
    
    $result_municipios = mysqli_query($conn, $query_municipios);
    
    // Calcular el total para porcentajes
    $total_municipios = 0;
    $datos_municipios = [];
    
    while ($row = mysqli_fetch_assoc($result_municipios)) {
        $total_municipios += $row['cantidad'];
        $datos_municipios[] = $row;
    }
    
    // Escribir datos con porcentajes
    foreach ($datos_municipios as $municipio) {
        $porcentaje = ($total_municipios > 0) ? round(($municipio['cantidad'] / $total_municipios) * 100, 1) . '%' : '0%';
        echo '<tr>
                <td>' . htmlspecialchars($municipio['nombre']) . '</td>
                <td class="cell-right">' . $municipio['cantidad'] . '</td>
                <td class="cell-right">' . $porcentaje . '</td>
              </tr>';
    }
    
    // Agregar fila total
    echo '<tr class="total-row">
            <td>TOTAL</td>
            <td>' . $total_municipios . '</td>
            <td>100%</td>
          </tr>';
    
    echo '</table>';

    // 3. SERVICIOS POR EMPLEADO
    echo '<table>
            <tr>
                <td colspan="3" class="sub-title">SERVICIOS REALIZADOS POR EMPLEADO</td>
            </tr>
            <tr>
                <th>Empleado</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>';
    
    // Consulta para empleados
    $query_empleados = "
        SELECT 
            e.Nombre, 
            COUNT(sr.Servicio_Realizado_id) AS cantidad
        FROM servicios_realizados sr
        JOIN empleados e ON sr.Cedula_Empleado_id_Servicios_Realizados = e.Cedula_Empleado_id
        WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
        GROUP BY e.Cedula_Empleado_id, e.Nombre
        ORDER BY cantidad DESC
    ";
    
    $result_empleados = mysqli_query($conn, $query_empleados);
    
    // Calcular el total para porcentajes
    $total_empleados = 0;
    $datos_empleados = [];
    
    while ($row = mysqli_fetch_assoc($result_empleados)) {
        $total_empleados += $row['cantidad'];
        $datos_empleados[] = $row;
    }
    
    // Escribir datos con porcentajes
    foreach ($datos_empleados as $empleado) {
        $porcentaje = ($total_empleados > 0) ? round(($empleado['cantidad'] / $total_empleados) * 100, 1) . '%' : '0%';
        echo '<tr>
                <td>' . htmlspecialchars($empleado['Nombre']) . '</td>
                <td class="cell-right">' . $empleado['cantidad'] . '</td>
                <td class="cell-right">' . $porcentaje . '</td>
              </tr>';
    }
    
    // Agregar fila total
    echo '<tr class="total-row">
            <td>TOTAL</td>
            <td>' . $total_empleados . '</td>
            <td>100%</td>
          </tr>';
    
    echo '</table>';
}

// SECCIÓN DE TÉCNICO
if ($verReportesTecnico) {
    // 4. SERVICIOS POR DÍA
    echo '<table>
            <tr>
                <td colspan="2" class="sub-title">SERVICIOS REALIZADOS POR DÍA</td>
            </tr>
            <tr>
                <th>Fecha</th>
                <th>Cantidad</th>
            </tr>';
    
    // Consulta para servicios por día
    $query_servicios_tecnico = "
        SELECT 
            DATE(Fecha) AS fecha,
            COUNT(*) AS cantidad
        FROM servicios_realizados
        WHERE DATE_FORMAT(Fecha, '%Y-%m') = $mesFiltroSQL
          AND Cedula_Empleado_id_Servicios_Realizados = $usuarioId
        GROUP BY fecha
        ORDER BY fecha ASC
    ";
    
    $result_servicios_tecnico = mysqli_query($conn, $query_servicios_tecnico);
    $total_servicios_tecnico = 0;
    
    // Escribir datos
    if ($result_servicios_tecnico) {
        while ($row = mysqli_fetch_assoc($result_servicios_tecnico)) {
            echo '<tr>
                    <td>' . $row['fecha'] . '</td>
                    <td class="cell-right">' . $row['cantidad'] . '</td>
                  </tr>';
            $total_servicios_tecnico += $row['cantidad'];
        }
    }
    
    // Agregar fila total
    echo '<tr class="total-row">
            <td>TOTAL</td>
            <td>' . $total_servicios_tecnico . '</td>
          </tr>';
    
    echo '</table>';
    
    // 5. DETALLE DE SERVICIOS REALIZADOS
    echo '<table>
            <tr>
                <td colspan="7" class="sub-title">DETALLE DE SERVICIOS REALIZADOS</td>
            </tr>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Servicio</th>
                <th>Vehículo</th>
                <th>Cliente</th>
                <th>Municipio</th>
                <th>Detalle</th>
            </tr>';
    
    // Consulta para detalles
    $query_detalle = "
        SELECT 
            sr.Fecha,
            s.Nombre_Servicio,
            sr.Vehiculo_id_Servicios_Realizados,
            c.Nombre AS cliente,
            m.nombre AS municipio,
            sr.Detalle_Servicio
        FROM servicios_realizados sr
        JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
        JOIN municipios m ON sr.municipio = m.id
        LEFT JOIN clientes c ON sr.Cliente_id_Servicios_Realizados = c.Cliente_id
        WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
          AND sr.Cedula_Empleado_id_Servicios_Realizados = $usuarioId
        ORDER BY sr.Fecha ASC
    ";
    
    $result_detalle = mysqli_query($conn, $query_detalle);
    
    // Escribir datos
    if ($result_detalle) {
        while ($row = mysqli_fetch_assoc($result_detalle)) {
            $fecha = date('Y-m-d', strtotime($row['Fecha']));
            $hora = date('H:i:s', strtotime($row['Fecha']));
            $cliente = isset($row['cliente']) ? htmlspecialchars($row['cliente']) : '-';
            
            echo '<tr>
                    <td>' . $fecha . '</td>
                    <td>' . $hora . '</td>
                    <td>' . htmlspecialchars($row['Nombre_Servicio']) . '</td>
                    <td>' . htmlspecialchars($row['Vehiculo_id_Servicios_Realizados']) . '</td>
                    <td>' . $cliente . '</td>
                    <td>' . htmlspecialchars($row['municipio']) . '</td>
                    <td>' . htmlspecialchars($row['Detalle_Servicio']) . '</td>
                  </tr>';
        }
    }
    
    echo '</table>';
}

// RESUMEN POR MES
echo '<table>';
echo '<tr><td colspan="3" class="sub-title">RESUMEN POR MES</td></tr>';
echo '<tr>
        <th style="mso-number-format:\@;">Mes</th>
        <th style="mso-number-format:\@;">Cantidad de Servicios</th>
        <th style="mso-number-format:\@;">Porcentaje</th>
      </tr>';

$sql_meses = "
    SELECT 
        DATE_FORMAT(sr.Fecha, '%Y-%m') as mes,
        DATE_FORMAT(sr.Fecha, '%M %Y') as mes_nombre,
        COUNT(*) as cantidad
    FROM servicios_realizados sr
    WHERE sr.Cedula_Empleado_id_Servicios_Realizados = ?
";
$params_meses = [$cedula_tecnico];
$param_types_meses = "i";

if (!empty($filtro_placa)) {
    $sql_meses .= " AND sr.Vehiculo_id_Servicios_Realizados LIKE ?";
    $params_meses[] = "%$filtro_placa%";
    $param_types_meses .= "s";
}
if (!empty($filtro_servicio)) {
    $sql_meses .= " AND s.Servicio_id = ?";  // Asegúrate de que la unión con "servicios" esté definida si se usa este filtro
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

$datos_meses = [];
$total_por_meses = 0;
while ($row = $result_meses->fetch_assoc()) {
    $datos_meses[] = $row;
    $total_por_meses += $row['cantidad'];
}
foreach ($datos_meses as $mes_data) {
    $porcentaje = ($total_por_meses > 0) ? round(($mes_data['cantidad'] / $total_por_meses) * 100, 1) . '%' : '0%';
    echo '<tr>';
    // Aquí convertimos la fecha (mes_nombre) a string forzado por mso-number-format:\@;
    echo '<td style="mso-number-format:\@;">' . htmlspecialchars($mes_data['mes_nombre']) . '</td>';
    echo '<td class="cell-right" style="mso-number-format:\@;">' . htmlspecialchars($mes_data['cantidad']) . '</td>';
    echo '<td class="cell-right" style="mso-number-format:\@;">' . htmlspecialchars($porcentaje) . '</td>';
    echo '</tr>';
}
echo '<tr class="total-row"><td style="mso-number-format:\@;">TOTAL</td><td class="cell-right" style="mso-number-format:\@;">' . $total_por_meses . '</td><td class="cell-right" style="mso-number-format:\@;">100%</td></tr>';
echo '</table>';
?>
</body>
</html>
<?php
// Cerrar la conexión
mysqli_close($conn);
exit;
?>