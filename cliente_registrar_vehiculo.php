<?php
session_start();
require_once 'config.php';

// Verificar si el cliente está logueado
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login_cliente.php");
    exit;
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $placa = $_POST['placa'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $color = $_POST['color'];
    $objetos_valiosos = $_POST['objetos_valiosos'];
    $cliente_id = $_SESSION['cliente_id'];

    // Insertar el vehículo en la base de datos
    $sql = "INSERT INTO vehiculos (Placa, Marca, Modelo, Color, Objetos_Valiosos, Clientes_Vehiculos) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $placa, $marca, $modelo, $color, $objetos_valiosos, $cliente_id);

    if ($stmt->execute()) {
        // Mostrar alerta de éxito
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Vehículo registrado',
                        text: 'Tu vehículo ha sido registrado exitosamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#ff6b6b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'cliente_dashboard.php';
                        }
                    });
                });
              </script>";
    } else {
        echo "Error al registrar el vehículo: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Vehículo</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/ClienteRegistrarVehiculo.jpg');
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
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
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
            margin: 120px auto 40px;
        }
        h2 {
            color: #680c39;
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-primary {
            background-color: #680c39;
            border-color: #680c39;
            width: 100%;
            padding: 10px;
            font-size: 18px;
        }
        .btn-primary:hover {
            background-color: #4d0929;
            border-color: #4d0929;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <img src="Imagenes/Logo.jpg" alt="Logo" class="logo">
    </div>
    <div>
        <a href="cliente_dashboard.php">Dashboard</a>
        <a href="logout_cliente.php">Cerrar Sesión</a>
    </div>
</div>

<div class="container">
    <h2>Registrar Nuevo Vehículo</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="placa">Placa del vehículo</label>
            <input type="text" name="placa" class="form-control" required placeholder="Ej: ABC123">
        </div>
        
        <div class="form-group">
            <label for="marca">Marca</label>
            <input type="text" name="marca" class="form-control" required placeholder="Ej: Toyota">
        </div>
        
        <div class="form-group">
            <label for="modelo">Modelo</label>
            <input type="text" name="modelo" class="form-control" required placeholder="Ej: Corolla">
        </div>
        
        <div class="form-group">
            <label for="color">Color</label>
            <input type="text" name="color" class="form-control" required placeholder="Ej: Rojo">
        </div>
        
        <div class="form-group">
            <label for="objetos_valiosos">Objetos valiosos en el vehículo</label>
            <textarea name="objetos_valiosos" class="form-control" rows="3" placeholder="Describa cualquier objeto de valor que suela llevar en el vehículo"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar Vehículo</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>