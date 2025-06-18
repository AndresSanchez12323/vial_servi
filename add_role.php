<?php
session_start();
require_once 'config.php';

// Verificar si el usuario tiene permiso para agregar roles
if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] != 0) {
    die("Acceso denegado.");
}

$mensaje = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    if (!empty($nombre) && !empty($descripcion)) {
        $sql = "INSERT INTO roles (nombre, descripcion) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nombre, $descripcion);
        if ($stmt->execute()) {
            $mensaje = "✅ Rol agregado correctamente.";
        } else {
            $mensaje = "❌ Error al agregar el rol.";
        }
    } else {
        $mensaje = "❌ Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nuevo Rol</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('Imagenes/ConsultaGeneral.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            width: 90%;
            max-width: 500px;
            margin: 120px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h2 {
            background: #2d0f2a;
            color: white;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 24px;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            background-color: #2d0f2a;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        .btn-back {
            display: inline-block;
            background-color: #2d0f2a;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-back:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        .mensaje {
            margin-top: 15px;
            font-weight: bold;
            padding: 12px;
            border-radius: 8px;
        }

        .mensaje-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Agregar Nuevo Rol</h2>
        <form method="POST">
            <input type="text" name="nombre" placeholder="Nombre del Rol" required>
            <textarea name="descripcion" placeholder="Descripción del Rol" rows="4" required></textarea>
            <button type="submit">Guardar Rol</button>
        </form>

        <?php
        if ($mensaje) {
            $class = strpos($mensaje, "✅") !== false ? "mensaje mensaje-success" : "mensaje mensaje-error";
            echo "<div class='$class'>$mensaje</div>";
        }
        ?>

        <a href="roles.php" class="btn-back">Volver a la Lista de Roles</a>
    </div>
</body>

</html>