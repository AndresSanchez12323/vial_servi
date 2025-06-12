<?php
session_start();
include('validPermissions.php');
require_once 'config.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

$usuarioId = $_SESSION['cedula'];
$permisoRequerido = 'actualizar_servicio';

if (!usuarioTienePermiso($usuarioId, $permisoRequerido, $conn)) {
    die("❌ Acceso denegado: No tienes permiso para editar este servicio.");
}

if (!isset($_GET['id'])) {
    die("⚠️ ID de servicio no proporcionado.");
}

$servicioId = $_GET['id'];

$sql = "SELECT * FROM servicios WHERE Servicio_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $servicioId);
$stmt->execute();
$result = $stmt->get_result();
$servicio = $result->fetch_assoc();

if (!$servicio) {
    die("❌ Servicio no encontrado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    if (empty($nombre) || empty($descripcion)) {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
        $tipo_mensaje = "warning";
    } else {
        $sql = "UPDATE servicios SET Nombre_Servicio = ?, Descripción = ? WHERE Servicio_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nombre, $descripcion, $servicioId);
        
        if ($stmt->execute()) {
            header("Location: gestionar_servicios.php");
            exit;
        } else {
            $mensaje = "❌ Error al actualizar el servicio.";
            $tipo_mensaje = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Servicio</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/session-check.js"></script>
    <style>
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
        .card {
            max-width: 500px;
            margin: 80px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2 class="text-center mb-4">Editar Servicio</h2>
        
        <?php if (isset($mensaje)) { ?>
            <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
                <?= $mensaje ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <form method="post">
            <div class="form-group">
                <label>Nombre del Servicio:</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($servicio['Nombre_Servicio']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Descripción:</label>
                <textarea name="descripcion" class="form-control" required><?= htmlspecialchars($servicio['Descripción']) ?></textarea>
            </div>

            <div class="d-grid gap-2">
                <button data-no-warning type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a data-no-warning href="consulta_general.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
