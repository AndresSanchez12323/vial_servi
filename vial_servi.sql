-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-04-2025 a las 06:51:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

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
  `intentos_fallidos` int(11) DEFAULT 0,
  `fecha_hora_bloqueo` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`Cedula_Empleado_id`, `Nombre`, `Apellido`, `Contraseña`, `Rol_id`, `Email`, `intentos_fallidos`, `fecha_hora_bloqueo`) VALUES
(1010, 'brian', 'rendon', '$2y$10$JQSVWMvzAwNhiA0JYD0fM.uUxZzylPV2Xx/7fp214BmOwTtSRSs1.', 2, NULL, 0, NULL),
(1040, 'edwin', 'sanchez', '$2y$10$dGu8U8jqn6E1BAhIqHk3Fe484U83cG7c/PfCpYi5mfxia5R17tTw6', 1, NULL, 0, NULL),
(1234, 'nube', 'sanchez', '$2y$10$/dIASvz2jJF8wZDQs4EmUuMlMlgu72rhFfnGkfEXj5xLdMixvQ9s.', 5, 'nube9916@gmail.com', 0, NULL),
(2025, 'patricia', 'patiño', '$2y$10$aiUAA6oEMZVf.YpYhBRwUOOBgbpFV0fm8f6o5.zQQiiqkDA/RcMzK', 3, NULL, 0, NULL),
(4040, 'Super', 'Admin', '$2y$10$F22tgMXGlfLCFN2dRocsWOOlfWflEXeIbDcCJtfJXsEwMhnpRLtmS', 0, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `geolocalización`
--

CREATE TABLE `geolocalización` (
  `Geolocalización_id` int(11) NOT NULL,
  `Servicios_realizados_id_Geolocalización` int(11) DEFAULT NULL,
  `Ubicación_Recogida` varchar(255) DEFAULT NULL,
  `Ubicación_Entrega` varchar(255) DEFAULT NULL,
  `Fecha_Recogida` date DEFAULT NULL,
  `Fecha_Entrega` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Brian', 'brian@gmail.com', 'hola mi bro', '2025-04-01 21:10:17');

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
(4, 'eliminar_servicio', 'Puede eliminar servicios');

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
(5, 'usuario', 'usuarios de la web');

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
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(3, 3),
(5, 2);

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
(5, 'Revisión de frenos', 'Revisión y ajuste del sistema de frenos');

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

INSERT INTO `servicios_realizados` (`Servicio_Realizado_id`, `Cedula_Empleado_id_Servicios_Realizados`, `Vehiculo_id_Servicios_Realizados`, `Servicio_id_Servicios_Realizados`, `Fecha`, `Ubicación`, `Novedades`, `Fotos`, `Detalle_Servicio`, `Custodia_Servicio`, `Facturación_Separada`) VALUES
(1, 1234, 'ABC123', 1, '2024-10-01', 'Avenida 122, Ciudad F', 'Cambio de llantas necesario', 'llanta_abc123.jpg', 'Cambio de llantas delantero y trasero', 'Custodia en taller', 1),
(33, NULL, 'ABC123', 1, '2024-11-06', 'Poli JIC', 'Motor con sobrecalentamiento', 'carro.img', 'dsfdsfds', 'Custodia en taller hasta reparación', 0),
(34, NULL, 'ABC123', 1, '2024-10-01', 'Avenida 122, Ciudad F', 'Cambio de llantas necesario', 'llanta_abc123.jpg', 'Cambio de llantas delantero y trasero', 'Custodia en taller', 1),
(35, NULL, 'DEF456', 2, '2024-10-05', 'Calle 10, Ciudad G', 'Falla en motor', 'motor_def456.jpg', 'Reparación completa del motor', 'Custodia hasta prueba', 0),
(36, NULL, 'GHI101', 3, '2024-10-10', 'Calle 15, Ciudad H', 'Cambio de aceite solicitado', 'aceite_ghi101.jpg', 'Cambio de aceite y filtros', 'Sin custodia', 1),
(37, NULL, 'JKL112', 4, '2024-10-15', 'Avenida 20, Ciudad I', 'Desbalance en ruedas', 'balanceo_jkl112.jpg', 'Alineación y balanceo', 'Custodia temporal', 0),
(38, NULL, 'XYZ789', 5, '2024-10-20', 'Calle 30, Ciudad J', 'Revisión de frenos urgente', 'frenos_xyz789.jpg', 'Ajuste y revisión del sistema de frenos', 'Custodia en taller', 1),
(39, NULL, 'MNO345', 2, '2024-10-25', 'Calle 25, Ciudad K', 'Reparación de motor', 'motor_mno345.jpg', 'Servicio de reparación de motor', 'Sin custodia', 0),
(40, 1010, 'PQR678', 1, '2024-10-30', 'Avenida 40, Ciudad L', 'Cambio de llantas tras desgaste', 'llanta_pqr678.jpg', 'Cambio de llantas y ajuste de presión', 'Custodia hasta prueba', 1),
(41, NULL, 'STU901', 4, '2024-11-01', 'Calle 50, Ciudad M', 'Desbalance en ruedas', 'balanceo_stu901.jpg', 'Alineación y balanceo', 'Custodia temporal', 0),
(42, NULL, 'ABC123', 3, '2024-11-03', 'Avenida 60, Ciudad N', 'Cambio de aceite', 'aceite_abc123.jpg', 'Cambio de aceite y revisión de motor', 'Sin custodia', 1),
(45, NULL, 'ABC123', 2, '2024-11-22', 'Poli JIC', 'Motor con sobrecalentamiento', 'carro.img', 'Reparación del sistema de enfriamiento', 'Custodia en taller hasta reparación', 1),
(46, 1040, 'ABC123', 5, '2025-04-01', 'poli jic', 'camara', 'foto.img', 'revision', 'linterna', 1),
(47, NULL, 'IEX747', 4, '2025-04-02', 'poli jic', NULL, NULL, 'quiero que vayan', '3102588225', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sincronizacion`
--

CREATE TABLE `sincronizacion` (
  `Sincronizacion_id` int(11) NOT NULL,
  `Servicio_Realizado_id_Sincronizacion` int(11) DEFAULT NULL,
  `Fecha_Sincronizacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD PRIMARY KEY (`Cedula_Empleado_id`);

--
-- Indices de la tabla `geolocalización`
--
ALTER TABLE `geolocalización`
  ADD PRIMARY KEY (`Geolocalización_id`),
  ADD KEY `Servicios_realizados_id_Geolocalización` (`Servicios_realizados_id_Geolocalización`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
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
  ADD KEY `Servicio_id_Servicios_Realizados` (`Servicio_id_Servicios_Realizados`);

--
-- Indices de la tabla `sincronizacion`
--
ALTER TABLE `sincronizacion`
  ADD PRIMARY KEY (`Sincronizacion_id`),
  ADD KEY `Servicio_Realizado_id_Sincronizacion` (`Servicio_Realizado_id_Sincronizacion`);

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
-- AUTO_INCREMENT de la tabla `geolocalización`
--
ALTER TABLE `geolocalización`
  MODIFY `Geolocalización_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `Servicio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `servicios_realizados`
--
ALTER TABLE `servicios_realizados`
  MODIFY `Servicio_Realizado_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `sincronizacion`
--
ALTER TABLE `sincronizacion`
  MODIFY `Sincronizacion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas_recordatorios`
--
ALTER TABLE `alertas_recordatorios`
  ADD CONSTRAINT `alertas_recordatorios_ibfk_1` FOREIGN KEY (`Servicio_Realizado_id_alertas_recordatorios`) REFERENCES `servicios_realizados` (`Servicio_Realizado_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `geolocalización`
--
ALTER TABLE `geolocalización`
  ADD CONSTRAINT `geolocalización_ibfk_1` FOREIGN KEY (`Servicios_realizados_id_Geolocalización`) REFERENCES `servicios_realizados` (`Servicio_Realizado_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `servicios_realizados_ibfk_1` FOREIGN KEY (`Cedula_Empleado_id_Servicios_Realizados`) REFERENCES `empleados` (`Cedula_Empleado_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `servicios_realizados_ibfk_2` FOREIGN KEY (`Vehiculo_id_Servicios_Realizados`) REFERENCES `vehiculos` (`Placa`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `servicios_realizados_ibfk_3` FOREIGN KEY (`Servicio_id_Servicios_Realizados`) REFERENCES `servicios` (`Servicio_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `sincronizacion`
--
ALTER TABLE `sincronizacion`
  ADD CONSTRAINT `sincronizacion_ibfk_1` FOREIGN KEY (`Servicio_Realizado_id_Sincronizacion`) REFERENCES `servicios_realizados` (`Servicio_Realizado_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`Clientes_Vehiculos`) REFERENCES `clientes` (`Cedula_Id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
