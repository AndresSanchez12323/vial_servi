<?php
session_start();
include('validPermissions.php');
include('header.php');

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

$sql = "SELECT Servicio_id, Nombre_Servicio, Descripción FROM servicios";
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

        .btn-success, .btn-warning, .btn-danger {
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
    </style>
</head>
<body>

<!-- Contenido principal -->
<div class="main-content">
    <div class="container">
        <h2>Tabla De Servicios</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Servicio</th>
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
                        echo "<tr id='row-" . $row['Servicio_id'] . "'>";
                        echo "<td>" . $row['Servicio_id'] . "</td>";
                        echo "<td>" . $row['Nombre_Servicio'] . "</td>";
                        echo "<td>" . $row['Descripción'] . "</td>";
                        if ($mostrarAcciones) {
                            echo "<td class='action-buttons'>";
                            if ($usuarioPuedeEditar) {
                                echo "<a href='edit_service.php?id=" . $row['Servicio_id'] . "' class='btn btn-warning' data-no-warning><i class='fas fa-edit'></i> Editar</a> ";
                            }
                            if ($usuarioPuedeEliminar) {
                                echo "<button onclick='confirmDelete(" . $row['Servicio_id'] . ")' class='btn btn-danger' data-no-warning><i class='fas fa-trash-alt'></i> Eliminar</button>";
                            }
                            echo "</td>";
                        }
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
                <button onclick="window.location.href='crear_servicio.php'" data-no-warning class="btn btn-success">
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
                fetch(`delete_service.php?id=${id}`, {
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
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo eliminar el registro. Puede haber un error o existen servicios en curso que están utilizando este registro, lo que impide su eliminación.',
                        icon: 'error',
                        didOpen: () => {
                            document.querySelector('.swal2-confirm').setAttribute('data-no-warning', '');
                        }
                    });
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

<script src="js/session-check.js"></script>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
