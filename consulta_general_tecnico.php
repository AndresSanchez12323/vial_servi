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

$sql = "SELECT 
            sr.Servicio_Realizado_id,
            s.Nombre_Servicio,
            s.Descripción,
            sr.Fecha,
            sr.Ubicación,
            m.nombre AS Municipio,
            sr.Novedades,
            sr.Detalle_Servicio,
            sr.Custodia_Servicio
        FROM servicios_realizados sr
        LEFT JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
        LEFT JOIN municipios m ON sr.municipio = m.id
        WHERE sr.Cedula_Empleado_id_Servicios_Realizados = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cedula_tecnico);
$stmt->execute();
$result = $stmt->get_result();

// Si se solicita exportar a Excel
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    $autoloadFile = 'vendor/autoload.php';
    
    if (!file_exists($autoloadFile)) {
        // Exportar como HTML para Excel sin protección real
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header("Content-Disposition: attachment; filename=servicios_tecnico_{$cedula_tecnico}.xls");
        header('Cache-Control: max-age=0');
        
        echo '<!DOCTYPE html>';
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        echo '<!--[if gte mso 9]>';
        echo '<xml>';
        echo '<x:ExcelWorkbook>';
        echo '<x:ExcelWorksheets>';
        echo '<x:ExcelWorksheet>';
        echo '<x:Name>Servicios</x:Name>';
        echo '<x:WorksheetOptions>';
        echo '<x:DisplayGridlines/>';
        echo '<x:ProtectContents>True</x:ProtectContents>';
        echo '</x:WorksheetOptions>';
        echo '</x:ExcelWorksheet>';
        echo '</x:ExcelWorksheets>';
        echo '</x:ExcelWorkbook>';
        echo '<x:ExcelName>';
        echo '<x:Name>Print_Titles</x:Name>';
        echo '<x:SheetIndex>1</x:SheetIndex>';
        echo '</x:ExcelName>';
        echo '</xml>';
        echo '<![endif]-->';
        echo '<style>';
        echo 'table { border-collapse: collapse; }';
        echo 'table, th, td { border: 1px solid black; }';
        echo 'th { background-color: #e8f0fe; font-weight: bold; text-align: center; }';
        echo 'td, th { mso-style-locked: 1; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        echo '<table>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Nombre del Servicio</th>';
        echo '<th>Descripción</th>';
        echo '<th>Fecha</th>';
        echo '<th>Ubicación</th>';
        echo '<th>Municipio</th>';
        echo '<th>Novedades</th>';
        echo '<th>Detalle</th>';
        echo '<th>Custodia</th>';
        echo '</tr>';
        
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['Servicio_Realizado_id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Nombre_Servicio']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Descripción']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Fecha']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Ubicación']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Municipio']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Novedades']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Detalle_Servicio']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Custodia_Servicio']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        echo '<div style="margin-top: 20px; color: #cc0000; font-weight: bold; text-align: center;">';
        echo 'DOCUMENTO DE SOLO LECTURA - NO MODIFICAR';
        echo '</div>';
        
        echo '</body>';
        echo '</html>';
        exit;
    } else {
        // Usar PhpSpreadsheet con protección y contraseña
        require $autoloadFile;
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Protección con contraseña
            $protection = $sheet->getProtection();
            $protection->setPassword('1234'); // Cambia '1234' por la contraseña que quieras
            $protection->setSheet(true);
            $protection->setSort(true);
            $protection->setInsertRows(true);
            $protection->setFormatCells(true);

            // Encabezados
            $headers = ["ID", "Nombre del Servicio", "Descripción", "Fecha", "Ubicación", "Municipio", "Novedades", "Detalle", "Custodia"];
            $sheet->fromArray($headers, NULL, 'A1');
            
            // Estilo para encabezados
            $styleArray = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F0FE'],
                ],
            ];
            $lastColumn = chr(65 + count($headers) - 1);
            $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($styleArray);

            // Datos
            $rowNumber = 2;
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                $sheet->fromArray(array_values($row), NULL, "A$rowNumber");
                $rowNumber++;
            }

            // Auto tamaño columnas
            foreach(range('A', $lastColumn) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Nota de solo lectura
            $lastRow = $rowNumber + 1;
            $sheet->setCellValue('A' . $lastRow, 'DOCUMENTO DE SOLO LECTURA - NO MODIFICAR');
            $sheet->mergeCells('A' . $lastRow . ':' . $lastColumn . $lastRow);
            $sheet->getStyle('A' . $lastRow)->getFont()->setBold(true)->getColor()->setRGB('CC0000');
            $sheet->getStyle('A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Descargar archivo
            $filename = "servicios_tecnico_{$cedula_tecnico}.xlsx";
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            echo "<script>
                alert('Error al generar el archivo Excel: " . addslashes($e->getMessage()) . "');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios Realizados</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --text-color: #333;
            --light-text: #ffffff;
            --card-bg: rgba(255, 255, 255, 0.95);
            --table-header-bg: #f8f9fa;
            --table-row-even: #f1f3f5;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        h2 {
            font-weight: 600;
            font-size: 28px;
            color: var(--primary-color);
            margin: 0;
        }
        
        .buttons-container {
            display: flex;
            gap: 12px;
            margin: 20px 0;
        }
        
        .button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background-color: var(--accent-color);
            color: var(--light-text);
            font-weight: 600;
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        
        .button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .button-secondary {
            background-color: #7f8c8d;
        }
        
        .button-secondary:hover {
            background-color: #6c7a7d;
        }
        
        .table-responsive {
            overflow-x: auto;
            margin-top: 10px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: white;
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        th {
            background-color: var(--table-header-bg);
            color: var(--primary-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        
        tr:nth-child(even) {
            background-color: var(--table-row-even);
        }
        
        tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #7f8c8d;
        }
        
        .icon {
            font-size: 18px;
        }
        
        @media screen and (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .buttons-container {
                flex-direction: column;
                width: 100%;
            }
            
            .button {
                width: 100%;
                justify-content: center;
            }
            
            th, td {
                padding: 10px 12px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="page-header">
                <h2>Servicios realizados por usted</h2>
            </div>
            
            <div class="buttons-container">
                <a href="?export=excel" class="button">
                    <span class="icon">📥</span> Descargar en Excel
                </a>
                <a href="dashboard.php" class="button button-secondary">
                    <span class="icon">⬅️</span> Volver al Dashboard
                </a>
            </div>
            
            <div class="table-responsive">
                <?php
                if ($result->num_rows > 0) {
                    echo "<table>";
                    echo "<thead><tr>
                            <th>ID</th>
                            <th>Nombre del Servicio</th>
                            <th>Descripción</th>
                            <th>Fecha</th>
                            <th>Ubicación</th>
                            <th>Municipio</th>
                            <th>Novedades</th>
                            <th>Detalle</th>
                            <th>Custodia</th>
                          </tr></thead><tbody>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['Servicio_Realizado_id']) . "</td>
                                <td>" . htmlspecialchars($row['Nombre_Servicio']) . "</td>
                                <td>" . htmlspecialchars($row['Descripción']) . "</td>
                                <td>" . htmlspecialchars($row['Fecha']) . "</td>
                                <td>" . htmlspecialchars($row['Ubicación']) . "</td>
                                <td>" . htmlspecialchars($row['Municipio']) . "</td>
                                <td>" . htmlspecialchars($row['Novedades']) . "</td>
                                <td>" . htmlspecialchars($row['Detalle_Servicio']) . "</td>
                                <td>" . htmlspecialchars($row['Custodia_Servicio']) . "</td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<div class='no-results'>No se encontraron servicios realizados.</div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>