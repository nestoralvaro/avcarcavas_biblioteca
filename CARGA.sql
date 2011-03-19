CREATE TABLE IF NOT EXISTS `disponibilidad` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `estado` varchar(250) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `disponibilidad` (`id`, `estado`, `descripcion`) VALUES
(1, 'Disponible', 'Libro presente en la biblioteca disponible para ser reservado y prestado'),
(2, 'Solicitado', 'Libro solicitado por un usuario, pero aún presente en la biblioteca (no recogido)'),
(3, 'Prestado', 'Libro no presente en la biblioteca por estar en posesión de un usuario'),
(4, 'Con retraso', 'Libro que debería estar en la biblioteca pero aún no ha sido devuelto. El usuario esta multado por ello');




CREATE TABLE IF NOT EXISTS `historico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `descripcion` varchar(250) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `id_libro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `nombre_usuario_externo` varchar(250) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `DNI` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;




CREATE TABLE IF NOT EXISTS `libros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(250) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `autor` varchar(250) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `fecha_alta` date NOT NULL,
  `disponibilidad` int(2) NOT NULL DEFAULT '1',
  `fecha_prestamo` date DEFAULT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `usuario` int(11) DEFAULT NULL,
  `nombre_usuario_externo` varchar(250) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `DNI` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;




CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) NOT NULL,
  `mail` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `telf` varchar(250) NOT NULL,
  `fecha_alta` date DEFAULT NULL,
  `multas` int(1) NOT NULL DEFAULT '0',
  `fecha_ultima_multa` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
