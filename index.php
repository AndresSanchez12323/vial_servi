<?php
session_start();
require_once 'config.php';

$mensajeError = "";
$intentosPermitidos = 3;
$bloqueoHoras = 24;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cedula = $_POST['cedula'];
    $password = $_POST['password'];

    // Verificar si el usuario está bloqueado
    $sqlCheckBloqueo = "SELECT intentos_fallidos, fecha_hora_bloqueo, Rol_id, Contraseña FROM empleados WHERE Cedula_Empleado_id = ?";
    $stmt = $conn->prepare($sqlCheckBloqueo);
    if (!$stmt) {
        die("Error en la consulta SQL: " . $conn->error);
    }
    $stmt->bind_param("i", $cedula);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();

    if ($resultado) {
        $intentosFallidos = $resultado['intentos_fallidos'];
        $fechaHoraBloqueo = $resultado['fecha_hora_bloqueo'];
        $rol = $resultado['Rol_id'];
        $hashedPassword = $resultado['Contraseña'];

        // Verificar si el usuario está bloqueado
        if ($intentosFallidos >= $intentosPermitidos && strtotime($fechaHoraBloqueo) + ($bloqueoHoras * 3600) > time()) {
            $mensajeError = "Usuario bloqueado. Inténtelo nuevamente en $bloqueoHoras horas o contacte al administrador.";
        } else {
            // Si estaba bloqueado, pero el tiempo ya pasó, restablecer intentos
            if ($intentosFallidos >= $intentosPermitidos) {
                $sqlReiniciarIntentos = "UPDATE empleados SET intentos_fallidos = 0, fecha_hora_bloqueo = NULL WHERE Cedula_Empleado_id = ?";
                $stmtReiniciar = $conn->prepare($sqlReiniciarIntentos);
                $stmtReiniciar->bind_param("i", $cedula);
                $stmtReiniciar->execute();
            }

            // Verificar contraseña encriptada
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['cedula'] = $cedula;
                $_SESSION['rol'] = $rol;

                // Resetear intentos fallidos
                $sqlResetIntentos = "UPDATE empleados SET intentos_fallidos = 0, fecha_hora_bloqueo = NULL WHERE Cedula_Empleado_id = ?";
                $stmtReset = $conn->prepare($sqlResetIntentos);
                $stmtReset->bind_param("i", $cedula);
                $stmtReset->execute();

                // Redirigir según el rol
                if ($rol == 0) {
                    header("Location: administrador.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                // Si la contraseña es incorrecta, aumentar el contador de intentos fallidos
                $intentosFallidos++;
                $mensajeError = "Usuario o contraseña incorrectos";

                if ($intentosFallidos >= $intentosPermitidos) {
                    $sqlBloquearUsuario = "UPDATE empleados SET intentos_fallidos = ?, fecha_hora_bloqueo = NOW() WHERE Cedula_Empleado_id = ?";
                    $stmtBloquear = $conn->prepare($sqlBloquearUsuario);
                    $stmtBloquear->bind_param("ii", $intentosFallidos, $cedula);
                    $stmtBloquear->execute();
                } else {
                    $sqlActualizarIntentos = "UPDATE empleados SET intentos_fallidos = ? WHERE Cedula_Empleado_id = ?";
                    $stmtActualizar = $conn->prepare($sqlActualizarIntentos);
                    $stmtActualizar->bind_param("ii", $intentosFallidos, $cedula);
                    $stmtActualizar->execute();
                }
            }
        }
    } else {
        $mensajeError = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            background-image: url('Imagenes/Login.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
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
            background-color: rgba(100, 67, 67, 0.7); /* Fondo más sutil */
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
            background-color: #2d0f2a; /* Usando .color1 */
            margin-right: 15px;
            border-radius: 50px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #440f33; /* Usando .color2 */
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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Sombra suave */
            border-radius: 50%;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8); /* Fondo suave */
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); /* Sombra de efecto hover */
        }

        h2 {
            margin-bottom: 20px;
            color: #680c39; /* Usando .color3 */
            font-size: 24px;
            font-weight: 600;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: black; /* Usando .color4 */
            font-size: 16px;
        }

        input[type="text"], input[type="password"] {
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

        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #2d0f2a; /* Usando .color1 */
            background-color: #fff;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #2d0f2a; /* Usando .color1 */
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background-color: #440f33; /* Usando .color2 */
            transform: translateY(-2px);
        }

        button:active {
            background-color: #680c39; /* Usando .color3 */
            transform: translateY(0);
        }

        .form-message {
            margin-top: 20px;
            font-size: 14px;
            color: #c70a3c; /* Usando .color5 */
        }

        .form-message a {
            color: #2d0f2a; /* Usando .color1 */
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .form-message a:hover {
            color: #440f33; /* Usando .color2 */
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div id="mySidebar" class="sidebar">
    <div class="logo-container">
        <img src="Imagenes/Logo.jpg" alt="Logo" class="logo">
    </div>
    <div>
        <a href="login_cliente.php" >Área Clientes</a>
    </div>
</div>

<div class="container">
    <h2>Iniciar Sesión</h2>
    <?php if ($mensajeError): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= $mensajeError ?>',
                confirmButtonColor: '#007bff'
            });
        </script>
    <?php endif; ?>
    <form method="POST">
        <label for="cedula">Cédula:</label>
        <input type="text" id="cedula" name="cedula" required>
        
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Iniciar sesión</button>
        <label for="">
            <a href="registro.php"></a>

            <button type="button" onclick="window.location.href='registro.php'">Registrarse</button>
        <label for="">
            <a href="recuperar_contraseña.php">¿Olvidaste tu contraseña?</a>
        </label>
    </form>
</div>

</body>
</html>
            
