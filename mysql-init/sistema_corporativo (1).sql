-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-02-2026 a las 17:00:54
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_corporativo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo_coste` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id`, `nombre`, `codigo_coste`) VALUES
(1, 'Tecnología', 'IT-SYS-01'),
(2, 'Recursos Humanos', 'HR-ADMIN-02'),
(3, 'Gerencia General', 'DIR-CEO-03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nominas`
--

CREATE TABLE `nominas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mes` tinyint(4) NOT NULL,
  `anio` int(11) NOT NULL,
  `total_horas_ordinarias` decimal(10,2) DEFAULT 0.00,
  `total_horas_extras` decimal(10,2) DEFAULT 0.00,
  `salario_bruto` decimal(10,2) NOT NULL,
  `deduccion_irpf` decimal(10,2) NOT NULL,
  `deduccion_seg_social` decimal(10,2) NOT NULL,
  `otros_complementos` decimal(10,2) DEFAULT 0.00,
  `salario_neto` decimal(10,2) NOT NULL,
  `archivo_pdf` varchar(255) DEFAULT NULL,
  `estado` enum('Pendiente','Pagada') DEFAULT 'Pendiente',
  `fecha_generacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `nominas`
--

INSERT INTO `nominas` (`id`, `usuario_id`, `mes`, `anio`, `total_horas_ordinarias`, `total_horas_extras`, `salario_bruto`, `deduccion_irpf`, `deduccion_seg_social`, `otros_complementos`, `salario_neto`, `archivo_pdf`, `estado`, `fecha_generacion`) VALUES
(1, 2, 2, 2026, 0.00, 0.00, 2800.00, 420.00, 131.60, 0.00, 2248.40, NULL, 'Pendiente', '2026-02-25 12:29:26'),
(2, 3, 2, 2026, 0.70, 0.00, 1800.00, 270.00, 84.60, 0.00, 1445.40, NULL, 'Pendiente', '2026-02-25 12:45:35'),
(3, 1, 2, 2026, 0.00, 0.00, 3500.00, 525.00, 164.50, 0.00, 2810.50, NULL, 'Pendiente', '2026-02-25 15:56:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_horarios`
--

CREATE TABLE `registros_horarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `entrada` datetime NOT NULL,
  `salida` datetime DEFAULT NULL,
  `ip_conexion` varchar(45) NOT NULL,
  `dispositivo` text DEFAULT NULL,
  `geolocalizacion` varchar(100) DEFAULT NULL,
  `horas_totales` decimal(10,2) DEFAULT 0.00,
  `editado_por` int(11) DEFAULT NULL,
  `motivo_edicion` text DEFAULT NULL,
  `fecha_modificacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registros_horarios`
--

INSERT INTO `registros_horarios` (`id`, `usuario_id`, `entrada`, `salida`, `ip_conexion`, `dispositivo`, `geolocalizacion`, `horas_totales`, `editado_por`, `motivo_edicion`, `fecha_modificacion`) VALUES
(1, 3, '2026-02-25 13:02:05', '2026-02-25 13:44:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, 0.70, NULL, NULL, NULL),
(2, 2, '2026-02-25 13:04:48', '2026-02-25 13:05:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, 0.00, NULL, NULL, NULL),
(3, 2, '2026-02-25 13:05:03', '2026-02-25 14:11:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, 1.10, NULL, NULL, NULL),
(4, 3, '2026-02-25 13:52:52', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, 0.00, NULL, NULL, NULL),
(5, 3, '2026-02-25 13:52:54', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, 0.00, NULL, NULL, NULL),
(6, 3, '2026-02-25 13:53:04', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, 0.00, NULL, NULL, NULL),
(7, 3, '2026-02-25 13:53:05', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, 0.00, NULL, NULL, NULL),
(8, 1, '2026-02-25 14:46:49', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, 0.00, NULL, NULL, NULL),
(9, 2, '2026-02-25 16:38:01', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, 0.00, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'admin_db'),
(3, 'direccion'),
(5003, 'director'),
(2, 'rrhh'),
(4, 'trabajador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `departamento_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `dni_nie` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `secret_2fa` varchar(32) DEFAULT NULL,
  `salario_base_mensual` decimal(10,2) DEFAULT 0.00,
  `precio_hora_extra` decimal(10,2) DEFAULT 0.00,
  `irpf_pct` decimal(5,2) DEFAULT 15.00,
  `seg_social_pct` decimal(5,2) DEFAULT 4.70,
  `estado` tinyint(4) DEFAULT 1,
  `fecha_contratacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `rol_id`, `departamento_id`, `nombre`, `apellido`, `dni_nie`, `email`, `password`, `secret_2fa`, `salario_base_mensual`, `precio_hora_extra`, `irpf_pct`, `seg_social_pct`, `estado`, `fecha_contratacion`) VALUES
(1, 1, 1, 'Admin', 'Sistemas', '12345678A', 'admin@empresa.com', '$2y$10$YE6t52ScmQ/raHccBW5kGu6mg7u44yVAZgWvVCzwJNo6XBsuwlwLu', NULL, 3500.00, 35.00, 15.00, 4.70, 1, NULL),
(2, 2, 2, 'Marta', 'RRHH', '22334455B', 'rrhh@empresa.com', '$2y$10$YE6t52ScmQ/raHccBW5kGu6mg7u44yVAZgWvVCzwJNo6XBsuwlwLu', NULL, 2800.00, 25.00, 15.00, 4.70, 1, NULL),
(3, 4, 1, 'Juan', 'Empleado', '99887766C', 'trabajador@empresa.com', '$2y$10$YE6t52ScmQ/raHccBW5kGu6mg7u44yVAZgWvVCzwJNo6XBsuwlwLu', NULL, 1800.00, 18.00, 15.00, 4.70, 1, NULL),
(4, 4, 1, 'pepe', 'potamo', '12398734P', 'pepe@empresa.com', '$2y$10$ImZMBsJ7XbSnR/bDeXQVr.0mX4Cr7Q8GlPT22x.FTM3wLXGBpuWlS', NULL, 1460.00, 0.00, 15.00, 4.70, 0, NULL),
(5, 5003, 1, 'Director', 'General', '00000000X', 'director@empresa.com', '$2y$10$YE6t52ScmQ/raHccBW5kGu6mg7u44yVAZgWvVCzwJNo6XBsuwlwLu', NULL, 5000.00, 0.00, 15.00, 4.70, 1, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_coste` (`codigo_coste`);

--
-- Indices de la tabla `nominas`
--
ALTER TABLE `nominas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`mes`,`anio`);

--
-- Indices de la tabla `registros_horarios`
--
ALTER TABLE `registros_horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `editado_por` (`editado_por`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni_nie` (`dni_nie`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `departamento_id` (`departamento_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `nominas`
--
ALTER TABLE `nominas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `registros_horarios`
--
ALTER TABLE `registros_horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5004;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `nominas`
--
ALTER TABLE `nominas`
  ADD CONSTRAINT `nominas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `registros_horarios`
--
ALTER TABLE `registros_horarios`
  ADD CONSTRAINT `registros_horarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registros_horarios_ibfk_2` FOREIGN KEY (`editado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
