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

    // COMPARATIVO MES ACTUAL VS MES ANTERIOR (ADMIN)
    $mesAnteriorAdmin    = date('Y-m', strtotime("$mesFiltro -1 month"));
    $mesAnteriorAdminSQL = "'" . mysqli_real_escape_string($conn, $mesAnteriorAdmin) . "'";
    $query_comp_admin    = "
        SELECT
           SUM(CASE WHEN DATE_FORMAT(Fecha, '%Y-%m') = $mesFiltroSQL THEN 1 ELSE 0 END)   AS actual,
           SUM(CASE WHEN DATE_FORMAT(Fecha, '%Y-%m') = $mesAnteriorAdminSQL THEN 1 ELSE 0 END) AS anterior
        FROM servicios_realizados
        WHERE Cedula_Empleado_id_Servicios_Realizados IS NOT NULL
    ";
    $res_comp_admin      = mysqli_query($conn, $query_comp_admin);
    $rc_admin            = $res_comp_admin
                             ? mysqli_fetch_assoc($res_comp_admin)
                             : ['actual'=>0,'anterior'=>0];
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES');
    $nombreMesAntAdmin   = ucfirst(strftime('%B %Y', strtotime($mesAnteriorAdmin.'-01')));
    $variacionAdmin      = $rc_admin['anterior'] > 0
                             ? round(($rc_admin['actual'] - $rc_admin['anterior']) / $rc_admin['anterior'] * 100, 1) . '%'
                             : 'N/A';

    echo '<table>';
    echo '<tr><td colspan="3" class="sub-title">COMPARATIVO MES ACTUAL VS MES ANTERIOR</td></tr>';
    echo '<tr>
            <th style="mso-number-format:\@;">Mes</th>
            <th>Cantidad</th>
            <th>% Variación</th>
          </tr>';
    // Fila para mes anterior
    echo '<tr>
            <td style="mso-number-format:\@;">'. htmlspecialchars($nombreMesAntAdmin) .'</td>
            <td class="cell-right">'. intval($rc_admin['anterior']) .'</td>
            <td class="cell-right">-</td>
          </tr>';
    // Fila para mes actual
    echo '<tr>
            <td style="mso-number-format:\@;">'. htmlspecialchars($nombreMes) .'</td>
            <td class="cell-right">'. intval($rc_admin['actual']) .'</td>
            <td class="cell-right">'. $variacionAdmin .'</td>
          </tr>';
    echo '</table>';

    // 4. TENDENCIA SEMESTRAL
    $primerMesInicio = date('Y-m-d', strtotime("$mesFiltro-01 -5 months"));
    $primerMesSQL    = "'" . mysqli_real_escape_string($conn, $primerMesInicio) . "'";
    $query_trend     = "
        SELECT DATE_FORMAT(Fecha, '%Y-%m') AS mes, COUNT(*) AS cantidad
        FROM servicios_realizados
        WHERE Fecha >= $primerMesSQL
          AND DATE_FORMAT(Fecha, '%Y-%m') <= $mesFiltroSQL
          AND Cedula_Empleado_id_Servicios_Realizados IS NOT NULL
        GROUP BY mes
        ORDER BY mes ASC
    ";
    $res_trend = mysqli_query($conn, $query_trend);
    
    echo '<table>';
    echo '<tr><td colspan="2" class="sub-title">TENDENCIA SEMESTRAL (Últimos 6 meses)</td></tr>';
    echo '<tr>
            <th>Mes</th>
            <th>Cantidad</th>
          </tr>';
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES');
    if ($res_trend) {
        while ($r = mysqli_fetch_assoc($res_trend)) {
            $ts    = strtotime($r['mes'].'-01');
            $label = ucfirst(strftime('%B %Y', $ts));
            echo '<tr>
                    <td style="mso-number-format:\@;">'. htmlspecialchars($label) .'</td>
                    <td class="cell-right">'. $r['cantidad'] .'</td>
                  </tr>';
        }
    }
    echo '</table>';

    // 6. GRÁFICO DE EVOLUCIÓN MENSUAL (últimos 6 meses)
    echo '<!--[if gte mso 9]><xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Evolución</x:Name>
    <x:WorksheetOptions>
     <x:Print>
      <x:ValidPrinterInfo/>
     </x:Print>
     <x:Charts>
      <x:Chart>
       <x:ID>1</x:ID>
       <x:ChartType>ColumnClustered</x:ChartType>
       <x:SeriesCollection>
        <x:Series>
         <x:Name><![CDATA[Servicios]]></x:Name>
         <!-- Ajusta Sheet1!R7C2:R12C2 y Sheet1!R7C1:R12C1 al rango donde estén tus datos -->
         <x:Values>Sheet1!R7C2:R12C2</x:Values>
         <x:CategoryLabels>Sheet1!R7C1:R12C1</x:CategoryLabels>
        </x:Series>
       </x:SeriesCollection>
      </x:Chart>
     </x:Charts>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml><![endif]-->';

    /*
    // 5. ESTADO DE SERVICIOS: Pendientes vs Completados
    $query_status = "
        SELECT
            SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END)   AS pendientes,
            SUM(CASE WHEN estado = 'Completado' THEN 1 ELSE 0 END)  AS completados
        FROM servicios_realizados
        WHERE DATE_FORMAT(Fecha, '%Y-%m') = $mesFiltroSQL
          AND Cedula_Empleado_id_Servicios_Realizados IS NOT NULL
    ";
    $res_status = mysqli_query($conn, $query_status);
    $st        = $res_status ? mysqli_fetch_assoc($res_status) : ['pendientes'=>0,'completados'=>0];
    
    echo '<table>';
    echo '<tr><td colspan="2" class="sub-title">ESTADO DE SERVICIOS</td></tr>';
    echo '<tr>
             <th>Estado</th>
             <th>Cantidad</th>
           </tr>';
    echo '<tr>
            <td>Pendientes</td>
            <td class="cell-right">'. intval($st['pendientes']) .'</td>
          </tr>';
    echo '<tr>
            <td>Completados</td>
            <td class="cell-right">'. intval($st['completados']) .'</td>
          </tr>';
    echo '</table>';
    */
 }  // fin if administrador

// SECCIÓN DE TÉCNICO
/*
if ($verReportesTecnico) {
    // Sección del técnico vacía - tablas eliminadas
    echo '<table>
            <tr>
                <td colspan="2" class="sub-title">SECCIÓN DE TÉCNICO</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; padding: 20px;">
                    Las tablas específicas del técnico han sido removidas del reporte.
                </td>
            </tr>
          </table>';
}
*/
?>
</body>
</html>
<?php
// Cerrar la conexión
mysqli_close($conn);
exit;
?>