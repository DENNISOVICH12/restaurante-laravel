-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-08-2025 a las 23:57:20
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
-- Base de datos: `restaurante1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bebidas`
--

CREATE TABLE `bebidas` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_detalle` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre_cliente`, `telefono`, `direccion`, `fecha_registro`) VALUES
(1, 'de', '2313', 'asdasd', '2025-05-18 19:59:00'),
(2, 'sdffsdf', 'sdfsdf', 'dsfsf', '2025-05-18 20:39:37'),
(3, 'maldonado ', '2312131', 'addada', '2025-05-18 20:48:45'),
(4, 'castro', '231232', '1sadas', '2025-05-18 20:53:35'),
(5, 'david', '2131231', 'adsada', '2025-05-18 20:54:01'),
(6, 'lll', '21313', 'dsadas', '2025-05-18 22:55:34'),
(7, 'sdfsdfs', '23123123', 'sdfsdfs', '2025-05-19 02:50:12'),
(8, 'ytr', '123453234', 'oifhghj', '2025-05-19 02:51:32'),
(9, 'fdghjkygf', '564345', 'kjhgdsxcvbn', '2025-05-19 02:54:16'),
(10, 'sdfgfsd', '2342143', 'cxvxv', '2025-05-19 17:53:20'),
(11, ',hhghkj', '6543456', 'khjgfcvbn', '2025-05-19 17:58:36'),
(12, 'Miguel Gonzalez', '3228379093', 'Calle 5# 19', '2025-05-20 17:23:03'),
(13, 'marialll', '12123123', 'sdad', '2025-05-28 18:08:09'),
(14, 'dennis maldonado', '23123', 'ASDFASDA', '2025-08-27 15:31:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `categoria` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id`, `id_pedido`, `nombre_producto`, `precio`, `cantidad`, `categoria`, `descripcion`) VALUES
(1, 1, 'tostada española', 0.00, 1, 'Plato', ''),
(2, 1, 'ensalada marina', 30000.00, 1, 'Plato', ''),
(3, 2, 'Ensalada Marina', 30000.00, 1, 'Plato', ''),
(4, 2, 'Filet Mignon', 40000.00, 1, 'Plato', ''),
(5, 2, 'Pulpo', 80000.00, 1, 'Plato', ''),
(6, 3, 'Ensalada Marina', 30000.00, 2, 'Plato', ''),
(7, 3, 'Filet Mignon', 40000.00, 2, 'Plato', ''),
(8, 3, 'Sushi', 25000.00, 1, 'Plato', ''),
(9, 3, 'pistacho', 22222.00, 2, 'Plato', ''),
(10, 4, 'Ensalada Marina', 30000.00, 1, 'Plato', ''),
(11, 4, 'Filet Mignon', 40000.00, 1, 'Plato', ''),
(12, 4, 'Pulpo', 80000.00, 1, 'Plato', ''),
(13, 5, 'Pulpo', 80000.00, 22, 'Plato', ''),
(14, 6, 'Té Helado', 8000.00, 3, 'Bebida', ''),
(15, 7, 'tostada española', 15000.00, 2, 'Plato', ''),
(16, 7, 'espanadas de pollo', 15000.00, 1, 'Plato', ''),
(17, 8, 'tostada española', 15000.00, 1, 'Plato', ''),
(18, 8, 'espanadas de pollo', 15000.00, 1, 'Plato', ''),
(19, 9, 'tostada española', 15000.00, 1, 'Plato', ''),
(20, 9, 'espanadas de pollo', 15000.00, 2, 'Plato', ''),
(21, 10, 'tostada española', 15000.00, 1, 'Plato', ''),
(22, 10, 'empanadas', 10000.00, 1, 'Plato', ''),
(23, 10, 'espanadas de pollo', 15000.00, 1, 'Plato', ''),
(24, 11, 'empanadas', 10000.00, 1, 'Plato', ''),
(25, 11, 'carne asada', 40000.00, 1, 'Plato', ''),
(26, 11, 'palitos', 30000.00, 1, 'Plato', ''),
(27, 12, 'espanadas de pollo', 15000.00, 1, 'Plato', ''),
(28, 12, 'carne asada', 40000.00, 1, 'Plato', ''),
(29, 12, 'Coca Cola', 4500.00, 1, 'bebida', ''),
(30, 13, 'empanadas', 10000.00, 1, 'Plato', ''),
(31, 13, 'tostada española', 15000.00, 2, 'Plato', ''),
(32, 14, 'empanadas', 10000.00, 1, 'Plato', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `categoria` enum('plato','bebida','postre') NOT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `menu_items`
--

INSERT INTO `menu_items` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `categoria`, `disponible`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(5, 'Coca Cola', 'Bebida gaseosa refrescante', 4500.00, 'coca-cola.png', 'bebida', 1, '2025-05-18 19:07:56', NULL),
(6, 'Sprite', 'Gaseosa con sabor a limón y lima', 3500.00, 'Sprite.webp', 'bebida', 1, '2025-05-18 19:07:56', NULL),
(7, 'Pepsi', 'Gaseosa refrescante de sabor cola', 3500.00, 'pepsi.png', 'bebida', 1, '2025-05-18 19:07:56', NULL),
(8, 'Piña Colada', 'Bebida tropical de piña y coco', 8000.00, '125707_large.jpg', 'bebida', 1, '2025-05-18 19:07:56', NULL),
(9, 'Té Helado', 'Bebida suave y refrescante', 8000.00, 'te.jpg', 'bebida', 1, '2025-05-18 19:07:56', NULL),
(10, 'Limonada', 'Bebida de limón natural', 5000.00, 'limonada1.jpg', 'bebida', 1, '2025-05-18 19:07:56', NULL),
(11, 'Agua Mineral', 'Bebida refrescante y natural', 2500.00, 'agua mineral.jpg', 'bebida', 1, '2025-05-18 19:07:56', NULL),
(12, 'Cerezada', 'Limón con cereza dulce', 8000.00, 'limonada_cerezada.png', 'bebida', 1, '2025-05-18 19:07:56', NULL),
(13, 'Coctel', 'Mezcla refrescante con licor', 20000.00, 'coctel.png', 'bebida', 1, '2025-05-18 19:07:56', NULL),
(14, 'Tiramisú', 'Postre italiano a base de café y queso mascarpone', 15000.00, 'tiramisu.jpg', 'postre', 1, '2025-05-18 19:07:56', NULL),
(15, 'Flan de Caramelo', 'Tradicional postre de huevo con caramelo', 12000.00, 'flan.jpg', 'postre', 1, '2025-05-18 19:07:56', NULL),
(16, 'Brownie con Helado', 'Brownie de chocolate con helado de vainilla', 14000.00, 'brownie.jpg', 'postre', 1, '2025-05-18 19:07:56', NULL),
(22, 'empanadas', 'empanadas fritas', 10000.00, '682abfc1f22cd.png', 'plato', 1, '2025-05-19 00:17:25', '2025-05-19 00:21:05'),
(23, 'tostada española', 'tostada española', 15000.00, '682abfb67da5b.png', 'plato', 1, '2025-05-19 00:18:13', '2025-05-19 00:20:54'),
(25, 'espanadas de pollo', 'espanadas de pollo', 15000.00, '682abfbcc1041.png', 'plato', 1, '2025-05-19 00:19:32', '2025-05-19 00:21:00'),
(26, 'carne asada', 'carne asada', 40000.00, '682abf917e121.png', 'plato', 1, '2025-05-19 00:20:17', NULL),
(27, 'palitos', 'palitos', 30000.00, '682abfe90aacc.png', 'plato', 1, '2025-05-19 00:21:45', NULL),
(28, 'carne con vegetales', 'carne con vegetales', 50000.00, '682ac014bc280.png', 'plato', 1, '2025-05-19 00:22:28', NULL),
(30, 'ewrrwerw', 'werwer', 12213.00, '683797c9f1f23.webp', 'plato', 1, '2025-05-28 18:10:01', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'pendiente',
  `mesa` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_cliente`, `fecha`, `estado`, `mesa`) VALUES
(1, 1, '2025-05-18 19:59:00', 'cancelado', NULL),
(2, 2, '2025-05-18 20:39:37', 'listo', NULL),
(3, 3, '2025-05-18 20:48:45', 'en_entrega', NULL),
(4, 4, '2025-05-18 20:53:35', 'en_entrega', NULL),
(5, 5, '2025-05-18 20:54:01', 'en_entrega', NULL),
(6, 6, '2025-05-18 22:55:34', 'cancelado', NULL),
(7, 7, '2025-05-19 02:50:12', 'en_entrega', NULL),
(8, 8, '2025-05-19 02:51:32', 'en_entrega', NULL),
(9, 9, '2025-05-19 02:54:16', 'entregado', NULL),
(10, 10, '2025-05-19 17:53:20', 'cancelado', NULL),
(11, 11, '2025-05-19 17:58:36', 'entregado', NULL),
(12, 12, '2025-05-20 17:23:03', 'cancelado', NULL),
(13, 13, '2025-05-28 18:08:09', 'entregado', NULL),
(14, 14, '2025-08-27 15:31:28', 'pendiente', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `platos`
--

CREATE TABLE `platos` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_detalle` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `platos`
--

INSERT INTO `platos` (`id`, `id_pedido`, `id_detalle`, `nombre_producto`, `precio`, `cantidad`, `descripcion`) VALUES
(1, 1, 1, 'tostada española', 0.00, 1, ''),
(2, 1, 2, 'ensalada marina', 30000.00, 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','cocinero','mesero','empleado') NOT NULL DEFAULT 'empleado',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` datetime DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `password`, `rol`, `fecha_registro`, `ultimo_acceso`, `activo`) VALUES
(1, 'Administrador', 'admin@restaurante.com', '11111', '', '2025-05-18 18:32:45', '2025-05-19 00:42:57', 1),
(2, 'Empleado', 'empleado@restaurante.com', '123456', 'empleado', '2025-05-18 18:33:21', '2025-05-27 00:42:49', 1),
(3, 'dennis maldonado', 'demaca05@hotmail.com', '$2y$10$7nctQCVhqexcAmbABZU4JuzEvCKQ7lylL6ojcM0jAejEEojiT6eau', 'admin', '2025-05-18 19:18:44', '2025-05-28 18:09:21', 1),
(4, 'dennis maldonado', 'llasldsada@hotmail.com', '123456', 'mesero', '2025-05-19 01:01:06', NULL, 1),
(5, 'restaurante1', 'llaslds@hotmail.com', '$2y$10$f.yx8wjQeaReyusTOduej.m8CB0R7dsmndH/X4EH6fffr4Z/ltwV.', 'cocinero', '2025-05-19 01:07:46', '2025-08-28 17:13:39', 1),
(6, 'restaurantewe', 'llasldre@hotmail.com', '$2y$10$MJkeR32ExUczIl3zVaQaU.JONjQ/MSx6NynjfII70JpUii3a.NIV2', 'mesero', '2025-05-19 02:48:57', '2025-05-28 18:12:27', 1),
(7, 'maldonado', 'sada@hotmail.com', '$2y$10$JrXtCjKuMhLgfk.r2vk0L.6ZmBv65xt1szJUMk9odceDp9Ytw9jVu', 'mesero', '2025-08-28 17:10:38', '2025-08-28 17:10:55', 1),
(8, 'maldonadoQ', 'sDDDada@hotmail.com', '$2y$10$aWph/t7HXNFgoXFnGVLRj.6No1LTOd0derxL1rEpYcXR3TExFK8Tq', 'mesero', '2025-08-28 17:11:32', '2025-08-28 17:11:33', 1),
(9, 'asdasdas', 'sadsadsa@hotmail.com', '$2y$10$UUsb5VhFdfB2H4m9vxAq5uW0yLymMeKRPUHVq4Thl5rAGA3jKE9uS', 'mesero', '2025-08-28 17:12:30', '2025-08-28 17:12:48', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bebidas`
--
ALTER TABLE `bebidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `idx_detalle_bebidas` (`id_detalle`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido_detalle` (`id_pedido`);

--
-- Indices de la tabla `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente_pedidos` (`id_cliente`);

--
-- Indices de la tabla `platos`
--
ALTER TABLE `platos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `idx_detalle_platos` (`id_detalle`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bebidas`
--
ALTER TABLE `bebidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `platos`
--
ALTER TABLE `platos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bebidas`
--
ALTER TABLE `bebidas`
  ADD CONSTRAINT `bebidas_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bebidas_ibfk_2` FOREIGN KEY (`id_detalle`) REFERENCES `detalle_pedido` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `platos`
--
ALTER TABLE `platos`
  ADD CONSTRAINT `platos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `platos_ibfk_2` FOREIGN KEY (`id_detalle`) REFERENCES `detalle_pedido` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
