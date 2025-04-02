<?php
session_start();
require_once 'config.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar y validar entradas
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $apellido = filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING);
    $cedula = filter_input(INPUT_POST, 'cedula', FILTER_SANITIZE_NUMBER_INT);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validaciones
    if (empty($nombre) || empty($apellido) || empty($cedula) || empty($telefono) || empty($email) || empty($password)) {
        $error_message = "Todos los campos son obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "El formato de email no es válido";
    } elseif ($password !== $confirm_password) {
        $error_message = "Las contraseñas no coinciden";
    } elseif (strlen($password) < 8) {
        $error_message = "La contraseña debe tener al menos 8 caracteres";
    } else {
        // Verificar si el correo o cédula ya existen
        $sql_check = "SELECT Cedula_Id FROM clientes WHERE Email = ? OR Cedula_Id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("si", $email, $cedula);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $error_message = "El email o cédula ya están registrados";
        } else {
            // Hash de contraseña (seguro)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Registrar nuevo cliente
            $sql = "INSERT INTO clientes (Cedula_Id, Nombre, Apellido, Teléfono, Email, password) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            $stmt->bind_param("isssss", $cedula, $nombre, $apellido, $telefono, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $_SESSION['registration_success'] = true;
                header("Location: login_cliente.php");
                exit();
            } else {
                $error_message = "Error al registrar: " . $conn->error;
            }
            
            $stmt->close();
        }
        $stmt_check->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente - VialServi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #680c39;
            --secondary-color: #2d0f2a;
            --accent-color: #ff6b6b;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
            --white: #ffffff;
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('Imagenes/RegistroCliente.jpg');
            background-color: var(--light-gray);
            color: var(--dark-gray);
            line-height: 1.6;
        }
        
        .main-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 100%;
            background-color: rgba(100, 67, 67, 0.9);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            z-index: 1000;
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo-container {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        
        .nav-link {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            background-color: var(--secondary-color);
            transition: var(--transition);
            font-size: 0.9rem;
        }
        
        .nav-link:hover {
            background-color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .content-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 6rem 1rem 2rem;
        }
        
        .registration-card {
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            padding: 2.5rem;
            margin: 1rem;
        }
        
        .registration-title {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 600;
            font-size: 1.8rem;
        }
        
        .form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            flex: 1;
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-gray);
        }
        
        .form-input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(104, 12, 57, 0.2);
            outline: none;
        }
        
        .submit-btn {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
            margin-top: 1rem;
        }
        
        .submit-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .login-redirect {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--dark-gray);
        }
        
        .login-link {
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        .alert-message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }
        
        .alert-error {
            background-color: #ffebee;
            color: #c70a3c;
            border: 1px solid #ffcdd2;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .registration-card {
                padding: 1.5rem;
            }
            
            .registration-title {
                font-size: 1.5rem;
            }
            
            .nav-container {
                flex-direction: column;
                gap: 1rem;
                padding: 0.5rem 0;
            }
            
            .logo-container {
                width: 60px;
                height: 60px;
            }
            
            .content-container {
                padding-top: 8rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <nav class="sidebar">
            <div class="nav-container">
                <div class="logo-container">
                    <img src="Imagenes/Logo.jpg" alt="Logo VialServi" class="logo">
                </div>
                <div class="nav-links">
                </div>
            </div>
        </nav>
        
        <div class="content-container">
            <div class="registration-card">
                <h1 class="registration-title">Registro de Cliente</h1>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert-message alert-error">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" class="form-input" required 
                                   value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="apellido" class="form-label">Apellido:</label>
                            <input type="text" id="apellido" name="apellido" class="form-input" required
                                   value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="cedula" class="form-label">Cédula:</label>
                        <input type="number" id="cedula" name="cedula" class="form-input" required
                               value="<?php echo isset($_POST['cedula']) ? htmlspecialchars($_POST['cedula']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" class="form-input" required
                               value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" class="form-input" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password" class="form-label">Contraseña:</label>
                            <input type="password" id="password" name="password" class="form-input" required minlength="8">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña:</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input" required minlength="8">
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Registrarse</button>
                </form>
                
                <p class="login-redirect">
                    ¿Ya tiene una cuenta? <a href="login_cliente.php" class="login-link">Inicie sesión aquí</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        <?php if (!empty($error_message)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error en registro',
                text: '<?php echo addslashes($error_message); ?>',
                confirmButtonColor: '#680c39'
            });
        <?php endif; ?>
    </script>
</body>
</html>