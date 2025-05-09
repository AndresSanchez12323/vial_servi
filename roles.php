<?php
session_start();
require_once 'config.php';

// Verificar si el usuario tiene permiso para ver roles
if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] != 0) {
    die("Acceso denegado.");
}

// Obtener los roles desde la base de datos
$sql = "SELECT id, nombre, descripcion FROM roles";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('Imagenes/Login.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 900px;
            margin: 120px auto 40px;
            text-align: center;
        }

        h2 {
            background-color: #2d0f2a;
            color: white;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #2d0f2a;
            color: white;
            padding: 12px;
            text-align: center;
        }

        td {
            padding: 12px;
            text-align: center;
            background-color: #fff;
        }

        tr:nth-child(even) td {
            background-color: #f8f8f8;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f39c12;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 10px 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background-color: #d68910;
            transform: translateY(-2px);
        }

        .btn-red {
            background-color: #e74c3c;
        }

        .btn-red:hover {
            background-color: #c0392b;
        }

        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2d0f2a;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px auto;
        }

        .btn-back:hover {
            background-color: #440f33;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            font-weight: bold;
        }

        .btn-purple {
            background-color: #27ae60;
            /* morado oscuro */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            margin: 10px 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-purple:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Administración de Roles</h2>
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert-success" id="alert-message">
                <?php echo htmlspecialchars($_SESSION['msg']); ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acción</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                    <td>
                        <a href="editar_rol.php?id=<?php echo $row['id']; ?>" class="btn">Editar</a>
                        <a href="eliminar_rol.php?id=<?php echo $row['id']; ?>" class="btn btn-red">Eliminar</a>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <a href="add_role.php" class="btn btn-purple">Agregar Nuevo Rol</a>
        <a href="manage_roles.php" class="btn btn-purple">Agregar Permisos</a>

        <br>
        <a href="administrador.php" class="btn-back">Volver</a>
    </div>
</body>

<script>
    setTimeout(() => {
        const alert = document.getElementById('alert-message');
        if (alert) {
            alert.style.transition = 'opacity 1s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 1000); // quita el div del DOM
        }
    }, 3000); // se oculta después de 3 segundos
</script>

</html>