-- Adminer 4.8.1 MySQL 10.11.6-MariaDB-0+deb12u1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categorias` (`id`, `categoria`, `created_at`, `updated_at`) VALUES
(1,	'Turismo cultural',	'2025-02-03 19:49:53',	'2025-02-03 19:49:53'),
(2,	'Turismo deportivo y de eventos',	'2025-02-03 19:50:13',	'2025-02-03 19:50:13'),
(3,	'Turismo sostenible',	'2025-02-03 19:50:32',	'2025-02-03 19:50:32'),
(4,	'Turismo de aventura',	'2025-02-03 19:50:56',	'2025-02-03 19:50:56'),
(5,	'Turismo recreativo o viajes de ocio',	'2025-02-03 19:54:53',	'2025-02-03 19:54:53'),
(6,	'Turismo de negocios o corporativo',	'2025-02-03 19:55:15',	'2025-02-03 19:55:15'),
(7,	'Turismo rural',	'2025-02-03 19:55:35',	'2025-02-03 19:55:35')
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `categoria` = VALUES(`categoria`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);

DROP TABLE IF EXISTS `perfiles`;
CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `perfil` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `perfiles` (`id`, `perfil`, `created_at`, `updated_at`) VALUES
(1,	'administrador',	'2025-02-03 19:43:19',	'2025-02-03 19:43:19'),
(2,	'gestor',	'2025-02-03 19:43:53',	'2025-02-03 19:43:53'),
(3,	'ofertante',	'2025-02-03 19:44:14',	'2025-02-03 19:44:14'),
(4,	'demandante',	'2025-02-03 19:44:39',	'2025-02-03 19:44:39'),
(5,	'Usuario',	'2025-02-06 20:00:43',	'2025-02-06 20:00:43')
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `perfil` = VALUES(`perfil`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `perfil_id` int(11) NOT NULL,
  `password` varchar(256) NOT NULL,
  `activo` tinyint(4) NOT NULL,
  `token` varchar(60) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `perfil_id` (`perfil_id`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `perfil_id`, `password`, `activo`, `token`, `created_at`, `updated_at`) VALUES
(1,	'Ofertante 1',	'ofertante1@email.com',	3,	'$2y$10$W35AGHEZPahUUwLQQ9nnyOL3eu6esjvUcE2Mue63OeoEtfK89C1GK',	1,	'68fd71bc7e982bf3945d6a829f238b6d',	'2025-02-03 20:03:52',	'2025-02-03 20:03:52'),
(2,	'Demandante 1',	'demandante1@email.com',	4,	'$2y$10$W35AGHEZPahUUwLQQ9nnyOL3eu6esjvUcE2Mue63OeoEtfK89C1GK',	1,	'68fd71bc7e982bf3945d6a829f238b6d',	'2025-02-03 20:04:52',	'2025-02-03 20:04:52'),
(3,	'Ofertante 2',	'ofertante2@email.com',	3,	'$2y$10$W35AGHEZPahUUwLQQ9nnyOL3eu6esjvUcE2Mue63OeoEtfK89C1GK',	1,	'68fd71bc7e982bf3945d6a829f238b6d',	'2025-02-03 20:06:51',	'2025-02-03 20:06:51'),
(4,	'Demandante 2',	'demandante2@email.com',	4,	'$2y$10$W35AGHEZPahUUwLQQ9nnyOL3eu6esjvUcE2Mue63OeoEtfK89C1GK',	1,	'68fd71bc7e982bf3945d6a829f238b6d',	'2025-02-03 20:07:40',	'2025-02-03 20:07:40')
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `nombre` = VALUES(`nombre`), `email` = VALUES(`email`), `perfil_id` = VALUES(`perfil_id`), `password` = VALUES(`password`), `activo` = VALUES(`activo`), `token` = VALUES(`token`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);

DROP TABLE IF EXISTS `gestores`;
CREATE TABLE `gestores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `perfil_id` int(11) NOT NULL,
  `nombre` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `perfil_id` (`perfil_id`),
  CONSTRAINT `gestores_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `gestores` (`id`, `perfil_id`, `nombre`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1,	1,	'Administrador ',	'admin@email.com',	'$2y$10$W35AGHEZPahUUwLQQ9nnyOL3eu6esjvUcE2Mue63OeoEtfK89C1GK',	'2025-02-03 19:56:56',	'2025-02-03 19:56:56'),
(2,	2,	'Gestor',	'gestor@email.com',	'$2y$10$W35AGHEZPahUUwLQQ9nnyOL3eu6esjvUcE2Mue63OeoEtfK89C1GK',	'2025-02-03 19:57:34',	'2025-03-03 19:04:24')
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `perfil_id` = VALUES(`perfil_id`), `nombre` = VALUES(`nombre`), `email` = VALUES(`email`), `password` = VALUES(`password`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);

DROP TABLE IF EXISTS `ofertas`;
CREATE TABLE `ofertas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(256) NOT NULL,
  `descripcion` varchar(256) NOT NULL,
  `fecha_actividad` datetime NOT NULL,
  `aforo` int(11) NOT NULL,
  `visada` int(11) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `ofertas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `ofertas_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ofertas` (`id`, `usuario_id`, `categoria_id`, `nombre`, `descripcion`, `fecha_actividad`, `aforo`, `visada`, `created_at`, `updated_at`) VALUES
(1,	1,	1,	'Concierto: octavo del Gran Sinfónico de la ROSS en Sevilla 2024-2025',	'El jueves 3 y el viernes 4 de abril de 2025 en el Teatro de la Maestranza de Sevilla octavo concierto del ciclo Gran Sinfónico de la Real Orquesta Sinfónica (ROSS).',	'2025-04-03 21:10:37',	150,	1,	'2025-02-03 20:10:37',	'2025-02-03 20:10:37'),
(2,	1,	1,	'\"Luces de Bohemia\", de Ramón del Valle-Inclán',	'Este mes el #clublecturatextosdramaticos de Sevilla analiza la obra Luces de bohemia, escrita por el dramaturgo español Ramón del Valle-Inclán, obra esencial para entender la literatura española del s. XX.',	'2025-04-04 21:13:00',	50,	1,	'2025-02-03 20:13:00',	'2025-02-03 20:13:00'),
(3,	3,	7,	'EXPLORANDO EL CORAZON DE CAZORLA',	'Solo para senderistas expert@s estaremos en el interior del parque natural alojados a una hora de el pueblo mas proximo, solo 15 personas',	'2025-02-14 21:20:13',	15,	1,	'2025-02-03 20:20:13',	'2025-02-03 20:20:13'),
(4,	3,	7,	'Tierras de José María el Tempranillo - Sendero del Paraje Natural del Embalse de Malpasillo- Ermira de Nuestra Señora de la Fuensanta',	'Este sendero forma parte de la red de senderos de la Ruta Tierras de José María el Tempranillo, su recorrido circular, permite al senderista descubrir lugares de gran belleza, como el Paraje Natural del Embalse de Malpasillo, el Meandro que el Río',	'2025-02-14 21:20:13',	20,	1,	'2025-02-03 20:24:58',	'2025-02-03 20:24:58'),
(5,	1,	5,	'Excursión de un día a Gibraltar desde Sevilla',	'En esta excursión vivirás la mejor experiencia de tu vida. No podrás olvidarlo nunca.',	'2025-03-05 11:25:00',	25,	0,	'2025-02-05 10:25:00',	'2025-02-05 10:25:00'),
(6,	3,	4,	'Senderismo en el Caminito del Rey desde Sevilla',	'Una vez conocido como el camino más peligroso de senderismo en todo el mundo, el Caminito del Rey es ahora uno de las atracciones más populares en España. Asegúrese la entrada al reservar con antelación.',	'2025-03-15 11:33:04',	10,	0,	'2025-02-05 10:33:04',	'2025-02-05 10:33:04'),
(8,	1,	6,	'Aforo Máximo',	'Prueba',	'2025-03-30 01:22:00',	1,	1,	'2025-03-03 19:18:42',	'2025-03-03 19:18:42')
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `usuario_id` = VALUES(`usuario_id`), `categoria_id` = VALUES(`categoria_id`), `nombre` = VALUES(`nombre`), `descripcion` = VALUES(`descripcion`), `fecha_actividad` = VALUES(`fecha_actividad`), `aforo` = VALUES(`aforo`), `visada` = VALUES(`visada`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);

DROP TABLE IF EXISTS `solicitudes`;
CREATE TABLE `solicitudes` (
  `oferta_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_solicitud` datetime NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`oferta_id`,`usuario_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`oferta_id`) REFERENCES `ofertas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `solicitudes` (`oferta_id`, `usuario_id`, `fecha_solicitud`, `created_at`, `updated_at`) VALUES
(1,	2,	'2025-02-03 21:27:46',	'2025-02-03 20:27:46',	'2025-02-03 20:27:46'),
(2,	2,	'2025-02-03 21:28:38',	'2025-02-03 20:28:38',	'2025-02-03 20:28:38'),
(3,	4,	'2025-02-03 21:28:49',	'2025-02-03 20:28:49',	'2025-02-03 20:28:49'),
(4,	2,	'2025-02-03 21:29:33',	'2025-02-03 20:29:33',	'2025-02-03 20:29:33'),
(4,	4,	'2025-02-03 21:29:44',	'2025-02-03 20:29:44',	'2025-02-03 20:29:44'),
(8,	2,	'2025-03-03 21:19:54',	'2025-03-03 20:19:54',	'2025-03-03 20:19:54')
ON DUPLICATE KEY UPDATE `oferta_id` = VALUES(`oferta_id`), `usuario_id` = VALUES(`usuario_id`), `fecha_solicitud` = VALUES(`fecha_solicitud`), `created_at` = VALUES(`created_at`), `updated_at` = VALUES(`updated_at`);

-- 2025-03-03 20:23:26
