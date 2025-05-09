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

// 👇🏽 SI ES PETICIÓN DE DATOS JSON
if (isset($_GET['data']) && $_GET['data'] === 'servicios') {
    $query = "
        SELECT 
            s.Nombre_Servicio AS tipo_servicio, 
            COUNT(sr.Servicio_id_Servicios_Realizados) AS cantidad 
        FROM 
            servicios_realizados sr 
        JOIN 
            servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id 
        GROUP BY 
            s.Nombre_Servicio 
        ORDER BY 
            cantidad DESC
    ";

    $result = mysqli_query($conn, $query);
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'name' => $row['tipo_servicio'],
            'y' => (int) $row['cantidad']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit; // 🚩 Muy importante: SALIR aquí para no seguir con HTML
}

if (isset($_GET['data']) && $_GET['data'] === 'municipios') {
    $query = "
        SELECT 
            m.nombre AS municipio, 
            COUNT(sr.Servicio_Realizado_id) AS cantidad
        FROM 
            servicios_realizados sr
        JOIN 
            municipios m ON sr.municipio = m.id
        GROUP BY 
            m.nombre
        ORDER BY 
            cantidad DESC
    ";

    $result = mysqli_query($conn, $query);
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'name' => $row['municipio'],
            'y' => (int) $row['cantidad']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if (isset($_GET['data']) && $_GET['data'] === 'empleados') {
    $query = "
    SELECT 
        e.Cedula_Empleado_id AS cedula,
        e.Nombre AS empleado, 
        COUNT(sr.Servicio_Realizado_id) AS cantidad
    FROM 
        servicios_realizados sr
    JOIN 
        empleados e 
        ON sr.Cedula_Empleado_id_Servicios_Realizados = e.Cedula_Empleado_id
    GROUP BY 
        e.Cedula_Empleado_id, e.Nombre
    ORDER BY 
        cantidad DESC;
";



    $result = mysqli_query($conn, $query);
    $categories = [];
    $values = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['empleado'];
        $values[] = (int) $row['cantidad'];
    }

    header('Content-Type: application/json');
    echo json_encode([
        'categories' => $categories,
        'data' => $values
    ]);
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
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 90%;
            text-align: center;
            margin-top: 100px;
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
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Bienvenido al Dashboard</h2>
        <p>Aquí podrás acceder a las diferentes funciones del sistema.</p>
        <a href="consulta_general.php" data-no-warning>Ir a Consulta General</a>
        <a href="consulta_identificacion.php" data-no-warning>Consulta por Identificación</a>

        <div class="reports">
            <div id="service-container" style="height: 400px;"></div>
            <div id="municipality-container" style="height: 400px;"></div>
            <div id="worker-container" style="height: 400px;"></div>
        </div>

    </div>

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
    </script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('dashboard.php?data=servicios')
                .then(response => response.json())
                .then(data => {
                    const categories = data.map(item => item.name);
                    const values = data.map(item => item.y);

                    Highcharts.chart('service-container', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Estadísticas de Servicios Realizados'
                        },
                        xAxis: {
                            categories: categories,
                            title: {
                                text: 'Tipo de Servicio'
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Cantidad de Servicios'
                            }
                        },
                        credits: {
                            enabled: false,
                        },
                        series: [{
                            name: 'Servicios',
                            data: values
                        }]
                    });
                })
                .catch(error => console.error('Error al cargar datos:', error));
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('dashboard.php?data=municipios')
                .then(response => response.json())
                .then(data => {
                    Highcharts.chart('municipality-container', {
                        chart: {
                            type: 'pie'
                        },
                        title: {
                            text: 'Servicios por Ubicación'
                        },
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        },
                        accessibility: {
                            point: {
                                valueSuffix: '%'
                            }
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: false
                                },
                                showInLegend: true
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Servicios',
                            colorByPoint: true,
                            data: data // 🎉 usa directamente los datos desde PHP
                        }]
                    });
                })
                .catch(error => console.error('Error al cargar datos:', error));
        });

    </script>

    <script>
        fetch('dashboard.php?data=empleados')
            .then(res => res.json())
            .then(response => {
                Highcharts.chart('worker-container', {
                    chart: { type: 'bar' },
                    title: { text: 'Servicios Realizados por Empleado' },
                    xAxis: {
                        categories: response.categories,
                        title: { text: 'Empleados' },
                        scrollbar: { enabled: response.categories.length > 10 }
                    },
                    yAxis: {
                        min: 0,
                        title: { text: 'Cantidad de Servicios' }
                    },
                    credits: { enabled: false },
                    series: [{
                        name: 'Servicios',
                        data: response.data
                    }]
                });
            });

    </script>
</body>

</html>