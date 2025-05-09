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

// Obtener rol para mostrar nombre
$sql = "SELECT nombre FROM roles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$rol = $result->fetch_assoc();

if (!$rol) {
    die("Rol no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete = $conn->prepare("DELETE FROM roles WHERE id = ?");
    $delete->bind_param("i", $id);

    if ($delete->execute()) {
        header("Location: roles.php?msg=Rol eliminado");
        exit();
    } else {
        $error = "Error al eliminar el rol.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Rol</title>
    <style>
        body {
            background: url('Imagenes/RecuperarContraseña.jpg') no-repeat center center fixed;
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
        .btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #c0392b;
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
    <h2>Eliminar Rol</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <p>¿Estás seguro de que deseas eliminar el rol <strong><?php echo htmlspecialchars($rol['nombre']); ?></strong>?</p>
    <form method="post">
        <button type="submit" class="btn">Eliminar</button>
        <a href="roles.php" class="btn btn-back">Cancelar</a>
    </form>
</div>
</body>
</html>
