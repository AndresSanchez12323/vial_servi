<?php
session_start();
require_once 'config.php'; // Archivo de conexión a la BD

// Obtener lista de roles
$sqlRoles = "SELECT id, nombre FROM roles";
$resultRoles = $conn->query($sqlRoles);

// Obtener lista de permisos
$sqlPermisos = "SELECT id, nombre FROM permisos";
$resultPermisos = $conn->query($sqlPermisos);

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rol_id = $_POST['rol_id'];
    $permiso_id = $_POST['permiso_id'];

    // Verificar si ya existe la relación
    $checkSql = "SELECT * FROM rol_permisos WHERE rol_id = ? AND permiso_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $rol_id, $permiso_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Insertar nuevo permiso para el rol
        $insertSql = "INSERT INTO rol_permisos (rol_id, permiso_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ii", $rol_id, $permiso_id);
        $stmt->execute();

        echo "<script>alert('Permiso asignado correctamente.');</script>";
    } else {
        echo "<script>alert('Este permiso ya está asignado a este rol.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Roles y Permisos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: inline-block;
            margin-top: 50px;
        }
        form{
            display: flex;
            flex-direction: column;
        }
        select, button {
            padding: 10px;
            margin: 10px;
            font-size: 16px;
            border-radius: 5px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Asignar Permiso a un Rol</h2>
        <form method="POST">
            <!-- Select para elegir el rol -->
            <label for="rol_id">Seleccionar Rol:</label>
            <select name="rol_id" required>
                <option value="">-- Seleccionar --</option>
                <?php while ($rol = $resultRoles->fetch_assoc()) { ?>
                    <option value="<?php echo $rol['id']; ?>"><?php echo $rol['nombre']; ?></option>
                <?php } ?>
            </select>

            <!-- Select para elegir el permiso -->
            <label for="permiso_id">Seleccionar Permiso:</label>
            <select name="permiso_id" required>
                <option value="">-- Seleccionar --</option>
                <?php while ($permiso = $resultPermisos->fetch_assoc()) { ?>
                    <option value="<?php echo $permiso['id']; ?>"><?php echo $permiso['nombre']; ?></option>
                <?php } ?>
            </select>

            <br>
            <button type="submit">Asignar Permiso</button>
        </form>

        <br>
        <button onclick="window.location.href='roles.php'">Volver a Roles</button>
    </div>

</body>
</html>
