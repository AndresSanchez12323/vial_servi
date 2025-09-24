<?php
/**
 * AI-Powered Service Recommendations for VialServi
 * Provides intelligent service recommendations based on vehicle data and history
 */

session_start();
require_once 'config.php';
require_once 'gemini_ai.php';

header('Content-Type: application/json');

// Check if client is logged in
if (!isset($_SESSION['cliente_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$vehiclePlate = $_GET['placa'] ?? '';

if (empty($vehiclePlate)) {
    echo json_encode(['error' => 'Placa de vehículo requerida']);
    exit;
}

try {
    // Get vehicle information
    $sql = "SELECT v.*, ma.nombre as marca_nombre, mo.nombre as modelo_nombre, 
                   co.nombre_color as color_nombre
            FROM vehiculos v
            LEFT JOIN marcas ma ON v.Marca = ma.id
            LEFT JOIN modelos mo ON v.modelo_id = mo.id
            LEFT JOIN colores co ON v.color_id = co.color_id
            WHERE v.Placa = ? AND v.Clientes_Vehiculos = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $vehiclePlate, $_SESSION['cliente_id']);
    $stmt->execute();
    $vehicleResult = $stmt->get_result();
    
    if ($vehicleResult->num_rows === 0) {
        echo json_encode(['error' => 'Vehículo no encontrado']);
        exit;
    }
    
    $vehicleData = $vehicleResult->fetch_assoc();
    
    // Get service history for this vehicle
    $sql = "SELECT sr.*, s.Nombre_Servicio, s.Descripción as servicio_descripcion,
                   e.Nombre as tecnico_nombre, e.Apellido as tecnico_apellido,
                   m.nombre as municipio_nombre
            FROM servicios_realizados sr
            JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
            LEFT JOIN empleados e ON sr.Cedula_Empleado_id_Servicios_Realizados = e.Cedula_Empleado_id
            LEFT JOIN municipios m ON sr.municipio = m.id
            WHERE sr.Vehiculo_id_Servicios_Realizados = ?
            ORDER BY sr.Fecha DESC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $vehiclePlate);
    $stmt->execute();
    $serviceHistory = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get available services
    $sql = "SELECT Servicio_id, Nombre_Servicio, Descripción FROM servicios WHERE Activo = 1";
    $availableServices = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    
    // Generate AI recommendations
    $gemini = getGeminiAI();
    
    // Prepare vehicle data for AI
    $vehicleInfo = [
        'placa' => $vehicleData['Placa'],
        'marca' => $vehicleData['marca_nombre'],
        'modelo' => $vehicleData['modelo_nombre'],
        'color' => $vehicleData['color_nombre'],
        'objetos_valiosos' => $vehicleData['Objetos_Valiosos']
    ];
    
    // Get recommendations
    $recommendations = $gemini->getServiceRecommendations($vehicleInfo, $serviceHistory);
    
    // Also get general maintenance tips
    $maintenanceTips = $gemini->generateContent(
        "Proporciona consejos específicos de mantenimiento preventivo para un vehículo " . 
        $vehicleData['marca_nombre'] . " " . $vehicleData['modelo_nombre'] . 
        " basándote en su historial de servicios y las mejores prácticas de la industria automotriz.",
        "Datos del vehículo: " . json_encode($vehicleInfo) . 
        "\nHistorial reciente: " . json_encode(array_slice($serviceHistory, 0, 3))
    );
    
    // Calculate service statistics
    $serviceStats = [
        'total_services' => count($serviceHistory),
        'last_service_date' => !empty($serviceHistory) ? $serviceHistory[0]['Fecha'] : null,
        'most_frequent_service' => null,
        'average_service_interval' => null
    ];
    
    if (!empty($serviceHistory)) {
        // Find most frequent service
        $serviceCounts = [];
        foreach ($serviceHistory as $service) {
            $serviceName = $service['Nombre_Servicio'];
            $serviceCounts[$serviceName] = ($serviceCounts[$serviceName] ?? 0) + 1;
        }
        arsort($serviceCounts);
        $serviceStats['most_frequent_service'] = key($serviceCounts);
        
        // Calculate average interval (if we have multiple services)
        if (count($serviceHistory) > 1) {
            $dates = array_column($serviceHistory, 'Fecha');
            $intervals = [];
            for ($i = 0; $i < count($dates) - 1; $i++) {
                $date1 = new DateTime($dates[$i]);
                $date2 = new DateTime($dates[$i + 1]);
                $intervals[] = $date1->diff($date2)->days;
            }
            $serviceStats['average_service_interval'] = round(array_sum($intervals) / count($intervals));
        }
    }
    
    echo json_encode([
        'success' => true,
        'vehicle' => $vehicleInfo,
        'service_history' => $serviceHistory,
        'service_stats' => $serviceStats,
        'available_services' => $availableServices,
        'ai_recommendations' => $recommendations,
        'maintenance_tips' => $maintenanceTips,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    error_log("Error en AI Recommendations: " . $e->getMessage());
    echo json_encode([
        'error' => 'No pude generar recomendaciones en este momento. Por favor, intenta más tarde.',
        'fallback' => true
    ]);
}
?>