<?php
include('validPermissions.php');
include('header.php');

// Solo iniciar sesión si no hay una sesión activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

require_once 'config.php';
$usuarioId = $_SESSION['cedula'];
$permisoRequerido = 'leer_servicio';

if (!usuarioTienePermiso($usuarioId, $permisoRequerido, $conn)) {
    die("❌ Acceso denegado: No tienes permiso para ver esta página.");
}
$usuarioPuedeEditar = usuarioTienePermiso($_SESSION['cedula'], 'actualizar_servicio', $conn);
$usuarioPuedeEliminar = usuarioTienePermiso($_SESSION['cedula'], 'eliminar_servicio', $conn);

$mostrarAcciones = $usuarioPuedeEditar || $usuarioPuedeEliminar;

// Verificar si el usuario es técnico (rol 2)
$es_tecnico = false;

// Modificar la consulta para usar la tabla correcta
$sql_rol = "SELECT Rol_id as rol FROM empleados WHERE Cedula_Empleado_id = ?";
$stmt_rol = $conn->prepare($sql_rol);

if ($stmt_rol) {
    $stmt_rol->bind_param("s", $usuarioId);
    $stmt_rol->execute();
    $resultado_rol = $stmt_rol->get_result();
    
    if ($fila_rol = $resultado_rol->fetch_assoc()) {
        $es_tecnico = ($fila_rol['rol'] == 2);
    }
    $stmt_rol->close();
} else {
    // Error al preparar la consulta - mostrar mensaje de error para depuración
    echo "Error preparando la consulta: " . $conn->error;
}

// Inicializar variables de filtro
$filtro_cedula = isset($_GET['cedula']) ? $_GET['cedula'] : '';
// Si es técnico, forzar el filtro a su propia cédula
if ($es_tecnico) {
    $filtro_cedula = $usuarioId;
}
$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$filtro_placa = isset($_GET['placa']) ? $_GET['placa'] : '';
$filtro_servicio = isset($_GET['servicio']) ? $_GET['servicio'] : '';
$filtro_municipio = isset($_GET['municipio']) ? $_GET['municipio'] : '';

// Obtener la lista de empleados para el filtro
$sql_empleados = "SELECT e.Cedula_Empleado_id, CONCAT(e.Nombre, ' ', e.Apellido) as nombre_completo 
                 FROM empleados e";
$result_empleados = $conn->query($sql_empleados);
$empleados_lista = $result_empleados->fetch_all(MYSQLI_ASSOC);

$sql = "
    SELECT
        sr.Servicio_Realizado_id,
        sr.Cedula_Empleado_id_Servicios_Realizados,
        CONCAT(e.Nombre, ' ', e.Apellido) as nombre_tecnico,
        sr.Vehiculo_id_Servicios_Realizados,
        s.Nombre_Servicio,
        sr.Fecha,
        m.nombre as nombre_municipio,
        sr.Ubicación,
        sr.Novedades,
        sr.Fotos,
        sr.Detalle_Servicio,
        sr.Custodia_Servicio
    FROM servicios_realizados sr
    JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
    JOIN municipios m ON sr.municipio = m.id
    LEFT JOIN empleados e ON sr.Cedula_Empleado_id_Servicios_Realizados = e.Cedula_Empleado_id
    WHERE 1 = 1
";

// Para técnicos, siempre filtrar por su propia cédula
if ($es_tecnico) {
    $sql .= " AND sr.Cedula_Empleado_id_Servicios_Realizados = '" . $usuarioId . "'";
} 
// Para otros roles, aplicar filtro de cédula solo si se ha proporcionado
else if (!empty($filtro_cedula)) {
    $sql .= " AND sr.Cedula_Empleado_id_Servicios_Realizados = '" . $filtro_cedula . "'";
}

// Aplicar otros filtros si se han proporcionado
if (!empty($filtro_fecha)) {
    $sql .= " AND sr.Fecha = '" . $filtro_fecha . "'";
}
if (!empty($filtro_placa)) {
    $sql .= " AND sr.Vehiculo_id_Servicios_Realizados LIKE '%" . $filtro_placa . "%'";
}
if (!empty($filtro_servicio)) {
    $sql .= " AND s.Servicio_id = " . $filtro_servicio;
}
if (!empty($filtro_municipio)) {
    $sql .= " AND m.id = " . $filtro_municipio;
}

// Configuración de paginación
$registros_por_pagina = 10; // Cantidad de servicios por página
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Primero obtener el total de registros para calcular las páginas
$sql_count = "
    SELECT COUNT(*) as total
    FROM servicios_realizados sr
    JOIN servicios s ON sr.Servicio_id_Servicios_Realizados = s.Servicio_id
    JOIN municipios m ON sr.municipio = m.id
    LEFT JOIN empleados e ON sr.Cedula_Empleado_id_Servicios_Realizados = e.Cedula_Empleado_id
    WHERE 1 = 1
";

// Aplicar los mismos filtros a la consulta de conteo
if ($es_tecnico) {
    $sql_count .= " AND sr.Cedula_Empleado_id_Servicios_Realizados = '" . $usuarioId . "'";
} else if (!empty($filtro_cedula)) {
    $sql_count .= " AND sr.Cedula_Empleado_id_Servicios_Realizados = '" . $filtro_cedula . "'";
}

if (!empty($filtro_fecha)) {
    $sql_count .= " AND sr.Fecha = '" . $filtro_fecha . "'";
}
if (!empty($filtro_placa)) {
    $sql_count .= " AND sr.Vehiculo_id_Servicios_Realizados LIKE '%" . $filtro_placa . "%'";
}
if (!empty($filtro_servicio)) {
    $sql_count .= " AND s.Servicio_id = " . $filtro_servicio;
}
if (!empty($filtro_municipio)) {
    $sql_count .= " AND m.id = " . $filtro_municipio;
}

$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_registros = $row_count['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Añadir límite y offset a la consulta principal
$sql .= " ORDER BY sr.Servicio_Realizado_id DESC LIMIT $registros_por_pagina OFFSET $offset";

// Función para construir las URLs de paginación
function construirUrlPaginacion($pagina) {
    global $filtro_cedula, $filtro_fecha, $filtro_placa, $filtro_servicio, $filtro_municipio;
    
    $url = $_SERVER['PHP_SELF'] . '?pagina=' . $pagina;
    
    if (!empty($filtro_cedula)) $url .= '&cedula=' . $filtro_cedula;
    if (!empty($filtro_fecha)) $url .= '&fecha=' . $filtro_fecha;
    if (!empty($filtro_placa)) $url .= '&placa=' . $filtro_placa;
    if (!empty($filtro_servicio)) $url .= '&servicio=' . $filtro_servicio;
    if (!empty($filtro_municipio)) $url .= '&municipio=' . $filtro_municipio;
    
    return $url;
}

$result = $conn->query($sql);

// Obtener la lista de servicios para el filtro
$sql_servicios = "SELECT Servicio_id, Nombre_Servicio FROM servicios";
$result_servicios = $conn->query($sql_servicios);
$servicios = $result_servicios->fetch_all(MYSQLI_ASSOC);

// Obtener la lista de municipios para el filtro
$sql_municipios = "SELECT id, nombre FROM municipios";
$result_municipios = $conn->query($sql_municipios);
$municipios_lista = $result_municipios->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta General</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/ConsultaGeneral.jpg');
            background-size: auto; /* Cambiado de "cover" a "auto" para mantener tamaño original */
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Mantiene la imagen fija mientras se desplaza */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 170px;
        }

        .main-content {
            padding: 20px;
            margin-top: 20px;
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
            overflow: auto;
        }

        h2 {
            text-align: center;
            color: #680c39;
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
        }

        .filter-form {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            justify-content: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 150px;
            max-width: 200px;
            flex: 1;
        }

        .filter-form label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .filter-form input[type="text"],
        .filter-form input[type="date"],
        .filter-form select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        .filter-actions {
            display: flex;
            align-items: flex-end;
            margin-top: 20px;
        }

        @media (max-width: 992px) {
            .filter-group {
                min-width: 120px;
            }
        }

        @media (max-width: 768px) {
            .filter-group {
                min-width: 45%;
            }
        }

        @media (max-width: 576px) {
            .filter-group {
                min-width: 100%;
            }
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

        th,
        td {
            padding: 12px;
            text-align: center;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td img {
            width: 200px;
            height: 140px;
            object-fit: cover;
            border-radius: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }

        .btn-success,
        .btn-warning,
        .btn-danger {
            margin: 5px;
            padding: 8px 15px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .details-row {
            background-color: #f9f9f9;
            font-size: 0.9em;
        }

        .pagination-container {
            margin: 20px 0;
        }

        .pagination .page-item.active .page-link {
            background-color: #680c39;
            border-color: #680c39;
            color: white;
        }

        .pagination .page-link {
            color: #680c39;
            border-color: #ddd;
        }

        .pagination .page-link:hover {
            background-color: #f8f9fa;
            color: #4a0b29;
            border-color: #ddd;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
        }
    </style>
</head>

<body>

    <div class="main-content">
        <div class="container">
            <h2>Servicios Realizados</h2>

            <form class="filter-form" action="" method="GET" id="filterForm">
                <?php if (!$es_tecnico): ?>
                <div class="filter-group">
                    <label for="cedula">Técnico:</label>
                    <select  data-no-warning name="cedula" id="cedula" class="auto-submit">
                        <option value="">Seleccione...</option>
                        <?php foreach ($empleados_lista as $emp): ?>
                            <option value="<?= $emp['Cedula_Empleado_id']; ?>" <?= ($emp['Cedula_Empleado_id'] == $filtro_cedula ? 'selected' : '') ?>>
                                <?= htmlspecialchars($emp['nombre_completo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="filter-group">
                    <label for="fecha">Fecha:</label>
                    <input data-no-warning type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($filtro_fecha); ?>" class="auto-submit">
                </div>

                <div class="filter-group">
                    <label for="placa">Placa:</label>
                    <input data-no-warning type="text" id="placa" name="placa" value="<?= htmlspecialchars($filtro_placa); ?>" placeholder="Ej. ABC123" class="auto-submit">
                </div>

                <div class="filter-group">
                    <label for="servicio">Servicio:</label>
                    <select data-no-warning name="servicio" id="servicio" class="auto-submit">
                        <option value="">Seleccione...</option>
                        <?php foreach ($servicios as $s): ?>
                            <option value="<?= $s['Servicio_id']; ?>" <?= ($s['Servicio_id'] == $filtro_servicio ? 'selected' : '') ?>>
                                <?= htmlspecialchars($s['Nombre_Servicio']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="municipio">Municipio:</label>
                    <select data-no-warning name="municipio" id="municipio" class="auto-submit">
                        <option value="">Seleccione...</option>
                        <?php foreach ($municipios_lista as $m): ?>
                            <option value="<?= $m['id']; ?>" <?= ($m['id'] == $filtro_municipio ? 'selected' : '') ?>>
                                <?= htmlspecialchars($m['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group filter-actions">
                    <a href="consulta_general.php" class="btn btn-secondary" data-no-warning>Limpiar</a>
                </div>
            </form>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Técnico</th>
                        <th>Placa</th>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Municipio</th>
                        <th>Detalle</th>
                        <?php if ($mostrarAcciones) { ?>
                            <th>Acciones</th> <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-" . $row['Servicio_Realizado_id'] . "'>";
                            echo "<td>" . $row['Servicio_Realizado_id'] . "</td>";
                            echo "<td>" . ($row['nombre_tecnico'] ?? 'No asignado') . "</td>";
                            echo "<td>" . $row['Vehiculo_id_Servicios_Realizados'] . "</td>";
                            echo "<td>" . $row['Nombre_Servicio'] . "</td>";
                            echo "<td>" . $row['Fecha'] . "</td>";
                            echo "<td>" . $row['nombre_municipio'] . "</td>";
                            echo "<td>";
                            echo "<button class='btn btn-info btn-sm' onclick='toggleDetails(" . $row['Servicio_Realizado_id'] . ")'>Ver más</button>";
                            echo "</td>";

                            if ($mostrarAcciones) {
                                echo "<td class='action-buttons'>";
                                if ($usuarioPuedeEditar) {
                                    echo "<a href='editar_servicio_realizado.php?id=" . $row['Servicio_Realizado_id'] . "' class='btn btn-warning btn-sm' data-no-warning><i class='fas fa-edit'></i> Editar</a> ";
                                }
                                if ($usuarioPuedeEliminar) {
                                    echo "<button onclick='confirmDelete(" . $row['Servicio_Realizado_id'] . ")' class='btn btn-danger btn-sm' data-no-warning><i class='fas fa-trash-alt'></i> Eliminar</button>";
                                }
                                echo "</td>";
                            }

                            echo "</tr>";

                            echo "<tr id='details-" . $row['Servicio_Realizado_id'] . "' class='details-row' style='display:none;'>";
                            echo "<td colspan='4'>";
                            echo "<strong>Empleado:</strong> " . ($row['nombre_tecnico'] ?? 'No asignado') . " (" . ($row['Cedula_Empleado_id_Servicios_Realizados'] ?? 'N/A') . ")<br>";
                            echo "<strong>Vehículo:</strong> " . $row['Vehiculo_id_Servicios_Realizados'] . "<br>";
                            echo "<strong>Dirección:</strong> " . $row['Ubicación'] . "<br>";
                            echo "<strong>Novedades:</strong> " . $row['Novedades'] . "<br>";
                            echo "<strong>Detalle del servicio:</strong> " . $row['Detalle_Servicio'] . "<br>";
                            echo "<strong>Custodia:</strong> " . $row['Custodia_Servicio'] . "<br>";
                            echo "</td>";
                            echo "<td colspan='".($mostrarAcciones ? '4' : '3')."'>";
                            echo "<strong>Foto:</strong><br>";
                            if (!empty($row['Fotos'])) {
                                echo "<img src='" . $row['Fotos'] . "' alt='Foto' style='max-width: 200px; max-height: 200px;'><br>";
                            } else {
                                echo "<p>No hay foto disponible</p>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='".($mostrarAcciones ? '8' : '7')."'>No se encontraron registros</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="text-center mt-4">
                <?php
                if (usuarioTienePermiso($_SESSION['cedula'], 'crear_servicio', $conn)) {
                    ?>
                    <button onclick="window.location.href='crear_servicio_realizado.php'" data-no-warning
                        class="btn btn-success">
                        <i class="fas fa-plus"></i> Crear Servicio
                    </button>
                <?php
                }
                ?>
            </div>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
            <div class="paginacion-container mt-4">
                <nav aria-label="Navegación de páginas">
                    <ul class="pagination justify-content-center">
                        <!-- Botón Anterior -->
                        <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= construirUrlPaginacion($pagina_actual - 1) ?>" data-no-warning aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Anterior</span>
                            </a>
                        </li>
                        
                        <?php
                        // Calcular rango de páginas a mostrar
                        $rango = 2; // Mostrar 2 páginas antes y después de la actual
                        $inicio_rango = max(1, $pagina_actual - $rango);
                        $fin_rango = min($total_paginas, $pagina_actual + $rango);
                        
                        // Mostrar primera página si no está en el rango
                        if ($inicio_rango > 1) {
                            echo '<li class="page-item"><a class="page-link" href="' . construirUrlPaginacion(1) . '" data-no-warning>1</a></li>';
                            if ($inicio_rango > 2) {
                                echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                            }
                        }
                        
                        // Mostrar páginas en el rango
                        for ($i = $inicio_rango; $i <= $fin_rango; $i++) {
                            echo '<li class="page-item ' . ($i == $pagina_actual ? 'active' : '') . '">
                                    <a class="page-link" href="' . construirUrlPaginacion($i) . '" data-no-warning>' . $i . '</a>
                                  </li>';
                        }
                        
                        // Mostrar última página si no está en el rango
                        if ($fin_rango < $total_paginas) {
                            if ($fin_rango < $total_paginas - 1) {
                                echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="' . construirUrlPaginacion($total_paginas) . '" data-no-warning>' . $total_paginas . '</a></li>';
                        }
                        ?>
                        
                        <!-- Botón Siguiente -->
                        <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= construirUrlPaginacion($pagina_actual + 1) ?>" data-no-warning aria-label="Siguiente">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Siguiente</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="text-center text-muted small">
                    Mostrando <?= min(($pagina_actual - 1) * $registros_por_pagina + 1, $total_registros) ?> 
                    a <?= min($pagina_actual * $registros_por_pagina, $total_registros) ?> 
                    de <?= $total_registros ?> registros
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="js/session-check.js"></script>
    <script>
        function toggleDetails(id) {
            var detailsRow = document.getElementById('details-' + id);
            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = 'table-row';
            } else {
                detailsRow.style.display = 'none';
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                didOpen: () => {
                    document.querySelector('.swal2-confirm').setAttribute('data-no-warning', '');
                    document.querySelector('.swal2-cancel').setAttribute('data-no-warning', '');
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`delete.php?id=${id}`, {
                        method: 'GET'
                    })
                        .then(response => response.text())
                        .then(data => {
                            if (data === 'success') {
                                Swal.fire({
                                    title: 'Eliminado',
                                    text: 'El registro ha sido eliminado exitosamente.',
                                    icon: 'success',
                                    didOpen: () => {
                                        document.querySelector('.swal2-confirm').setAttribute('data-no-warning', '');
                                    }
                                }).then(() => {
                                    const row = document.getElementById(`row-${id}`);
                                    if (row) {
                                        row.remove();
                                    }
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    'Hubo un problema al eliminar el registro.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error',
                                'Ocurrió un error inesperado.',
                                'error'
                            );
                        });
                }
            });
        }

        // Script para enviar el formulario automáticamente al cambiar los filtros
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los elementos con la clase auto-submit
            const autoSubmitElements = document.querySelectorAll('.auto-submit');
            
            // Agregar listener de eventos para cada elemento
            autoSubmitElements.forEach(element => {
                element.addEventListener('change', function() {
                    // Pequeño retraso para mejor experiencia de usuario
                    setTimeout(() => {
                        document.getElementById('filterForm').submit();
                    }, 300);
                });
            });
            
            // Para el campo de texto de placa, aplicar un retraso después de que el usuario deje de escribir
            const placaInput = document.getElementById('placa');
            let typingTimer;
            
            placaInput.addEventListener('input', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 1000); // Esperar 1 segundo después de que el usuario deje de escribir
            });
        });
    </script>
</body>
</html>