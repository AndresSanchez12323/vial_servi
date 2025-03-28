<?php
include('validPermissions.php');
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
$sql = "SELECT * FROM servicios_realizados"; 
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

        .sidebar {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: rgba(100, 67, 67, 0.7);
            padding: 15px 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            padding: 12px 25px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            background-color: #2d0f2a;
            margin-right: 15px;
            border-radius: 50px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        .logo-container {
            margin-left: 30px;
            border-radius: 50%;
            width: 90px;
            height: 90px;
            overflow: hidden;
        }

        .logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
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

<!-- Barra superior -->
<div id="mySidebar" class="sidebar">
    <div class="logo-container">
        <img src="Imagenes/Logo.jpg" alt="Logo" class="logo">
    </div>
    <div>
         <a href="dashboard.php" data-no-warning>Dashboard</a>
         <a href="consulta_general.php" data-no-warning>Consulta General</a>
         <a href="consulta_identificacion.php" data-no-warning>Consulta por Identificación</a>
         <a href="logout.php" data-no-warning>Cerrar Sesión</a>
     </div>
</div>

<!-- Contenido principal -->
<div class="main-content">
    <div class="container">
        <h2>Servicios Realizados</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cédula Empleado</th>
                    <th>Placa Vehículo</th>
                    <th>Servicio</th>
                    <th>Fecha</th>
                    <th>Ubicación</th>
                    <th>Novedades</th>
                    <th>Fotos</th>
                    <th>Detalle</th>
                    <th>Custodia</th>
                    <th>Facturación Separada</th>
                    <?php if ($mostrarAcciones) { ?>
                        <th>Acciones</th> <!-- Solo se muestra si el usuario tiene permisos -->
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Servicio_Realizado_id'] . "</td>";
                        echo "<td>" . $row['Cedula_Empleado_id_Servicios_Realizados'] . "</td>";
                        echo "<td>" . $row['Vehiculo_id_Servicios_Realizados'] . "</td>";
                        echo "<td>" . $row['Servicio_id_Servicios_Realizados'] . "</td>";
                        echo "<td>" . $row['Fecha'] . "</td>";
                        echo "<td>" . $row['Ubicación'] . "</td>";
                        echo "<td>" . $row['Novedades'] . "</td>";
                        echo "<td>" . $row['Fotos'] . "</td>";
                        echo "<td>" . $row['Detalle_Servicio'] . "</td>";
                        echo "<td>" . $row['Custodia_Servicio'] . "</td>";
                        echo "<td>" . ($row['Facturación_Separada'] ? 'Sí' : 'No') . "</td>";
                        if ($mostrarAcciones) {
                            echo "<td class='action-buttons'>";
                            if ($usuarioPuedeEditar) {
                                echo "<a href='editar_servicio_realizado.php?id=" . $row['Servicio_Realizado_id'] . "' class='btn btn-warning' data-no-warning><i class='fas fa-edit'></i> Editar</a> ";
                            }
                            if ($usuarioPuedeEliminar) {
                                echo "<button onclick='confirmDelete(" . $row['Servicio_Realizado_id'] . ")' class='btn btn-danger' data-no-warning><i class='fas fa-trash-alt'></i> Eliminar</button>";
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
                <button onclick="window.location.href='crear_servicio_realizado.php'" data-no-warning class="btn btn-success">
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
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`delete.php?id=${id}`, {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        Swal.fire(
                            'Eliminado',
                            'El registro ha sido eliminado exitosamente.',
                            'success'
                        ).then(() => {
                            location.reload();
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

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>











