<?php
include('validPermissions.php');
include('header.php');

session_start();
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
$sql = "
    SELECT 
        sr.Servicio_Realizado_id,
        sr.Cedula_Empleado_id_Servicios_Realizados,
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
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta General</title>

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
            background-image: url('Imagenes/ConsultaGeneral.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 120px;
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
            overflow: auto;
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
    </style>
</head>

<body>

    <!-- Contenido principal -->
    <div class="main-content">
        <div class="container">
            <h2>Servicios Realizados</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Placa</th>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Municipio</th>
                        <th>Detalle</th>
                        <?php if ($mostrarAcciones) { ?>
                            <th>Acciones</th> <!-- Solo se muestra si el usuario tiene permisos -->
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-" . $row['Servicio_Realizado_id'] . "'>";
                            echo "<td>" . $row['Servicio_Realizado_id'] . "</td>";
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
                            echo "<strong>Empleado:</strong> " . $row['Cedula_Empleado_id_Servicios_Realizados'] . "<br>";
                            echo "<strong>Vehículo:</strong> " . $row['Vehiculo_id_Servicios_Realizados'] . "<br>";
                            echo "<strong>Ubicación:</strong> " . $row['Ubicación'] . "<br>";
                            echo "<strong>Novedades:</strong> " . $row['Novedades'] . "<br>";
                            echo "<strong>Detalle del servicio:</strong> " . $row['Detalle_Servicio'] . "<br>";
                            echo "<strong>Custodia:</strong> " . $row['Custodia_Servicio'] . "<br>";
                            echo "</td>";
                            echo "<td colspan='3'>";
                            echo "<strong>Foto:</strong><br><img src='" . $row['Fotos'] . "' alt='Foto' style='max-width: 200px; max-height: 200px;'><br>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12'>No se encontraron registros</td></tr>";
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
        </div>
    </div>

    <!-- JavaScript para eliminar con confirmación usando SweetAlert2 -->
    <script>

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
    </script>

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

    <script>
        function toggleDetails(id) {
            const row = document.getElementById('details-' + id);
            if (row.style.display === 'none') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    </script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>