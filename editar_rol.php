<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] != 0) {
    die("Acceso denegado.");
}

if (!isset($_GET['id'])) {
    die("ID de rol no proporcionado.");
}

$id = intval($_GET['id']);

// Obtener datos del rol
$sql = "SELECT nombre, descripcion FROM roles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$rol = $result->fetch_assoc();

if (!$rol) {
    die("Rol no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    $update = $conn->prepare("UPDATE roles SET nombre = ?, descripcion = ? WHERE id = ?");
    $update->bind_param("ssi", $nombre, $descripcion, $id);

    if ($update->execute()) {
        header("Location: roles.php?msg=Rol actualizado");
        exit();
    } else {
        $error = "Error al actualizar el rol.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Rol</title>
    <style>
        body {
            background: url('Imagenes/CrearServicioRealizado.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            background: rgba(255,255,255,0.95);
            width: 90%;
            max-width: 500px;
            margin: 120px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            text-align: center;
        }
        h2 {
            background: #2d0f2a;
            color: white;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            box-sizing: border-box;
        }
        .btn {
            background-color: #f39c12;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #d68910;
        }
        .btn-back {
            background-color: #2d0f2a;
            
        }
        .btn-back:hover {
            background-color: #440f33;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Editar Rol</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <label for="nombre">Nombre del Rol</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($rol['nombre']); ?>" required>
        
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($rol['descripcion']); ?></textarea>
        
        <button type="submit" class="btn">Guardar Cambios</button>
        <a href="roles.php" class="btn btn-back">Cancelar</a>
    </form>
</div>
</body>
</html>
