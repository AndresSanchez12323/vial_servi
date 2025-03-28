<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] != 0) {
    header("Location: index.php");
    exit();
}

$sqlUsuarios = "
    SELECT e.Cedula_Empleado_id, e.Nombre, e.Apellido, e.Rol_id, r.nombre AS rol_nombre, 
           GROUP_CONCAT(p.nombre SEPARATOR ', ') AS permisos
    FROM empleados e
    LEFT JOIN roles r ON e.Rol_id = r.id
    LEFT JOIN rol_permisos rp ON r.id = rp.rol_id
    LEFT JOIN permisos p ON rp.permiso_id = p.id
    GROUP BY e.Cedula_Empleado_id, e.Nombre, e.Apellido, r.nombre
";

$resultUsuarios = $conn->query($sqlUsuarios);
$sql = "SELECT id, nombre FROM roles";
$result = $conn->query($sql);

$rolesDisponibles = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rolesDisponibles[$row['id']] = $row['nombre'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['usuario_id']) && isset($_POST['nuevo_rol'])) {
        $usuario_id = $_POST['usuario_id'];
        $nuevo_rol = $_POST['nuevo_rol'];
        
        $sqlActualizar = "UPDATE empleados SET Rol_id = ? WHERE Cedula_Empleado_id = ?";
        $stmt = $conn->prepare($sqlActualizar);
        $stmt->bind_param("si", $nuevo_rol, $usuario_id);
        $stmt->execute();
        
        $sqlRegistro = "INSERT INTO actividad (usuario_id, accion) VALUES (?, 'Cambio de rol')";
        $stmt = $conn->prepare($sqlRegistro);
        $stmt->bind_param("i", $_SESSION['cedula']);
        $stmt->execute();
    }
    
    if (isset($_POST['usuario_id']) && isset($_POST['permisos'])) {
        $usuario_id = $_POST['usuario_id'];
        $permisosSeleccionados = $_POST['permisos'];
        
        $sqlBorrarPermisos = "DELETE FROM usuario_permisos WHERE usuario_id = ?";
        $stmt = $conn->prepare($sqlBorrarPermisos);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        
        foreach ($permisosSeleccionados as $permiso_id) {
            $sqlInsertPermiso = "INSERT INTO usuario_permisos (usuario_id, permiso_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sqlInsertPermiso);
            $stmt->bind_param("ii", $usuario_id, $permiso_id);
            $stmt->execute();
        }
    }
    
    if (isset($_POST['eliminar_usuario'])) {
        $usuario_id = $_POST['eliminar_usuario'];
        $sqlEliminar = "DELETE FROM empleados WHERE Cedula_Empleado_id = ?";
        $stmt = $conn->prepare($sqlEliminar);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
    }
    
    if (isset($_POST['nuevo_nombre']) && isset($_POST['nuevo_apellido']) && isset($_POST['nuevo_cedula']) && isset($_POST['nuevo_rol'])) {
        $nombre = $_POST['nuevo_nombre'];
        $apellido = $_POST['nuevo_apellido'];
        $cedula = $_POST['nuevo_cedula'];
        $rol = $_POST['nuevo_rol'];
        $password = password_hash("123456", PASSWORD_DEFAULT);
        
        $sqlInsertar = "INSERT INTO empleados (Cedula_Empleado_id, Nombre, Apellido, Contraseña, Rol_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlInsertar);
        $stmt->bind_param("isssi", $cedula, $nombre, $apellido, $password, $rol);
        $stmt->execute();
    }
    
    echo "<script>alert('Cambios guardados correctamente.'); window.location.href='administrador.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Estilos -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/Administrador.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            text-align: center;
            height: 80%;
        }

        .container_scroll {
            height: 100%;
            overflow: auto;
        }

        h2, h3 {
            color: #680c39;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            font-size: 16px;
            color: #333;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #2d0f2a;
            background-color: #fff;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #2d0f2a;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        button:active {
            background-color: #680c39;
            transform: translateY(0);
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 20px;
        }

        th {
            background-color: #2d0f2a;
            color: white;
            font-weight: bold;
            padding: 12px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-weight: 500;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        button.delete {
            background-color: #dc3545;
        }

        button.delete:hover {
            background-color: #b02a37;
        }

        #buscarUsuario {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
            background-color: #f9f9f9;
        }

        #buscarUsuario:focus {
            border-color: #2d0f2a;
            background-color: #fff;
        }

        .logout {
            margin-top: 20px;
        }

        .logout a {
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            color: white;
            background-color: #680c39;
            padding: 12px 20px;
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .logout a:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }
    </style>

    <script>
        function filtrarUsuarios() {
            let input = document.getElementById("buscarUsuario");
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll("table tr");

            rows.forEach((row, index) => {
                if (index === 0) return; // Saltar encabezados
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        }
    </script>
</head>
<body>

<div class="container">
    <main class="container_scroll">
        <button onclick="window.location.href='roles.php'">
            <i class="fas fa-users-cog"></i> Gestionar Roles
        </button>
    
        <h3>Agregar Nuevo Usuario</h3>
        <form method="POST">
            <div class="form">
                <input type="text" name="nuevo_nombre" placeholder="Nombre" required>
                <input type="text" name="nuevo_apellido" placeholder="Apellido" required>
                <input type="number" name="nuevo_cedula" placeholder="Cédula" required>
                <select name="nuevo_rol">
                    <?php foreach ($rolesDisponibles as $id => $nombre) { ?>
                        <option value="<?php echo $id; ?>"><?php echo $nombre; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit">Agregar Usuario</button>
        </form>

        <br>                
        <h2>Administración de Usuarios</h2>
        <input type="text" id="buscarUsuario" placeholder="Buscar usuario..." onkeyup="filtrarUsuarios()">
    
        <div class="table-container">
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Rol</th>
                    <th>Permisos</th>
                    <th>Acción</th>
                </tr>
                <?php while ($usuario = $resultUsuarios->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $usuario['Nombre']; ?></td>
                    <td><?php echo $usuario['Apellido']; ?></td>
                    <td><?php echo $usuario['rol_nombre']; ?></td>
                    <td><?php echo $usuario['Rol_id'] == 0 ? 'Control de usuarios y roles' : ($usuario['permisos'] ? $usuario['permisos'] : 'No asignados'); ?></td>
                    <td>
                        <?php if ($usuario['Rol_id'] != 0) { ?>
                            <form method="POST">
                                <input type="hidden" name="eliminar_usuario" value="<?php echo $usuario['Cedula_Empleado_id']; ?>">
                                <button type="submit" class="delete">Eliminar</button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    
        <div class="logout">
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </main>
</div>

</body>
</html>

