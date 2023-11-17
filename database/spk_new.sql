-- Adminer 4.8.1 MySQL 5.5.5-10.11.2-MariaDB-1:10.11.2+maria~ubu2204 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `kriteria`;
CREATE TABLE `kriteria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `bobot` float(8,2) NOT NULL,
  `status_sub` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `sub_kriteria`;
CREATE TABLE `sub_kriteria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kriteria_id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `bobot` float(8,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kriteria_id` (`kriteria_id`),
  CONSTRAINT `sub_kriteria_ibfk_1` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `alternatif`;
CREATE TABLE `alternatif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `alternatif_bobot`;
CREATE TABLE `alternatif_bobot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bulan` int(11) NOT NULL,
  `alternatif_id` int(11) NOT NULL,
  `kriteria_id` int(11) NOT NULL,
  `sub_kriteria_id` int(11) DEFAULT NULL,
  `bobot` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alternatif_id` (`alternatif_id`),
  KEY `kriteria_id` (`kriteria_id`),
  KEY `sub_kriteria_id` (`sub_kriteria_id`),
  CONSTRAINT `alternatif_bobot_ibfk_1` FOREIGN KEY (`alternatif_id`) REFERENCES `alternatif` (`id`),
  CONSTRAINT `alternatif_bobot_ibfk_2` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`),
  CONSTRAINT `alternatif_bobot_ibfk_3` FOREIGN KEY (`sub_kriteria_id`) REFERENCES `sub_kriteria` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `hasil`;
CREATE TABLE `hasil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bulan` int(11) NOT NULL,
  `alternatif_id` int(11) NOT NULL,
  `no` int(11) NOT NULL,
  `nilai` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alternatif_id` (`alternatif_id`),
  CONSTRAINT `hasil_ibfk_1` FOREIGN KEY (`alternatif_id`) REFERENCES `alternatif` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user` (`id`, `username`, `password`, `nama`) VALUES
(1,	'admin',	'21232f297a57a5a743894a0e4a801fc3',	'admin');

-- 2023-10-25 12:59:57
