<?php
session_start();
require_once 'config.php';
require_once 'gemini_ai.php';

$mensajeError = "";
$mensajeExito = "";
$respuestaAI = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $mensaje = $_POST['mensaje'];

    // Verificar que los campos no estén vacíos
    if (!empty($nombre) && !empty($email) && !empty($mensaje)) {
        
        // Insertar mensaje en la tabla 'mensajes' sin validar el correo
        $sql = "INSERT INTO mensajes (nombre, email, mensaje) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            // Si la preparación de la consulta falla, muestra el error
            $mensajeError = "Error en la preparación de la consulta SQL: " . $conn->error;
        } else {
            // Si la consulta se preparó correctamente, procedemos a vincular los parámetros
            $stmt->bind_param("sss", $nombre, $email, $mensaje);
            
            if ($stmt->execute()) {
                $mensajeExito = "Mensaje enviado correctamente.";
                
                // Generar respuesta inteligente con Gemini AI
                try {
                    $gemini = getGeminiAI();
                    $clientData = ['nombre' => $nombre, 'email' => $email];
                    $respuestaAI = $gemini->generateContactResponse($mensaje, $clientData);
                } catch (Exception $e) {
                    // Si falla la AI, continuar normalmente
                    error_log("Error generando respuesta AI: " . $e->getMessage());
                }
            } else {
                $mensajeError = "Error al enviar el mensaje. " . $stmt->error;
            }
        }
    } else {
        $mensajeError = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contáctenos</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/Contactenos.jpg');
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
            max-width: 450px;
            text-align: center;
            margin-top: 150px; /* Separación entre la barra superior y el contenedor */
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

        input[type="text"], input[type="email"], textarea {
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

        input[type="text"]:focus, input[type="email"]:focus, textarea:focus {
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

        .ai-response {
            margin-top: 30px;
            padding: 20px;
            background-color: rgba(45, 15, 42, 0.1);
            border-left: 4px solid #2d0f2a;
            border-radius: 0 10px 10px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .ai-response h3 {
            color: #2d0f2a;
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .ai-response h3:before {
            content: "🤖";
            margin-right: 10px;
            font-size: 20px;
        }

        .ai-response p {
            color: #555;
            line-height: 1.6;
            font-size: 14px;
        }

        .ai-badge {
            display: inline-block;
            background: linear-gradient(135deg, #2d0f2a, #440f33);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <img src="Imagenes/Logo.jpg" alt="Logo" class="logo">
    </div>
    <div>
        <a href="login_cliente.php" >Inicio</a>
        <a href="contactenos.php" >Contáctenos</a>
        <a href="quienes_somos.html" >Quiénes Somos</a>
    </div>
</div>

<div class="container">
    <h2>Contáctenos</h2>

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
    
    <?php if ($mensajeExito): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '<?= $mensajeExito ?>',
                confirmButtonColor: '#007bff'
            });
        </script>
    <?php endif; ?>

    <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="mensaje">Mensaje:</label>
        <textarea id="mensaje" name="mensaje" rows="4" required></textarea>
        
        <button type="submit">Enviar</button>
    </form>

    <?php if (!empty($respuestaAI)): ?>
        <div class="ai-response">
            <div class="ai-badge">✨ Respuesta Inteligente VialServi AI</div>
            <h3>Respuesta Inmediata</h3>
            <p><?= nl2br(htmlspecialchars($respuestaAI)) ?></p>
            <small style="color: #888; font-style: italic;">
                Esta respuesta fue generada automáticamente para ayudarte de inmediato. 
                Nuestro equipo también revisará tu mensaje y te contactará pronto.
            </small>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
