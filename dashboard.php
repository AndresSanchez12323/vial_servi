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
$usuarioId = $_SESSION['cedula'];
$permisoRequerido = 'leer_servicio';

if (!usuarioTienePermiso($usuarioId, $permisoRequerido, $conn)) {
    die("❌ Acceso denegado: No tienes permiso para ver esta página.");
}

$verReportesAdministrador = usuarioTienePermiso($_SESSION['cedula'], 'ver_reporte_administrador', $conn);
$verReportesTecnico = usuarioTienePermiso($_SESSION['cedula'], 'ver_reporte_tecnico', $conn);
$mesSeleccionado = date('Y-m'); // Mes actual por defecto

// PETICIONES DE DATOS DINÁMICOS
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
    </style>
</head>

<body>

    <div class="container">
        <h2>Bienvenido al Dashboard</h2>
        <p>Aquí podrás acceder a las diferentes funciones del sistema.</p>
        <a href="consulta_general.php" data-no-warning>Ir a Consulta General</a>
        <a href="consulta_identificacion.php" data-no-warning>Consulta por Identificación</a>

        <!-- Selector de mes -->
        <div class="month-selector">
            <label for="mes-reporte"><strong>Selecciona el mes del reporte:</strong></label>
            <input type="month" id="mes-reporte" name="mes-reporte" onchange="actualizarReportesPorMes()" />
        </div>

        <?php
        if (usuarioTienePermiso($_SESSION['cedula'], 'ver_reporte_administrador', $conn)) {
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
        if (usuarioTienePermiso($_SESSION['cedula'], 'ver_reporte_tecnico', $conn)) {
            ?>
            <div id="serviciosEmpleados-container" class="chart-placeholder">Cargando empleados...</div>
            <?php
        }
        ?>
    </div>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        const checkSession = () => {
            fetch('session.php')
                .then(response => response.text())
                .then(data => {
                    if (data.includes('No active session')) {
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => console.error('Error al validar la sesión:', error));
        };
        checkSession();

        const beforeUnloadHandler = (event) => {
            if (event.target.activeElement?.hasAttribute('data-no-warning')) return;
            navigator.sendBeacon('session.php', new URLSearchParams({ logout: 'true' }));
        };
        window.addEventListener('beforeunload', beforeUnloadHandler);

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
                .then(data => {
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
                .then(data => {
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
                .then(data => {
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
    </script>

</body>

</html>