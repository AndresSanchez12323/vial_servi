<?php
session_start();

// Validar logout
if (isset($_POST['logout']) && $_POST['logout'] === 'true') {
    session_destroy();
    session_unset();
    echo 'Sesión eliminada';
    exit;
}

// Redirigir si no está logueado
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

include('validPermissions.php');

// NUEVO: Verificar si el usuario tiene rol 5
$usuarioId = $_SESSION['cedula'];
$rolUsuario = $_SESSION['rol'] ?? 0;

// Si el usuario tiene rol 5, mostrar mensaje de espera y no permitir más acciones
if ($rolUsuario == 5) {
    // Definir mensaje para mostrar
    $rolEsperaMessage = true;
    
    // No necesitamos verificar permisos u otras funcionalidades para este rol
} else {
    $permisoRequerido = 'leer_servicio';

    if (!usuarioTienePermiso($usuarioId, $permisoRequerido, $conn)) {
        die("❌ Acceso denegado: No tienes permiso para ver esta página.");
    }

    $verReportesAdministrador = usuarioTienePermiso($_SESSION['cedula'], 'ver_reporte_administrador', $conn);
    $verReportesTecnico = usuarioTienePermiso($_SESSION['cedula'], 'ver_reporte_tecnico', $conn);
    $mesSeleccionado = date('Y-m'); // Mes actual por defecto
}

// PETICIONES DE DATOS DINÁMICOS (mantener igual)
if (isset($_GET['data'])) {
    $dataType = $_GET['data'];
    $mesFiltro = $_GET['mes'];
    $query = "";
    $outputFormat = 'simple'; // 'simple' para pie/column, 'category' para bar chart
    $mesFiltroSQL = "'" . mysqli_real_escape_string($conn, $mesFiltro) . "'";


    if (!$mesFiltro || !preg_match('/^\d{4}-\d{2}$/', $mesFiltro)) {
        http_response_code(400);
        echo json_encode(['error' => 'El parámetro "mes" es obligatorio y debe tener formato YYYY-MM']);
        exit;
    }

    switch ($dataType) {
        case 'servicios':
            $query = "
                SELECT 
                s.Nombre_Servicio AS name, 
                COUNT(sr.Servicio_id_Servicios_Realizados) AS y
                FROM servicios_realizados sr
                JOIN servicios s 
                    ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
                WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
                GROUP BY s.Nombre_Servicio
                ORDER BY y DESC
            ";
            break;
        case 'municipios':
            $query = "
                SELECT 
                m.nombre AS name, 
                COUNT(sr.Servicio_Realizado_id) AS y
                FROM servicios_realizados sr
                JOIN municipios m 
                    ON sr.municipio = m.id
                WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
                GROUP BY m.nombre
                ORDER BY y DESC
            ";
            break;
        case 'empleados':
            $query = "
                SELECT 
                e.Nombre AS name, 
                COUNT(sr.Servicio_Realizado_id) AS y
                FROM servicios_realizados sr
                JOIN empleados e 
                    ON sr.Cedula_Empleado_id_Servicios_Realizados = e.Cedula_Empleado_id
                WHERE DATE_FORMAT(sr.Fecha, '%Y-%m') = $mesFiltroSQL
                GROUP BY e.Cedula_Empleado_id, e.Nombre
                ORDER BY y DESC
            ";
            $outputFormat = 'category';
            break;
        case 'serviciosEmpleados':
            $query = "
                SELECT 
                DATE(Fecha) AS fecha,
                COUNT(*) AS cantidad_reservas
                FROM servicios_realizados
                WHERE DATE_FORMAT(Fecha, '%Y-%m') = $mesFiltroSQL
                  AND Cedula_Empleado_id_Servicios_Realizados = $usuarioId
                GROUP BY fecha
                ORDER BY fecha ASC;
                ";
            $outputFormat = 'tecnico';
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Tipo de dato no válido']);
            exit;
    }

    $result = mysqli_query($conn, $query);
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => mysqli_error($conn)]);
        exit;
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        if ($outputFormat === 'simple') {
            $data[] = ['name' => $row['name'], 'y' => (int) $row['y']];
        } else if ($outputFormat === 'tecnico') {
            $data[] = [
                'fecha' => $row['fecha'],
                'cantidad_reservas' => (int) $row['cantidad_reservas']
            ];
        } else {
            $data['categories'][] = $row['name'];
            $data['data'][] = (int) $row['y'];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.85);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 90%;
            text-align: center;
            margin-top: 60px;
            transition: all 0.3s ease;
        }

        h2 {
            margin-bottom: 20px;
            color: #680c39;
            font-size: 24px;
            font-weight: 600;
        }

        p {
            color: #555;
            font-size: 16px;
        }

        a {
            padding: 12px 20px;
            background-color: #2d0f2a;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            display: inline-block;
            margin: 10px 5px;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        a:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        a:active {
            background-color: #680c39;
            transform: translateY(0);
        }

        .reports {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
            width: 100%;
            padding: 20px;
        }

        @media (max-width: 600px) {
            .reports {
                grid-template-columns: 1fr;
            }
        }

        .chart-placeholder {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 400px;
            background-color: rgba(255, 255, 255, 0.6);
            border-radius: 10px;
            color: #777;
            font-style: italic;
        }

        .month-selector {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .month-selector label {
            font-weight: 600;
            color: #333;
        }

        .month-selector input[type="month"] {
            padding: 6px 10px;
            font-size: 16px;
            border: 1px solid #aaa;
            border-radius: 6px;
            background-color: #fff;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .month-selector input[type="month"]:hover,
        .month-selector input[type="month"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn-logout {
            background-color: #440f33;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-logout:hover {
            background-color: #680c39;
            background-color: ;
            transform: translateY(-2px);
        }

        .btn-logout:active {
            background-color: #dc3545;
            transform: translateY(0);
        }

        /* Mejorar el estilo del botón de Excel */
        .btn-excel {
            background-color: #1e7e34;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-left: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .btn-excel:hover {
            background-color: #218838;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-excel:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Incluir el ícono de Font Awesome */
        .fa-file-excel:before {
            content: "\f1c3";
        }

        /* Botón de AI Insights */
        .btn-ai-insights {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-left: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .btn-ai-insights:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-ai-insights:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Modal para insights AI */
        .ai-insights-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .ai-insights-content {
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 1000px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .ai-insights-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ai-insights-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .ai-insights-close {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            padding: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .ai-insights-close:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .ai-insights-body {
            padding: 30px;
        }

        .insights-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .insights-section h3 {
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }

        .insights-section p {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .loading-insights {
            text-align: center;
            padding: 40px;
            color: #667eea;
        }

        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #667eea;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* NUEVO: Estilo para el mensaje de espera de rol */
        .waiting-message {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 30px;
            border-radius: 10px;
            margin: 30px auto;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .waiting-message h3 {
            color: #721c24;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        .waiting-message p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .waiting-icon {
            font-size: 60px;
            color: #721c24;
            margin-bottom: 20px;
        }
    </style>

    <!-- Añadir referencia a Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Añadir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <!-- NUEVO: Mostrar mensaje de espera para rol 5 -->
        <?php if (isset($rolEsperaMessage) && $rolEsperaMessage): ?>
            <div class="waiting-message">
                <div class="waiting-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <h3>Cuenta en proceso de activación</h3>
                <p>Estimado usuario, tu cuenta ha sido registrada correctamente pero actualmente no tienes asignado un rol con permisos suficientes para acceder al sistema.</p>
                <p>Un administrador debe revisar y asignar un rol adecuado a tu cuenta. Este proceso puede tomar de 24 a 48 horas hábiles.</p>
                <p>Por favor, intenta acceder nuevamente más tarde o contacta al administrador del sistema si este mensaje persiste por más de 48 horas.</p>
                
                <!-- Botón de cerrar sesión -->
                <a href="logout.php" data-no-warning class="btn-logout">Cerrar Sesión</a>
            </div>
        <?php else: ?>
            <!-- Contenido normal del dashboard para usuarios con roles válidos -->
            <h2>Bienvenido al Dashboard</h2>
            <p>Aquí podrás acceder a las diferentes funciones del sistema.</p>

            <?php
            // Verificar si el usuario es técnico (rol 2)
            $esTecnico = isset($_SESSION['rol']) && $_SESSION['rol'] == 2;
            
            if ($verReportesAdministrador) {
                ?>
                <a href="consulta_general.php" data-no-warning>Ir a Consulta General</a>
                <?php
            }
            ?>
            <?php
            if ($verReportesTecnico) {
                ?>
                <a href="consulta_general_tecnico.php" data-no-warning>Ver Mis Servicios Realizados</a>
                <a href="editar_novedades_servicio.php" data-no-warning>Editar Novedades de Servicios</a>
                <?php
            }
            ?>
            
            <?php
            // Mostrar el botón de Consulta por Identificación solo si NO es técnico
            if (!$esTecnico) {
                ?>
                <a href="consulta_identificacion.php" data-no-warning>Consulta por Identificación</a>
                <?php
            }
            ?>
            
            <!-- Botón de cerrar sesión -->
            <a href="logout.php" data-no-warning class="btn-logout">Cerrar Sesión</a>

            <!-- Selector de mes -->
            <div class="month-selector">
                <label for="mes-reporte"><strong>Selecciona el mes del reporte:</strong></label>
                <input type="month" id="mes-reporte" name="mes-reporte" onchange="actualizarReportesPorMes()" />
                
                <?php
                // Mostrar el botón de descarga Excel solo si NO es técnico
                if (!$esTecnico) {
                ?>
                    <!-- Botón de descarga Excel mejorado -->
                    <button id="btn-descargar-excel" class="btn-excel" onclick="descargarExcel()">
                        <i class="fas fa-file-excel"></i> Descargar datos del mes en Excel
                    </button>
                    
                    <!-- Botón de insights AI -->
                    <button id="btn-ai-insights" class="btn-ai-insights" onclick="mostrarInsightsAI()">
                        <i class="fas fa-robot"></i> Insights AI del Dashboard
                    </button>
                <?php
                }
                ?>
            </div>

            <?php
            if ($verReportesAdministrador) {
                ?>
                <div class="reports">
                    <div id="service-container" class="chart-placeholder">Cargando servicios...</div>
                    <div id="municipality-container" class="chart-placeholder">Cargando municipios...</div>
                    <div id="worker-container" class="chart-placeholder">Cargando empleados...</div>
                </div>
                <?php
            }
            ?>
            <?php
            if ($verReportesTecnico) {
                ?>
                <div id="serviciosEmpleados-container" class="chart-placeholder">Cargando empleados...</div>
                <?php
            }
            ?>
        <?php endif; ?>
    </div>

    <!-- Scripts - Solo cargar si el usuario no tiene rol 5 -->
    <?php if (!isset($rolEsperaMessage) || !$rolEsperaMessage): ?>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="js/session-check.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const inputMes = document.getElementById('mes-reporte');
                let mesActual = '';
                if (inputMes) {
                    const hoy = new Date();
                    mesActual = hoy.toISOString().slice(0, 7); // formato YYYY-MM
                    inputMes.value = mesActual;
                }
                cargarServiciosTecnico(mesActual);
                cargarServicios(mesActual);
                cargarMunicipios(mesActual);
                cargarEmpleados(mesActual);
            });

            function cargarServiciosTecnico(mes) {
                fetch(`dashboard.php?data=serviciosEmpleados&mes=${mes}`)
                    .then(res => res.json())
                    .then(data => { // Corregido: agregar paréntesis
                        console.log(data);

                        const categories = data.map(item => item.fecha);
                        const values = data.map(item => item.cantidad_reservas);
                        Highcharts.chart('serviciosEmpleados-container', {
                            chart: { type: 'column' },
                            title: { text: 'Servicios realizados por mes' },
                            xAxis: {
                                categories,
                                title: { text: 'Mes' }
                            },
                            yAxis: {
                                min: 0,
                                title: { text: 'Cantidad de servicios' }
                            },
                            credits: { enabled: false },
                            series: [{
                                name: 'Servicios',
                                data: values
                            }]
                        });
                    })
                    .catch(error => {
                        document.getElementById('serviciosEmpleados-container').innerText = 'Error al cargar datos.';
                        console.error('Error al cargar servicios:', error);
                    });
            }


            function cargarServicios(mes) {
                fetch(`dashboard.php?data=servicios&mes=${mes}`)
                    .then(res => res.json())
                    .then(data => {  // CORREGIDO: añadido el paréntesis
                        const categories = data.map(item => item.name);
                        const values = data.map(item => item.y);
                        Highcharts.chart('service-container', {
                            chart: { type: 'column' },
                            title: { text: 'Estadísticas de Servicios Realizados' },
                            xAxis: { categories, title: { text: 'Tipo de Servicio' } },
                            yAxis: { min: 0, title: { text: 'Cantidad de Servicios' } },
                            credits: { enabled: false },
                            series: [{ name: 'Servicios', data: values }]
                        });
                    })
                    .catch(error => {
                        document.getElementById('service-container').innerText = 'Error al cargar datos.';
                        console.error('Error al cargar servicios:', error);
                    });
            }

            function cargarMunicipios(mes) {
                fetch(`dashboard.php?data=municipios&mes=${mes}`)
                    .then(res => res.json())
                    .then(data => {
                        Highcharts.chart('municipality-container', {
                            chart: { type: 'pie' },
                            title: { text: 'Servicios por Ubicación' },
                            tooltip: { pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' },
                            accessibility: { point: { valueSuffix: '%' } },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: { enabled: false },
                                    showInLegend: true
                            }
                        },
                            credits: { enabled: false },
                            series: [{ name: 'Servicios', colorByPoint: true, data }]
                        });
                    })
                    .catch(error => {
                        document.getElementById('municipality-container').innerText = 'Error al cargar datos.';
                        console.error('Error al cargar municipios:', error);
                    });
            }

            function cargarEmpleados(mes) {
                fetch(`dashboard.php?data=empleados&mes=${mes}`)
                    .then(res => res.json())
                    .then (data => {
                        Highcharts.chart('worker-container', {
                            chart: { type: 'bar' },
                            title: { text: 'Servicios Realizados por Empleado' },
                            xAxis: { categories: data.categories, title: { text: 'Empleados' }, scrollbar: { enabled: data.categories.length > 10 } },
                            yAxis: { min: 0, title: { text: 'Cantidad de Servicios' } },
                            credits: { enabled: false },
                            series: [{ name: 'Servicios', data: data.data }]
                        });
                    })
                    .catch(error => {
                        document.getElementById('worker-container').innerText = 'Error al cargar datos.';
                        console.error('Error al cargar empleados:', error);
                    });
            }
            function actualizarReportesPorMes() {
                const mes = document.getElementById('mes-reporte').value; // formato: '2025-05'
                if (!mes) return;
                cargarServiciosTecnico(mes);
                cargarServicios(mes);
                cargarMunicipios(mes);
                cargarEmpleados(mes);
            }

            // Añadir función para descargar Excel
            function descargarExcel() {
                const mes = document.getElementById('mes-reporte').value;
                if (!mes) {
                    alert('Por favor selecciona un mes para descargar el reporte');
                    return;
                }
                
                // Mostrar mensaje de procesamiento
                Swal.fire({
                    title: 'Generando Excel',
                    text: 'Procesando los datos del mes ' + mes + '...',
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
                
                // SOLUCIÓN: Usar un iframe oculto para mantener la sesión
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.name = 'download_frame';
                document.body.appendChild(iframe);
                
                // Crear un formulario para enviar con POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'descargar_dashboard_excel.php';
                form.target = 'download_frame'; // Importante: usar el iframe
    
                // Agregar el mes seleccionado como parámetro
                const mesInput = document.createElement('input');
                mesInput.type = 'hidden';
                mesInput.name = 'mes';
                mesInput.value = mes;
                form.appendChild(mesInput);
                
                // Enviar el formulario
                document.body.appendChild(form);
                form.submit();
                
                // Cerrar el mensaje después de un tiempo
                setTimeout(() => {
                    Swal.close();
                    // Limpiar recursos después de la descarga
                    setTimeout(() => {
                        document.body.removeChild(form);
                        document.body.removeChild(iframe);
                    }, 1000);
                }, 2000);
            }

            // Función para mostrar insights AI
            function mostrarInsightsAI() {
                const mes = document.getElementById('mes-reporte').value;
                if (!mes) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Mes requerido',
                        text: 'Por favor selecciona un mes para generar insights AI'
                    });
                    return;
                }

                // Crear modal
                const modal = document.createElement('div');
                modal.className = 'ai-insights-modal';
                modal.innerHTML = `
                    <div class="ai-insights-content">
                        <div class="ai-insights-header">
                            <h2>🤖 Insights AI - Dashboard VialServi</h2>
                            <button class="ai-insights-close">&times;</button>
                        </div>
                        <div class="ai-insights-body">
                            <div class="loading-insights">
                                <div class="loading-spinner"></div>
                                <p>Generando insights inteligentes para ${mes}...</p>
                                <small>La IA está analizando los datos del dashboard...</small>
                            </div>
                        </div>
                    </div>
                `;

                document.body.appendChild(modal);
                modal.style.display = 'block';

                // Event listener para cerrar modal
                const closeBtn = modal.querySelector('.ai-insights-close');
                closeBtn.onclick = () => {
                    document.body.removeChild(modal);
                };

                // Cerrar modal al hacer clic fuera
                modal.onclick = (e) => {
                    if (e.target === modal) {
                        document.body.removeChild(modal);
                    }
                };

                // Obtener insights AI
                fetch(`ai_dashboard_insights.php?mes=${mes}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            mostrarInsights(modal, data);
                        } else {
                            mostrarError(modal, data.error || 'Error generando insights');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        mostrarError(modal, 'Error de conexión. Por favor, intenta de nuevo.');
                    });
            }

            function mostrarInsights(modal, data) {
                const body = modal.querySelector('.ai-insights-body');
                const insights = data.ai_insights;
                const summary = data.summary;
                
                body.innerHTML = `
                    <div class="insights-section">
                        <h3>📊 Resumen del Mes</h3>
                        <p><strong>Servicios totales:</strong> ${summary.total_services}</p>
                        <p><strong>Empleados activos:</strong> ${summary.active_employees}</p>
                        <p><strong>Municipios atendidos:</strong> ${summary.municipalities_served}</p>
                        <p><strong>Clientes atendidos:</strong> ${summary.clients_served}</p>
                        <p><strong>Ingresos estimados:</strong> $${parseInt(summary.estimated_revenue || 0).toLocaleString()}</p>
                    </div>
                    
                    <div class="insights-section">
                        <h3>🎯 Análisis General</h3>
                        <p>${insights.general_insights}</p>
                    </div>
                    
                    <div class="insights-section">
                        <h3>💡 Recomendaciones</h3>
                        <p>${insights.recommendations}</p>
                    </div>
                    
                    <div class="insights-section">
                        <h3>👥 Análisis de Rendimiento</h3>
                        <p>${insights.performance_analysis}</p>
                    </div>
                    
                    <div class="insights-section">
                        <h3>🚀 Oportunidades de Mercado</h3>
                        <p>${insights.market_opportunities}</p>
                    </div>
                `;
            }

            function mostrarError(modal, errorMessage) {
                const body = modal.querySelector('.ai-insights-body');
                body.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #e74c3c;">
                        <h3>❌ Error</h3>
                        <p>${errorMessage}</p>
                        <small>Por favor, intenta de nuevo más tarde o contacta al administrador del sistema.</small>
                    </div>
                `;
            }
        </script>
    <?php endif; ?>

    <?php include 'ai_chat_widget.php'; ?>
</body>

</html>