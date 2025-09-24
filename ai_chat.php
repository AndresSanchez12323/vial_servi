<?php
/**
 * AI Chat Assistant Endpoint for VialServi
 * Handles AJAX requests for AI-powered assistance
 */

session_start();
require_once 'config.php';
require_once 'gemini_ai.php';

header('Content-Type: application/json');

// Check if user is logged in (client or employee)
$isClientLoggedIn = isset($_SESSION['cliente_id']);
$isEmployeeLoggedIn = isset($_SESSION['loggedin']);

if (!$isClientLoggedIn && !$isEmployeeLoggedIn) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';
$context = $input['context'] ?? '';

if (empty($message)) {
    echo json_encode(['error' => 'Mensaje vacío']);
    exit;
}

try {
    $gemini = getGeminiAI();
    
    // Determine user role and get additional context
    $userRole = 'cliente';
    $userData = [];
    
    if ($isEmployeeLoggedIn) {
        $userRole = 'empleado';
        // Get employee data
        $sql = "SELECT Nombre, Apellido, Rol_id FROM empleados WHERE Cedula_Empleado_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION['cedula']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $userData = $row;
        }
    } elseif ($isClientLoggedIn) {
        // Get client data and vehicles
        $sql = "SELECT c.*, COUNT(v.Placa) as total_vehiculos 
                FROM clientes c 
                LEFT JOIN vehiculos v ON c.Cedula_Id = v.Clientes_Vehiculos 
                WHERE c.Cedula_Id = ? 
                GROUP BY c.Cedula_Id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['cliente_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $userData = $row;
        }
        
        // Get recent services
        $sql = "SELECT sr.*, s.Nombre_Servicio 
                FROM servicios_realizados sr 
                JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id 
                JOIN vehiculos v ON sr.Vehiculo_id_Servicios_Realizados = v.Placa 
                WHERE v.Clientes_Vehiculos = ? 
                ORDER BY sr.Fecha DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['cliente_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $recentServices = $result->fetch_all(MYSQLI_ASSOC);
        $userData['recent_services'] = $recentServices;
    }
    
    // Add context about available services
    $sql = "SELECT Servicio_id, Nombre_Servicio, Descripción FROM servicios WHERE Activo = 1";
    $result = $conn->query($sql);
    $availableServices = $result->fetch_all(MYSQLI_ASSOC);
    $userData['available_services'] = $availableServices;
    
    // Build comprehensive context
    $fullContext = $context . "\nRol del usuario: $userRole\n" . 
                   "Datos del usuario: " . json_encode($userData);
    
    // Generate AI response
    $response = $gemini->generateContent($message, $fullContext);
    
    echo json_encode([
        'success' => true,
        'response' => $response,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    error_log("Error en AI Chat: " . $e->getMessage());
    echo json_encode([
        'error' => 'Lo siento, no pude procesar tu consulta en este momento. Por favor, contacta a nuestro equipo de soporte.',
        'fallback' => true
    ]);
}
?>