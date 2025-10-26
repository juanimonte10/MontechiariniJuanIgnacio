-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para stock_db
CREATE DATABASE IF NOT EXISTS `stock_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `stock_db`;

-- Volcando estructura para tabla stock_db.detalle_pedido
CREATE TABLE IF NOT EXISTS `detalle_pedido` (
  `id_pedido` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_pedido`,`id_producto`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla stock_db.detalle_pedido: ~2 rows (aproximadamente)
INSERT INTO `detalle_pedido` (`id_pedido`, `id_producto`, `cantidad`, `subtotal`) VALUES
	(1, 2, 2, 5000.00),
	(2, 2, 1, 2500.00),
	(3, 2, 3, 1500.00);

-- Volcando estructura para tabla stock_db.pedidos
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id_pedido` int NOT NULL AUTO_INCREMENT,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla stock_db.pedidos: ~2 rows (aproximadamente)
INSERT INTO `pedidos` (`id_pedido`, `fecha`, `total`, `id_usuario`) VALUES
	(1, '2025-10-01 22:12:59', 5000.00, 2),
	(2, '2025-10-01 22:13:04', 2500.00, 2),
	(3, '2025-10-01 22:27:33', 1500.00, 2);

-- Volcando estructura para tabla stock_db.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla stock_db.productos: ~11 rows (aproximadamente)
INSERT INTO `productos` (`id_producto`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`) VALUES
	(2, 'coca', 'coca cola 2,5lt', 2500.00, 10, 'https://www.coca-cola.com/content/dam/onexp/mx/es/brands/coca-cola/coca-cola-original/Product-Information-Section-Coca-Cola-Original.jpg'),
	(4, 'PAPAS', 'CHEESE X 109 GR CAJA X 18', 72000.00, 10, 'https://jumboargentina.vtexassets.com/arquivos/ids/800603/Papas-Fritas-Pringles-Queso-X109gs-1-1000006.jpg?v=638355837793670000'),
	(5, 'CAFE', 'BONAFIDE TORRADO SUAVE 1KG', 120000.00, 21, 'https://static.cotodigital3.com.ar/sitios/fotos/large/00038400/00038448.jpg'),
	(6, 'Bono o bon de corazon', '105 gr', 1200.00, 2, 'https://tse2.mm.bing.net/th/id/OIP.0GFM9RMzDK06jnkqIWUtFAHaHa?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3'),
	(7, 'Arcor Mogul', 'bolsa de 1kg', 200.00, 3, 'https://wongfood.vteximg.com.br/arquivos/ids/353832-1000-1000/138150-1.jpg?v=637226754565800000'),
	(8, 'Sprite', 'Sprite 2.5 lt', 1000.00, 3, 'https://tse1.mm.bing.net/th/id/OIP.IhP_NAoweU6n_DHRwJl3UgHaHa?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3'),
	(9, 'Manao', 'Manao 2.25 lt', 1200.00, 3, 'https://tse4.mm.bing.net/th/id/OIP.sRSAmx3cUxJuDxWiONst8AHaHa?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3'),
	(10, 'Fanta', 'Fanta 1,5 lt', 1500.00, 10, 'https://www.coca-cola.com/content/dam/onexp/lk/brands/fanta/fanta-2-desktop.png'),
	(11, 'Azucar Iansa', '1 kg', 450.00, 2, 'https://tse2.mm.bing.net/th/id/OIP.NvzER1Kn3c3lpNj8NbNHXgHaHa?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3'),
	(12, 'Hambuerguesas', 'Patty Clasico', 2100.00, 12, 'https://tse1.mm.bing.net/th/id/OIP.Na6MUcX0GdhTZnA5c5ag8wHaHa?cb=12&rs=1&pid=ImgDetMain&o=7&rm=3'),
	(13, 'Salchicha', 'Patty', 2000.00, 3, 'https://carrefourar.vtexassets.com/arquivos/ids/183134/7790670045199_02.jpg?v=637468590268370000');

-- Volcando estructura para tabla stock_db.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL,
  `tipo` enum('cliente','admin') NOT NULL DEFAULT 'cliente',
  PRIMARY KEY (`id_usuario`) USING BTREE,
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla stock_db.usuarios: ~2 rows (aproximadamente)
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `password`, `rol`, `tipo`) VALUES
	(1, 'nicoputo', 'nicoromani@gmail.com', '$2y$10$cDb/MyWsm2rfYyWGrCn2AOs/t7Cn.rNCMeuZb5FEHvnQKegiHwixW', 'cliente', 'cliente'),
	(2, 'cami', 'camila_gasc@gmail.com', '$2y$10$WuO3m53rxeUJR60r19BQb.1E/tn/qyMKP1Rc7FX.iNlR/GnL0Fw5G', 'cliente', 'cliente'),
	(5, 'tano', 'jmonte@gmail.com', '$2y$10$9cTHkr7rXYY.3SCiW6Jwf.A6ThJRTXIkbYFzUFFXM31pt6yl494pu', 'admin', 'cliente');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
