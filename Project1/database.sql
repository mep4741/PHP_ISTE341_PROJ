-- Adminer 4.8.1 MySQL 8.0.27 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `attendee`;
CREATE TABLE `attendee` (
  `idattendee` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `role` int DEFAULT NULL,
  PRIMARY KEY (`idattendee`),
  KEY `role_idx` (`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `attendee` (`idattendee`, `name`, `password`, `role`) VALUES
(1,	'Anoushka Shenoy',	'f791c737b231449a328ed4f35e212b29b097b4b86da68048a47abdafbe449b56',	2),
(2,	'Maija Philip',	'a21dfbbe620433ccc573682542a7b10f565c17befb5abdae910be34ebb53f15c',	1),
(3,	'Kyra Brosnahan',	'2f988ff25d0c2d4d6917f345f8a8df44bbc46b0c4851c551c4a1512aa8603270',	3),
(4,	'Matt Lynch',	'815f9df6b2592f189e9a4609ecd56e21cd81fd4725fb598c7f4a45ad692ac9b0',	3),
(14,	'Elizabeth Morgan',	'emorgan',	3);

DROP TABLE IF EXISTS `attendee_event`;
CREATE TABLE `attendee_event` (
  `event` int NOT NULL,
  `attendee` int NOT NULL,
  `paid` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`event`,`attendee`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `attendee_event` (`event`, `attendee`, `paid`) VALUES
(1,	2,	0),
(1,	3,	0),
(2,	1,	0),
(2,	4,	1),
(2,	14,	0),
(1,	14,	0),
(2,	2,	0),
(1,	1,	0);

DROP TABLE IF EXISTS `attendee_session`;
CREATE TABLE `attendee_session` (
  `session` int NOT NULL,
  `attendee` int NOT NULL,
  PRIMARY KEY (`session`,`attendee`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `attendee_session` (`session`, `attendee`) VALUES
(2,	1),
(2,	4),
(6,	1),
(6,	2),
(6,	3),
(6,	14);

DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `idevent` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `datestart` datetime NOT NULL,
  `dateend` datetime NOT NULL,
  `numberallowed` int NOT NULL,
  `venue` int NOT NULL,
  PRIMARY KEY (`idevent`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  KEY `venue_fk_idx` (`venue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `event` (`idevent`, `name`, `datestart`, `dateend`, `numberallowed`, `venue`) VALUES
(1,	'Group Hiking Year',	'2024-01-01 00:00:00',	'2025-01-01 00:00:00',	14,	1),
(2,	'Kelsey McCallum Wedding',	'2025-08-01 00:00:00',	'2025-11-01 00:00:00',	45,	2),
(4,	'Maija\'s 21 Bday Party',	'2023-02-01 00:00:00',	'2023-02-02 00:00:00',	8,	3),
(7,	'Anoushka\'s Bday party',	'2023-05-23 00:00:00',	'2023-05-24 00:00:00',	13,	3),
(8,	'Blah',	'2023-10-24 00:00:00',	'2023-11-02 00:00:00',	3,	1),
(11,	'Prom Night Afterparty',	'2019-03-13 00:00:00',	'2019-03-14 00:00:00',	9,	1);

DROP TABLE IF EXISTS `manager_event`;
CREATE TABLE `manager_event` (
  `event` int NOT NULL,
  `manager` int NOT NULL,
  PRIMARY KEY (`event`,`manager`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `manager_event` (`event`, `manager`) VALUES
(1,	2),
(2,	1),
(10,	2),
(11,	1);

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `idrole` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`idrole`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `role` (`idrole`, `name`) VALUES
(1,	'admin'),
(2,	'event manager'),
(3,	'attendee');

DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `idsession` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `numberallowed` int NOT NULL,
  `event` int NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  PRIMARY KEY (`idsession`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `session` (`idsession`, `name`, `numberallowed`, `event`, `startdate`, `enddate`) VALUES
(2,	'Wedding Planning Session',	10,	2,	'2025-08-10 00:00:00',	'2025-08-10 00:00:00'),
(6,	'Rancho Farm Hike',	6,	1,	'2024-02-01 00:00:00',	'2024-02-01 00:00:00'),
(3,	'Cake Time',	4,	4,	'2023-02-01 00:00:00',	'2023-02-01 00:00:00'),
(5,	'Donuts at DonutWheel',	9,	11,	'2019-03-13 00:00:00',	'2019-03-13 00:00:00');

DROP TABLE IF EXISTS `super_admin`;
CREATE TABLE `super_admin` (
  `AdminID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `super_admin` (`AdminID`) VALUES
(2);

DROP TABLE IF EXISTS `venue`;
CREATE TABLE `venue` (
  `idvenue` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `capacity` int DEFAULT NULL,
  PRIMARY KEY (`idvenue`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

INSERT INTO `venue` (`idvenue`, `name`, `capacity`) VALUES
(1,	'Rancho San Antonio Open Space Preserve',	20),
(2,	'Santana Row Event Space',	45),
(3,	'Maija\'s House',	25);

-- 2023-11-01 18:18:40