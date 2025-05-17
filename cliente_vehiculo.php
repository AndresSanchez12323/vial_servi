<?php
session_start();
require_once 'config.php';

// Verificar si el cliente está logueado
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login_cliente.php");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];
$mensaje = '';
$tipo_mensaje = '';

// Obtener lista de vehículos del cliente
$sql_vehiculos = "SELECT * FROM vehiculos WHERE Clientes_Vehiculos = ?";
$stmt_vehiculos = $conn->prepare($sql_vehiculos);
$stmt_vehiculos->bind_param("i", $cliente_id);
$stmt_vehiculos->execute();
$vehiculos = $stmt_vehiculos->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_vehiculos->close();

// Determinar la acción a realizar
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'listar';
$placa_editar = isset($_GET['placa']) ? $_GET['placa'] : '';

// Variables para formulario de edición
$vehiculo_editar = null;

// ---------- CREAR VEHÍCULO ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    // Obtener datos del formulario
    $placa = $_POST['placa'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $color = $_POST['color'];
    $objetos_valiosos = $_POST['objetos_valiosos'];

    // Verificar si la placa ya existe
    $check_sql = "SELECT COUNT(*) as count FROM vehiculos WHERE Placa = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $placa);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($check_result['count'] > 0) {
        $mensaje = "La placa ya está registrada en el sistema.";
        $tipo_mensaje = "error";
    } else {
        // Insertar el vehículo en la base de datos
        $sql = "INSERT INTO vehiculos (Placa, Marca, Modelo, Color, Objetos_Valiosos, Clientes_Vehiculos) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $placa, $marca, $modelo, $color, $objetos_valiosos, $cliente_id);

        if ($stmt->execute()) {
            $mensaje = "Tu vehículo ha sido registrado exitosamente.";
            $tipo_mensaje = "success";
            
            // Actualizar la lista de vehículos
            $stmt_vehiculos = $conn->prepare($sql_vehiculos);
            $stmt_vehiculos->bind_param("i", $cliente_id);
            $stmt_vehiculos->execute();
            $vehiculos = $stmt_vehiculos->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt_vehiculos->close();
            
            // Redirigir a la lista después de registrar
            header("Location: cliente_vehiculo.php?mensaje=registro_exitoso");
            exit;
        } else {
            $mensaje = "Error al registrar el vehículo: " . $stmt->error;
            $tipo_mensaje = "error";
        }
        $stmt->close();
    }
}

// ---------- ACTUALIZAR VEHÍCULO ----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
    // Obtener datos del formulario
    $placa = $_POST['placa'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $color = $_POST['color'];
    $objetos_valiosos = $_POST['objetos_valiosos'];

    // Verificar que el vehículo pertenezca al cliente
    $check_sql = "SELECT COUNT(*) as count FROM vehiculos WHERE Placa = ? AND Clientes_Vehiculos = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $placa, $cliente_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($check_result['count'] == 0) {
        $mensaje = "No tienes permiso para editar este vehículo.";
        $tipo_mensaje = "error";
    } else {
        // Actualizar el vehículo
        $sql = "UPDATE vehiculos SET Marca = ?, Modelo = ?, Color = ?, Objetos_Valiosos = ? WHERE Placa = ? AND Clientes_Vehiculos = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $marca, $modelo, $color, $objetos_valiosos, $placa, $cliente_id);

        if ($stmt->execute()) {
            $mensaje = "La información del vehículo ha sido actualizada exitosamente.";
            $tipo_mensaje = "success";
            
            // Actualizar la lista de vehículos
            $stmt_vehiculos = $conn->prepare($sql_vehiculos);
            $stmt_vehiculos->bind_param("i", $cliente_id);
            $stmt_vehiculos->execute();
            $vehiculos = $stmt_vehiculos->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt_vehiculos->close();
            
            // Redirigir a la lista después de actualizar
            header("Location: cliente_vehiculo.php?mensaje=actualizacion_exitosa");
            exit;
        } else {
            $mensaje = "Error al actualizar el vehículo: " . $stmt->error;
            $tipo_mensaje = "error";
        }
        $stmt->close();
    }
}

// ---------- ELIMINAR VEHÍCULO ----------
if ($accion == 'eliminar' && !empty($placa_editar)) {
    // Verificar que el vehículo pertenezca al cliente
    $check_sql = "SELECT COUNT(*) as count FROM vehiculos WHERE Placa = ? AND Clientes_Vehiculos = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $placa_editar, $cliente_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($check_result['count'] == 0) {
        $mensaje = "No tienes permiso para eliminar este vehículo.";
        $tipo_mensaje = "error";
    } else {
        // Verificar si hay servicios asociados
        $check_services = "SELECT COUNT(*) as count FROM servicios_realizados WHERE Vehiculo_id_Servicios_Realizados = ?";
        $check_stmt = $conn->prepare($check_services);
        $check_stmt->bind_param("s", $placa_editar);
        $check_stmt->execute();
        $services_result = $check_stmt->get_result()->fetch_assoc();
        $check_stmt->close();

        if ($services_result['count'] > 0) {
            $mensaje = "No se puede eliminar el vehículo porque tiene servicios asociados.";
            $tipo_mensaje = "warning";
        } else {
            // Eliminar el vehículo
            $sql = "DELETE FROM vehiculos WHERE Placa = ? AND Clientes_Vehiculos = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $placa_editar, $cliente_id);

            if ($stmt->execute()) {
                $mensaje = "El vehículo ha sido eliminado exitosamente.";
                $tipo_mensaje = "success";
                
                // Actualizar la lista de vehículos
                $stmt_vehiculos = $conn->prepare($sql_vehiculos);
                $stmt_vehiculos->bind_param("i", $cliente_id);
                $stmt_vehiculos->execute();
                $vehiculos = $stmt_vehiculos->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt_vehiculos->close();
                
                // Redirigir a la lista después de eliminar
                header("Location: cliente_vehiculo.php?mensaje=eliminacion_exitosa");
                exit;
            } else {
                $mensaje = "Error al eliminar el vehículo: " . $stmt->error;
                $tipo_mensaje = "error";
            }
            $stmt->close();
        }
    }
}

// Cargar datos del vehículo para edición
if ($accion == 'editar' && !empty($placa_editar)) {
    $sql_edit = "SELECT * FROM vehiculos WHERE Placa = ? AND Clientes_Vehiculos = ?";
    $stmt_edit = $conn->prepare($sql_edit);
    $stmt_edit->bind_param("si", $placa_editar, $cliente_id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    
    if ($result_edit->num_rows > 0) {
        $vehiculo_editar = $result_edit->fetch_assoc();
    } else {
        $mensaje = "No se encontró el vehículo o no tienes permiso para editarlo.";
        $tipo_mensaje = "error";
        $accion = 'listar';
    }
    $stmt_edit->close();
}

// Procesar mensajes de redirección
if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'registro_exitoso':
            $mensaje = "Tu vehículo ha sido registrado exitosamente.";
            $tipo_mensaje = "success";
            break;
        case 'actualizacion_exitosa':
            $mensaje = "La información del vehículo ha sido actualizada exitosamente.";
            $tipo_mensaje = "success";
            break;
        case 'eliminacion_exitosa':
            $mensaje = "El vehículo ha sido eliminado exitosamente.";
            $tipo_mensaje = "success";
            break;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vehículos</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/ClienteRegistrarVehiculo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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
            max-width: 1200px;
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
        }
        .btn-primary:hover {
            background-color: #4d0929;
            border-color: #4d0929;
        }
        .btn-action {
            margin-right: 5px;
        }
        .btn-warning {
            background-color: #f9a825;
            border-color: #f9a825;
            color: white;
        }
        .btn-danger {
            background-color: #e53935;
            border-color: #e53935;
        }
        .readonly-placa {
            background-color: #f2f2f2;
            cursor: not-allowed;
        }
        .form-group label {
            font-weight: 600;
            color: #555;
        }
        .vehicle-card {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .vehicle-card h3 {
            color: #680c39;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .vehicle-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        .vehicle-info-item {
            margin-bottom: 10px;
        }
        .vehicle-actions {
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
        }
        .welcome-message {
            background-color: rgba(104, 12, 57, 0.1);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .welcome-message h3 {
            color: #680c39;
            margin-bottom: 10px;
        }
        .btn-add-vehicle {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-add-vehicle i {
            margin-right:.5rem;
        }
        .btn-add-vehicle:hover {
            background-color: #218838;
            text-decoration: none;
            color: white;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            font-size: 0.9rem;
            display: block;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 1rem;
            color: #333;
        }
        .no-vehicles {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-top: 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #680c39;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
            color: #4d0929;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <img src="Imagenes/Logo.jpg" alt="Logo" class="logo">
    </div>
    <div>
        <a href="cliente_dashboard.php"></i> Dashboard</a>
        <a href="logout_cliente.php"></i> Cerrar Sesión</a>
    </div>
</div>

<div class="container">
    <div class="welcome-message">
        <h3>Gestión de vehículos</h3>
        <p>Aquí puedes gestionar todos tus vehículos registrados en VialServi</p>
    </div>
    
    <?php if (!empty($mensaje)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '<?php echo ($tipo_mensaje == 'success') ? 'Éxito' : (($tipo_mensaje == 'warning') ? 'Advertencia' : 'Error'); ?>',
                text: '<?php echo $mensaje; ?>',
                icon: '<?php echo $tipo_mensaje; ?>',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#680c39'
            });
        });
    </script>
    <?php endif; ?>

    <?php if ($accion == 'listar'): ?>
        <!-- Lista de vehículos -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mis vehículos</h2>
            <a href="?accion=nuevo" class="btn-add-vehicle"><i class="fas fa-plus-circle"></i> Registrar nuevo vehículo</a>
        </div>
        
        <?php if (empty($vehiculos)): ?>
            <div class="no-vehicles">
                <p>No tienes vehículos registrados.</p>
                <a href="?accion=nuevo" class="btn btn-primary mt-3">Registrar mi primer vehículo</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($vehiculos as $vehiculo): ?>
                    <div class="col-md-6 mb-4">
                        <div class="vehicle-card">
                            <h3><?php echo htmlspecialchars($vehiculo['Marca'] . ' ' . $vehiculo['Modelo']); ?></h3>
                            <div class="vehicle-info">
                                <div class="vehicle-info-item">
                                    <span class="info-label">Placa:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($vehiculo['Placa']); ?></span>
                                </div>
                                <div class="vehicle-info-item">
                                    <span class="info-label">Color:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($vehiculo['Color']); ?></span>
                                </div>
                                <?php if (!empty($vehiculo['Objetos_Valiosos'])): ?>
                                <div class="vehicle-info-item" style="grid-column: 1 / -1;">
                                    <span class="info-label">Objetos valiosos:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($vehiculo['Objetos_Valiosos']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="vehicle-actions">
                                <a href="?accion=editar&placa=<?php echo urlencode($vehiculo['Placa']); ?>" class="btn btn-warning btn-action">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <button class="btn btn-danger btn-action" onclick="confirmarEliminar('<?php echo htmlspecialchars($vehiculo['Placa']); ?>')">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    
    <?php elseif ($accion == 'nuevo'): ?>
        <!-- Formulario para nuevo vehículo -->
        <a href="?accion=listar" class="back-link"><i class="fas fa-arrow-left"></i> Volver a mis vehículos</a>
        <h2>Registrar Nuevo Vehículo</h2>
        <form method="post" action="" class="mt-4">
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
            
            <div class="form-group text-center">
                <a href="?accion=listar" class="btn btn-secondary mr-2">Cancelar</a>
                <button type="submit" name="registrar" value="1" class="btn btn-primary">Registrar Vehículo</button>
            </div>
        </form>
    
    <?php elseif ($accion == 'editar' && $vehiculo_editar): ?>
        <!-- Formulario para editar vehículo -->
        <a href="?accion=listar" class="back-link"><i class="fas fa-arrow-left"></i> Volver a mis vehículos</a>
        <h2>Editar Vehículo</h2>
        <form method="post" action="" class="mt-4">
            <div class="form-group">
                <label for="placa">Placa del vehículo</label>
                <input type="text" name="placa" class="form-control readonly-placa" value="<?php echo htmlspecialchars($vehiculo_editar['Placa']); ?>" readonly>
                <small class="form-text text-muted">La placa no se puede modificar</small>
            </div>
            
            <div class="form-group">
                <label for="marca">Marca</label>
                <input type="text" name="marca" class="form-control" required value="<?php echo htmlspecialchars($vehiculo_editar['Marca']); ?>">
            </div>
            
            <div class="form-group">
                <label for="modelo">Modelo</label>
                <input type="text" name="modelo" class="form-control" required value="<?php echo htmlspecialchars($vehiculo_editar['Modelo']); ?>">
            </div>
            
            <div class="form-group">
                <label for="color">Color</label>
                <input type="text" name="color" class="form-control" required value="<?php echo htmlspecialchars($vehiculo_editar['Color']); ?>">
            </div>
            
            <div class="form-group">
                <label for="objetos_valiosos">Objetos valiosos en el vehículo</label>
                <textarea name="objetos_valiosos" class="form-control" rows="3"><?php echo htmlspecialchars($vehiculo_editar['Objetos_Valiosos'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group text-center">
                <a href="?accion=listar" class="btn btn-secondary mr-2">Cancelar</a>
                <button type="submit" name="actualizar" value="1" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Función para confirmar eliminación con SweetAlert2
    function confirmarEliminar(placa) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminará el vehículo con placa " + placa + ". Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?accion=eliminar&placa=' + encodeURIComponent(placa);
            }
        });
    }
</script>
</body>
</html>