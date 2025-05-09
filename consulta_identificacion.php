<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit;
}

require_once 'config.php';
include('validPermissions.php');
include('header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $placa = $_POST['placa'];

    // Modificar la consulta para buscar por la placa del vehículo
    $sql = "SELECT * FROM Servicios_Realizados WHERE Vehiculo_id_Servicios_Realizados = '$placa'";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta General</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('Imagenes/ConsultaIdentificacion.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            padding: 20px;
            margin-top: 120px;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #680c39;
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
        }

        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: auto;
        }

        th {
            background-color: #2d0f2a;
            color: white;
            font-weight: bold;
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td img {
            width: 100px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #666;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #2d0f2a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2d0f2a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #2d0f2a;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #2d0f2a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Contenido principal -->
<div class="main-content">
    <div class="container">
        <h2>Buscar Servicios por Placa de Vehículo</h2>
        <form method="POST">
            <label for="placa">Placa Vehículo:</label>
            <input type="text" id="placa" name="placa" required>
            <button  data-no-warning type="submit">Buscar</button>
        </form>

        <?php if (isset($result)): ?>
            <h3>Resultados</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Placa Vehículo</th>
                    <th>Fecha</th>
                    <th>Ubicación</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Servicio_Realizado_id'] . "</td>";
                        echo "<td>" . $row['Vehiculo_id_Servicios_Realizados'] . "</td>";
                        echo "<td>" . $row['Fecha'] . "</td>";
                        echo "<td>" . $row['Ubicación'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No se encontraron registros</td></tr>";
                }
                ?>
            </table>
        <?php endif; ?>
    </div>
</div>
<script>
        const checkSession = () => {
            fetch('session.php')
                .then(response => response.text())
                .then(data => {
                    if (data.includes('No active session')) {
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => console.error('Error al validar la sesión:', error));
        };

        checkSession();

        const beforeUnloadHandler = (event) => {
            if (event.target.activeElement?.hasAttribute('data-no-warning')) return;
            navigator.sendBeacon('session.php', new URLSearchParams({ logout: 'true' }));
        };

        window.addEventListener('beforeunload', beforeUnloadHandler);
</script>

</body>
</html>
