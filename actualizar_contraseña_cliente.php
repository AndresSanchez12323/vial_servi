<?php
session_start();
require_once 'config.php';

$error_message = '';
$success_message = '';

// Verificar si el código y el email existen en la sesión
if (!isset($_SESSION['codigo_verificacion']) || !isset($_SESSION['email_recuperacion'])) {
    header("Location: recuperar_contraseña_cliente.php");
    exit();
}

// Procesar el formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_ingresado = trim($_POST['codigo_verificacion']);
    $nueva_contraseña = trim($_POST['nueva_contraseña']);
    
    // Validaciones
    if (empty($codigo_ingresado) || empty($nueva_contraseña)) {
        $error_message = "Todos los campos son obligatorios.";
    } elseif (strlen($nueva_contraseña) < 8) {
        $error_message = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($codigo_ingresado != $_SESSION['codigo_verificacion']) {
        $error_message = "El código de verificación es incorrecto.";
    } else {
        // Actualizar contraseña en la base de datos
        $hashed_password = password_hash($nueva_contraseña, PASSWORD_BCRYPT);
        $email = $_SESSION['email_recuperacion'];
        
        $sql = "UPDATE clientes SET password = ? WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);
        
        if ($stmt->execute()) {
            $success_message = "Contraseña actualizada correctamente.";
            // Limpiar la sesión
            unset($_SESSION['codigo_verificacion']);
            unset($_SESSION['email_recuperacion']);
        } else {
            $error_message = "Error al actualizar la contraseña. Por favor intenta nuevamente.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Contraseña - Cliente</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php if (isset($error_message) && !empty($error_message)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $error_message; ?>',
                confirmButtonColor: '#2d0f2a'
            }).then(() => {
                window.history.back();
            });
        </script>
    <?php elseif (isset($success_message) && !empty($success_message)): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?php echo $success_message; ?>',
                confirmButtonColor: '#2d0f2a'
            }).then(() => {
                window.location.href = 'login_cliente.php';
            });
        </script>
    <?php endif; ?>
</body>
</html>