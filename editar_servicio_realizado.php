<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}


// Verificar si se ha enviado un ID válido para editar
if (isset($_GET['id'])) {
    // Obtener datos del servicio
    $id = $_GET['id'];
    $sql = "SELECT * FROM servicios_realizados WHERE Servicio_Realizado_id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $servicio = $result->fetch_assoc();
    } else {
        echo "Registro no encontrado.";
        exit;
    }

    // Obtener empleados
    $empleados = [];
    $query = "SELECT Cedula_Empleado_id, Nombre, Apellido FROM empleados WHERE Rol_id = 2";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $empleados[] = $row;
        }
    }

    // Obtener municipios
    $municipality = [];
    $query = "SELECT id, nombre FROM municipios";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $municipality[] = $row;
        }
    }
}

// Procesar el formulario al enviarlo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedulaEmpleado = $_POST['Cedula_Empleado_id_Servicios_Realizados'];
    $fecha = $_POST['Fecha'];
    $municipality = $_POST['municipality'];
    $ubicacion = $_POST['Ubicacion'];
    $novedades = $_POST['Novedades'];
    $detalleServicio = $_POST['Detalle_Servicio'];
    $custodiaServicio = $_POST['Custodia_Servicio'];

    // Actualizar el registro en la base de datos
    $stmt = $conn->prepare("UPDATE servicios_realizados SET Cedula_Empleado_id_Servicios_Realizados=?, Fecha=?, municipio=?, Ubicación=?, Novedades=?, Detalle_Servicio=?, Custodia_Servicio=? WHERE Servicio_Realizado_id=?");
    $stmt->bind_param("sssssssi", $cedulaEmpleado, $fecha, $municipality, $ubicacion, $novedades, $detalleServicio, $custodiaServicio, $id);
    
    if ($stmt->execute()) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Éxito',
                    text: 'Registro actualizado exitosamente',
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#ff6b6b',
                    didOpen: () => {
                        document.querySelector('.swal2-confirm').setAttribute('data-no-warning', '');
                        window.location.href = 'consulta_general.php';
                    }
                })
            });
        </script>";
    } else {
        echo "Error al actualizar el registro: " . $stmt->error;
    }
}

$conn->close();
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
            background-image: url('Imagenes/EditarServicioRealizado.jpg');
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
     </div>
</div>

<div class="container mt-5">
    <h2>Editar Servicio Realizado</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="Cedula_Empleado_id_Servicios_Realizados">Técnico asignado</label>
            <select name="Cedula_Empleado_id_Servicios_Realizados" class="form-control" required>
                <option value="">Seleccione un empleado</option>
                <?php foreach ($empleados as $empleado): ?>
                    <option value="<?= $empleado['Cedula_Empleado_id'] ?>"
                        <?= ($empleado['Cedula_Empleado_id'] == $servicio['Cedula_Empleado_id_Servicios_Realizados']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($empleado['Cedula_Empleado_id']) ?> - <?= htmlspecialchars($empleado['Nombre']) ?> <?= htmlspecialchars($empleado['Apellido']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="Fecha">Fecha</label>
            <input type="date" name="Fecha" class="form-control" value="<?php echo $servicio['Fecha']; ?>" required>
        </div>
        <div class="form-group">
            <label for="municipality">Municipio</label>
            <select name="municipality" class="form-control" required>
                <option value="">SSeleccione un municipio</option>
                <?php foreach ($municipality as $municipality): ?>
                    <option value="<?= $municipality['id'] ?>"
                        <?= ($municipality['id'] == $servicio['municipio']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($municipality['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="Ubicacion">Ubicación</label>
            <input type="text" name="Ubicacion" class="form-control" value="<?php echo $servicio['Ubicación']; ?>" required>
        </div>
        <div class="form-group">
            <label for="Novedades">Novedades</label>
            <input type="text" name="Novedades" class="form-control" value="<?php echo $servicio['Novedades']; ?>">
        </div>
        <div class="form-group">
            <label for="Detalle_Servicio">Detalle del Servicio</label>
            <input type="text" name="Detalle_Servicio" class="form-control" value="<?php echo $servicio['Detalle_Servicio']; ?>">
        </div>
        <div class="form-group">
            <label for="Custodia_Servicio">Custodia del Servicio</label>
            <input type="text" name="Custodia_Servicio" class="form-control" value="<?php echo $servicio['Custodia_Servicio']; ?>">
        </div>
        <button type="button" onclick="window.location.href='consulta_general.php'" class="btn btn-danger mt-3" data-no-warning>Cancelar</button>
        <button type="submit" class="btn btn-primary mt-3" data-no-warning>Actualizar Registro</button>
    </form>
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

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
