<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

require_once 'config.php';
include('validPermissions.php');
include('header.php');

$empleados = [];
$query = "SELECT Cedula_Empleado_id, Nombre, Apellido FROM empleados WHERE Rol_id = 2";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $empleados[] = $row;
    }
}

$cars = [];
$query = "
    SELECT vehiculos.Placa, clientes.Nombre AS NombreCliente, clientes.Apellido AS ApellidoCliente
    FROM vehiculos
    INNER JOIN clientes ON vehiculos.Clientes_Vehiculos = clientes.Cedula_Id
";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}

$service = [];
$query = "SELECT Servicio_id, Nombre_Servicio FROM servicios";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $service[] = $row;
    }
}

$municipality = [];
$query = "SELECT id, nombre FROM municipios";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $municipality[] = $row;
    }
}

$fotoPath = '';
if (isset($_FILES['Fotos']) && $_FILES['Fotos']['error'] === UPLOAD_ERR_OK) {
    $fotoTmpPath = $_FILES['Fotos']['tmp_name'];
    $fotoName = basename($_FILES['Fotos']['name']);
    $fotoExtension = strtolower(pathinfo($fotoName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

    $mimeType = mime_content_type($fotoTmpPath);

    if (in_array($fotoExtension, $allowedExtensions) && in_array($mimeType, $allowedMimeTypes)) {
        $fotoTmpPath = $_FILES['Fotos']['tmp_name'];
        $fotoNewName = uniqid('img_', true) . '.' . $fotoExtension;
        $uploadDir = __DIR__ . '/uploads/';
        $destinationPath = $uploadDir . $fotoNewName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($fotoTmpPath, $destinationPath)) {
            $fotoPath = 'uploads/' . $fotoNewName; // ruta relativa para la BD o frontend
        } else {
            error_log("Fallo al mover archivo desde $fotoTmpPath hacia $destinationPath");
            die("Error al mover la imagen al directorio destino.");
        }
    } else {
        die("Archivo no permitido. Asegúrate de subir una imagen válida (JPG, PNG o GIF).");
    }
} elseif (isset($_FILES['Fotos']) && $_FILES['Fotos']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Si hubo un error distinto a no haber archivo
    switch ($_FILES['Fotos']['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            die("El archivo es demasiado grande.");
        case UPLOAD_ERR_PARTIAL:
            die("El archivo fue subido parcialmente.");
        default:
            die("Error al subir el archivo. Código: " . $_FILES['Fotos']['error']);
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedulaEmpleado = $_POST['Cedula_Empleado_id_Servicios_Realizados'];
    $vehiculoId = $_POST['Vehiculo_id_Servicios_Realizados'];
    $servicioId = $_POST['Servicio_id_Servicios_Realizados'];
    $fecha = $_POST['Fecha'];
    $municipality = $_POST['municipality'];
    $ubicacion = $_POST['Ubicacion'];
    $novedades = $_POST['Novedades'];
    $fotos = $fotoPath;
    $detalleServicio = $_POST['Detalle_Servicio'];
    $custodiaServicio = $_POST['Custodia_Servicio'];
    $facturacionSeparada = isset($_POST['Facturacion_Separada']) ? 1 : 0;

    // Consulta preparada para insertar los datos
    $stmt = $conn->prepare("INSERT INTO servicios_realizados (
        Cedula_Empleado_id_Servicios_Realizados, 
        Vehiculo_id_Servicios_Realizados, 
        Servicio_id_Servicios_Realizados, 
        Fecha, 
        municipio,
        Ubicación, 
        Novedades, 
        Fotos, 
        Detalle_Servicio, 
        Custodia_Servicio, 
        Facturación_Separada
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("isisssssssi", $cedulaEmpleado, $vehiculoId, $servicioId, $fecha, $municipality, $ubicacion, $novedades, $fotoPath, $detalleServicio, $custodiaServicio, $facturacionSeparada);

    if ($stmt->execute()) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Registro agregado exitosamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#ff6b6b'
                        didOpen: () => {
                                        document.querySelector('.swal2-confirm').setAttribute('data-no-warning', '');
                                    }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'consulta_general.php';
                        }
                    });
                });
              </script>";
    } else {
        echo "Error al agregar el registro: " . $stmt->error;
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
    <title>Agregar Un Servicio </title>

    <!-- SweetAlert2 CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/session-check.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/CrearServicioRealizado.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
            text-align: center;
            margin-top: 100px;
            transition: all 0.3s ease;
        }

        .container:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        h2 {
            margin-bottom: 30px;
            color: #680c39;
            font-size: 28px;
            font-weight: 600;
        }

        label {
            display: block;
            margin: 15px 0 8px;
            color: black;
            font-size: 16px;
            text-align: left;
        }

        input[type="text"],
        input[type="date"],
        input[type="password"],
        select,
        textarea {
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

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="password"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2d0f2a;
            background-color: #fff;
        }

        .form-check {
            text-align: left;
            margin: 15px 0;
        }

        .form-check-label {
            margin-left: 10px;
            color: #333;
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
            margin-top: 20px;
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
    </style>
</head>

<body>

    <!-- Contenido principal -->
    <div class="container">
        <h2>Agregar Un Servico </h2>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="Cedula_Empleado_id_Servicios_Realizados">Técnico asignado</label>
                <select name="Cedula_Empleado_id_Servicios_Realizados" class="form-control" required>
                    <option value="">Seleccione un empleado</option>
                    <?php foreach ($empleados as $empleado): ?>
                        <option value="<?= htmlspecialchars($empleado['Cedula_Empleado_id']) ?>">
                            <?= htmlspecialchars($empleado['Cedula_Empleado_id']) ?> -
                            <?= htmlspecialchars($empleado['Nombre']) ?>     <?= htmlspecialchars($empleado['Apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="Vehiculo_id_Servicios_Realizados">Placa del Vehículo</label>
                <select name="Vehiculo_id_Servicios_Realizados" class="form-control" required>
                    <option value="">Seleccione un Vehículo</option>
                    <?php foreach ($cars as $car): ?>
                        <option value="<?= htmlspecialchars($car['Placa']) ?>">
                            <?= htmlspecialchars($car['Placa']) ?> - <?= htmlspecialchars($car['NombreCliente']) ?>
                            <?= htmlspecialchars($car['ApellidoCliente']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="Servicio_id_Servicios_Realizados">Servicio a realizar</label>
                <select name="Servicio_id_Servicios_Realizados" class="form-control" required>
                    <option value="">Seleccione un Servicio</option>
                    <?php foreach ($service as $service): ?>
                        <option value="<?= htmlspecialchars($service['Servicio_id']) ?>">
                            <?= htmlspecialchars($service['Nombre_Servicio']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="Fecha">Fecha</label>
                <input type="date" name="Fecha" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="municipality">Municipio</label>
                <select name="municipality" class="form-control" required>
                    <option value="">Seleccione un municipio</option>
                    <?php foreach ($municipality as $municipality): ?>
                        <option value="<?= htmlspecialchars($municipality['id']) ?>">
                            <?= htmlspecialchars($municipality['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="Ubicacion">Ubicación</label>
                <input type="text" name="Ubicacion" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="Novedades">Novedades</label>
                <input type="text" name="Novedades" class="form-control">
            </div>
            <div class="form-group">
                <label for="Fotos">Fotos (seleccionar archivo)</label>
                <input type="file" name="Fotos" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label for="Detalle_Servicio">Detalle del Servicio</label>
                <input type="text" name="Detalle_Servicio" class="form-control">
            </div>
            <div class="form-group">
                <label for="Custodia_Servicio">Custodia del Servicio</label>
                <input type="text" name="Custodia_Servicio" class="form-control">
            </div>
            <!-- <div class="form-check">
            <input type="checkbox" name="Facturacion_Separada" class="form-check-input" value="1">
            <label for="Facturacion_Separada" class="form-check-label">¿Facturación Separada?</label>
        </div> -->
            <button data-no-warning type="submit" class="btn btn-primary" style="background-color: #680c39; border-color: #680c39;">Agregar Registro</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>