<?php
require_once 'config.php'; // conexión a la BD

if (!isset($_GET['id'])) {
    echo 'error';
    exit;
}

$servicioId = intval($_GET['id']);

// Intentar eliminar el servicio
$sqlDelete = "DELETE FROM servicios WHERE Servicio_id = ?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param('i', $servicioId);

if ($stmtDelete->execute()) {
    if ($stmtDelete->affected_rows > 0) {
        echo 'success'; // Servicio eliminado correctamente
    } else {
        echo 'not_found'; // No existía ese servicio
    }
} else {
    // Error al intentar borrar, probablemente por restricción de clave foránea
    echo 'error_restriccion';
}

$conn->close();
?>