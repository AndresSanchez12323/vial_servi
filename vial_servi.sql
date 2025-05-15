-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-05-2025 a las 16:37:34
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `vial_servi`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas_recordatorios`
--

CREATE TABLE `alertas_recordatorios` (
  `Alertas_id` int(11) NOT NULL,
  `Servicio_Realizado_id_alertas_recordatorios` int(11) DEFAULT NULL,
  `Fecha_Alerta` date DEFAULT NULL,
  `Descripción_Alertas` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `Cedula_Id` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `Teléfono` varchar(20) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`Cedula_Id`, `Nombre`, `Apellido`, `Teléfono`, `Email`, `password`) VALUES
(1010, 'nube', 'sand', '310022', 'usuario1@gmail.com', '123456789'),
(5050, 'Brian', 'sanchez', '3104725242', 'onmigodcuenta@gmail.com', '$2y$10$fj1qq8LqNZsUbso/eiZ/XeL6vGo53NoeQmT2zrwvrFznmthohQzLe'),
(6060, 'nico', 'patiño', '2416464646464', 'hola@gmail.com', '$2y$10$l7C.sx2IVYMctX7qX3WStOe3CQ9YzVYiAcfUwe.ZonnIchGIXPA8e'),
(11111, 'Carlos', 'Sánchez', '321654987', 'carlos.sanchez@example.com', ''),
(22222, 'María', 'López', '654987321', 'maria.lopez@example.com', ''),
(33333, 'Andrés', 'Gómez', '987654123', 'andres.gomez@example.com', ''),
(44444, 'Sandra', 'Martínez', '123456789', 'sandra.martinez@example.com', ''),
(55555, 'Laura', 'Rodríguez', '456123789', 'laura.rodriguez@example.com', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `Cedula_Empleado_id` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `Contraseña` varchar(100) NOT NULL,
  `Rol_id` int(11) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Telefono` varchar(20) NOT NULL,
  `intentos_fallidos` int(11) DEFAULT 0,
  `fecha_hora_bloqueo` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`Cedula_Empleado_id`, `Nombre`, `Apellido`, `Contraseña`, `Rol_id`, `Email`, `Telefono`, `intentos_fallidos`, `fecha_hora_bloqueo`) VALUES
(1010, 'brian', 'rendon', '$2y$10$JQSVWMvzAwNhiA0JYD0fM.uUxZzylPV2Xx/7fp214BmOwTtSRSs1.', 2, 'brianloquito@gmail.com', '', 0, NULL),
(1040, 'edwin', 'sanchez', '$2y$10$dGu8U8jqn6E1BAhIqHk3Fe484U83cG7c/PfCpYi5mfxia5R17tTw6', 1, NULL, '', 0, NULL),
(2025, 'patricia', 'patiño', '$2y$10$aiUAA6oEMZVf.YpYhBRwUOOBgbpFV0fm8f6o5.zQQiiqkDA/RcMzK', 3, NULL, '', 0, NULL),
(4040, 'Super', 'Admin', '$2y$10$F22tgMXGlfLCFN2dRocsWOOlfWflEXeIbDcCJtfJXsEwMhnpRLtmS', 0, NULL, '', 0, NULL),
(6000, 'Luis', 'García', '$2y$10$A1UZH0ZOcDEuX/k5LDykfeG3NAWaQ3se/0uG1dAx/I3JoHlh2Sbw2', 2, 'luis.garcía@ejemplo.com', '3100000010', 0, NULL),
(6001, 'Ana', 'Torres', '$2y$10$examplehashedpasswordstring1234567890', 2, 'ana.torres@ejemplo.com', '3100000011', 0, NULL),
(6002, 'Jorge', 'Ramírez', '$2y$10$examplehashedpasswordstring1234567890', 2, 'jorge.ramírez@ejemplo.com', '3100000012', 0, NULL),
(6003, 'Lucía', 'Fernández', '$2y$10$examplehashedpasswordstring1234567890', 2, 'lucía.fernández@ejemplo.com', '3100000013', 0, NULL),
(6004, 'Carlos', 'Mejía', '$2y$10$examplehashedpasswordstring1234567890', 2, 'carlos.mejía@ejemplo.com', '3100000014', 0, NULL),
(6005, 'Diana', 'Martínez', '$2y$10$examplehashedpasswordstring1234567890', 2, 'diana.martínez@ejemplo.com', '3100000015', 0, NULL),
(6006, 'Santiago', 'López', '$2y$10$examplehashedpasswordstring1234567890', 2, 'santiago.lópez@ejemplo.com', '3100000016', 0, NULL),
(101112, 'Carlos ', 'marin', '12123', 6, NULL, '', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `nombre`, `email`, `mensaje`, `fecha`) VALUES
(1, 'nube', 'usuario1@gmail.com', ',nmklnklklkljkl', '2025-04-01 20:54:22'),
(2, 'Brian', 'brian@gmail.com', 'hola mi bro', '2025-04-01 21:10:17'),
(3, 'Juan', 'hola@gmail.com', 'llamame', '2025-04-02 23:14:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `municipios`
--

INSERT INTO `municipios` (`id`, `nombre`) VALUES
(1, 'Medellín'),
(2, 'Bello'),
(3, 'Itagüí'),
(4, 'Envigado'),
(5, 'Sabaneta'),
(6, 'La Estrella'),
(7, 'Copacabana'),
(8, 'Girardota'),
(9, 'Caldas'),
(10, 'Barbosa'),
(11, 'Rionegro'),
(12, 'Guarne'),
(13, 'La Ceja'),
(14, 'El Retiro'),
(15, 'El Santuario'),
(16, 'Marinilla'),
(17, 'Guatapé'),
(18, 'Santa Fe de Antioquia'),
(19, 'Apartadó'),
(20, 'Turbo'),
(21, 'Necoclí'),
(22, 'Carepa'),
(23, 'Chigorodó'),
(24, 'Caucasia'),
(25, 'Yarumal'),
(26, 'Amalfi'),
(27, 'Santa Rosa de Osos'),
(28, 'Sonsón'),
(29, 'Jericó'),
(30, 'Jardín'),
(31, 'Andes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `descripcion`) VALUES
(1, 'crear_servicio', 'Puede crear nuevos servicios'),
(2, 'leer_servicio', 'Puede ver los servicios'),
(3, 'actualizar_servicio', 'Puede modificar servicios'),
(4, 'eliminar_servicio', 'Puede eliminar servicios'),
(5, 'ver_reporte_administrador', 'Ver los reportes del administrador'),
(6, 'ver_reporte_tecnico', 'Ver los reportes del tecnico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(0, 'SuperAdmin', 'Manejo de roles y usuarios'),
(1, 'Administrador', 'Acceso total al sistema'),
(2, 'Técnico', 'Puede gestionar servicios, pero con restricciones'),
(3, 'Central', 'Acceso intermedio para monitoreo y control'),
(6, 'prueba', 'Prueba eliminacion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`rol_id`, `permiso_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(2, 2),
(2, 3),
(2, 6),
(3, 1),
(3, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `Servicio_id` int(11) NOT NULL,
  `Nombre_Servicio` varchar(100) NOT NULL,
  `Descripción` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`Servicio_id`, `Nombre_Servicio`, `Descripción`) VALUES
(1, 'Cambio de llantas', 'Cambio completo de las llantas del vehículo'),
(2, 'Reparación de motor', 'Servicio de reparación de motor averiado'),
(3, 'Cambio de aceite', 'Cambio de aceite y revisión de filtros'),
(4, 'Alineación y balanceo', 'Alineación y balanceo de ruedas para mejor estabilidad'),
(5, 'Revisión de frenos', 'Revisión y ajuste del sistema de frenos'),
(10, 'Conductor elegido', 'Personal capacitado para llevarte a salvo a cualquier lugar que desees ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_realizados`
--

CREATE TABLE `servicios_realizados` (
  `Servicio_Realizado_id` int(11) NOT NULL,
  `Cedula_Empleado_id_Servicios_Realizados` int(11) DEFAULT NULL,
  `Vehiculo_id_Servicios_Realizados` varchar(20) DEFAULT NULL,
  `Servicio_id_Servicios_Realizados` int(11) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `municipio` int(11) DEFAULT 11,
  `Ubicación` varchar(255) DEFAULT NULL,
  `Novedades` text DEFAULT NULL,
  `Fotos` text DEFAULT NULL,
  `Detalle_Servicio` text DEFAULT NULL,
  `Custodia_Servicio` text DEFAULT NULL,
  `Facturación_Separada` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios_realizados`
--

INSERT INTO `servicios_realizados` (`Servicio_Realizado_id`, `Cedula_Empleado_id_Servicios_Realizados`, `Vehiculo_id_Servicios_Realizados`, `Servicio_id_Servicios_Realizados`, `Fecha`, `municipio`, `Ubicación`, `Novedades`, `Fotos`, `Detalle_Servicio`, `Custodia_Servicio`, `Facturación_Separada`) VALUES
(1, NULL, 'ABC123', 1, '2024-10-01', 11, 'Avenida 122, Ciudad F', 'Cambio de llantas necesario', 'uploads/img_681d8699c25663.30185867.jpg', 'Cambio de llantas delantero y trasero', 'Custodia en taller', 1),
(33, NULL, 'ABC123', 1, '2024-11-06', 11, 'Poli JIC', 'Motor con sobrecalentamiento', 'uploads/img_681d8699c25663.30185867.jpg', 'dsfdsfds', 'Custodia en taller hasta reparación', 0),
(34, NULL, 'ABC123', 1, '2024-10-01', 11, 'Avenida 122, Ciudad F', 'Cambio de llantas necesario', 'uploads/img_681d8699c25663.30185867.jpg', 'Cambio de llantas delantero y trasero', 'Custodia en taller', 1),
(35, NULL, 'DEF456', 2, '2024-10-05', 11, 'Calle 10, Ciudad G', 'Falla en motor', 'uploads/img_681d8699c25663.30185867.jpg', 'Reparación completa del motor', 'Custodia hasta prueba', 0),
(36, NULL, 'GHI101', 3, '2024-10-10', 11, 'Calle 15, Ciudad H', 'Cambio de aceite solicitado', 'uploads/img_681d8699c25663.30185867.jpg', 'Cambio de aceite y filtros', 'Sin custodia', 1),
(37, NULL, 'JKL112', 4, '2024-10-15', 11, 'Avenida 20, Ciudad I', 'Desbalance en ruedas', 'uploads/img_681d8699c25663.30185867.jpg', 'Alineación y balanceo', 'Custodia temporal', 0),
(38, NULL, 'XYZ789', 5, '2024-10-20', 11, 'Calle 30, Ciudad J', 'Revisión de frenos urgente', 'uploads/img_681d8699c25663.30185867.jpg', 'Ajuste y revisión del sistema de frenos', 'Custodia en taller', 1),
(39, NULL, 'MNO345', 2, '2024-10-25', 11, 'Calle 25, Ciudad K', 'Reparación de motor', 'uploads/img_681d8699c25663.30185867.jpg', 'Servicio de reparación de motor', 'Sin custodia', 0),
(40, 1010, 'PQR678', 1, '2024-10-30', 11, 'Avenida 40, Ciudad L', 'Cambio de llantas tras desgaste', 'uploads/img_681d8699c25663.30185867.jpg', 'Cambio de llantas y ajuste de presión', 'Custodia hasta prueba', 1),
(41, NULL, 'STU901', 4, '2024-11-01', 11, 'Calle 50, Ciudad M', 'Desbalance en ruedas', 'uploads/img_681d8699c25663.30185867.jpg', 'Alineación y balanceo', 'Custodia temporal', 0),
(42, NULL, 'ABC123', 3, '2024-11-03', 11, 'Avenida 60, Ciudad N', 'Cambio de aceite', 'uploads/img_681d8699c25663.30185867.jpg', 'Cambio de aceite y revisión de motor', 'Sin custodia', 1),
(45, NULL, 'ABC123', 2, '2024-11-22', 11, 'Poli JIC', 'Motor con sobrecalentamiento', 'uploads/img_681d8699c25663.30185867.jpg', 'Reparación del sistema de enfriamiento', 'Custodia en taller hasta reparación', 1),
(46, 1040, 'ABC123', 5, '2025-04-01', 11, 'poli jic', 'camara', 'uploads/img_681d8699c25663.30185867.jpg', 'revision', 'linterna', 1),
(67, 1010, 'ABC123', NULL, '2025-05-08', 11, 'Km3 via llanogrande', 'Sin novedades', 'uploads/img_681d8699c25663.30185867.jpg', 'Solucionado', 'Entrega al cliente', 0),
(73, 1010, 'XYZ789', 5, '2025-05-09', 14, 'CC Viva', 'El cliente solicita el fin se semana completo en el parqueadero', 'uploads/img_681d9803117623.97392120.png', 'Solucionado', 'Entrega al cliente ', 0),
(74, 1010, 'MNI982', 10, '2025-05-09', 13, 'CC Viva', 'El cliente solicita el fin se semana completo en el parqueadero', '', 'Solucionado', 'Entrega al cliente', 0),
(192, 1010, 'STU901', 3, '2024-07-18', 5, 'Cra 44 # 24-20', 'Cliente reporta ruidos inusuales', '', 'Cambio de aceite', 'Entrega al cliente', 0),
(193, 1010, 'PQR678', 2, '2024-11-01', 3, 'Cra 78 # 14-49', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Custodia en taller', 1),
(194, 1010, 'ABC123', 2, '2024-08-06', 9, 'Cra 96 # 27-27', 'Revisión preventiva', '', 'Reparación de motor', 'Sin custodia', 0),
(195, 1010, 'JKL112', 5, '2024-06-09', 4, 'Cra 14 # 69-8', 'Solicitado por central', '', 'Revisión de frenos', 'Custodia temporal', 1),
(196, 1010, 'MNO345', 5, '2024-10-29', 1, 'Cra 15 # 3-21', 'Mantenimiento programado', '', 'Revisión de frenos', 'Custodia en taller', 0),
(197, 1010, 'MNI982', 3, '2025-04-23', 8, 'Cra 18 # 28-43', 'Urgencia por fallas mecánicas', '', 'Cambio de aceite', 'Sin custodia', 0),
(198, 1010, 'DEF456', 2, '2024-12-24', 1, 'Cra 89 # 53-25', 'Revisión preventiva', '', 'Reparación de motor', 'Entrega al cliente', 0),
(199, 1010, 'XYZ789', 4, '2024-08-25', 10, 'Cra 97 # 96-34', 'Revisión preventiva', '', 'Alineación y balanceo', 'Custodia en taller', 1),
(200, 1010, 'MNO345', 3, '2025-01-04', 11, 'Cra 67 # 78-2', 'Mantenimiento programado', '', 'Cambio de aceite', 'Sin custodia', 0),
(201, 1010, 'DEF456', 5, '2024-12-05', 3, 'Cra 90 # 93-12', 'Solicitado por central', '', 'Revisión de frenos', 'Custodia temporal', 1),
(202, 1010, 'ABC123', 5, '2025-02-13', 4, 'Cra 36 # 66-7', 'Solicitado por central', '', 'Revisión de frenos', 'Custodia temporal', 0),
(203, 1010, 'MNO345', 1, '2025-02-06', 1, 'Cra 24 # 98-35', 'Urgencia por fallas mecánicas', '', 'Cambio de llantas', 'Entrega al cliente', 0),
(204, 1010, 'STU901', 2, '2024-08-25', 9, 'Cra 51 # 38-13', 'Mantenimiento programado', '', 'Reparación de motor', 'Custodia temporal', 1),
(205, 1010, 'STU901', 10, '2024-12-18', 10, 'Cra 15 # 56-8', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Sin custodia', 0),
(206, 1010, 'ABC123', 10, '2025-02-14', 7, 'Cra 71 # 5-15', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Custodia temporal', 1),
(207, 1010, 'XYZ789', 10, '2025-04-18', 4, 'Cra 100 # 22-47', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Sin custodia', 0),
(208, 1010, 'XYZ789', 5, '2024-07-02', 6, 'Cra 17 # 64-40', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Custodia temporal', 0),
(209, 1010, 'ABC123', 10, '2024-05-06', 4, 'Cra 50 # 50-11', 'Solicitado por central', '', 'Conductor elegido', 'Entrega al cliente', 1),
(210, 1010, 'DEF456', 1, '2024-08-02', 3, 'Cra 24 # 22-49', 'Urgencia por fallas mecánicas', '', 'Cambio de llantas', 'Custodia temporal', 1),
(211, 1010, 'STU901', 5, '2024-07-24', 9, 'Cra 59 # 37-40', 'Mantenimiento programado', '', 'Revisión de frenos', 'Custodia temporal', 0),
(212, 1010, 'ABC123', 1, '2025-02-21', 1, 'Cra 54 # 78-29', 'Revisión preventiva', '', 'Cambio de llantas', 'Custodia temporal', 1),
(213, 1010, 'GHI101', 5, '2024-09-10', 8, 'Cra 43 # 48-15', 'Mantenimiento programado', '', 'Revisión de frenos', 'Sin custodia', 0),
(214, 1010, 'STU901', 10, '2024-07-26', 2, 'Cra 36 # 7-23', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Custodia en taller', 1),
(215, 1010, 'XYZ789', 3, '2024-10-18', 3, 'Cra 7 # 66-37', 'Solicitado por central', '', 'Cambio de aceite', 'Custodia en taller', 1),
(216, 1010, 'MNO345', 10, '2025-02-20', 3, 'Cra 30 # 17-47', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Custodia temporal', 0),
(217, 1010, 'MNO345', 4, '2024-12-19', 2, 'Cra 19 # 19-6', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Sin custodia', 0),
(218, 1010, 'JKL112', 10, '2024-12-31', 5, 'Cra 24 # 10-7', 'Mantenimiento programado', '', 'Conductor elegido', 'Custodia en taller', 0),
(219, 1010, 'XYZ789', 3, '2024-05-22', 2, 'Cra 81 # 95-41', 'Urgencia por fallas mecánicas', '', 'Cambio de aceite', 'Sin custodia', 1),
(220, 1010, 'DEF456', 1, '2024-07-22', 8, 'Cra 14 # 12-16', 'Cliente reporta ruidos inusuales', '', 'Cambio de llantas', 'Custodia temporal', 0),
(221, 1010, 'MNO345', 2, '2024-05-16', 7, 'Cra 71 # 96-33', 'Mantenimiento programado', '', 'Reparación de motor', 'Entrega al cliente', 0),
(222, 1010, 'JKL112', 2, '2024-11-29', 5, 'Cra 86 # 43-22', 'Cliente reporta ruidos inusuales', '', 'Reparación de motor', 'Custodia en taller', 0),
(223, 1010, 'MNO345', 1, '2024-05-27', 8, 'Cra 21 # 29-28', 'Mantenimiento programado', '', 'Cambio de llantas', 'Custodia temporal', 1),
(224, 1010, 'MNO345', 5, '2025-01-22', 9, 'Cra 18 # 89-27', 'Urgencia por fallas mecánicas', '', 'Revisión de frenos', 'Custodia en taller', 1),
(225, 1010, 'STU901', 10, '2024-08-01', 2, 'Cra 44 # 39-7', 'Revisión preventiva', '', 'Conductor elegido', 'Custodia en taller', 0),
(226, 1010, 'ABC123', 4, '2025-02-23', 3, 'Cra 57 # 84-39', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Custodia en taller', 0),
(227, 1010, 'ABC123', 1, '2025-01-04', 1, 'Cra 5 # 37-50', 'Cliente reporta ruidos inusuales', '', 'Cambio de llantas', 'Entrega al cliente', 1),
(228, 1010, 'ABC123', 5, '2024-07-30', 11, 'Cra 31 # 27-12', 'Revisión preventiva', '', 'Revisión de frenos', 'Sin custodia', 1),
(229, 1010, 'JKL112', 4, '2024-12-05', 1, 'Cra 13 # 81-35', 'Cliente reporta ruidos inusuales', '', 'Alineación y balanceo', 'Custodia en taller', 0),
(230, 1010, 'MNI982', 10, '2025-04-06', 7, 'Cra 60 # 100-26', 'Revisión preventiva', '', 'Conductor elegido', 'Sin custodia', 0),
(231, 1010, 'GHI101', 3, '2024-06-25', 6, 'Cra 48 # 99-20', 'Cliente reporta ruidos inusuales', '', 'Cambio de aceite', 'Sin custodia', 0),
(232, 1010, 'DEF456', 10, '2024-05-19', 4, 'Cra 38 # 99-1', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Custodia en taller', 1),
(233, 1010, 'GHI101', 2, '2024-08-05', 6, 'Cra 49 # 70-1', 'Mantenimiento programado', '', 'Reparación de motor', 'Custodia en taller', 1),
(234, 1010, 'JKL112', 3, '2024-07-26', 10, 'Cra 34 # 9-32', 'Solicitado por central', '', 'Cambio de aceite', 'Sin custodia', 0),
(235, 1010, 'XYZ789', 3, '2025-02-08', 4, 'Cra 67 # 29-33', 'Revisión preventiva', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(236, 1010, 'GHI101', 5, '2025-03-21', 6, 'Cra 95 # 39-30', 'Solicitado por central', '', 'Revisión de frenos', 'Entrega al cliente', 1),
(237, 1010, 'DEF456', 5, '2024-08-30', 2, 'Cra 26 # 63-47', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Entrega al cliente', 0),
(238, 1010, 'DEF456', 4, '2024-05-17', 3, 'Cra 86 # 34-40', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Sin custodia', 1),
(239, 1010, 'GHI101', 10, '2025-04-18', 9, 'Cra 34 # 36-15', 'Mantenimiento programado', '', 'Conductor elegido', 'Entrega al cliente', 0),
(240, 1010, 'MNI982', 5, '2024-12-27', 6, 'Cra 10 # 24-39', 'Solicitado por central', '', 'Revisión de frenos', 'Custodia en taller', 1),
(241, 1010, 'DEF456', 1, '2025-04-29', 10, 'Cra 4 # 57-28', 'Cliente reporta ruidos inusuales', '', 'Cambio de llantas', 'Sin custodia', 1),
(242, 1010, 'STU901', 2, '2025-09-04', 9, 'Cra 18 # 31-3', 'Solicitado por central', '', 'Reparación de motor', 'Entrega al cliente', 1),
(243, 1010, 'XYZ789', 2, '2025-08-05', 5, 'Cra 99 # 56-33', 'Mantenimiento programado', '', 'Reparación de motor', 'Custodia en taller', 0),
(244, 1010, 'JKL112', 2, '2025-10-19', 5, 'Cra 86 # 65-14', 'Mantenimiento programado', '', 'Reparación de motor', 'Entrega al cliente', 0),
(245, 1010, 'JKL112', 2, '2025-07-30', 10, 'Cra 10 # 5-35', 'Cliente reporta ruidos inusuales', '', 'Reparación de motor', 'Custodia en taller', 0),
(246, 1010, 'DEF456', 10, '2025-10-06', 4, 'Cra 1 # 75-6', 'Revisión preventiva', '', 'Conductor elegido', 'Custodia temporal', 1),
(247, 1010, 'STU901', 4, '2025-08-21', 11, 'Cra 60 # 79-46', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(248, 1010, 'MNI982', 4, '2025-12-03', 3, 'Cra 17 # 30-20', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Sin custodia', 1),
(249, 1010, 'JKL112', 2, '2025-05-30', 7, 'Cra 82 # 47-13', 'Mantenimiento programado', '', 'Reparación de motor', 'Entrega al cliente', 0),
(250, 1010, 'STU901', 10, '2025-09-05', 3, 'Cra 7 # 80-46', 'Mantenimiento programado', '', 'Conductor elegido', 'Custodia en taller', 1),
(251, 1010, 'XYZ789', 1, '2025-06-15', 9, 'Cra 11 # 91-45', 'Mantenimiento programado', '', 'Cambio de llantas', 'Custodia temporal', 1),
(252, 1010, 'JKL112', 2, '2025-11-17', 10, 'Cra 57 # 14-24', 'Mantenimiento programado', '', 'Reparación de motor', 'Sin custodia', 1),
(253, 1010, 'STU901', 2, '2025-06-29', 8, 'Cra 100 # 39-33', 'Cliente reporta ruidos inusuales', '', 'Reparación de motor', 'Entrega al cliente', 0),
(254, 1010, 'MNI982', 4, '2025-08-25', 6, 'Cra 72 # 12-30', 'Solicitado por central', '', 'Alineación y balanceo', 'Sin custodia', 0),
(255, 1010, 'MNI982', 10, '2025-09-29', 8, 'Cra 77 # 9-6', 'Revisión preventiva', '', 'Conductor elegido', 'Entrega al cliente', 0),
(256, 1010, 'GHI101', 2, '2025-05-18', 7, 'Cra 27 # 4-18', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Custodia temporal', 1),
(257, 1010, 'GHI101', 3, '2025-05-16', 10, 'Cra 64 # 18-6', 'Revisión preventiva', '', 'Cambio de aceite', 'Custodia temporal', 0),
(258, 1010, 'GHI101', 3, '2025-11-29', 9, 'Cra 22 # 6-35', 'Cliente reporta ruidos inusuales', '', 'Cambio de aceite', 'Custodia temporal', 1),
(259, 1010, 'GHI101', 10, '2025-10-16', 3, 'Cra 16 # 82-46', 'Solicitado por central', '', 'Conductor elegido', 'Custodia temporal', 1),
(260, 1010, 'ABC123', 5, '2025-06-18', 11, 'Cra 51 # 53-48', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Custodia temporal', 1),
(261, 1010, 'MNI982', 3, '2025-08-30', 1, 'Cra 41 # 3-34', 'Revisión preventiva', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(262, 1010, 'JKL112', 3, '2025-09-26', 6, 'Cra 43 # 39-40', 'Cliente reporta ruidos inusuales', '', 'Cambio de aceite', 'Custodia temporal', 0),
(263, 1010, 'MNI982', 3, '2025-09-03', 1, 'Cra 36 # 76-1', 'Solicitado por central', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(264, 1010, 'JKL112', 3, '2025-06-07', 4, 'Cra 82 # 58-24', 'Mantenimiento programado', '', 'Cambio de aceite', 'Custodia en taller', 1),
(265, 1010, 'XYZ789', 2, '2025-06-14', 4, 'Cra 99 # 58-12', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Custodia temporal', 0),
(266, 1010, 'XYZ789', 10, '2025-11-18', 7, 'Cra 87 # 11-18', 'Mantenimiento programado', '', 'Conductor elegido', 'Custodia temporal', 1),
(267, 1010, 'GHI101', 2, '2025-07-10', 3, 'Cra 81 # 78-26', 'Mantenimiento programado', '', 'Reparación de motor', 'Custodia en taller', 1),
(268, 1010, 'ABC123', 4, '2025-06-04', 10, 'Cra 41 # 13-45', 'Solicitado por central', '', 'Alineación y balanceo', 'Custodia en taller', 1),
(269, 1010, 'GHI101', 5, '2025-10-07', 5, 'Cra 56 # 95-34', 'Revisión preventiva', '', 'Revisión de frenos', 'Sin custodia', 0),
(270, 1010, 'JKL112', 5, '2025-08-10', 5, 'Cra 61 # 5-42', 'Urgencia por fallas mecánicas', '', 'Revisión de frenos', 'Custodia en taller', 1),
(271, 1010, 'STU901', 3, '2025-11-18', 2, 'Cra 13 # 81-17', 'Revisión preventiva', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(272, 1010, 'MNI982', 2, '2025-11-27', 8, 'Cra 51 # 8-24', 'Cliente reporta ruidos inusuales', '', 'Reparación de motor', 'Custodia temporal', 1),
(273, 1010, 'MNO345', 5, '2025-06-09', 8, 'Cra 92 # 37-34', 'Solicitado por central', '', 'Revisión de frenos', 'Sin custodia', 0),
(274, 1010, 'ABC123', 5, '2025-05-10', 1, 'Cra 51 # 34-20', 'Mantenimiento programado', '', 'Revisión de frenos', 'Custodia temporal', 1),
(275, 1010, 'XYZ789', 4, '2025-10-09', 8, 'Cra 83 # 92-23', 'Revisión preventiva', '', 'Alineación y balanceo', 'Custodia temporal', 1),
(276, 1010, 'PQR678', 4, '2025-08-28', 9, 'Cra 77 # 30-33', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(277, 1010, 'MNO345', 4, '2025-12-12', 1, 'Cra 98 # 94-16', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Custodia en taller', 0),
(278, 1010, 'XYZ789', 10, '2025-10-14', 1, 'Cra 70 # 62-18', 'Revisión preventiva', '', 'Conductor elegido', 'Entrega al cliente', 1),
(279, 1010, 'MNI982', 3, '2025-08-10', 5, 'Cra 28 # 76-23', 'Revisión preventiva', '', 'Cambio de aceite', 'Custodia en taller', 0),
(280, 1010, 'MNI982', 4, '2025-06-22', 2, 'Cra 74 # 18-50', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Entrega al cliente', 1),
(281, 1010, 'DEF456', 5, '2025-11-04', 1, 'Cra 35 # 83-34', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Entrega al cliente', 1),
(282, 1010, 'DEF456', 10, '2025-06-04', 5, 'Cra 97 # 78-6', 'Revisión preventiva', '', 'Conductor elegido', 'Custodia en taller', 0),
(283, 1010, 'PQR678', 1, '2025-12-25', 10, 'Cra 42 # 99-40', 'Revisión preventiva', '', 'Cambio de llantas', 'Custodia temporal', 0),
(284, 1010, 'STU901', 4, '2025-08-11', 3, 'Cra 75 # 72-27', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(285, 1010, 'PQR678', 5, '2025-06-11', 5, 'Cra 85 # 34-24', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Custodia en taller', 1),
(286, 1010, 'MNO345', 3, '2025-08-22', 2, 'Cra 59 # 6-32', 'Solicitado por central', '', 'Cambio de aceite', 'Sin custodia', 1),
(287, 1010, 'ABC123', 10, '2025-09-06', 8, 'Cra 22 # 54-30', 'Mantenimiento programado', '', 'Conductor elegido', 'Custodia en taller', 0),
(288, 1010, 'STU901', 1, '2025-06-09', 10, 'Cra 98 # 31-30', 'Urgencia por fallas mecánicas', '', 'Cambio de llantas', 'Custodia en taller', 0),
(289, 1010, 'JKL112', 10, '2025-10-12', 2, 'Cra 45 # 53-12', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Sin custodia', 0),
(290, 1010, 'MNI982', 5, '2025-09-25', 11, 'Cra 55 # 44-46', 'Solicitado por central', '', 'Revisión de frenos', 'Custodia en taller', 0),
(291, 1010, 'MNO345', 5, '2025-06-15', 11, 'Cra 73 # 34-25', 'Solicitado por central', '', 'Revisión de frenos', 'Entrega al cliente', 1),
(292, 1010, 'GHI101', 2, '2025-09-16', 11, 'Cra 51 # 24-27', 'Cliente reporta ruidos inusuales', '', 'Reparación de motor', 'Custodia en taller', 1),
(293, 1010, 'PQR678', 1, '2025-11-27', 5, 'Cra 75 # 64-46', 'Mantenimiento programado', '', 'Cambio de llantas', 'Entrega al cliente', 0),
(294, 1010, 'MNI982', 10, '2025-09-02', 6, 'Cra 63 # 35-47', 'Revisión preventiva', '', 'Conductor elegido', 'Sin custodia', 0),
(295, 1010, 'XYZ789', 4, '2025-07-16', 2, 'Cra 82 # 9-3', 'Revisión preventiva', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(296, 1010, 'JKL112', 10, '2025-11-17', 11, 'Cra 40 # 61-15', 'Revisión preventiva', '', 'Conductor elegido', 'Sin custodia', 0),
(297, 1010, 'DEF456', 2, '2025-09-04', 6, 'Cra 49 # 34-3', 'Mantenimiento programado', '', 'Reparación de motor', 'Custodia temporal', 0),
(298, 1010, 'DEF456', 10, '2025-05-14', 9, 'Cra 77 # 44-8', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Custodia temporal', 1),
(299, 1010, 'STU901', 1, '2025-06-19', 4, 'Cra 72 # 11-48', 'Mantenimiento programado', '', 'Cambio de llantas', 'Custodia temporal', 0),
(300, 1010, 'ABC123', 10, '2025-08-22', 9, 'Cra 88 # 34-2', 'Solicitado por central', '', 'Conductor elegido', 'Custodia temporal', 0),
(301, 1010, 'STU901', 2, '2025-09-11', 7, 'Cra 55 # 28-33', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Custodia temporal', 0),
(302, 1010, 'DEF456', 3, '2025-11-09', 1, 'Cra 99 # 73-35', 'Revisión preventiva', '', 'Cambio de aceite', 'Sin custodia', 1),
(303, 1010, 'STU901', 1, '2025-09-04', 7, 'Cra 63 # 7-41', 'Cliente reporta ruidos inusuales', '', 'Cambio de llantas', 'Entrega al cliente', 1),
(304, 1010, 'STU901', 2, '2025-10-01', 3, 'Cra 27 # 13-40', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Sin custodia', 1),
(305, 1010, 'MNI982', 2, '2025-05-05', 1, 'Cra 74 # 84-20', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Entrega al cliente', 1),
(306, 1010, 'MNO345', 3, '2025-07-29', 6, 'Cra 65 # 59-1', 'Mantenimiento programado', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(307, 1010, 'STU901', 5, '2025-08-09', 7, 'Cra 33 # 25-14', 'Mantenimiento programado', '', 'Revisión de frenos', 'Custodia en taller', 1),
(308, 1010, 'XYZ789', 1, '2025-05-28', 2, 'Cra 32 # 56-41', 'Revisión preventiva', '', 'Cambio de llantas', 'Entrega al cliente', 0),
(309, 1010, 'XYZ789', 5, '2025-09-05', 6, 'Cra 12 # 83-1', 'Revisión preventiva', '', 'Revisión de frenos', 'Custodia en taller', 0),
(310, 1010, 'DEF456', 1, '2025-06-12', 11, 'Cra 41 # 89-50', 'Urgencia por fallas mecánicas', '', 'Cambio de llantas', 'Custodia en taller', 0),
(311, 1010, 'STU901', 2, '2025-08-28', 6, 'Cra 10 # 43-38', 'Solicitado por central', '', 'Reparación de motor', 'Sin custodia', 0),
(312, 1010, 'MNO345', 3, '2025-09-11', 7, 'Cra 11 # 98-7', 'Revisión preventiva', '', 'Cambio de aceite', 'Custodia temporal', 1),
(313, 1010, 'MNI982', 1, '2025-08-13', 3, 'Cra 37 # 96-46', 'Urgencia por fallas mecánicas', '', 'Cambio de llantas', 'Entrega al cliente', 1),
(314, 1010, 'MNO345', 10, '2025-11-13', 9, 'Cra 81 # 21-16', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Sin custodia', 0),
(315, 1010, 'MNI982', 1, '2025-06-26', 6, 'Cra 9 # 13-42', 'Revisión preventiva', '', 'Cambio de llantas', 'Sin custodia', 1),
(316, 1010, 'DEF456', 2, '2025-10-16', 4, 'Cra 33 # 15-21', 'Revisión preventiva', '', 'Reparación de motor', 'Sin custodia', 1),
(317, 1010, 'MNO345', 5, '2025-06-04', 6, 'Cra 89 # 58-25', 'Solicitado por central', '', 'Revisión de frenos', 'Sin custodia', 0),
(318, 1010, 'JKL112', 10, '2025-05-30', 11, 'Cra 50 # 81-37', 'Solicitado por central', '', 'Conductor elegido', 'Entrega al cliente', 1),
(319, 1010, 'JKL112', 4, '2025-09-15', 11, 'Cra 53 # 82-26', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(320, 1010, 'MNI982', 3, '2025-05-31', 4, 'Cra 19 # 36-18', 'Solicitado por central', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(321, 1010, 'XYZ789', 4, '2025-12-23', 8, 'Cra 88 # 44-34', 'Solicitado por central', '', 'Alineación y balanceo', 'Sin custodia', 1),
(322, 1010, 'JKL112', 10, '2025-11-06', 7, 'Cra 94 # 62-26', 'Revisión preventiva', '', 'Conductor elegido', 'Entrega al cliente', 1),
(323, 1010, 'PQR678', 3, '2025-09-09', 11, 'Cra 5 # 62-30', 'Urgencia por fallas mecánicas', '', 'Cambio de aceite', 'Custodia temporal', 1),
(324, 1010, 'DEF456', 10, '2025-09-09', 6, 'Cra 71 # 96-3', 'Solicitado por central', '', 'Conductor elegido', 'Custodia temporal', 0),
(325, 1010, 'MNI982', 4, '2025-07-09', 4, 'Cra 97 # 98-31', 'Revisión preventiva', '', 'Alineación y balanceo', 'Custodia en taller', 1),
(326, 1010, 'DEF456', 5, '2025-11-09', 1, 'Cra 41 # 56-15', 'Mantenimiento programado', '', 'Revisión de frenos', 'Entrega al cliente', 0),
(327, 1010, 'JKL112', 10, '2025-11-10', 3, 'Cra 4 # 89-3', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Custodia en taller', 1),
(328, 1010, 'MNO345', 1, '2025-08-02', 2, 'Cra 100 # 58-12', 'Solicitado por central', '', 'Cambio de llantas', 'Sin custodia', 1),
(329, 1010, 'ABC123', 5, '2025-07-15', 10, 'Cra 34 # 24-47', 'Mantenimiento programado', '', 'Revisión de frenos', 'Custodia temporal', 1),
(330, 1010, 'PQR678', 3, '2025-09-02', 2, 'Cra 27 # 35-44', 'Cliente reporta ruidos inusuales', '', 'Cambio de aceite', 'Custodia en taller', 0),
(331, 1010, 'XYZ789', 10, '2025-09-15', 4, 'Cra 28 # 66-29', 'Mantenimiento programado', '', 'Conductor elegido', 'Sin custodia', 0),
(332, 1010, 'DEF456', 4, '2025-07-08', 3, 'Cra 44 # 95-49', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Entrega al cliente', 1),
(333, 1010, 'DEF456', 10, '2025-09-14', 1, 'Cra 23 # 5-40', 'Mantenimiento programado', '', 'Conductor elegido', 'Entrega al cliente', 0),
(334, 1010, 'JKL112', 10, '2025-06-13', 11, 'Cra 26 # 10-1', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Entrega al cliente', 1),
(335, 1010, 'JKL112', 10, '2025-07-04', 4, 'Cra 58 # 35-3', 'Solicitado por central', '', 'Conductor elegido', 'Sin custodia', 1),
(336, 1010, 'JKL112', 5, '2025-07-08', 11, 'Cra 68 # 78-43', 'Revisión preventiva', '', 'Revisión de frenos', 'Custodia temporal', 1),
(337, 1010, 'JKL112', 5, '2025-12-18', 11, 'Cra 6 # 71-29', 'Mantenimiento programado', '', 'Revisión de frenos', 'Entrega al cliente', 1),
(338, 1010, 'STU901', 1, '2025-05-15', 1, 'Cra 94 # 51-34', 'Mantenimiento programado', '', 'Cambio de llantas', 'Custodia temporal', 0),
(339, 1010, 'DEF456', 10, '2025-07-27', 9, 'Cra 99 # 62-31', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Sin custodia', 0),
(340, 1010, 'JKL112', 1, '2025-12-02', 7, 'Cra 49 # 12-22', 'Mantenimiento programado', '', 'Cambio de llantas', 'Sin custodia', 0),
(341, 1010, 'DEF456', 3, '2025-12-24', 3, 'Cra 28 # 19-46', 'Mantenimiento programado', '', 'Cambio de aceite', 'Custodia temporal', 1),
(342, 1010, 'STU901', 2, '2025-09-04', 9, 'Cra 18 # 31-3', 'Solicitado por central', '', 'Reparación de motor', 'Entrega al cliente', 1),
(343, 1010, 'XYZ789', 2, '2025-08-05', 5, 'Cra 99 # 56-33', 'Mantenimiento programado', '', 'Reparación de motor', 'Custodia en taller', 0),
(344, 1010, 'JKL112', 2, '2025-10-19', 5, 'Cra 86 # 65-14', 'Mantenimiento programado', '', 'Reparación de motor', 'Entrega al cliente', 0),
(345, 1010, 'JKL112', 2, '2025-07-30', 10, 'Cra 10 # 5-35', 'Cliente reporta ruidos inusuales', '', 'Reparación de motor', 'Custodia en taller', 0),
(346, 1010, 'DEF456', 10, '2025-10-06', 4, 'Cra 1 # 75-6', 'Revisión preventiva', '', 'Conductor elegido', 'Custodia temporal', 1),
(347, 1010, 'STU901', 4, '2025-08-21', 11, 'Cra 60 # 79-46', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(348, 1010, 'MNI982', 4, '2025-12-03', 3, 'Cra 17 # 30-20', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Sin custodia', 1),
(349, 1010, 'JKL112', 2, '2025-05-30', 7, 'Cra 82 # 47-13', 'Mantenimiento programado', '', 'Reparación de motor', 'Entrega al cliente', 0),
(350, 1010, 'STU901', 10, '2025-09-05', 3, 'Cra 7 # 80-46', 'Mantenimiento programado', '', 'Conductor elegido', 'Custodia en taller', 1),
(351, 1010, 'XYZ789', 1, '2025-06-15', 9, 'Cra 11 # 91-45', 'Mantenimiento programado', '', 'Cambio de llantas', 'Custodia temporal', 1),
(352, 1010, 'JKL112', 2, '2025-11-17', 10, 'Cra 57 # 14-24', 'Mantenimiento programado', '', 'Reparación de motor', 'Sin custodia', 1),
(353, 1010, 'STU901', 2, '2025-06-29', 8, 'Cra 100 # 39-33', 'Cliente reporta ruidos inusuales', '', 'Reparación de motor', 'Entrega al cliente', 0),
(354, 1010, 'MNI982', 4, '2025-08-25', 6, 'Cra 72 # 12-30', 'Solicitado por central', '', 'Alineación y balanceo', 'Sin custodia', 0),
(355, 1010, 'MNI982', 10, '2025-09-29', 8, 'Cra 77 # 9-6', 'Revisión preventiva', '', 'Conductor elegido', 'Entrega al cliente', 0),
(356, 1010, 'GHI101', 2, '2025-05-18', 7, 'Cra 27 # 4-18', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Custodia temporal', 1),
(357, 1010, 'GHI101', 3, '2025-05-16', 10, 'Cra 64 # 18-6', 'Revisión preventiva', '', 'Cambio de aceite', 'Custodia temporal', 0),
(358, 1010, 'GHI101', 3, '2025-11-29', 9, 'Cra 22 # 6-35', 'Cliente reporta ruidos inusuales', '', 'Cambio de aceite', 'Custodia temporal', 1),
(359, 1010, 'GHI101', 10, '2025-10-16', 3, 'Cra 16 # 82-46', 'Solicitado por central', '', 'Conductor elegido', 'Custodia temporal', 1),
(360, 1010, 'ABC123', 5, '2025-06-18', 11, 'Cra 51 # 53-48', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Custodia temporal', 1),
(361, 1010, 'MNI982', 3, '2025-08-30', 1, 'Cra 41 # 3-34', 'Revisión preventiva', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(362, 1010, 'JKL112', 3, '2025-09-26', 6, 'Cra 43 # 39-40', 'Cliente reporta ruidos inusuales', '', 'Cambio de aceite', 'Custodia temporal', 0),
(363, 1010, 'MNI982', 3, '2025-09-03', 1, 'Cra 36 # 76-1', 'Solicitado por central', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(364, 1010, 'JKL112', 3, '2025-06-07', 4, 'Cra 82 # 58-24', 'Mantenimiento programado', '', 'Cambio de aceite', 'Custodia en taller', 1),
(365, 1010, 'XYZ789', 2, '2025-06-14', 4, 'Cra 99 # 58-12', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Custodia temporal', 0),
(366, 1010, 'XYZ789', 10, '2025-11-18', 7, 'Cra 87 # 11-18', 'Mantenimiento programado', '', 'Conductor elegido', 'Custodia temporal', 1),
(367, 1010, 'GHI101', 2, '2025-07-10', 3, 'Cra 81 # 78-26', 'Mantenimiento programado', '', 'Reparación de motor', 'Custodia en taller', 1),
(368, 1010, 'ABC123', 4, '2025-06-04', 10, 'Cra 41 # 13-45', 'Solicitado por central', '', 'Alineación y balanceo', 'Custodia en taller', 1),
(369, 1010, 'GHI101', 5, '2025-10-07', 5, 'Cra 56 # 95-34', 'Revisión preventiva', '', 'Revisión de frenos', 'Sin custodia', 0),
(370, 1010, 'JKL112', 5, '2025-08-10', 5, 'Cra 61 # 5-42', 'Urgencia por fallas mecánicas', '', 'Revisión de frenos', 'Custodia en taller', 1),
(371, 1010, 'STU901', 3, '2025-11-18', 2, 'Cra 13 # 81-17', 'Revisión preventiva', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(372, 1010, 'MNI982', 2, '2025-11-27', 8, 'Cra 51 # 8-24', 'Cliente reporta ruidos inusuales', '', 'Reparación de motor', 'Custodia temporal', 1),
(373, 1010, 'MNO345', 5, '2025-06-09', 8, 'Cra 92 # 37-34', 'Solicitado por central', '', 'Revisión de frenos', 'Sin custodia', 0),
(374, 1010, 'ABC123', 5, '2025-05-10', 1, 'Cra 51 # 34-20', 'Mantenimiento programado', '', 'Revisión de frenos', 'Custodia temporal', 1),
(375, 1010, 'XYZ789', 4, '2025-10-09', 8, 'Cra 83 # 92-23', 'Revisión preventiva', '', 'Alineación y balanceo', 'Custodia temporal', 1),
(376, 1010, 'PQR678', 4, '2025-08-28', 9, 'Cra 77 # 30-33', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(377, 1010, 'MNO345', 4, '2025-12-12', 1, 'Cra 98 # 94-16', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Custodia en taller', 0),
(378, 1010, 'XYZ789', 10, '2025-10-14', 1, 'Cra 70 # 62-18', 'Revisión preventiva', '', 'Conductor elegido', 'Entrega al cliente', 1),
(379, 1010, 'MNI982', 3, '2025-08-10', 5, 'Cra 28 # 76-23', 'Revisión preventiva', '', 'Cambio de aceite', 'Custodia en taller', 0),
(380, 1010, 'MNI982', 4, '2025-06-22', 2, 'Cra 74 # 18-50', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Entrega al cliente', 1),
(381, 1010, 'DEF456', 5, '2025-11-04', 1, 'Cra 35 # 83-34', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Entrega al cliente', 1),
(382, 1010, 'DEF456', 10, '2025-06-04', 5, 'Cra 97 # 78-6', 'Revisión preventiva', '', 'Conductor elegido', 'Custodia en taller', 0),
(383, 1010, 'PQR678', 1, '2025-12-25', 10, 'Cra 42 # 99-40', 'Revisión preventiva', '', 'Cambio de llantas', 'Custodia temporal', 0),
(384, 1010, 'STU901', 4, '2025-08-11', 3, 'Cra 75 # 72-27', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(385, 1010, 'PQR678', 5, '2025-06-11', 5, 'Cra 85 # 34-24', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Custodia en taller', 1),
(386, 1010, 'MNO345', 3, '2025-08-22', 2, 'Cra 59 # 6-32', 'Solicitado por central', '', 'Cambio de aceite', 'Sin custodia', 1),
(387, 1010, 'ABC123', 10, '2025-09-06', 8, 'Cra 22 # 54-30', 'Mantenimiento programado', '', 'Conductor elegido', 'Custodia en taller', 0),
(388, 1010, 'STU901', 1, '2025-06-09', 10, 'Cra 98 # 31-30', 'Urgencia por fallas mecánicas', '', 'Cambio de llantas', 'Custodia en taller', 0),
(389, 1010, 'JKL112', 10, '2025-10-12', 2, 'Cra 45 # 53-12', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Sin custodia', 0),
(390, 1010, 'MNI982', 5, '2025-09-25', 11, 'Cra 55 # 44-46', 'Solicitado por central', '', 'Revisión de frenos', 'Custodia en taller', 0),
(391, 1010, 'MNO345', 5, '2025-06-15', 11, 'Cra 73 # 34-25', 'Solicitado por central', '', 'Revisión de frenos', 'Entrega al cliente', 1),
(392, 1010, 'GHI101', 2, '2025-09-16', 11, 'Cra 51 # 24-27', 'Cliente reporta ruidos inusuales', '', 'Reparación de motor', 'Custodia en taller', 1),
(393, 1010, 'PQR678', 1, '2025-11-27', 5, 'Cra 75 # 64-46', 'Mantenimiento programado', '', 'Cambio de llantas', 'Entrega al cliente', 0),
(394, 1010, 'MNI982', 10, '2025-09-02', 6, 'Cra 63 # 35-47', 'Revisión preventiva', '', 'Conductor elegido', 'Sin custodia', 0),
(395, 1010, 'XYZ789', 4, '2025-07-16', 2, 'Cra 82 # 9-3', 'Revisión preventiva', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(396, 1010, 'JKL112', 10, '2025-11-17', 11, 'Cra 40 # 61-15', 'Revisión preventiva', '', 'Conductor elegido', 'Sin custodia', 0),
(397, 1010, 'DEF456', 2, '2025-09-04', 6, 'Cra 49 # 34-3', 'Mantenimiento programado', '', 'Reparación de motor', 'Custodia temporal', 0),
(398, 1010, 'DEF456', 10, '2025-05-14', 9, 'Cra 77 # 44-8', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Custodia temporal', 1),
(399, 1010, 'STU901', 1, '2025-06-19', 4, 'Cra 72 # 11-48', 'Mantenimiento programado', '', 'Cambio de llantas', 'Custodia temporal', 0),
(400, 1010, 'ABC123', 10, '2025-08-22', 9, 'Cra 88 # 34-2', 'Solicitado por central', '', 'Conductor elegido', 'Custodia temporal', 0),
(401, 1010, 'STU901', 2, '2025-09-11', 7, 'Cra 55 # 28-33', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Custodia temporal', 0),
(402, 1010, 'DEF456', 3, '2025-11-09', 1, 'Cra 99 # 73-35', 'Revisión preventiva', '', 'Cambio de aceite', 'Sin custodia', 1),
(403, 1010, 'STU901', 1, '2025-09-04', 7, 'Cra 63 # 7-41', 'Cliente reporta ruidos inusuales', '', 'Cambio de llantas', 'Entrega al cliente', 1),
(404, 1010, 'STU901', 2, '2025-10-01', 3, 'Cra 27 # 13-40', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Sin custodia', 1),
(405, 1010, 'MNI982', 2, '2025-05-05', 1, 'Cra 74 # 84-20', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Entrega al cliente', 1),
(406, 1010, 'MNO345', 3, '2025-07-29', 6, 'Cra 65 # 59-1', 'Mantenimiento programado', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(407, 1010, 'STU901', 5, '2025-08-09', 7, 'Cra 33 # 25-14', 'Mantenimiento programado', '', 'Revisión de frenos', 'Custodia en taller', 1),
(408, 1010, 'XYZ789', 1, '2025-05-28', 2, 'Cra 32 # 56-41', 'Revisión preventiva', '', 'Cambio de llantas', 'Entrega al cliente', 0),
(409, 1010, 'XYZ789', 5, '2025-09-05', 6, 'Cra 12 # 83-1', 'Revisión preventiva', '', 'Revisión de frenos', 'Custodia en taller', 0),
(410, 1010, 'DEF456', 1, '2025-06-12', 11, 'Cra 41 # 89-50', 'Urgencia por fallas mecánicas', '', 'Cambio de llantas', 'Custodia en taller', 0),
(411, 1010, 'STU901', 2, '2025-08-28', 6, 'Cra 10 # 43-38', 'Solicitado por central', '', 'Reparación de motor', 'Sin custodia', 0),
(412, 1010, 'MNO345', 3, '2025-09-11', 7, 'Cra 11 # 98-7', 'Revisión preventiva', '', 'Cambio de aceite', 'Custodia temporal', 1),
(413, 1010, 'MNI982', 1, '2025-08-13', 3, 'Cra 37 # 96-46', 'Urgencia por fallas mecánicas', '', 'Cambio de llantas', 'Entrega al cliente', 1),
(414, 1010, 'MNO345', 10, '2025-11-13', 9, 'Cra 81 # 21-16', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Sin custodia', 0),
(415, 1010, 'MNI982', 1, '2025-06-26', 6, 'Cra 9 # 13-42', 'Revisión preventiva', '', 'Cambio de llantas', 'Sin custodia', 1),
(416, 1010, 'DEF456', 2, '2025-10-16', 4, 'Cra 33 # 15-21', 'Revisión preventiva', '', 'Reparación de motor', 'Sin custodia', 1),
(417, 1010, 'MNO345', 5, '2025-06-04', 6, 'Cra 89 # 58-25', 'Solicitado por central', '', 'Revisión de frenos', 'Sin custodia', 0),
(418, 1010, 'JKL112', 10, '2025-05-30', 11, 'Cra 50 # 81-37', 'Solicitado por central', '', 'Conductor elegido', 'Entrega al cliente', 1),
(419, 1010, 'JKL112', 4, '2025-09-15', 11, 'Cra 53 # 82-26', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(420, 1010, 'MNI982', 3, '2025-05-31', 4, 'Cra 19 # 36-18', 'Solicitado por central', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(421, 1010, 'XYZ789', 4, '2025-12-23', 8, 'Cra 88 # 44-34', 'Solicitado por central', '', 'Alineación y balanceo', 'Sin custodia', 1),
(422, 1010, 'JKL112', 10, '2025-11-06', 7, 'Cra 94 # 62-26', 'Revisión preventiva', '', 'Conductor elegido', 'Entrega al cliente', 1),
(423, 1010, 'PQR678', 3, '2025-09-09', 11, 'Cra 5 # 62-30', 'Urgencia por fallas mecánicas', '', 'Cambio de aceite', 'Custodia temporal', 1),
(424, 1010, 'DEF456', 10, '2025-09-09', 6, 'Cra 71 # 96-3', 'Solicitado por central', '', 'Conductor elegido', 'Custodia temporal', 0),
(425, 1010, 'MNI982', 4, '2025-07-09', 4, 'Cra 97 # 98-31', 'Revisión preventiva', '', 'Alineación y balanceo', 'Custodia en taller', 1),
(426, 1010, 'DEF456', 5, '2025-11-09', 1, 'Cra 41 # 56-15', 'Mantenimiento programado', '', 'Revisión de frenos', 'Entrega al cliente', 0),
(427, 1010, 'JKL112', 10, '2025-11-10', 3, 'Cra 4 # 89-3', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Custodia en taller', 1),
(428, 1010, 'MNO345', 1, '2025-08-02', 2, 'Cra 100 # 58-12', 'Solicitado por central', '', 'Cambio de llantas', 'Sin custodia', 1),
(429, 1010, 'ABC123', 5, '2025-07-15', 10, 'Cra 34 # 24-47', 'Mantenimiento programado', '', 'Revisión de frenos', 'Custodia temporal', 1),
(430, 1010, 'PQR678', 3, '2025-09-02', 2, 'Cra 27 # 35-44', 'Cliente reporta ruidos inusuales', '', 'Cambio de aceite', 'Custodia en taller', 0),
(431, 1010, 'XYZ789', 10, '2025-09-15', 4, 'Cra 28 # 66-29', 'Mantenimiento programado', '', 'Conductor elegido', 'Sin custodia', 0),
(432, 1010, 'DEF456', 4, '2025-07-08', 3, 'Cra 44 # 95-49', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Entrega al cliente', 1),
(433, 1010, 'DEF456', 10, '2025-09-14', 1, 'Cra 23 # 5-40', 'Mantenimiento programado', '', 'Conductor elegido', 'Entrega al cliente', 0),
(434, 1010, 'JKL112', 10, '2025-06-13', 11, 'Cra 26 # 10-1', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Entrega al cliente', 1),
(435, 1010, 'JKL112', 10, '2025-07-04', 4, 'Cra 58 # 35-3', 'Solicitado por central', '', 'Conductor elegido', 'Sin custodia', 1),
(436, 1010, 'JKL112', 5, '2025-07-08', 11, 'Cra 68 # 78-43', 'Revisión preventiva', '', 'Revisión de frenos', 'Custodia temporal', 1),
(437, 1010, 'JKL112', 5, '2025-12-18', 11, 'Cra 6 # 71-29', 'Mantenimiento programado', '', 'Revisión de frenos', 'Entrega al cliente', 1),
(438, 1010, 'STU901', 1, '2025-05-15', 1, 'Cra 94 # 51-34', 'Mantenimiento programado', '', 'Cambio de llantas', 'Custodia temporal', 0),
(439, 1010, 'DEF456', 10, '2025-07-27', 9, 'Cra 99 # 62-31', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Sin custodia', 0),
(440, 1010, 'JKL112', 1, '2025-12-02', 7, 'Cra 49 # 12-22', 'Mantenimiento programado', '', 'Cambio de llantas', 'Sin custodia', 0),
(441, 1010, 'DEF456', 3, '2025-12-24', 3, 'Cra 28 # 19-46', 'Mantenimiento programado', '', 'Cambio de aceite', 'Custodia temporal', 1),
(442, 6000, 'ABC123', 5, '2025-07-22', 5, 'Cra 78 # 62-24', 'Urgencia por fallas mecánicas', '', 'Revisión de frenos', 'Custodia en taller', 1),
(443, 6000, 'GHI101', 4, '2025-05-30', 10, 'Cra 21 # 41-46', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Custodia temporal', 1),
(444, 6000, 'MNO345', 3, '2025-07-03', 5, 'Cra 18 # 71-10', 'Solicitado por central', '', 'Cambio de aceite', 'Custodia temporal', 1),
(445, 6000, 'GHI101', 10, '2025-06-04', 3, 'Cra 86 # 19-34', 'Mantenimiento programado', '', 'Conductor elegido', 'Custodia temporal', 1),
(446, 6000, 'XYZ789', 3, '2025-09-09', 10, 'Cra 86 # 47-8', 'Urgencia por fallas mecánicas', '', 'Cambio de aceite', 'Sin custodia', 0),
(447, 6000, 'STU901', 5, '2025-07-04', 1, 'Cra 80 # 87-17', 'Revisión preventiva', '', 'Revisión de frenos', 'Sin custodia', 0),
(448, 6000, 'GHI101', 5, '2025-05-03', 1, 'Cra 41 # 99-33', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Custodia en taller', 1),
(449, 6000, 'GHI101', 10, '2025-09-25', 10, 'Cra 11 # 11-45', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Custodia temporal', 0),
(450, 6000, 'PQR678', 2, '2025-08-05', 7, 'Cra 47 # 44-22', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Custodia temporal', 0),
(451, 6000, 'PQR678', 3, '2025-10-14', 11, 'Cra 31 # 83-37', 'Cliente reporta ruidos inusuales', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(452, 6001, 'STU901', 4, '2025-09-05', 5, 'Cra 66 # 70-24', 'Cliente reporta ruidos inusuales', '', 'Alineación y balanceo', 'Custodia en taller', 0),
(453, 6001, 'GHI101', 5, '2025-07-02', 2, 'Cra 65 # 62-5', 'Revisión preventiva', '', 'Revisión de frenos', 'Custodia temporal', 0),
(454, 6001, 'MNI982', 2, '2025-07-14', 2, 'Cra 28 # 84-46', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Entrega al cliente', 0),
(455, 6001, 'DEF456', 2, '2025-08-23', 4, 'Cra 10 # 23-5', 'Solicitado por central', '', 'Reparación de motor', 'Sin custodia', 1),
(456, 6001, 'MNO345', 1, '2025-09-01', 7, 'Cra 44 # 46-29', 'Cliente reporta ruidos inusuales', '', 'Cambio de llantas', 'Custodia en taller', 0),
(457, 6001, 'MNO345', 2, '2025-06-29', 10, 'Cra 63 # 93-39', 'Cliente reporta ruidos inusuales', '', 'Reparación de motor', 'Sin custodia', 1),
(458, 6001, 'MNO345', 4, '2025-10-13', 3, 'Cra 16 # 18-23', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Sin custodia', 0),
(459, 6001, 'JKL112', 3, '2025-07-09', 9, 'Cra 56 # 97-46', 'Urgencia por fallas mecánicas', '', 'Cambio de aceite', 'Custodia en taller', 1),
(460, 6001, 'GHI101', 3, '2025-06-10', 3, 'Cra 87 # 21-40', 'Solicitado por central', '', 'Cambio de aceite', 'Entrega al cliente', 0),
(461, 6001, 'MNO345', 1, '2025-06-02', 2, 'Cra 97 # 1-50', 'Revisión preventiva', '', 'Cambio de llantas', 'Entrega al cliente', 0),
(462, 6002, 'PQR678', 10, '2025-06-28', 9, 'Cra 41 # 58-30', 'Solicitado por central', '', 'Conductor elegido', 'Sin custodia', 0),
(463, 6002, 'XYZ789', 4, '2025-06-17', 5, 'Cra 78 # 66-39', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Custodia en taller', 1),
(464, 6002, 'JKL112', 5, '2025-07-11', 11, 'Cra 39 # 20-45', 'Solicitado por central', '', 'Revisión de frenos', 'Sin custodia', 1),
(465, 6002, 'STU901', 1, '2025-06-17', 10, 'Cra 98 # 61-12', 'Revisión preventiva', '', 'Cambio de llantas', 'Sin custodia', 1),
(466, 6002, 'MNI982', 4, '2025-08-18', 7, 'Cra 68 # 65-8', 'Urgencia por fallas mecánicas', '', 'Alineación y balanceo', 'Custodia temporal', 0),
(467, 6002, 'MNO345', 1, '2025-05-13', 2, 'Cra 10 # 68-4', 'Solicitado por central', '', 'Cambio de llantas', 'Sin custodia', 0),
(468, 6002, 'XYZ789', 5, '2025-05-04', 1, 'Cra 51 # 48-18', 'Mantenimiento programado', '', 'Revisión de frenos', 'Custodia temporal', 0),
(469, 6002, 'MNI982', 1, '2025-06-11', 3, 'Cra 99 # 61-35', 'Cliente reporta ruidos inusuales', '', 'Cambio de llantas', 'Custodia en taller', 1),
(470, 6002, 'PQR678', 4, '2025-06-21', 3, 'Cra 38 # 6-11', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Custodia en taller', 0),
(471, 6002, 'PQR678', 1, '2025-09-30', 5, 'Cra 54 # 63-43', 'Solicitado por central', '', 'Cambio de llantas', 'Custodia temporal', 0),
(472, 6003, 'ABC123', 4, '2025-10-24', 8, 'Cra 20 # 59-49', 'Cliente reporta ruidos inusuales', '', 'Alineación y balanceo', 'Custodia en taller', 1),
(473, 6003, 'MNO345', 10, '2025-05-04', 11, 'Cra 29 # 48-24', 'Revisión preventiva', '', 'Conductor elegido', 'Custodia en taller', 1),
(474, 6003, 'MNI982', 2, '2025-06-14', 8, 'Cra 54 # 69-11', 'Solicitado por central', '', 'Reparación de motor', 'Entrega al cliente', 1),
(475, 6003, 'MNI982', 10, '2025-08-23', 5, 'Cra 85 # 27-29', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Entrega al cliente', 1),
(476, 6003, 'XYZ789', 4, '2025-10-24', 6, 'Cra 27 # 61-31', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Entrega al cliente', 1),
(477, 6003, 'DEF456', 3, '2025-07-02', 3, 'Cra 99 # 56-2', 'Solicitado por central', '', 'Cambio de aceite', 'Entrega al cliente', 0),
(478, 6003, 'DEF456', 5, '2025-09-08', 8, 'Cra 25 # 77-45', 'Urgencia por fallas mecánicas', '', 'Revisión de frenos', 'Sin custodia', 0),
(479, 6003, 'DEF456', 10, '2025-10-13', 5, 'Cra 62 # 15-7', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Custodia en taller', 1),
(480, 6003, 'PQR678', 10, '2025-07-30', 8, 'Cra 14 # 22-43', 'Revisión preventiva', '', 'Conductor elegido', 'Sin custodia', 1),
(481, 6003, 'XYZ789', 1, '2025-06-03', 10, 'Cra 70 # 98-44', 'Solicitado por central', '', 'Cambio de llantas', 'Custodia en taller', 1),
(482, 6004, 'XYZ789', 4, '2025-08-01', 5, 'Cra 82 # 90-35', 'Revisión preventiva', '', 'Alineación y balanceo', 'Custodia en taller', 1),
(483, 6004, 'GHI101', 3, '2025-07-27', 1, 'Cra 64 # 67-2', 'Urgencia por fallas mecánicas', '', 'Cambio de aceite', 'Custodia en taller', 0),
(484, 6004, 'JKL112', 10, '2025-09-18', 2, 'Cra 23 # 57-38', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Custodia en taller', 1),
(485, 6004, 'DEF456', 5, '2025-08-04', 6, 'Cra 62 # 97-39', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Sin custodia', 1),
(486, 6004, 'MNO345', 2, '2025-05-11', 8, 'Cra 62 # 70-47', 'Solicitado por central', '', 'Reparación de motor', 'Entrega al cliente', 1),
(487, 6004, 'DEF456', 10, '2025-07-29', 5, 'Cra 75 # 45-29', 'Mantenimiento programado', '', 'Conductor elegido', 'Entrega al cliente', 1),
(488, 6004, 'JKL112', 5, '2025-06-06', 3, 'Cra 10 # 19-2', 'Urgencia por fallas mecánicas', '', 'Revisión de frenos', 'Custodia temporal', 1),
(489, 6004, 'JKL112', 5, '2025-08-19', 1, 'Cra 95 # 21-45', 'Revisión preventiva', '', 'Revisión de frenos', 'Entrega al cliente', 0),
(490, 6004, 'ABC123', 3, '2025-05-17', 11, 'Cra 48 # 91-10', 'Mantenimiento programado', '', 'Cambio de aceite', 'Entrega al cliente', 1),
(491, 6004, 'ABC123', 5, '2025-09-26', 2, 'Cra 10 # 19-12', 'Mantenimiento programado', '', 'Revisión de frenos', 'Custodia en taller', 1),
(492, 6005, 'ABC123', 5, '2025-05-11', 5, 'Cra 13 # 25-21', 'Cliente reporta ruidos inusuales', '', 'Revisión de frenos', 'Custodia en taller', 1),
(493, 6005, 'ABC123', 10, '2025-05-22', 11, 'Cra 85 # 38-4', 'Urgencia por fallas mecánicas', '', 'Conductor elegido', 'Sin custodia', 1),
(494, 6005, 'ABC123', 2, '2025-09-28', 6, 'Cra 12 # 44-32', 'Urgencia por fallas mecánicas', '', 'Reparación de motor', 'Entrega al cliente', 0),
(495, 6005, 'XYZ789', 5, '2025-07-09', 11, 'Cra 79 # 48-37', 'Urgencia por fallas mecánicas', '', 'Revisión de frenos', 'Sin custodia', 0),
(496, 6005, 'MNI982', 5, '2025-10-04', 2, 'Cra 22 # 92-33', 'Solicitado por central', '', 'Revisión de frenos', 'Custodia en taller', 1),
(497, 6005, 'MNO345', 1, '2025-06-22', 1, 'Cra 30 # 17-28', 'Mantenimiento programado', '', 'Cambio de llantas', 'Entrega al cliente', 0),
(498, 6005, 'JKL112', 4, '2025-08-21', 2, 'Cra 80 # 84-9', 'Revisión preventiva', '', 'Alineación y balanceo', 'Custodia en taller', 0),
(499, 6005, 'MNO345', 10, '2025-06-13', 11, 'Cra 89 # 98-12', 'Cliente reporta ruidos inusuales', '', 'Conductor elegido', 'Custodia en taller', 1),
(500, 6005, 'PQR678', 4, '2025-09-22', 2, 'Cra 67 # 50-4', 'Solicitado por central', '', 'Alineación y balanceo', 'Sin custodia', 0),
(501, 6005, 'PQR678', 10, '2025-05-08', 7, 'Cra 67 # 78-29', 'Solicitado por central', '', 'Conductor elegido', 'Custodia en taller', 1),
(502, 6006, 'DEF456', 1, '2025-09-01', 10, 'Cra 78 # 89-34', 'Solicitado por central', '', 'Cambio de llantas', 'Entrega al cliente', 0),
(503, 6006, 'STU901', 4, '2025-05-23', 6, 'Cra 77 # 69-38', 'Solicitado por central', '', 'Alineación y balanceo', 'Sin custodia', 0),
(504, 6006, 'XYZ789', 3, '2025-06-27', 2, 'Cra 34 # 67-39', 'Urgencia por fallas mecánicas', '', 'Cambio de aceite', 'Custodia en taller', 0),
(505, 6006, 'MNI982', 10, '2025-10-12', 6, 'Cra 72 # 98-34', 'Mantenimiento programado', '', 'Conductor elegido', 'Custodia en taller', 0),
(506, 6006, 'ABC123', 2, '2025-08-19', 8, 'Cra 39 # 22-14', 'Revisión preventiva', '', 'Reparación de motor', 'Sin custodia', 1),
(507, 6006, 'STU901', 2, '2025-08-08', 3, 'Cra 13 # 13-8', 'Mantenimiento programado', '', 'Reparación de motor', 'Custodia en taller', 0),
(508, 6006, 'DEF456', 3, '2025-08-30', 9, 'Cra 60 # 61-36', 'Revisión preventiva', '', 'Cambio de aceite', 'Sin custodia', 0),
(509, 6006, 'MNO345', 4, '2025-05-21', 9, 'Cra 11 # 98-35', 'Mantenimiento programado', '', 'Alineación y balanceo', 'Custodia en taller', 1),
(510, 6006, 'STU901', 2, '2025-06-26', 5, 'Cra 11 # 22-11', 'Revisión preventiva', '', 'Reparación de motor', 'Custodia en taller', 0),
(511, 6006, 'MNO345', 3, '2025-09-22', 6, 'Cra 67 # 50-22', 'Mantenimiento programado', '', 'Cambio de aceite', 'Custodia en taller', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `Placa` varchar(20) NOT NULL,
  `Marca` varchar(50) DEFAULT NULL,
  `Modelo` varchar(50) DEFAULT NULL,
  `Color` varchar(20) DEFAULT NULL,
  `Objetos_Valiosos` text DEFAULT NULL,
  `Clientes_Vehiculos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`Placa`, `Marca`, `Modelo`, `Color`, `Objetos_Valiosos`, `Clientes_Vehiculos`) VALUES
('ABC123', 'Toyota', 'Corolla', 'Rojo', 'GPS, Radio', 5050),
('DEF456', 'Toyota', 'Corolla', 'Gris', 'Herramientas', 5050),
('GHI101', 'Honda', 'Civic', 'Negro', 'Aire acondicionado', 33333),
('IEX747', 'Duster Dynamiq', '2015', 'Blanco', 'celular', 5050),
('JKL112', 'Ford', 'Fiesta', 'Azul', 'Documentos', 44444),
('MNI982', 'Renault', 'Logan', 'Negro', 'Computador', 5050),
('MNO345', 'Chevrolet', 'Spark', 'Verde', 'Teléfono', 11111),
('PQR678', 'Nissan', 'Sentra', 'Plateado', 'Bocinas', 33333),
('STU901', 'Kia', 'Rio', 'Amarillo', 'Cámara', 22222),
('XYZ789', 'Honda', 'Accord', 'Blanco', 'Laptop', 55555);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas_recordatorios`
--
ALTER TABLE `alertas_recordatorios`
  ADD PRIMARY KEY (`Alertas_id`),
  ADD KEY `Servicio_Realizado_id_alertas_recordatorios` (`Servicio_Realizado_id_alertas_recordatorios`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`Cedula_Id`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`Cedula_Empleado_id`),
  ADD KEY `fk_empleados_roles` (`Rol_id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`rol_id`,`permiso_id`),
  ADD KEY `permiso_id` (`permiso_id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`Servicio_id`);

--
-- Indices de la tabla `servicios_realizados`
--
ALTER TABLE `servicios_realizados`
  ADD PRIMARY KEY (`Servicio_Realizado_id`),
  ADD KEY `Cedula_Empleado_id_Servicios_Realizados` (`Cedula_Empleado_id_Servicios_Realizados`),
  ADD KEY `Vehiculo_id_Servicios_Realizados` (`Vehiculo_id_Servicios_Realizados`),
  ADD KEY `Servicio_id_Servicios_Realizados` (`Servicio_id_Servicios_Realizados`),
  ADD KEY `fk_municipio` (`municipio`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`Placa`),
  ADD KEY `Clientes_Vehiculos` (`Clientes_Vehiculos`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas_recordatorios`
--
ALTER TABLE `alertas_recordatorios`
  MODIFY `Alertas_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `Servicio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `servicios_realizados`
--
ALTER TABLE `servicios_realizados`
  MODIFY `Servicio_Realizado_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=512;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas_recordatorios`
--
ALTER TABLE `alertas_recordatorios`
  ADD CONSTRAINT `alertas_recordatorios_ibfk_1` FOREIGN KEY (`Servicio_Realizado_id_alertas_recordatorios`) REFERENCES `servicios_realizados` (`Servicio_Realizado_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `fk_empleados_roles` FOREIGN KEY (`Rol_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rol_permisos_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `servicios_realizados`
--
ALTER TABLE `servicios_realizados`
  ADD CONSTRAINT `fk_municipio` FOREIGN KEY (`municipio`) REFERENCES `municipios` (`id`),
  ADD CONSTRAINT `fk_servicio_id` FOREIGN KEY (`Servicio_id_Servicios_Realizados`) REFERENCES `servicios` (`Servicio_id`),
  ADD CONSTRAINT `servicios_realizados_ibfk_1` FOREIGN KEY (`Cedula_Empleado_id_Servicios_Realizados`) REFERENCES `empleados` (`Cedula_Empleado_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `servicios_realizados_ibfk_2` FOREIGN KEY (`Vehiculo_id_Servicios_Realizados`) REFERENCES `vehiculos` (`Placa`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `servicios_realizados_ibfk_3` FOREIGN KEY (`Servicio_id_Servicios_Realizados`) REFERENCES `servicios` (`Servicio_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`Clientes_Vehiculos`) REFERENCES `clientes` (`Cedula_Id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
