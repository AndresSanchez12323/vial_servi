<?php
require_once 'config.php'; // Asegúrate de incluir la conexión a la base de datos

function usuarioTienePermiso($usuarioId, $permisoNombre, $conexion) {
    $sql = "
        SELECT p.nombre 
        FROM empleados e
        JOIN roles r ON e.rol_id = r.id
        JOIN rol_permisos rp ON r.id = rp.rol_id
        JOIN permisos p ON rp.permiso_id = p.id
        WHERE e.Cedula_Empleado_id = ? AND p.nombre = ?
    ";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error en la consulta SQL: " . $conexion->error);
    }
    $stmt->bind_param("is", $usuarioId, $permisoNombre);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() !== null;
}
?>
