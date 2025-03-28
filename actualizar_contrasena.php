<?php
session_start();
require_once 'config.php';

$error_message = '';
$mensaje_exito = '';

// Verificar si el código y el email existen en la sesión
if (!isset($_SESSION['codigo_verificacion']) || !isset($_SESSION['email_recuperacion'])) {
    header("Location: recuperar_contraseña.php");
    exit();
}

// Validar que los campos han sido enviados correctamente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_ingresado = isset($_POST['codigo_verificacion']) ? $_POST['codigo_verificacion'] : '';
    $nueva_contraseña = isset($_POST['nueva_contraseña']) ? $_POST['nueva_contraseña'] : '';

    if (empty($codigo_ingresado) || empty($nueva_contraseña)) {
        $error_message = "Todos los campos son obligatorios.";
    } else {
        // Verificar si el código ingresado es correcto
        if ($codigo_ingresado != $_SESSION['codigo_verificacion']) {
            $error_message = "Código de verificación incorrecto.";
        } else {
            $hashed_password = password_hash($nueva_contraseña, PASSWORD_BCRYPT);
            $email = $_SESSION['email_recuperacion'];

            // Actualizar contraseña en la base de datos
            $sql_update = "UPDATE empleados SET Contraseña = ? WHERE Email = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("ss", $hashed_password, $email);

            if ($stmt->execute()) {
                $mensaje_exito = "¡Contraseña actualizada exitosamente!";
                session_unset(); // Limpiar sesión después del éxito
                session_destroy(); // Cerrar sesión completamente
            } else {
                $error_message = "Error al actualizar la contraseña.";
            }
        }
    }
}
?>
  



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Contraseña</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <form method="POST">
    </form>

    <?php if ($error_message): ?>
        <script>Swal.fire('Error', '<?php echo $error_message; ?>', 'error');</script>
    <?php elseif ($mensaje_exito): ?>
        <script>
            Swal.fire('Éxito', '<?php echo $mensaje_exito; ?>', 'success')
//esto es una prueba de un cambio
//de contraseñajdcdpjdn
            .then(() => window.location.href = 'index.php');
        </script>
    <?php endif; ?>
</body>
</html>