-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-10-2024 a las 23:28:21
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
-- Base de datos: `reservas_caf`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `Run_Administrador` varchar(20) NOT NULL,
  `PNombre_Administrador` varchar(50) NOT NULL,
  `SNombre_Administrador` varchar(50) DEFAULT NULL,
  `PApellido_Administrador` varchar(50) NOT NULL,
  `SApellido_Administrador` varchar(50) DEFAULT NULL,
  `Mail_Administrador` varchar(100) NOT NULL,
  `Contraseña_Administrador` varchar(100) NOT NULL,
  `Fecha_Registro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`Run_Administrador`, `PNombre_Administrador`, `SNombre_Administrador`, `PApellido_Administrador`, `SApellido_Administrador`, `Mail_Administrador`, `Contraseña_Administrador`, `Fecha_Registro`) VALUES
('210963199', 'Felipe', 'Jose', 'Gonzalez', 'Gutierrez', 'benjalol@duoccaf.cl', 'admin', '2024-08-30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario`
--

CREATE TABLE `horario` (
  `Id_Horario` int(11) NOT NULL,
  `Hora_Inicio` time NOT NULL,
  `Hora_Fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horario`
--

INSERT INTO `horario` (`Id_Horario`, `Hora_Inicio`, `Hora_Fin`) VALUES
(1, '08:30:00', '09:59:00'),
(2, '10:00:00', '11:29:00'),
(3, '11:30:00', '12:59:00'),
(4, '13:00:00', '14:29:00'),
(5, '14:30:00', '15:59:00'),
(6, '16:00:00', '17:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `Id_Reserva` int(11) NOT NULL,
  `Fecha_Reserva` date NOT NULL,
  `Hora_libre` time NOT NULL,
  `Limite_Usuario` int(11) NOT NULL,
  `Estado` enum('Confirmada','Cancelada','Pendiente') NOT NULL,
  `Fecha_Actividad` date NOT NULL,
  `Run_Usuario` varchar(20) DEFAULT NULL,
  `Run_Administrador` varchar(20) DEFAULT NULL,
  `Id_Horario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_duoc`
--

CREATE TABLE `usuario_duoc` (
  `Run_Usuario` varchar(20) NOT NULL,
  `Tipo_Usuario` enum('Estudiante','Docente','Personal') NOT NULL,
  `PNombre_Usuario` varchar(50) NOT NULL,
  `SNombre_Usuario` varchar(50) DEFAULT NULL,
  `PApellido_Usuario` varchar(50) NOT NULL,
  `SApellido_Usuario` varchar(50) DEFAULT NULL,
  `Mail_Usuario` varchar(100) NOT NULL,
  `Contraseña_Usuario` varchar(100) NOT NULL,
  `Fecha_Registro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_duoc`
--

INSERT INTO `usuario_duoc` (`Run_Usuario`, `Tipo_Usuario`, `PNombre_Usuario`, `SNombre_Usuario`, `PApellido_Usuario`, `SApellido_Usuario`, `Mail_Usuario`, `Contraseña_Usuario`, `Fecha_Registro`) VALUES
('210963197', 'Estudiante', 'Braihan', 'Benjamin', 'Gonzalez', 'Guti3rrez', 'brai.gonzalez@duocuc.cl', 'Benjalol', '2024-09-01');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`Run_Administrador`);

--
-- Indices de la tabla `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`Id_Horario`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`Id_Reserva`),
  ADD KEY `Run_Usuario` (`Run_Usuario`),
  ADD KEY `Run_Administrador` (`Run_Administrador`),
  ADD KEY `Id_Horario` (`Id_Horario`);

--
-- Indices de la tabla `usuario_duoc`
--
ALTER TABLE `usuario_duoc`
  ADD PRIMARY KEY (`Run_Usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `horario`
--
ALTER TABLE `horario`
  MODIFY `Id_Horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `Id_Reserva` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`Run_Usuario`) REFERENCES `usuario_duoc` (`Run_Usuario`),
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`Run_Administrador`) REFERENCES `administrador` (`Run_Administrador`),
  ADD CONSTRAINT `reserva_ibfk_3` FOREIGN KEY (`Id_Horario`) REFERENCES `horario` (`Id_Horario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
