<!-- header.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Sistema</title>

    <!-- 👇 Aquí cargas tu CSS general -->
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div id="mySidebar" class="sidebar">
    <div class="logo-container">
        <img src="Imagenes/Logo.jpg" alt="Logo" class="logo">
    </div>
    <div class="menu-links">
        <?php if ($currentPage !== 'dashboard.php') { ?>
            <a href="dashboard.php" data-no-warning>Dashboard</a>
        <?php } ?>

        <?php if ($currentPage !== 'gestionar_servicios.php' && usuarioTienePermiso($_SESSION['cedula'], 'crear_servicio', $conn)) { ?>
            <a href="gestionar_servicios.php" data-no-warning>Servicios</a>
        <?php } ?>

        <?php if ($currentPage !== 'consulta_general.php') { ?>
            <a href="consulta_general.php" data-no-warning>Consulta General</a>
        <?php } ?>

        <?php if ($currentPage !== 'consulta_identificacion.php') { ?>
            <a href="consulta_identificacion.php" data-no-warning>Consulta por Placa</a>
        <?php } ?>

        <a href="logout.php" data-no-warning>Cerrar Sesión</a>
    </div>
</div>


<script>
function toggleMenu() {
    document.querySelector('.menu-links').classList.toggle('show');
}
</script>
