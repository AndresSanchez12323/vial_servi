<?php
session_start();
$mostrar_formulario = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    include('config.php');
    
    $email = $_POST['email'];
    $sql = "SELECT * FROM clientes WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generar código de 6 dígitos
        $codigo = rand(100000, 999999);
        $_SESSION['codigo_verificacion'] = $codigo;
        $_SESSION['email_recuperacion'] = $email;
        $mostrar_formulario = true;
    } else {
        $error_message = "El correo no está registrado en nuestro sistema.";
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
    <title>Recuperar Contraseña - Cliente</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <style>
        /* Estilos anteriores se mantienen igual */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/RecuperarContraseñaCliente.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 450px;
            text-align: center;
        }

        h2 {
            color: #2d0f2a;
            margin-bottom: 20px;
            font-size: 24px;
        }

        label {
            display: block;
            text-align: left;
            margin: 15px 0 5px;
            color: #333;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #2d0f2a;
            box-shadow: 0 0 0 2px rgba(45, 15, 42, 0.2);
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #2d0f2a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        .login-link {
            margin-top: 20px;
            font-size: 14px;
        }

        .login-link a {
            color: #2d0f2a;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recuperar Contraseña</h2>
        
        <?php if (!$mostrar_formulario): ?>
            <form method="POST" id="emailForm">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required placeholder="Ingresa tu correo registrado">
                
                <button type="submit">Continuar</button>
            </form>
        <?php else: ?>
            <div style="text-align: left; margin-bottom: 20px;">
                <p>Hemos enviado un código de verificación a:<br>
                <strong><?php echo $_SESSION['email_recuperacion']; ?></strong></p>
                <p>Por favor revisa tu bandeja de entrada.</p>
            </div>
            
            <form method="POST" action="actualizar_contraseña_cliente.php" id="resetForm">
                <input type="hidden" name="email" value="<?php echo $_SESSION['email_recuperacion']; ?>">

                <label for="codigo">Código de Verificación:</label>
                <input type="text" id="codigo" name="codigo_verificacion" required placeholder="Ingresa el código de 6 dígitos">
                
                <button type="button" onclick="enviarCodigo()">Reenviar Código</button>
                
                <label for="nueva_contraseña">Nueva Contraseña:</label>
                <input type="password" id="nueva_contraseña" name="nueva_contraseña" required placeholder="Mínimo 8 caracteres">
                
                <button type="submit">Actualizar Contraseña</button>
            </form>
        <?php endif; ?>
        
        <div class="login-link">
            <a href="login_cliente.php">← Volver al inicio de sesión</a>
        </div>
    </div>

    <script>
        // Inicializar EmailJS con tu Public Key
        emailjs.init("hYStEhW9mcF4nSFND"); // Reemplaza con tu clave pública
        
        function enviarCodigo() {
    const params = {
        user_email: "<?php echo $_SESSION['email_recuperacion']; ?>",
        user_name: "Cliente VialServi",  // Coincide con {{user_name}} en la plantilla
        verification_code: "<?php echo $_SESSION['codigo_verificacion']; ?>",  // Coincide con {{verification_code}}
        company_name: "VialServi"  // Opcional, si lo usas en la plantilla
    };

    emailjs.send("service_1xhowko", "template_869hmxf", params)
        .then(function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Código enviado',
                text: 'Hemos enviado el código a tu correo electrónico.',
                confirmButtonColor: '#2d0f2a'
            });
            console.log("Correo enviado:", response);
            console.log("Datos enviados:", params); // Para depuración
        }, function(error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al enviar el código: ' + error.text,
                confirmButtonColor: '#2d0f2a'
            });
            console.error("Error al enviar:", error);
        });
}

        // Enviar código automáticamente al mostrar el formulario
        <?php if ($mostrar_formulario): ?>
            document.addEventListener('DOMContentLoaded', function() {
                enviarCodigo();
            });
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $error_message; ?>',
                confirmButtonColor: '#2d0f2a'
            });
        <?php endif; ?>
    </script>
</body>
</html>