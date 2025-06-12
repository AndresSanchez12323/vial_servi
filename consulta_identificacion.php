<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

require_once 'config.php';
include('validPermissions.php');
include('header.php');

// Inicializar la variable placa
$placa = isset($_POST['placa']) ? trim($_POST['placa']) : (isset($_GET['placa']) ? trim($_GET['placa']) : '');
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$registros_por_pagina = 10;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

if ((!empty($placa) && $_SERVER['REQUEST_METHOD'] == 'POST') || (!empty($placa) && isset($_GET['pagina']))) {
    // Primero, obtener el total de registros para la paginación
    $sql_count = "SELECT COUNT(*) as total FROM Servicios_Realizados WHERE Vehiculo_id_Servicios_Realizados LIKE ?";
    $stmt_count = $conn->prepare($sql_count);
    $param = "%$placa%";
    $stmt_count->bind_param("s", $param);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_assoc();
    $total_registros = $row_count['total'];
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    
    // Consulta principal con paginación
    $sql = "SELECT 
                sr.Servicio_Realizado_id, 
                sr.Vehiculo_id_Servicios_Realizados, 
                sr.Fecha, 
                sr.Ubicación,
                s.Nombre_Servicio,
                m.nombre as nombre_municipio,
                CONCAT(e.Nombre, ' ', e.Apellido) as nombre_tecnico,
                sr.Novedades
            FROM Servicios_Realizados sr
            LEFT JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
            LEFT JOIN municipios m ON sr.municipio = m.id
            LEFT JOIN empleados e ON sr.Cedula_Empleado_id_Servicios_Realizados = e.Cedula_Empleado_id
            WHERE sr.Vehiculo_id_Servicios_Realizados LIKE ?
            ORDER BY sr.Fecha DESC
            LIMIT ? OFFSET ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $param, $registros_por_pagina, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Función para construir URL de paginación
function construirUrlPaginacion($pagina, $placa) {
    return "consulta_identificacion.php?pagina=$pagina&placa=" . urlencode($placa);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta por Placa</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/ConsultaIdentificacion.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            padding: 20px;
            margin-top: 120px;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #680c39;
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
        }

        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: auto;
        }

        th {
            background-color: #2d0f2a;
            color: white;
            font-weight: bold;
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td img {
            width: 100px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #666;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #2d0f2a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2d0f2a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #2d0f2a;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #2d0f2a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        
        /* Estilos de paginación */
        .pagination-container {
            margin: 20px 0;
        }

        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }

        .pagination .page-item {
            margin: 0 2px;
        }

        .pagination .page-item a {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #2d0f2a;
        }

        .pagination .page-item.active a {
            background-color: #2d0f2a;
            color: #fff;
            border-color: #2d0f2a;
        }

        .pagination .page-item a:hover {
            background-color: #f5f5f5;
        }

        .pagination .page-item.disabled a {
            color: #6c757d;
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<!-- Contenido principal -->
<div class="main-content">
    <div class="container">
        <h2>Buscar Servicios por Placa de Vehículo</h2>
        <form method="POST">
            <label for="placa">Placa Vehículo:</label>
            <input type="text" id="placa" name="placa" required value="<?= htmlspecialchars($placa) ?>">
            <button data-no-warning type="submit">Buscar</button>
        </form>

        <?php if (isset($result)): ?>
            <h3 class="mt-4">Resultados para: <?= htmlspecialchars($placa) ?></h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Placa Vehículo</th>
                    <th>Servicio</th>
                    <th>Fecha</th>
                    <th>Municipio</th>
                    <th>Técnico</th>
                    <th>Ubicación</th>
                    <th>Novedades</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Servicio_Realizado_id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['Vehiculo_id_Servicios_Realizados']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Nombre_Servicio'] ?? 'No especificado') . "</td>";
                        echo "<td>" . htmlspecialchars($row['Fecha']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nombre_municipio'] ?? 'No especificado') . "</td>";
                        echo "<td>" . htmlspecialchars($row['nombre_tecnico'] ?? 'No asignado') . "</td>";
                        echo "<td>" . htmlspecialchars($row['Ubicación']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Novedades']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No se encontraron registros</td></tr>";
                }
                ?>
            </table>
            
            <?php if (isset($total_paginas) && $total_paginas > 1): ?>
            <!-- Paginación -->
            <div class="pagination-container">
                <ul class="pagination">
                    <!-- Botón Anterior -->
                    <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                        <a href="<?= construirUrlPaginacion($pagina_actual - 1, $placa) ?>" data-no-warning>
                            &laquo; Anterior
                        </a>
                    </li>
                    
                    <?php
                    // Mostrar números de página
                    $rango = 2; // Mostrar 2 páginas antes y después de la actual
                    $inicio_rango = max(1, $pagina_actual - $rango);
                    $fin_rango = min($total_paginas, $pagina_actual + $rango);
                    
                    // Primera página si no está en el rango
                    if ($inicio_rango > 1) {
                        echo '<li class="page-item"><a href="' . construirUrlPaginacion(1, $placa) . '" data-no-warning>1</a></li>';
                        if ($inicio_rango > 2) {
                            echo '<li class="page-item disabled"><a>...</a></li>';
                        }
                    }
                    
                    // Páginas en el rango
                    for ($i = $inicio_rango; $i <= $fin_rango; $i++) {
                        echo '<li class="page-item ' . ($i == $pagina_actual ? 'active' : '') . '">
                                <a href="' . construirUrlPaginacion($i, $placa) . '" data-no-warning>' . $i . '</a>
                              </li>';
                    }
                    
                    // Última página si no está en el rango
                    if ($fin_rango < $total_paginas) {
                        if ($fin_rango < $total_paginas - 1) {
                            echo '<li class="page-item disabled"><a>...</a></li>';
                        }
                        echo '<li class="page-item"><a href="' . construirUrlPaginacion($total_paginas, $placa) . '" data-no-warning>' . $total_paginas . '</a></li>';
                    }
                    ?>
                    
                    <!-- Botón Siguiente -->
                    <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                        <a href="<?= construirUrlPaginacion($pagina_actual + 1, $placa) ?>" data-no-warning>
                            Siguiente &raquo;
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="text-center text-muted small">
                Mostrando <?= min(($pagina_actual - 1) * $registros_por_pagina + 1, $total_registros) ?> 
                a <?= min($pagina_actual * $registros_por_pagina, $total_registros) ?> 
                de <?= $total_registros ?> registros
            </div>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>
</div>
<script src="js/session-check.js"></script>

</body>
</html>
