<!-- header.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Sistema</title>

    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilos existentes... */
        
        /* Evitar que los enlaces se vuelvan azules al pasar el ratón */
        .sidebar a:hover {
            background-color: #440f33; /* Color oscuro para el hover */
            color: #fff; /* Mantener el texto en blanco */
            text-decoration: none; /* Eliminar subrayado */
            transform: translateY(-2px); /* Mantener el efecto de elevación */
        }
        
        /* Eliminar el estilo predeterminado de los enlaces */
        .sidebar a {
            text-decoration: none;
            color: #fff;
            background-color: #2d0f2a;
            margin-right: 15px;
            border-radius: 50px;
            padding: 12px 25px;
            font-size: 18px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        
        /* Asegurarse de que no hay cambios de color al hacer clic */
        .sidebar a:active, 
        .sidebar a:focus, 
        .sidebar a:visited {
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>

<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Verificar si el usuario es técnico (rol 2)
$esTecnico = isset($_SESSION['rol']) && $_SESSION['rol'] == 2;

// Verificar si la sesión tiene la cedula del usuario antes de usarla
$tieneSesionValida = isset($_SESSION['loggedin']) && isset($_SESSION['cedula']);
?>

<div id="mySidebar" class="sidebar">
    <div class="logo-container">
        <img src="Imagenes/Logo.jpg" alt="Logo" class="logo">
    </div>
    <div class="menu-links">
        <?php if ($currentPage !== 'dashboard.php' && $tieneSesionValida) { ?>
            <a href="dashboard.php" data-no-warning>Dashboard</a>
        <?php } ?>

        <?php if ($currentPage !== 'gestionar_servicios.php' && $tieneSesionValida && usuarioTienePermiso($_SESSION['cedula'], 'crear_servicio', $conn)) { ?>
            <a href="gestionar_servicios.php" data-no-warning>Servicios</a>
        <?php } ?>

        <?php if ($currentPage !== 'consulta_identificacion.php' && $tieneSesionValida) { ?>
            <a href="consulta_identificacion.php" data-no-warning>Consulta por Placa</a>
        <?php } ?>

        <?php if ($tieneSesionValida) { ?>
            <a href="logout.php" data-no-warning>Cerrar Sesión</a>
        <?php } else { ?>
            <a href="index.php" data-no-warning>Iniciar Sesión</a>
        <?php } ?>
    </div>
</div>


<script>
function toggleMenu() {
    document.querySelector('.menu-links').classList.toggle('show');
}
</script>
