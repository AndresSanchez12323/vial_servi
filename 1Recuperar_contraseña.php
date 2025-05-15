<?php
require_once 'config.php'; // Asegúrate que aquí está tu conexión $conn

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'] ?? '';
    $nuevaContrasena = $_POST['nueva_contrasena'] ?? '';

    if ($cedula && $nuevaContrasena) {
        // Validar que el usuario exista
        $stmt = $conn->prepare("SELECT * FROM empleados WHERE Cedula_Empleado_id = ?");
        $stmt->bind_param("i", $cedula);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $hash = password_hash($nuevaContrasena, PASSWORD_BCRYPT);

            $update = $conn->prepare("UPDATE empleados SET Contraseña = ? WHERE Cedula_Empleado_id = ?");
            $update->bind_param("si", $hash, $cedula);

            if ($update->execute()) {
                $mensaje = "✅ Contraseña actualizada correctamente.";
            } else {
                $mensaje = "❌ Error al actualizar la contraseña.";
            }
        } else {
            $mensaje = "❌ No se encontró un usuario con esa cédula.";
        }
    } else {
        $mensaje = "❌ Debe ingresar cédula y nueva contraseña.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/CrearServicioRealizadoCliente.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        .title {
            font-size: 2em;
            color: #fff;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 350px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button {
            padding: 10px;
            width: 100%;
            background-color: #2c7;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .mensaje {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div>
        <h2 class="title" >Cambiar Contraseña</h2>

        <form method="POST">
            <label>Cédula del usuario</label>
            <input type="number" name="cedula" required>

            <label>Nueva contraseña</label>
            <input type="password" name="nueva_contrasena" required>

            <button type="submit">Actualizar Contraseña</button>

            <?php if ($mensaje): ?>
                <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>
        </form>
    </div>

</body>

</html>