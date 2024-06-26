-- Adminer 4.8.1 MySQL 8.0.36 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `company-web`;
CREATE DATABASE `company-web` /*!40100 DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `company-web`;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id_order` int NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(75) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `prijmeni` varchar(75) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `adresa` varchar(150) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefon` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `internet` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `text_internet` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `iptv` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `balicek` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `text_iptv` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `voipcislo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `voip` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `text_voip` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `poznamka` varchar(4096) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `datum_vlozeni` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
  `id_question` int NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `prijmeni` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefon` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vs` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `dotaz` varchar(4096) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text` varchar(4096) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `datum_vlozeni` date NOT NULL,
  PRIMARY KEY (`id_question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


-- 2024-05-10 22:58:35