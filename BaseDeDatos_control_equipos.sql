-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-11-2022 a las 23:54:07
-- Versión del servidor: 10.4.20-MariaDB
-- Versión de PHP: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `control_equipos`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_estado_pc` (IN `id_equipo` INT, IN `estado_equipo` VARCHAR(1))  BEGIN

IF estado_equipo = 'F' THEN
	UPDATE `ingreso_pc` SET `estado_equipo` = 'F' WHERE `id_pc` = id_equipo;
ELSEIF estado_equipo = 'D' THEN
	UPDATE `ingreso_pc` SET `estado_equipo` = 'D' WHERE `id_pc` = id_equipo;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `datos_del_pc` (IN `cedula` INT)  BEGIN

SELECT u.cedula_usuario_con_pc, u.nombre, u.correo, p.descripcion_equipo FROM usuarios_pc u INNER JOIN ingreso_pc p on p.cedula_responsable = u.cedula_usuario_con_pc WHERE p.cedula_responsable = cedula;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ingresar_nuevo_pc` (IN `cedula` VARCHAR(30), IN `nombre` VARCHAR(30), IN `correo` VARCHAR(30), IN `id_equipo_fisico` VARCHAR(30), IN `descripcion_pc` VARCHAR(300), IN `estado_pc` CHAR(1))  BEGIN

insert into usuarios_pc (`cedula_usuario_con_pc`,`nombre`,`correo`)
VALUES (cedula, nombre, correo);

insert into ingreso_pc (`id_pc`,`cedula_responsable`,`id_equipo_fisico`,`descripcion_equipo`,`estado_equipo`) values (DEFAULT, cedula, id_equipo_fisico, descripcion_pc, estado_pc);

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_de_equipos`
--

CREATE TABLE `historial_de_equipos` (
  `id_historial` int(11) NOT NULL,
  `id_pc` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `fecha_historial_del_equipo` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `historial_de_equipos`
--

INSERT INTO `historial_de_equipos` (`id_historial`, `id_pc`, `descripcion`, `fecha_historial_del_equipo`) VALUES
(131, 41, 'Este equipo ha entrado a la gobernación', '2021-09-01 02:22:30'),
(132, 43, 'Este equipo ha entrado a la gobernación', '2021-09-01 02:25:22'),
(133, 41, 'Este equipo ha salido de la gobernación', '2021-09-01 02:25:33'),
(134, 46, 'Este equipo ha entrado a la gobernación', '2021-09-01 02:26:25'),
(135, 46, 'Este equipo ha entrado a la gobernación', '2021-09-01 02:26:47'),
(136, 41, 'Este equipo ha entrado a la gobernación', '2021-09-05 04:35:00'),
(154, 49, 'Este equipo ha entrado a la gobernación', '2021-09-06 16:12:54'),
(155, 49, 'Este equipo ha salido de la gobernación', '2021-09-06 16:14:04'),
(156, 41, 'Este equipo ha salido de la gobernación', '2021-09-06 16:22:10'),
(157, 46, 'Este equipo ha salido de la gobernación', '2021-09-06 16:40:13'),
(158, 50, 'Este equipo ha entrado a la gobernación', '2021-10-05 17:24:27'),
(159, 52, 'Este equipo ha entrado a la gobernación', '2021-11-17 07:31:17'),
(160, 53, 'Este equipo ha salido de la gobernación', '2021-11-17 07:31:21'),
(161, 53, 'Este equipo ha entrado a la gobernación', '2021-11-18 08:05:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_pc`
--

CREATE TABLE `ingreso_pc` (
  `id_pc` int(11) NOT NULL,
  `cedula_responsable` int(11) NOT NULL,
  `id_equipo_fisico` varchar(10) NOT NULL DEFAULT 'n',
  `descripcion_equipo` varchar(30) NOT NULL,
  `estado_equipo` char(1) NOT NULL DEFAULT 'n',
  `ultima_fecha_actualizacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `ingreso_pc`
--

INSERT INTO `ingreso_pc` (`id_pc`, `cedula_responsable`, `id_equipo_fisico`, `descripcion_equipo`, `estado_equipo`, `ultima_fecha_actualizacion`) VALUES
(41, 1035, '12345', 'Razer - Verde', 'F', '2021-09-01 02:18:21'),
(43, 1035, '790', 'MSI - Amarillo', 'D', '2021-09-01 02:21:36'),
(44, 1036, '90', 'Lenovo - Verde', 'D', '2021-08-27 00:06:26'),
(45, 1036, 'n', 'Acer - Amarillo', 'D', '2021-08-27 00:07:45'),
(46, 1035, '9', 'Apple - Verde', 'F', '2021-09-01 02:26:47'),
(49, 7878, '9090', 'Dell - Azul', 'F', '2021-09-06 16:12:17'),
(50, 7878, '', 'Apple - Verde', 'D', '2021-09-06 16:13:43'),
(51, 7878, '90908', 'MSI - Amarillo', 'D', '2021-09-06 16:18:03'),
(52, 123, '5555', 'Lenovo - Azul', 'D', '2021-11-17 07:20:06'),
(53, 123, '98', 'Apple - Verde', 'D', '2021-11-17 07:31:11');

--
-- Disparadores `ingreso_pc`
--
DELIMITER $$
CREATE TRIGGER `actualizar_historial_estado` BEFORE UPDATE ON `ingreso_pc` FOR EACH ROW BEGIN
  IF NEW.estado_equipo = 'F'
    THEN
      INSERT INTO `historial_de_equipos`(`id_historial`, `id_pc`, `descripcion`, `fecha_historial_del_equipo`) VALUES (DEFAULT, NEW.id_pc, "Este equipo ha salido de la gobernación", DEFAULT);
    ELSE
      INSERT INTO `historial_de_equipos`(`id_historial`, `id_pc`, `descripcion`, `fecha_historial_del_equipo`) VALUES (DEFAULT, NEW.id_pc, "Este equipo ha entrado a la gobernación", DEFAULT);
  END IF ;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_porteria`
--

CREATE TABLE `ingreso_porteria` (
  `id_ingreso` int(11) NOT NULL,
  `tipo` varchar(2) NOT NULL,
  `documento` int(11) NOT NULL,
  `nombres` varchar(30) NOT NULL,
  `secretaria` varchar(30) NOT NULL,
  `proposito` varchar(20) NOT NULL,
  `piso` int(11) NOT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_pc`
--

CREATE TABLE `usuarios_pc` (
  `cedula_usuario_con_pc` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `correo` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios_pc`
--

INSERT INTO `usuarios_pc` (`cedula_usuario_con_pc`, `nombre`, `correo`) VALUES
(123, 'Christian', 'a@papa.com'),
(1035, 'Christian Benitez', '@hotmail.com'),
(1036, 'Mateo', 'A@papa.com'),
(7878, 'Mateo', 'Mateo@jd');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `historial_de_equipos`
--
ALTER TABLE `historial_de_equipos`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `fk_id_pc` (`id_pc`);

--
-- Indices de la tabla `ingreso_pc`
--
ALTER TABLE `ingreso_pc`
  ADD PRIMARY KEY (`id_pc`),
  ADD KEY `fk_cedula_responsable` (`cedula_responsable`);

--
-- Indices de la tabla `ingreso_porteria`
--
ALTER TABLE `ingreso_porteria`
  ADD PRIMARY KEY (`id_ingreso`);

--
-- Indices de la tabla `usuarios_pc`
--
ALTER TABLE `usuarios_pc`
  ADD PRIMARY KEY (`cedula_usuario_con_pc`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `historial_de_equipos`
--
ALTER TABLE `historial_de_equipos`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT de la tabla `ingreso_pc`
--
ALTER TABLE `ingreso_pc`
  MODIFY `id_pc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `ingreso_porteria`
--
ALTER TABLE `ingreso_porteria`
  MODIFY `id_ingreso` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historial_de_equipos`
--
ALTER TABLE `historial_de_equipos`
  ADD CONSTRAINT `fk_id_pc` FOREIGN KEY (`id_pc`) REFERENCES `ingreso_pc` (`id_pc`);

--
-- Filtros para la tabla `ingreso_pc`
--
ALTER TABLE `ingreso_pc`
  ADD CONSTRAINT `fk_cedula_responsable` FOREIGN KEY (`cedula_responsable`) REFERENCES `usuarios_pc` (`cedula_usuario_con_pc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
