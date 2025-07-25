-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 25, 2025 at 01:59 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cmdb_php_db2`
--

-- --------------------------------------------------------

--
-- Table structure for table `asignaciones`
--

CREATE DATABASE IF NOT EXISTS `cmdb_php_db2`;
USE `cmdb_php_db2`;

CREATE TABLE `asignaciones` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `colaborador_id` int(11) NOT NULL,
  `fecha_asignacion` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asignaciones`
--

INSERT INTO `asignaciones` (`id`, `inventario_id`, `colaborador_id`, `fecha_asignacion`, `fecha_devolucion`) VALUES
(1, 1, 1, '2024-01-15', NULL),
(2, 6, 3, '2024-02-05', NULL),
(3, 14, 9, '2023-01-10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(4, 'Equipo de Cómputo'),
(3, 'Equipo de Red'),
(5, 'Equipo de Telefonía'),
(1, 'Hardware'),
(8, 'Licencias de Software'),
(7, 'Mobiliario de Oficina'),
(6, 'Periféricos'),
(2, 'Software');

-- --------------------------------------------------------

--
-- Table structure for table `colaboradores`
--

CREATE TABLE `colaboradores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `identificacion_unica` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT 'default.png',
  `ubicacion` varchar(255) DEFAULT NULL COMMENT 'Edificio 303, casa 257, etc.',
  `telefono` varchar(50) DEFAULT NULL,
  `ip_asignada` varchar(45) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colaboradores`
--

INSERT INTO `colaboradores` (`id`, `nombre`, `apellido`, `departamento`, `identificacion_unica`, `email`, `password_hash`, `foto_perfil`, `ubicacion`, `telefono`, `ip_asignada`, `fecha_creacion`) VALUES
(1, 'Juan', 'Pérez', 'Marketing', '123456789-JP', 'juan.perez@example.com', '$2y$10$Onp9LqZYDGKGZAdm888Zf.5GB8c/XyLD8lX9h7TmQO/czeZQrfcs2', 'default.png', 'Oficina 101', '6123-4567', '192.168.1.10', '2025-07-24 23:53:20'),
(2, 'María', 'García', 'Ventas', '987654321-MG', 'maria.garcia@example.com', '$2y$10$Nvo4w5YFpwdO5cHXW6dJTOguUAyDLYqLS9XwItle.Z.oRKipnm6M.', 'default.png', 'Oficina 102', '6234-5678', '192.168.1.11', '2025-07-24 23:53:20'),
(3, 'Carlos', 'Rodríguez', 'IT', '112233445-CR', 'carlos.r@example.com', '$2y$10$Wn7IfL3IG7EE.uqjWh5Fau3.F.1BQorKMjMaR3VtD8/yEAoEPRwWa', 'default.png', 'Server Room', '6345-6789', '192.168.1.12', '2025-07-24 23:53:20'),
(4, 'Ana', 'López', 'Recursos Humanos', '556677889-AL', 'ana.lopez@example.com', '$2y$10$CqqsagnPqdpycaC3Xnfb6OwPdKqY45BZ6fgwFe2vWVQau/LjyFe72', 'default.png', 'Oficina 201', '6456-7890', '192.168.1.13', '2025-07-24 23:53:20'),
(5, 'Pedro', 'Martínez', 'Finanzas', '998877665-PM', 'pedro.m@example.com', '$2y$10$vJ..mTASJDNLtaDD7cmBfOztPAAZoklsnLd.EJGmfgT7JpR18tJF6', 'default.png', 'Oficina 202', '6567-8901', '192.168.1.14', '2025-07-24 23:53:20'),
(6, 'Sofía', 'Hernández', 'Logística', '123123123-SH', 'sofia.h@example.com', '$2y$10$jxNw3oIQW4OT6FR/OzD7EO954ePG3riMJrKhkOshR4ynb4p4T1hRq', 'default.png', 'Almacén Central', '6678-9012', '192.168.1.15', '2025-07-24 23:53:20'),
(7, 'Luis', 'González', 'Marketing', '456456456-LG', 'luis.g@example.com', '$2y$10$A9pS3HOM8T3nQLlamOgCKeFnstilyMqH5dSQl65Xh.4cNJc1BcDe6', 'default.png', 'Oficina 101', '6789-0123', '192.168.1.16', '2025-07-24 23:53:20'),
(8, 'Laura', 'Díaz', 'Ventas', '789789789-LD', 'laura.d@example.com', '$2y$10$rcT7/JHmPrYqocKJaUXu8uIMwmmVKrVl8mbGG1983pPPAhBgEltly', 'default.png', 'Oficina 102', '6890-1234', '192.168.1.17', '2025-07-24 23:53:20'),
(9, 'Fernando', 'Sánchez', 'IT', '147147147-FS', 'fernando.s@example.com', '$2y$10$WsODKDZBz4jtLe1O5Bjajuoh5v7KImFtuLzBA2BuMkaozESevGEee', 'default.png', 'Help Desk', '6901-2345', '192.168.1.18', '2025-07-24 23:53:21'),
(10, 'Elena', 'Torres', 'Recursos Humanos', '258258258-ET', 'elena.t@example.com', '$2y$10$FzbL3HhLfL367aDK4ZYDbesSN/kXQPV6IfEsn.ZxI7K3eA7qadQLW', 'default.png', 'Oficina 201', '6012-3456', '192.168.1.19', '2025-07-24 23:53:21'),
(11, 'Miguel', 'Ramírez', 'Finanzas', '369369369-MR', 'miguel.r@example.com', '$2y$10$yCgqFiNGUsQT6d2XLmxe6eMa6CQa/NlyWxqGGITuLxdrLPzAPoBqG', 'default.png', 'Oficina 202', '6123-4567', '192.168.1.20', '2025-07-24 23:53:21'),
(12, 'Gabriela', 'Flores', 'Logística', '741741741-GF', 'gabriela.f@example.com', '$2y$10$KfaygKlNEZL0j0sZazwMk.2MlP.snD9.iigNZiQuTo9vLSdzhseU6', 'default.png', 'Recepción', '6234-5678', '192.168.1.21', '2025-07-24 23:53:21'),
(13, 'Ricardo', 'Benítez', 'Marketing', '852852852-RB', 'ricardo.b@example.com', '$2y$10$JaU.Zuin2p2teyilk.DK6.Hltl3IlxnhU1GOulvNAn.mqiF/am1nu', 'default.png', 'Oficina 101', '6345-6789', '192.168.1.22', '2025-07-24 23:53:21'),
(14, 'Carmen', 'Vargas', 'Ventas', '963963963-CV', 'carmen.v@example.com', '$2y$10$awbm0VTh3PQcNNOJDcYHeeHamXe3BVlnLHg/wMZ7X0lF2uFoJgfvu', 'default.png', 'Oficina 102', '6456-7890', '192.168.1.23', '2025-07-24 23:53:21'),
(15, 'Andrés', 'Silva', 'IT', '321321321-AS', 'andres.s@example.com', '$2y$10$On7NntvbPeU/UBxrK9JkYOZmjuFz91eF0Ooc7gRitnhizp0eaQm4G', 'default.png', 'Data Center', '6567-8901', '192.168.1.24', '2025-07-24 23:53:21');

-- --------------------------------------------------------

--
-- Table structure for table `historial_login`
--

CREATE TABLE `historial_login` (
  `id` int(11) NOT NULL,
  `colaborador_id` int(11) NOT NULL,
  `fecha_login` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_origen` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historial_login`
--

INSERT INTO `historial_login` (`id`, `colaborador_id`, `fecha_login`, `ip_origen`) VALUES
(1, 1, '2025-07-24 13:00:00', '192.168.1.10'),
(2, 2, '2025-07-24 13:05:00', '192.168.1.11');

-- --------------------------------------------------------

--
-- Table structure for table `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `nombre_equipo` varchar(150) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `serie` varchar(100) DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT 0.00,
  `fecha_ingreso` date NOT NULL,
  `tiempo_depreciacion_anios` int(11) DEFAULT 0,
  `estado` enum('En Stock','Asignado','En Reparación','Dañado','En Descarte','Donado') NOT NULL DEFAULT 'En Stock',
  `notas_donacion` text DEFAULT NULL,
  `categoria_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventario`
--

INSERT INTO `inventario` (`id`, `nombre_equipo`, `marca`, `modelo`, `serie`, `costo`, `fecha_ingreso`, `tiempo_depreciacion_anios`, `estado`, `notas_donacion`, `categoria_id`) VALUES
(1, 'Laptop Ejecutiva X1', 'Lenovo', 'ThinkPad X1 Carbon Gen 9', 'TPX1-0001', 1500.00, '2024-01-10', 4, 'Asignado', NULL, 4),
(2, 'Monitor Curvo UltraWide', 'Samsung', 'Odyssey G9', 'SMG9-0001', 1200.00, '2023-05-20', 5, 'En Stock', NULL, 1),
(3, 'Impresora Láser Color', 'Brother', 'HL-L8360CDW', 'BRLS-0001', 450.00, '2023-08-01', 3, 'En Reparación', 'Problema con el fusor.', 1),
(4, 'Teléfono IP Cisco', 'Cisco', 'IP Phone 8841', 'CIP-0001', 250.00, '2022-11-15', 5, 'Dañado', 'Pantalla rota, no enciende.', 5),
(5, 'Switch de Red Gestionado', 'Ubiquiti', 'UniFi Switch 24', 'USW-0001', 300.00, '2021-03-01', 7, 'En Stock', NULL, 3),
(6, 'Software de Diseño Gráfico', 'Adobe', 'Creative Suite Pro', 'ADCS-LIC01', 1000.00, '2024-02-01', 1, 'Asignado', NULL, 8),
(7, 'Silla Ergonómica Ejecutiva', 'Herman Miller', 'Aeron', 'AERON-0001', 900.00, '2023-04-10', 10, 'En Stock', NULL, 7),
(8, 'Servidor de Base de Datos', 'Dell', 'PowerEdge R750', 'DBSRV-0001', 8000.00, '2022-06-01', 8, 'En Stock', NULL, 1),
(9, 'Auriculares con Cancelación', 'Sony', 'WH-1000XM5', 'SONY-0001', 350.00, '2024-03-15', 2, 'En Stock', NULL, 6),
(10, 'Teclado Mecánico RGB', 'Razer', 'BlackWidow V4', 'RAZER-0001', 150.00, '2024-04-20', 3, 'En Stock', NULL, 6),
(11, 'Cámara de Videoconferencia', 'Logitech', 'MeetUp', 'LOGI-0001', 700.00, '2023-09-01', 4, 'En Stock', NULL, 1),
(12, 'Router WiFi 6 Empresarial', 'TP-Link', 'Omada EAP660 HD', 'OMADA-0001', 180.00, '2023-11-05', 3, 'En Stock', NULL, 3),
(13, 'UPS 1500VA', 'APC', 'Back-UPS Pro BR1500MS', 'APC-0001', 200.00, '2022-01-25', 5, 'En Stock', NULL, 1),
(14, 'Software CRM', 'Salesforce', 'Enterprise Edition', 'SFCRM-LIC01', 5000.00, '2023-01-01', 1, 'Asignado', NULL, 8),
(15, 'Tableta Gráfica Wacom', 'Wacom', 'Intuos Pro Medium', 'WCM-0001', 380.00, '2024-06-10', 3, 'En Stock', NULL, 6),
(16, 'Laptop Básica de Oficina', 'Acer', 'Aspire 3', 'ACER-0001', 500.00, '2024-07-01', 3, 'En Stock', NULL, 4),
(17, 'Laptop Básica de Oficina', 'Acer', 'Aspire 3', 'ACER-0002', 500.00, '2024-07-01', 3, 'En Stock', NULL, 4),
(18, 'Laptop Básica de Oficina', 'Acer', 'Aspire 3', 'ACER-0003', 500.00, '2024-07-01', 3, 'En Stock', NULL, 4),
(19, 'Monitor de Oficina 22\"', 'HP', 'E22 G5', 'HPMON-0001', 150.00, '2024-07-05', 5, 'En Stock', NULL, 1),
(20, 'Monitor de Oficina 22\"', 'HP', 'E22 G5', 'HPMON-0002', 150.00, '2024-07-05', 5, 'En Stock', NULL, 1),
(21, 'Monitor de Oficina 22\"', 'HP', 'E22 G5', 'HPMON-0003', 150.00, '2024-07-05', 5, 'En Stock', NULL, 1),
(22, 'Monitor de Oficina 22\"', 'HP', 'E22 G5', 'HPMON-0004', 150.00, '2024-07-05', 5, 'En Stock', NULL, 1),
(23, 'Teléfono IP Básico', 'Grandstream', 'GXP1610', 'GSIP-0001', 80.00, '2024-06-15', 4, 'En Stock', NULL, 5),
(24, 'Teléfono IP Básico', 'Grandstream', 'GXP1610', 'GSIP-0002', 80.00, '2024-06-15', 4, 'En Stock', NULL, 5),
(25, 'Teléfono IP Básico', 'Grandstream', 'GXP1610', 'GSIP-0003', 80.00, '2024-06-15', 4, 'En Stock', NULL, 5),
(26, 'Teléfono IP Básico', 'Grandstream', 'GXP1610', 'GSIP-0004', 80.00, '2024-06-15', 4, 'En Stock', NULL, 5),
(27, 'Teléfono IP Básico', 'Grandstream', 'GXP1610', 'GSIP-0005', 80.00, '2024-06-15', 4, 'En Stock', NULL, 5),
(28, 'Mouse Óptico USB', 'Logitech', 'M90', 'LOGIM-0001', 15.00, '2024-05-10', 2, 'En Stock', NULL, 6),
(29, 'Mouse Óptico USB', 'Logitech', 'M90', 'LOGIM-0002', 15.00, '2024-05-10', 2, 'En Stock', NULL, 6),
(30, 'Mouse Óptico USB', 'Logitech', 'M90', 'LOGIM-0003', 15.00, '2024-05-10', 2, 'En Stock', NULL, 6),
(31, 'Mouse Óptico USB', 'Logitech', 'M90', 'LOGIM-0004', 15.00, '2024-05-10', 2, 'En Stock', NULL, 6),
(32, 'Mouse Óptico USB', 'Logitech', 'M90', 'LOGIM-0005', 15.00, '2024-05-10', 2, 'En Stock', NULL, 6),
(33, 'Teclado USB Estándar', 'Dell', 'KB216', 'DELLK-0001', 25.00, '2024-05-10', 3, 'En Stock', NULL, 6),
(34, 'Teclado USB Estándar', 'Dell', 'KB216', 'DELLK-0002', 25.00, '2024-05-10', 3, 'En Stock', NULL, 6),
(35, 'Teclado USB Estándar', 'Dell', 'KB216', 'DELLK-0003', 25.00, '2024-05-10', 3, 'En Stock', NULL, 6),
(36, 'Teclado USB Estándar', 'Dell', 'KB216', 'DELLK-0004', 25.00, '2024-05-10', 3, 'En Stock', NULL, 6),
(37, 'Teclado USB Estándar', 'Dell', 'KB216', 'DELLK-0005', 25.00, '2024-05-10', 3, 'En Stock', NULL, 6),
(38, 'Licencia Antivirus Pro', 'Kaspersky', 'Endpoint Security', 'KAV-LIC-001', 80.00, '2024-01-01', 1, 'En Stock', NULL, 8),
(39, 'Licencia Antivirus Pro', 'Kaspersky', 'Endpoint Security', 'KAV-LIC-002', 80.00, '2024-01-01', 1, 'En Stock', NULL, 8),
(40, 'Licencia Antivirus Pro', 'Kaspersky', 'Endpoint Security', 'KAV-LIC-003', 80.00, '2024-01-01', 1, 'En Stock', NULL, 8),
(41, 'Licencia Antivirus Pro', 'Kaspersky', 'Endpoint Security', 'KAV-LIC-004', 80.00, '2024-01-01', 1, 'En Stock', NULL, 8),
(42, 'Licencia Antivirus Pro', 'Kaspersky', 'Endpoint Security', 'KAV-LIC-005', 80.00, '2024-01-01', 1, 'En Stock', NULL, 8),
(43, 'Monitor CRT Antiguo', 'ViewSonic', 'E50', 'CRT-DON-001', 10.00, '2015-03-01', 10, 'Donado', 'Donado a centro comunitario.', 1),
(44, 'PC de Escritorio Obsoleta', 'HP', 'Compaq d530', 'PC-DESC-001', 50.00, '2017-07-01', 7, 'En Descarte', 'Para reciclaje electrónico, no funciona.', 4),
(45, 'Laptop de Pruebas', 'Dell', 'Latitude E6420', 'TEST-0001', 200.00, '2020-01-01', 3, 'En Stock', NULL, 4),
(46, 'Proyector Sala B', 'Epson', 'EB-S05', 'PROY-0001', 300.00, '2022-01-01', 3, 'En Stock', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `inventario_imagenes`
--

CREATE TABLE `inventario_imagenes` (
  `id` int(11) NOT NULL,
  `inventario_id` int(11) NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `es_thumbnail` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventario_imagenes`
--

INSERT INTO `inventario_imagenes` (`id`, `inventario_id`, `ruta_imagen`, `es_thumbnail`) VALUES
(1, 1, 'laptop_ejecutiva.png', 1),
(2, 2, 'monitor_curvo.png', 1),
(3, 16, 'laptop_basica.png', 1),
(4, 19, 'monitor_oficina.png', 1),
(5, 43, 'monitor_crt.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `necesidades`
--

CREATE TABLE `necesidades` (
  `id` int(11) NOT NULL,
  `colaborador_id` int(11) NOT NULL,
  `descripcion` text NOT NULL COMMENT 'Descripción del equipo o software requerido',
  `estado` enum('Solicitado','Aprobado','Rechazado','Completado') DEFAULT 'Solicitado',
  `fecha_resolucion` datetime DEFAULT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `necesidades`
--

INSERT INTO `necesidades` (`id`, `colaborador_id`, `descripcion`, `estado`, `fecha_resolucion`, `fecha_solicitud`) VALUES
(1, 2, 'Necesito un nuevo monitor de 27 pulgadas para diseño gráfico.', 'Solicitado', NULL, '2025-07-20 19:30:00'),
(2, 4, 'Solicito una licencia de Microsoft Office 2021 para mi equipo.', 'Aprobado', '2025-07-22 10:00:00', '2025-07-21 14:00:00'),
(3, 5, 'Mi teclado y mouse no funcionan correctamente, necesito reemplazo.', 'Solicitado', NULL, '2025-07-23 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(1, 'juan.perez@empresa.com', 'token_ejemplo_1', '2025-07-26 10:00:00', '2025-07-25 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password_hash`, `activo`, `fecha_creacion`) VALUES
(1, 'Admin Principal', 'admin@cmdb.com', '$2y$10$lr/BPskJGClyX2iCSY1Vqu5N0aKibjDGe0jWlqWSSGLNijHEToY2K', 1, '2025-07-24 23:53:03'),
(2, 'Moderador Uno', 'mod1@cmdb.com', '$2y$10$p1hb7E9pPNLOJbRWBPvkL.AXKbFwqo.8jXsCTg92Gy653GJtp/QBW', 1, '2025-07-24 23:53:03'),
(3, 'Supervisor Dos', 'sup2@cmdb.com', '$2y$10$ScjSQ5Gc26wbQ.P7fV7RmOHUZLETZ8A34g5W7Wjvam9ZoO2p0wqQe', 1, '2025-07-24 23:53:03'),
(4, 'Gestor Tres', 'gestor3@cmdb.com', '$2y$10$yd0Aj0H1B5/8lb1VfB.6cu.rs1PMqXrlsFAoVGmAtYkeHURkiDBqS', 1, '2025-07-24 23:53:03'),
(5, 'Admin Cuatro', 'admin4@cmdb.com', '$2y$10$Fuo8EDjFK0i/OBeFgdJ.muyh/Wqx5RWgtrhjSCfcpI5OElKEGsXum', 1, '2025-07-24 23:53:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`),
  ADD KEY `colaborador_id` (`colaborador_id`);

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indexes for table `colaboradores`
--
ALTER TABLE `colaboradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificacion_unica` (`identificacion_unica`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `historial_login`
--
ALTER TABLE `historial_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `colaborador_id` (`colaborador_id`);

--
-- Indexes for table `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serie` (`serie`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indexes for table `inventario_imagenes`
--
ALTER TABLE `inventario_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_id` (`inventario_id`);

--
-- Indexes for table `necesidades`
--
ALTER TABLE `necesidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `colaborador_id` (`colaborador_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `asignaciones`
--
ALTER TABLE `asignaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `colaboradores`
--
ALTER TABLE `colaboradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `historial_login`
--
ALTER TABLE `historial_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `inventario_imagenes`
--
ALTER TABLE `inventario_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `necesidades`
--
ALTER TABLE `necesidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD CONSTRAINT `asignaciones_ibfk_1` FOREIGN KEY (`inventario_id`) REFERENCES `inventario` (`id`),
  ADD CONSTRAINT `asignaciones_ibfk_2` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`);

--
-- Constraints for table `historial_login`
--
ALTER TABLE `historial_login`
  ADD CONSTRAINT `historial_login_ibfk_1` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`);

--
-- Constraints for table `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Constraints for table `inventario_imagenes`
--
ALTER TABLE `inventario_imagenes`
  ADD CONSTRAINT `inventario_imagenes_ibfk_1` FOREIGN KEY (`inventario_id`) REFERENCES `inventario` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `necesidades`
--
ALTER TABLE `necesidades`
  ADD CONSTRAINT `necesidades_ibfk_1` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
