<?php
session_start();
require_once 'config.php';

// Verificar sesión de cliente
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login_cliente.php");
    exit;
}

// Obtener información del cliente
$cliente_id = $_SESSION['cliente_id'];
$sql_cliente = "SELECT * FROM clientes WHERE Cedula_Id = ?";
$stmt_cliente = $conn->prepare($sql_cliente);
$stmt_cliente->bind_param("i", $cliente_id);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();
$cliente = $result_cliente->fetch_assoc();
$stmt_cliente->close();

// Recuperar filtros
$filtro_placa = isset($_GET['placa']) ? $_GET['placa'] : '';
$filtro_servicio = isset($_GET['servicio']) ? $_GET['servicio'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_mes = isset($_GET['mes']) ? $_GET['mes'] : '';

// Obtener vehículos del cliente
$sql_vehiculos = "
    SELECT
      v.Placa,
      v.Marca,
      mo.nombre        AS Modelo,
      co.nombre_color  AS Color,
      COALESCE(v.Objetos_Valiosos,'') AS Objetos_Valiosos
    FROM vehiculos v
    LEFT JOIN modelos mo ON v.modelo_id = mo.id
    LEFT JOIN colores co ON v.color_id   = co.color_id
    WHERE v.Clientes_Vehiculos = ?
";
$stmt_vehiculos = $conn->prepare($sql_vehiculos);
if (!$stmt_vehiculos) {
    die("Error al preparar consulta de vehículos: " . $conn->error);
}
$stmt_vehiculos->bind_param("i", $cliente_id);
$stmt_vehiculos->execute();
$vehiculos = $stmt_vehiculos->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_vehiculos->close();

// Obtener servicios realizados para los vehículos del cliente con filtros
$servicios = array();

if (!empty($vehiculos)) {
    $placas = array_column($vehiculos, 'Placa');
    $placeholders = implode(',', array_fill(0, count($placas), '?'));
    
    // Consulta para exportar todos los registros (sin paginación)
    $sql = "SELECT sr.*, s.Nombre_Servicio, s.Descripción, m.nombre AS Municipio
            FROM servicios_realizados sr
            JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
            LEFT JOIN municipios m ON sr.municipio = m.id
            WHERE sr.Vehiculo_id_Servicios_Realizados IN ($placeholders)";
    
    $params = $placas;
    $types = str_repeat('s', count($placas));
    
    // Aplicar filtro de placa
    if (!empty($filtro_placa)) {
        $sql .= " AND sr.Vehiculo_id_Servicios_Realizados = ?";
        $params[] = $filtro_placa;
        $types .= 's';
    }
    
    // Aplicar filtro de tipo de servicio
    if (!empty($filtro_servicio)) {
        $sql .= " AND s.Servicio_id = ?";
        $params[] = $filtro_servicio;
        $types .= 'i';
    }
    
    // Aplicar filtro de estado
    if ($filtro_estado == 'programado') {
        $sql .= " AND sr.Fecha >= CURDATE()";
    } elseif ($filtro_estado == 'completado') {
        $sql .= " AND sr.Fecha < CURDATE()";
    }
    
    // Aplicar filtro de mes
    if (!empty($filtro_mes)) {
        $mes_anio = explode('-', $filtro_mes);
        if (count($mes_anio) == 2) {
            $sql .= " AND MONTH(sr.Fecha) = ? AND YEAR(sr.Fecha) = ?";
            $params[] = $mes_anio[1]; // Mes
            $params[] = $mes_anio[0]; // Año
            $types .= 'ii';
        }
    }
    
    // Ordenar por fecha
    $sql .= " ORDER BY sr.Fecha DESC";
    
    // Ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $servicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Asegurar zona horaria correcta para Colombia
date_default_timezone_set('America/Bogota');

// Configurar cabeceras para Excel
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header("Content-Disposition: attachment; filename=reporte_servicios_cliente_{$cliente_id}.xls");
header('Cache-Control: max-age=0');

// Iniciar HTML para Excel
?>
<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Servicios Cliente</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                        <x:Print>
                            <x:ValidPrinterInfo/>
                        </x:Print>
                        <x:ProtectContents>True</x:ProtectContents>
                        <x:ProtectObjects>True</x:ProtectObjects>
                        <x:ProtectScenarios>True</x:ProtectScenarios>
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
        .futuro {
            background-color: #E2EFDA;
        }
        .pasado {
            background-color: #F2F2F2;
        }
    </style>
</head>
<body>
    <!-- ENCABEZADO DEL DOCUMENTO -->
    <table>
        <tr><td colspan="9" class="section-title">VIALSERVI - REPORTE DE SERVICIOS DEL CLIENTE</td></tr>
        <tr><td colspan="9" class="sub-title">INFORMACIÓN GENERAL</td></tr>
        
        <tr class="info-row">
            <td>Cliente:</td>
            <td colspan="8"><?php echo htmlspecialchars($cliente['Nombre']); ?></td>
        </tr>
        
        <tr class="info-row">
            <td>Identificación:</td>
            <td colspan="8"><?php echo htmlspecialchars($cliente['Cedula_Id']); ?></td>
        </tr>
        
        <tr class="info-row">
            <td>Fecha de generación:</td>
            <td colspan="8"><?php echo date('d/m/Y H:i:s'); ?></td>
        </tr>
        
        <tr class="info-row">
            <td>Filtros aplicados:</td>
            <td colspan="8">
                <?php 
                $filtros_texto = [];
                
                if (!empty($filtro_placa)) {
                    $filtros_texto[] = "Placa: " . $filtro_placa;
                }
                
                if (!empty($filtro_servicio)) {
                    // Obtener nombre del servicio
                    $stmt_s = $conn->prepare("SELECT Nombre_Servicio FROM servicios WHERE Servicio_id = ?");
                    $stmt_s->bind_param("i", $filtro_servicio);
                    $stmt_s->execute();
                    $resultado_s = $stmt_s->get_result();
                    if ($fila_s = $resultado_s->fetch_assoc()) {
                        $filtros_texto[] = "Servicio: " . $fila_s['Nombre_Servicio'];
                    }
                    $stmt_s->close();
                }
                
                if (!empty($filtro_estado)) {
                    $filtros_texto[] = "Estado: " . ($filtro_estado == 'programado' ? 'Programados' : 'Completados');
                }
                
                if (!empty($filtro_mes)) {
                    $filtros_texto[] = "Mes: " . $filtro_mes;
                }
                
                echo !empty($filtros_texto) ? implode(" | ", $filtros_texto) : "Sin filtros";
                ?>
            </td>
        </tr>
    </table>
    
    <!-- RESUMEN DE VEHÍCULOS -->
    <table>
        <tr><td colspan="5" class="sub-title">MIS VEHÍCULOS</td></tr>
        <tr>
            <th>Placa</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Color</th>
            <th>Objetos Valiosos</th>
        </tr>
        
        <?php foreach ($vehiculos as $vehiculo): ?>
        <tr>
            <td><?php echo htmlspecialchars($vehiculo['Placa']); ?></td>
            <td><?php echo htmlspecialchars($vehiculo['Marca']); ?></td>
            <td><?php echo htmlspecialchars($vehiculo['Modelo']); ?></td>
            <td><?php echo htmlspecialchars($vehiculo['Color']); ?></td>
            <td><?php echo htmlspecialchars($vehiculo['Objetos_Valiosos'] ?? 'Ninguno registrado'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <!-- TABLA DE SERVICIOS -->
    <table>
        <tr><td colspan="9" class="sub-title">HISTORIAL DE SERVICIOS</td></tr>
        <tr>
            <th>ID</th>
            <th>Servicio</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Placa</th>
            <th>Ubicación</th>
            <th>Municipio</th>
            <th>Novedades</th>
        </tr>
        
        <?php 
        $total_records = 0;
        $total_futuros = 0;
        $total_pasados = 0;
        
        foreach ($servicios as $servicio): 
            $hoy = strtotime(date('Y-m-d'));
            $fechaServicio = strtotime($servicio['Fecha']);
            $esFuturo = $fechaServicio >= $hoy;
            
            if ($esFuturo) {
                $total_futuros++;
                $clase = 'futuro';
                $estado = 'Programado';
            } else {
                $total_pasados++;
                $clase = 'pasado';
                $estado = 'Completado';
            }
            
            $total_records++;
        ?>
        <tr class="<?php echo $clase; ?>">
            <td><?php echo htmlspecialchars($servicio['Servicio_Realizado_id']); ?></td>
            <td><?php echo htmlspecialchars($servicio['Nombre_Servicio']); ?></td>
            <td><?php echo htmlspecialchars($servicio['Descripción']); ?></td>
            <td><?php echo htmlspecialchars($servicio['Fecha']); ?></td>
            <td><?php echo $estado; ?></td>
            <td><?php echo htmlspecialchars($servicio['Vehiculo_id_Servicios_Realizados']); ?></td>
            <td><?php echo htmlspecialchars($servicio['Ubicación']); ?></td>
            <td><?php echo htmlspecialchars($servicio['Municipio'] ?? 'No especificado'); ?></td>
            <td><?php echo htmlspecialchars($servicio['Novedades'] ?? 'Ninguna'); ?></td>
        </tr>
        <?php endforeach; ?>
        
        <tr class="total-row">
            <td colspan="8">Total de servicios:</td>
            <td><?php echo $total_records; ?></td>
        </tr>
    </table>
    
    <!-- RESUMEN ESTADÍSTICO -->
    <table>
        <tr><td colspan="3" class="sub-title">RESUMEN DE SERVICIOS</td></tr>
        <tr>
            <th>Estado</th>
            <th>Cantidad</th>
            <th>Porcentaje</th>
        </tr>
        
        <?php if ($total_records > 0): ?>
        <tr>
            <td>Programados</td>
            <td class="cell-right"><?php echo $total_futuros; ?></td>
            <td class="cell-right"><?php echo round(($total_futuros / $total_records) * 100, 1); ?>%</td>
        </tr>
        <tr>
            <td>Completados</td>
            <td class="cell-right"><?php echo $total_pasados; ?></td>
            <td class="cell-right"><?php echo round(($total_pasados / $total_records) * 100, 1); ?>%</td>
        </tr>
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="cell-right"><?php echo $total_records; ?></td>
            <td class="cell-right">100%</td>
        </tr>
        <?php else: ?>
        <tr>
            <td colspan="3" style="text-align:center;">No se encontraron servicios con los filtros seleccionados</td>
        </tr>
        <?php endif; ?>
    </table>
    
    <!-- PIE DEL DOCUMENTO -->
    <table>
        <tr><td colspan="9" class="section-title">DOCUMENTO GENERADO EL <?php echo date('d/m/Y H:i:s'); ?></td></tr>
    </table>
</body>
</html>
<?php
// Cerrar la conexión
$conn->close();
exit;
?>
