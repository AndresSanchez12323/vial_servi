<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['registration_success']) && $_SESSION['registration_success']) {
    unset($_SESSION['registration_success']);
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "success",
                    title: "¡Registro exitoso!",
                    text: "Tu cuenta ha sido creada correctamente. Ahora puedes iniciar sesión.",
                    confirmButtonColor: "#680c39",
                    confirmButtonText: "Entendido"
                });
            });
          </script>';
}
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    $sql = "SELECT Cedula_Id, Nombre, Apellido, Email, password FROM clientes WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $cliente = $result->fetch_assoc();
        
        if (password_verify($password, $cliente['password'])) {
            $_SESSION['cliente_id'] = $cliente['Cedula_Id'];
            $_SESSION['cliente_nombre'] = $cliente['Nombre'] . ' ' . $cliente['Apellido'];
            $_SESSION['cliente_email'] = $cliente['Email'];
            $_SESSION['loggedin'] = true;
            
            header("Location: cliente_dashboard.php");
            exit();
        } else {
            $error_message = "Correo o contraseña incorrectos";
        }
    } else {
        $error_message = "Correo o contraseña incorrectos"; // Mismo mensaje por seguridad
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Cliente - VialServi</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/LoginCliente.jpg');
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

        input[type="email"], input[type="password"] {
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

        input[type="email"]:focus, input[type="password"]:focus {
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

        .error-message {
            color: #c70a3c;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="Imagenes/Logo.jpg" alt="Logo VialServi" class="logo">
        </div>
        <div>
            <a href="index.php">Área Empleados</a>
            <a href="contactenos.php" >Contáctenos</a>
            <a href="quienes_somos.html" >Quiénes Somos</a>
        </div>
    </div>

    <div class="container">
        <h2>Acceso Clientes</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div>
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Ingresar</button>
        </form>
        
        <div class="form-message">
            ¿Primera vez aquí? <a href="registro_cliente.php">Regístrese</a>
        </div>
        <div class="form-message">
            <a href="recuperar_contraseña_cliente.php">¿Olvidó su contraseña?</a>
        </div>
    </div>

    <script>
        <?php if (!empty($error_message)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error de autenticación',
                text: '<?php echo $error_message; ?>',
                confirmButtonColor: '#2d0f2a'
            });
        <?php endif; ?>
    </script>
</body>
</html>