<?php
session_start();
$mostrar_formulario = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    require_once 'config.php';
    
    $email = $_POST['email'];
    $sql = "SELECT * FROM empleados WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['codigo_verificacion'] = rand(100000, 999999);
        $_SESSION['email_recuperacion'] = $email;
        $mostrar_formulario = true;
    } else {
        $error_message = "El correo no está registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>

    <!-- SweetAlert2 y EmailJS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/RecuperarContraseña.jpg');
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
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin-top: 50px;
            transition: all 0.3s ease;
        }

        .container:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        h2 {
            color: #680c39;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 16px;
            margin: 10px 0 5px;
            color: black;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            font-size: 16px;
            color: #333;
            transition: all 0.3s ease;
        }

        input:focus {
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
            margin-top: 10px;
        }

        button:hover {
            background-color: #440f33;
            transform: translateY(-2px);
        }

        button:active {
            background-color: #680c39;
            transform: translateY(0);
        }

        .login-link {
            margin-top: 20px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>

    <script>
        emailjs.init("hYStEhW9mcF4nSFND");

        function enviarCorreo() {
            console.log("Enviando correo...");

            const parametros = {
                code: "<?php echo $_SESSION['codigo_verificacion']; ?>",
                to_email: "<?php echo $_SESSION['email_recuperacion']; ?>"
            };

            emailjs.send("service_1xhowko", "template_0rarsdo", parametros)
                .then(function(response) {
                    console.log("Correo enviado con éxito!", response);
                    Swal.fire("Éxito", "Correo enviado correctamente.", "success");
                }, function(error) {
                    console.log("Error al enviar el correo", error);
                    Swal.fire("Error", "Hubo un error al enviar el correo.", "error");
                });
        }
    </script>
</head>
<body>

<!-- Contenedor de recuperación de contraseña -->
<div class="container">
    <h2>Recuperar Contraseña</h2>

    <?php if (!$mostrar_formulario): ?>
        <form method="POST">
            <label for="email">Ingresa tu correo electrónico:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Recuperar Contraseña</button>
        </form>
    <?php endif; ?>
    
    <?php if ($mostrar_formulario): ?>
        <form method="POST" action="actualizar_contraseña.php">
            <input type="hidden" name="email" value="<?php echo $_SESSION['email_recuperacion']; ?>">

            <label for="codigo">Código de Verificación:</label>
            <input type="text" id="codigo" name="codigo_verificacion" required>
            
            <button type="button" onclick="enviarCorreo()">Enviar Código</button>
            
            <label for="nueva_contraseña">Nueva Contraseña:</label>
            <input type="password" id="nueva_contraseña" name="nueva_contraseña" required>
            
            <button type="submit">Actualizar Contraseña</button>
        </form>
    <?php endif; ?>

    <div class="login-link">
        <button onclick="window.location.href='index.php'">Volver al Login</button>
    </div>
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
