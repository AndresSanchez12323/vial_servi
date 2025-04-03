<?php
session_start();
require_once 'config.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula_empleado_id = $_POST['cedula_empleado_id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];  // Added this line
    $password = $_POST['password'];
    $rol_id = 5; // Rol predeterminado: Técnico

    // Validar formato de correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Por favor, ingresa un correo electrónico válido.";
    } else {
        // Verificar si la cédula ya está registrada
        $sql_check_cedula = "SELECT * FROM empleados WHERE Cedula_Empleado_id = ?";
        $stmt_cedula = $conn->prepare($sql_check_cedula);
        $stmt_cedula->bind_param("i", $cedula_empleado_id);
        $stmt_cedula->execute();
        $result_cedula = $stmt_cedula->get_result();

        if ($result_cedula->num_rows > 0) {
            $error_message = "La cédula del empleado ya está registrada. Por favor, inicia sesión.";
        } else {
            // Verificar si el email ya está registrado
            $sql_check_email = "SELECT * FROM empleados WHERE Email = ?";
            $stmt_email = $conn->prepare($sql_check_email);
            $stmt_email->bind_param("s", $email);
            $stmt_email->execute();
            $result_email = $stmt_email->get_result();

            if ($result_email->num_rows > 0) {
                $error_message = "Este correo electrónico ya está registrado.";
            } else {
                // Cifrar la contraseña
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insertar nuevo empleado con rol de Técnico en la base de datos
                $sql_insert = "INSERT INTO empleados (Cedula_Empleado_id, Nombre, Apellido, Email, Telefono, Contraseña, Rol_id) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);

                if ($stmt_insert === false) {
                    $error_message = "Error al preparar la consulta de inserción: " . $conn->error;
                } else {
                    $stmt_insert->bind_param("isssssi", $cedula_empleado_id, $nombre, $apellido, $email, $telefono, $hashed_password, $rol_id);

                    if ($stmt_insert->execute()) {
                        $success_message = "¡Registro exitoso! Ahora puedes iniciar sesión.";
                    } else {
                        $error_message = "Error al registrar el usuario: " . $conn->error;
                    }
                }
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
    <title>Registro</title>

    <!-- SweetAlert2 CSS y JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/Registro.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: rgba(100, 67, 67, 0.7);
            padding: 15px 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            padding: 12px 25px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            background-color: #2d0f2a;
            margin-right: 15px;
            border-radius: 50px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        .logo-container {
            margin-left: 30px;
            border-radius: 50%;
            width: 90px;
            height: 90px;
            overflow: hidden;
        }

        .logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin-top: 100px;
            transition: all 0.3s ease;
        }

        .container:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        h2 {
            margin-bottom: 20px;
            color: #680c39;
            font-size: 24px;
            font-weight: 600;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: black;
            font-size: 16px;
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="tel"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-sizing: border-box;
            background-color: #f9f9f9;
            font-size: 16px;
            color: #333;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, input[type="tel"]:focus {
            outline: none;
            border-color: #2d0f2a;
            background-color: #fff;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #2d0f2a;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        button:active {
            background-color: #680c39;
            transform: translateY(0);
        }

        .form-message {
            margin-top: 20px;
            font-size: 14px;
            color: #c70a3c;
        }

        .form-message a {
            color: #2d0f2a;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .form-message a:hover {
            color: #440f33;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Barra de navegación -->
<div id="mySidebar" class="sidebar">
    <div class="logo-container">
        <img src="Imagenes/Logo.jpg" alt="Logo" class="logo">
    </div>
    <div>
    
    </div>
</div>

<!-- Contenedor de Registro -->
<div class="container">
    <h2>Formulario de Registro</h2>
    <form action="registro.php" method="POST">
        <input type="text" name="cedula_empleado_id" placeholder="Cédula del Empleado" required>
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellido" placeholder="Apellido" required>
        <input type="email" name="email" placeholder="Correo Electrónico" required>
        <input type="tel" name="telefono" placeholder="Teléfono" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrarse</button>
    </form>
    <br>
    <a href="index.php">¿Ya tienes cuenta?</a>
</div>

<!-- SweetAlert2 para mensajes de error o éxito -->
<?php if (isset($error_message) && !empty($error_message)): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $error_message; ?>'
        });
    </script>
<?php endif; ?>

<?php if (isset($success_message) && !empty($success_message)): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '<?php echo $success_message; ?>'
        }).then(() => {
            window.location.href = 'index.php';
        });
    </script>
<?php endif; ?>

</body>
</html>