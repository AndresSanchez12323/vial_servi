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
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            margin: auto;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: #fff;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }
        button:hover {
            background-color: #0056b3;
        }
        .mensaje {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Agregar Nuevo Rol</h2>
        <form method="POST">
            <input type="text" name="nombre" placeholder="Nombre del Rol" required>
            <textarea name="descripcion" placeholder="Descripción del Rol" required></textarea>
            <button type="submit">Guardar Rol</button>
        </form>
        <?php if ($mensaje) { echo "<p class='mensaje'>$mensaje</p>"; } ?>
        <br>
        <a href="roles.php" class="btn">Volver a la Lista de Roles</a>
    </div>
</body>
</html>
