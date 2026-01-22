-- 1. Tabla Clientes
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cliente` varchar(100) NOT NULL,
  `cedula_rif` varchar(20) NOT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `cedula_rif` (`cedula_rif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tabla Proveedores
CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_proveedor` varchar(100) NOT NULL,
  `rif` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_proveedor`),
  UNIQUE KEY `rif` (`rif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `proveedores` (`id_proveedor`, `nombre_proveedor`, `rif`, `telefono`, `correo`, `ciudad`) VALUES
(1, 'Suministros Globales, C.A.', 'J-300123-4', '0212-5551234', 'ventas@sumiglobales.com', 'Caracas'),
(2, 'Papelera del Centro', 'J-295556-1', '0241-8884567', 'contacto@papelcentro.com', 'Valencia'),
(3, 'Distribuidora Alfa', 'J-412233-0', '0251-7779911', 'pedidos@distalfa.com', 'Barquisimeto'),
(4, 'Arte & Trazo', 'J-500678-2', '0274-2223344', 'info@artetrazos.com', 'Mérida'),
(5, 'Importaciones Express', 'J-102030-4', '0261-4445566', 'logisticaxpress@gmail.com', 'Maracaibo'),
(6, 'Papelería Industrial 21', 'J-600789-0', '0243-1112233', 'ventas@papel21.com', 'Maracay'),
(7, 'Librería Universal', 'J-700890-1', '0212-9998877', 'contacto@universal.com', 'Caracas'),
(8, 'Artes y Colores', 'J-800901-2', '0276-3334455', 'pedidos@artesycolores.com', 'San Cristóbal'),
(9, 'Tecno-Oficina C.A.', 'J-900012-3', '0241-7776655', 'soporte@tecnooficina.com', 'Valencia');

-- 3. Tabla Productos
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `precio_de_entrada` decimal(10,2) NOT NULL,
  `precio_de_salida` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 10,
  `fecha_de_ingreso` date NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `codigo` (`codigo`),
  CONSTRAINT `fk_producto_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `productos` (`id_producto`, `codigo`, `nombre_producto`, `precio_de_entrada`, `precio_de_salida`, `cantidad`, `stock_minimo`, `fecha_de_ingreso`, `ubicacion`, `id_proveedor`) VALUES
(1, 'OF-001', 'Grapadora Industrial', 3577.73, 5515.65, 25, 5, '2025-12-15', 'Pasillo 1 Estante 4', 5),
(2, 'OF-002', 'Perforadora de 3 huecos', 2534.22, 4174.00, 15, 5, '2025-12-15', 'Pasillo 1 Estante 4', 5),
(3, 'OF-003', 'Clips Mariposa (Caja)', 357.77, 745.36, 96, 20, '2026-01-10', 'Pasillo 1 Estante 2', 5),
(4, 'OF-004', 'Notas Adhesivas (Set)', 596.29, 1351.64, 80, 15, '2026-01-10', 'Pasillo 1 Estante 1', 5),
(5, 'OF-005', 'Organizador de Escritorio', 4472.15, 7453.58, 12, 3, '2025-12-20', 'Pasillo 2 Estante 3', 5),
(6, 'OF-006', 'Archivador de Palanca', 1341.64, 2236.07, 50, 10, '2026-01-05', 'Pasillo 2 Estante 5', 5),
(7, 'OF-007', 'Protector de pantalla', 1490.72, 2683.29, 30, 5, '2026-01-12', 'Pasillo 4 Estante 1', 5),
(8, 'OF-008', 'Mouse Pad Ergonómico', 1788.86, 3279.57, 40, 8, '2026-01-08', 'Pasillo 4 Estante 2', 5),
(9, 'OF-009', 'Pizarra Blanca (60x40)', 6559.15, 10435.01, 8, 2, '2025-12-18', 'Pasillo 5 Estante 1', 5),
(10, 'OF-010', 'Marcadores de Pizarra', 1043.50, 1788.86, 60, 12, '2026-01-10', 'Pasillo 1 Estante 3', 5),
(11, 'PA-101', 'Resma de Papel Carta', 1222.39, 1937.93, 197, 50, '2026-01-12', 'Pasillo 3 Estante 1', 6),
(12, 'PA-102', 'Resma de Papel Oficio', 1431.09, 2146.63, 150, 40, '2026-01-12', 'Pasillo 3 Estante 1', 6),
(13, 'PA-103', 'Cartulina Escolar', 149.07, 357.77, 500, 100, '2026-01-02', 'Pasillo 3 Estante 4', 6),
(14, 'PA-104', 'Papel Crepé (Varios)', 89.44, 238.51, 300, 50, '2026-01-02', 'Pasillo 3 Estante 4', 6),
(15, 'PA-105', 'Papel Bond (Pliego)', 119.26, 298.14, 400, 100, '2026-01-02', 'Pasillo 3 Estante 4', 6),
(16, 'PA-106', 'Sobres Manila (Caja 50)', 2087.00, 3577.72, 45, 10, '2026-01-05', 'Pasillo 3 Estante 2', 6),
(17, 'PA-107', 'Papel Fotográfico', 2981.43, 5366.58, 25, 5, '2026-01-08', 'Pasillo 3 Estante 5', 6),
(18, 'PA-108', 'Etiquetas Autoadhesivas', 894.43, 1639.79, 70, 15, '2026-01-10', 'Pasillo 3 Estante 3', 6),
(19, 'PA-109', 'Papel Carbón (Pack)', 745.36, 1341.64, 20, 5, '2025-12-20', 'Pasillo 3 Estante 2', 6),
(20, 'PA-110', 'Block de Dibujo', 1132.94, 1937.93, 85, 20, '2026-01-05', 'Pasillo 3 Estante 5', 6),
(21, 'LI-201', 'El Quijote (Ed. Lujo)', 7453.58, 13416.44, 5, 2, '2025-12-01', 'Pasillo 6 Estante 1', 7),
(22, 'LI-202', 'Cien Años de Soledad', 3130.50, 6559.15, 12, 3, '2026-01-05', 'Pasillo 6 Estante 1', 7),
(23, 'LI-203', 'Antología Poética', 3130.50, 5366.58, 8, 2, '2026-01-05', 'Pasillo 6 Estante 2', 7),
(24, 'LI-204', 'Rayuela - Cortázar', 4174.00, 7155.43, 10, 3, '2026-01-10', 'Pasillo 6 Estante 1', 7),
(25, 'LI-205', 'Diccionario RAE', 8944.29, 14907.16, 6, 2, '2025-12-15', 'Pasillo 6 Estante 5', 7),
(26, 'LI-206', 'Guía de Ortografía', 1490.72, 2832.36, 20, 5, '2025-12-15', 'Pasillo 6 Estante 4', 7),
(27, 'LI-207', 'Separadores Metálicos', 447.21, 1043.50, 100, 20, '2026-01-12', 'Pasillo 7 Estante 1', 7),
(28, 'LI-208', 'Lámpara de Lectura', 2683.29, 4770.29, 15, 4, '2025-12-20', 'Pasillo 7 Estante 3', 7),
(29, 'LI-209', 'Atril de Madera', 5366.58, 9540.58, 4, 1, '2025-12-18', 'Pasillo 7 Estante 2', 7),
(30, 'LI-210', 'Cuaderno de Cuero', 6559.15, 3279.57, 20, 5, '2026-01-05', 'Pasillo 7 Estante 4', 7),
(31, 'AR-301', 'Pluma Fuente', 10435.01, 17888.59, 10, 2, '2026-01-10', 'Pasillo 8 Estante 1', 8),
(32, 'AR-302', 'Tinta para Pluma', 2385.14, 4174.00, 18, 4, '2026-01-10', 'Pasillo 8 Estante 1', 8),
(33, 'AR-303', 'Set de Carboncillos', 8944.29, 1937.93, 12, 5, '2026-01-04', 'Pasillo 8 Estante 3', 8),
(34, 'AR-304', 'Acuarelas (Set 24)', 4174.00, 7304.51, 15, 3, '2026-01-04', 'Pasillo 8 Estante 4', 8),
(35, 'AR-305', 'Pinceles Sintéticos', 2087.00, 3875.86, 40, 10, '2026-01-04', 'Pasillo 8 Estante 4', 8),
(36, 'AR-306', 'Lápices de Colores (36)', 2832.36, 5068.43, 55, 12, '2026-01-08', 'Pasillo 8 Estante 2', 8),
(37, 'AR-307', 'Borrador de Miga', 74.54, 208.70, 200, 50, '2026-01-08', 'Pasillo 8 Estante 2', 8),
(38, 'AR-308', 'Sacapuntas Metálico', 238.51, 447.21, 150, 30, '2026-01-08', 'Pasillo 8 Estante 2', 8),
(39, 'AR-309', 'Regla Metálica 30cm', 596.29, 1192.57, 60, 15, '2026-01-12', 'Pasillo 8 Estante 5', 8),
(40, 'AR-310', 'Estuche de Geometría', 1639.79, 2981.43, 35, 8, '2026-01-12', 'Pasillo 8 Estante 5', 8),
(41, 'MS-401', 'Tijeras de Acero', 1043.50, 1937.93, 40, 10, '2025-12-15', 'Pasillo 2 Estante 1', 9),
(42, 'MS-402', 'Cinta Adhesiva', 327.96, 685.73, 120, 25, '2026-01-05', 'Pasillo 2 Estante 1', 9),
(43, 'MS-403', 'Pegamento en Barra', 149.07, 596.29, 90, 20, '2026-01-05', 'Pasillo 2 Estante 2', 9),
(44, 'MS-404', 'Engrapadora Brazo Largo', 5366.58, 8944.29, 8, 2, '2025-12-20', 'Pasillo 1 Estante 5', 9),
(45, 'MS-405', 'Calculadora Científica', 5317.40, 9925.82, 20, 5, '2026-01-10', 'Pasillo 4 Estante 3', 9),
(46, 'MS-406', 'USB 64GB', 2481.45, 4785.66, 50, 10, '2026-01-12', 'Pasillo 4 Estante 4', 9),
(47, 'MS-407', 'Sello Fechador', 2683.29, 4919.36, 12, 3, '2026-01-08', 'Pasillo 2 Estante 4', 9),
(48, 'MS-408', 'Tinta para Sellos', 745.36, 1431.09, 25, 6, '2026-01-08', 'Pasillo 2 Estante 4', 9),
(49, 'MS-409', 'Destructora de Papel', 13416.44, 22369.73, 5, 1, '2025-12-15', 'Pasillo 5 Estante 2', 9),
(50, 'MS-410', 'Reposapiés Oficina', 5366.58, 9549.58, 10, 2, '2025-12-20', 'Pasillo 5 Estante 3', 9);
(51, 'TG-501', 'Teclado Mecánico RGB', 8944.29, 14907.16, 15, 3, '2026-01-15', 'Pasillo 4 Estante 5', 1),
(52, 'TG-502', 'Monitor 24 Pulgadas', 35777.18, 53665.77, 10, 2, '2026-01-15', 'Pasillo 5 Estante 4', 1),
(53, 'TG-503', 'Cámara Web HD', 4472.15, 8348.01, 25, 5, '2026-01-15', 'Pasillo 4 Estante 5', 1),
(54, 'TG-504', 'Audífonos con Micrófono', 2981.43, 5962.86, 30, 5, '2026-01-16', 'Pasillo 4 Estante 5', 1),
(55, 'TG-505', 'Disco Duro Externo 1TB', 17888.59, 26832.88, 12, 3, '2026-01-16', 'Pasillo 5 Estante 5', 1),
(56, 'TG-506', 'Cable HDMI 3mts', 596.29, 1192.57, 50, 10, '2026-01-16', 'Pasillo 4 Estante 2', 1),
(57, 'TG-507', 'Adaptador USB-C', 894.43, 1788.86, 40, 8, '2026-01-16', 'Pasillo 4 Estante 2', 1),
(58, 'TG-508', 'Router Inalámbrico', 7453.58, 11925.73, 15, 3, '2026-01-17', 'Pasillo 5 Estante 3', 1),
(59, 'TG-509', 'Soporte para Laptop', 2683.29, 4472.15, 20, 4, '2026-01-17', 'Pasillo 2 Estante 4', 1),
(60, 'TG-510', 'Baterías Recargables (Pack)', 1192.57, 2385.14, 60, 15, '2026-01-17', 'Pasillo 1 Estante 5', 1),
(61, 'PT-601', 'Carpeta de Archivo (Pack 12)', 1490.72, 2683.29, 100, 20, '2026-01-18', 'Pasillo 3 Estante 2', 2),
(62, 'PT-602', 'Separadores Numéricos', 298.14, 596.29, 200, 50, '2026-01-18', 'Pasillo 3 Estante 2', 2),
(63, 'PT-603', 'Índices Alfabéticos', 298.14, 596.29, 150, 40, '2026-01-18', 'Pasillo 3 Estante 2', 2),
(64, 'PT-604', 'Sobres Acolchados (Unidad)', 89.44, 238.51, 300, 50, '2026-01-19', 'Pasillo 3 Estante 3', 2),
(65, 'PT-605', 'Cinta para Embalar', 447.21, 1043.50, 80, 20, '2026-01-19', 'Pasillo 2 Estante 1', 2),
(66, 'PT-606', 'Film Estirable (Rollo)', 2087.00, 3875.86, 20, 5, '2026-01-19', 'Pasillo 5 Estante 4', 2),
(67, 'PT-607', 'Etiquetadora Manual', 5366.58, 8944.29, 10, 2, '2026-01-20', 'Pasillo 2 Estante 5', 2),
(68, 'PT-608', 'Papel Térmico (Rollos)', 149.07, 447.21, 500, 100, '2026-01-20', 'Pasillo 3 Estante 1', 2),
(69, 'PT-609', 'Talonario de Facturas', 596.29, 1192.57, 60, 15, '2026-01-20', 'Pasillo 3 Estante 5', 2),
(70, 'PT-610', 'Recibo de Caja (Block)', 298.14, 745.36, 120, 30, '2026-01-20', 'Pasillo 3 Estante 5', 2),
(71, 'ED-701', 'Enciclopedia Escolar', 14907.16, 23851.45, 8, 2, '2026-01-21', 'Pasillo 6 Estante 3', 3),
(72, 'ED-702', 'Atlas Geográfico', 5366.58, 8944.29, 15, 3, '2026-01-21', 'Pasillo 6 Estante 3', 3),
(73, 'ED-703', 'Libro de Caligrafía', 447.21, 1043.50, 100, 25, '2026-01-21', 'Pasillo 6 Estante 4', 3),
(74, 'ED-704', 'Mapa Mundi (Lámina)', 149.07, 447.21, 200, 50, '2026-01-21', 'Pasillo 3 Estante 4', 3),
(75, 'ED-705', 'Globo Terráqueo', 7453.58, 11925.73, 5, 1, '2026-01-22', 'Pasillo 7 Estante 1', 3),
(76, 'ED-706', 'Set de Reglas Escolares', 238.51, 596.29, 150, 30, '2026-01-22', 'Pasillo 8 Estante 5', 3),
(77, 'ED-707', 'Compás de Precisión', 1192.57, 2683.29, 45, 10, '2026-01-22', 'Pasillo 8 Estante 5', 3),
(78, 'ED-708', 'Flauta Dulce', 1788.86, 3577.72, 35, 8, '2026-01-22', 'Pasillo 7 Estante 5', 3),
(79, 'ED-709', 'Cuaderno de Música', 447.21, 1043.50, 80, 20, '2026-01-22', 'Pasillo 7 Estante 5', 3),
(80, 'ED-710', 'Pinceles Escolares (Set)', 596.29, 1192.57, 120, 25, '2026-01-22', 'Pasillo 8 Estante 4', 3),
(81, 'AP-801', 'Lienzo 40x50', 2683.29, 4770.29, 20, 5, '2026-01-22', 'Pasillo 8 Estante 1', 4),
(82, 'AP-802', 'Caballete de Aluminio', 11925.73, 17888.59, 6, 2, '2026-01-22', 'Pasillo 8 Estante 1', 4),
(83, 'AP-803', 'Óleos (Caja 12 colores)', 8944.29, 14907.16, 12, 3, '2026-01-22', 'Pasillo 8 Estante 3', 4),
(84, 'AP-804', 'Espátulas de Arte (Set)', 2385.14, 4472.15, 18, 4, '2026-01-22', 'Pasillo 8 Estante 3', 4),
(85, 'AP-805', 'Paleta de Mezclado', 447.21, 1043.50, 40, 10, '2026-01-22', 'Pasillo 8 Estante 3', 4),
(86, 'AP-806', 'Barniz para Óleo', 1788.86, 3279.57, 25, 5, '2026-01-22', 'Pasillo 8 Estante 3', 4),
(87, 'AP-807', 'Papel Acuarela 300g', 2981.43, 5366.58, 50, 10, '2026-01-22', 'Pasillo 8 Estante 4', 4),
(88, 'AP-808', 'Goma Maleable', 149.07, 357.77, 100, 20, '2026-01-22', 'Pasillo 8 Estante 2', 4),
(89, 'AP-809', 'Estuche de Tiralíneas', 6559.15, 10435.01, 15, 3, '2026-01-22', 'Pasillo 8 Estante 2', 4),
(90, 'AP-910', 'Maletín de Arte', 13416.44, 22369.73, 5, 1, '2026-01-22', 'Pasillo 8 Estante 5', 4);


-- 4. Tabla Ventas (Maestro)
CREATE TABLE `venta` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) DEFAULT NULL,
  `fecha_venta` datetime DEFAULT current_timestamp(),
  `metodo_pago` enum('Efectivo','Tarjeta de Débito','Tarjeta de Crédito','Transferencia','Pago Móvil','Otro') NOT NULL,
  `referencia_pago` varchar(50) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `total_venta` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_venta`),
  CONSTRAINT `fk_venta_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Tabla Detalle de Ventas
CREATE TABLE `detalle_venta` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_producto` int(11) NOT NULL,
  `precio_unitario_venta` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  CONSTRAINT `fk_detalle_venta_maestro` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_venta_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Tabla Compras (Maestro)
CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_compra` datetime DEFAULT current_timestamp(),
  `id_proveedor` int(11) DEFAULT NULL,
  `total_compra` decimal(10,2) NOT NULL,
  `tipo_compra` enum('Adquisición','Reabastecimiento') NOT NULL,
  PRIMARY KEY (`id_compra`),
  CONSTRAINT `fk_compra_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Tabla Detalle de Compras
CREATE TABLE `detalle_compras` (
  `id_detalle_compra` int(11) NOT NULL AUTO_INCREMENT,
  `id_compra` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_comprada` int(11) NOT NULL,
  `precio_unitario_compra` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle_compra`),
  CONSTRAINT `fk_detalle_compra_maestro` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_compra_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. Tabla Usuarios
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(50) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_usuario` (`nombre_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuario` (`id`, `nombre_usuario`, `contraseña`) VALUES
(1, 'Admin_Supremo', '$2y$10$qh0UCzP2XhnJwpZ0zKw2k.d4MFiuAEpe1GfarVRcITQYq7rKqdSJe');