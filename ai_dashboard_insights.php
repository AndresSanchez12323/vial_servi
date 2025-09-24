<?php
/**
 * AI Dashboard Insights for VialServi
 * Provides intelligent analysis of dashboard data for administrators
 */

session_start();
require_once 'config.php';
require_once 'gemini_ai.php';
include('validPermissions.php');

header('Content-Type: application/json');

// Check if user is logged in and has admin permissions
if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Check permissions
$verReportesAdministrador = usuarioTienePermiso($_SESSION['cedula'], 'ver_reporte_administrador', $conn);
if (!$verReportesAdministrador) {
    echo json_encode(['error' => 'Sin permisos de administrador']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$mes = $_GET['mes'] ?? date('Y-m');

if (!preg_match('/^\d{4}-\d{2}$/', $mes)) {
    echo json_encode(['error' => 'Formato de mes inválido']);
    exit;
}

try {
    $mesFiltroSQL = "'" . mysqli_real_escape_string($conn, $mes) . "'";
    
    // Get services data
    $sqlServicios = "
        SELECT 
        s.Nombre_Servicio, 
        COUNT(sr.Servicio_id_Servicios_Realizados) as cantidad
        FROM servicios_realizados sr
        JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
        WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
        GROUP BY s.Nombre_Servicio
        ORDER BY cantidad DESC
    ";
    $resultServicios = $conn->query($sqlServicios);
    $serviciosData = $resultServicios->fetch_all(MYSQLI_ASSOC);
    
    // Get municipalities data
    $sqlMunicipios = "
        SELECT 
        m.nombre, 
        COUNT(sr.Servicio_Realizado_id) as cantidad
        FROM servicios_realizados sr
        JOIN municipios m ON sr.municipio = m.id
        WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
        GROUP BY m.nombre
        ORDER BY cantidad DESC
    ";
    $resultMunicipios = $conn->query($sqlMunicipios);
    $municipiosData = $resultMunicipios->fetch_all(MYSQLI_ASSOC);
    
    // Get employees data
    $sqlEmpleados = "
        SELECT 
        e.Nombre, 
        e.Apellido,
        COUNT(sr.Servicio_Realizado_id) as cantidad
        FROM servicios_realizados sr
        JOIN empleados e ON sr.Cedula_Empleado_id_Servicios_Realizados = e.Cedula_Empleado_id
        WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
        GROUP BY e.Cedula_Empleado_id, e.Nombre, e.Apellido
        ORDER BY cantidad DESC
    ";
    $resultEmpleados = $conn->query($sqlEmpleados);
    $empleadosData = $resultEmpleados->fetch_all(MYSQLI_ASSOC);
    
    // Get monthly trend (last 6 months)
    $sqlTendencia = "
        SELECT 
        DATE_FORMAT(Fecha, '%Y-%m') as mes,
        COUNT(*) as cantidad
        FROM servicios_realizados
        WHERE Fecha >= DATE_SUB(CONCAT('$mes', '-01'), INTERVAL 5 MONTH)
        AND Fecha <= LAST_DAY(CONCAT('$mes', '-01'))
        GROUP BY DATE_FORMAT(Fecha, '%Y-%m')
        ORDER BY mes
    ";
    $resultTendencia = $conn->query($sqlTendencia);
    $tendenciaData = $resultTendencia->fetch_all(MYSQLI_ASSOC);
    
    // Get client satisfaction data (if available)
    $sqlClientes = "
        SELECT 
        COUNT(DISTINCT v.Clientes_Vehiculos) as clientes_atendidos,
        COUNT(*) as servicios_total
        FROM servicios_realizados sr
        JOIN vehiculos v ON sr.Vehiculo_id_Servicios_Realizados = v.Placa
        WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
    ";
    $resultClientes = $conn->query($sqlClientes);
    $clientesData = $resultClientes->fetch_assoc();
    
    // Get revenue estimation (if pricing is available)
    $sqlIngresos = "
        SELECT 
        AVG(precio_estimado) as precio_promedio,
        SUM(precio_estimado) as ingresos_estimados
        FROM (
            SELECT 
            CASE 
                WHEN s.Nombre_Servicio LIKE '%Reparación%' THEN 150000
                WHEN s.Nombre_Servicio LIKE '%Mantenimiento%' THEN 80000
                WHEN s.Nombre_Servicio LIKE '%Inspección%' THEN 50000
                ELSE 100000
            END as precio_estimado
            FROM servicios_realizados sr
            JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
            WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
        ) as precios
    ";
    $resultIngresos = $conn->query($sqlIngresos);
    $ingresosData = $resultIngresos->fetch_assoc();
    
    // Prepare comprehensive data for AI analysis
    $dashboardData = [
        'mes_analizado' => $mes,
        'servicios_por_tipo' => $serviciosData,
        'servicios_por_municipio' => $municipiosData,
        'rendimiento_empleados' => $empleadosData,
        'tendencia_mensual' => $tendenciaData,
        'metricas_clientes' => $clientesData,
        'estimacion_ingresos' => $ingresosData,
        'total_servicios_mes' => array_sum(array_column($serviciosData, 'cantidad')),
        'empleados_activos' => count($empleadosData),
        'municipios_atendidos' => count($municipiosData)
    ];
    
    // Generate AI insights
    $gemini = getGeminiAI();
    $insights = $gemini->generateDashboardInsights($dashboardData);
    
    // Generate specific recommendations
    $recommendations = $gemini->generateContent(
        "Basándote en los datos del dashboard de VialServi, proporciona 5 recomendaciones específicas y accionables para mejorar la operación del negocio, optimizar recursos y aumentar la satisfacción del cliente.",
        "Datos del dashboard: " . json_encode($dashboardData)
    );
    
    // Generate performance analysis
    $performanceAnalysis = $gemini->generateContent(
        "Analiza el rendimiento de los empleados y identifica oportunidades de mejora, reconocimientos necesarios y estrategias de optimización del equipo de trabajo.",
        "Datos de rendimiento: " . json_encode($empleadosData) . "\nMétricas generales: " . json_encode($clientesData)
    );
    
    // Generate market opportunities
    $marketOpportunities = $gemini->generateContent(
        "Identifica oportunidades de mercado y áreas de crecimiento basándote en la distribución geográfica de servicios y tipos de servicios más demandados.",
        "Datos geográficos: " . json_encode($municipiosData) . "\nTipos de servicios: " . json_encode($serviciosData)
    );
    
    echo json_encode([
        'success' => true,
        'dashboard_data' => $dashboardData,
        'ai_insights' => [
            'general_insights' => $insights,
            'recommendations' => $recommendations,
            'performance_analysis' => $performanceAnalysis,
            'market_opportunities' => $marketOpportunities
        ],
        'summary' => [
            'total_services' => $dashboardData['total_servicios_mes'],
            'active_employees' => $dashboardData['empleados_activos'],
            'municipalities_served' => $dashboardData['municipios_atendidos'],
            'clients_served' => $clientesData['clientes_atendidos'],
            'estimated_revenue' => $ingresosData['ingresos_estimados']
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    error_log("Error en AI Dashboard Insights: " . $e->getMessage());
    echo json_encode([
        'error' => 'No pude generar insights en este momento. Por favor, intenta más tarde.',
        'fallback' => true
    ]);
}
?>