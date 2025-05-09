<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: login_cliente.php");
    exit();
}

// Obtener vehículos del cliente
$sql_vehiculos = "SELECT Placa FROM vehiculos WHERE Clientes_Vehiculos = ?";
$stmt_vehiculos = $conn->prepare($sql_vehiculos);
$stmt_vehiculos->bind_param("i", $_SESSION['cliente_id']);
$stmt_vehiculos->execute();
$result_vehiculos = $stmt_vehiculos->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_vehiculos->close();

$placas = array_column($result_vehiculos, 'Placa');

if (empty($placas)) {
    die('No hay vehículos registrados.');
}

$placeholders = implode(',', array_fill(0, count($placas), '?'));
$sql_servicios = "
    SELECT sr.*, s.Nombre_Servicio, v.Placa 
    FROM servicios_realizados sr
    JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
    JOIN vehiculos v ON sr.Vehiculo_id_Servicios_Realizados = v.Placa
    WHERE sr.Vehiculo_id_Servicios_Realizados IN ($placeholders)
";
$stmt_servicios = $conn->prepare($sql_servicios);
$types = str_repeat('s', count($placas));
$stmt_servicios->bind_param($types, ...$placas);
$stmt_servicios->execute();
$result_servicios = $stmt_servicios->get_result();

// Enviar encabezados para descargar CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="reporte_servicios.csv"');
echo "\xEF\xBB\xBF"; // 👈 Muy importante para evitar símbolos raros como �

$output = fopen('php://output', 'w');

// Escribir encabezados
fputcsv($output, ['ID Servicio', 'Placa', 'Nombre Servicio', 'Fecha', 'Ubicación', 'Novedades', 'Detalle'], ';');

// Escribir filas
while ($row = $result_servicios->fetch_assoc()) {
    fputcsv($output, [
        $row['Servicio_Realizado_id'],
        $row['Placa'],
        $row['Nombre_Servicio'],
        $row['Fecha'],
        $row['Ubicación'],
        $row['Novedades'] ?? '',
        $row['Detalle_Servicio'] ?? ''
    ], ';'); // 👈 cambia delimitador aquí también
}

fclose($output);
exit;
?>
