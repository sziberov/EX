/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `ex` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `ex`;

CREATE TABLE IF NOT EXISTS `aliases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `object_id` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `object_id` (`object_id`),
  CONSTRAINT `FK_aliases_objects` FOREIGN KEY (`object_id`) REFERENCES `objects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `size` int NOT NULL,
  `edit_time` timestamp NOT NULL,
  `md5` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `fs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `domain` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `fs_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fs_id` int NOT NULL,
  `file_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_fs_files_fs` (`fs_id`),
  KEY `FK_fs_files_files` (`file_id`),
  CONSTRAINT `FK_fs_files_files` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`),
  CONSTRAINT `FK_fs_files_fs` FOREIGN KEY (`fs_id`) REFERENCES `fs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_id` int NOT NULL,
  `to_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_links_objects` (`from_id`),
  KEY `FK_links_objects_2` (`to_id`),
  KEY `FK_links_objects_3` (`user_id`),
  CONSTRAINT `FK_links_objects` FOREIGN KEY (`from_id`) REFERENCES `objects` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_links_objects_2` FOREIGN KEY (`to_id`) REFERENCES `objects` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_links_objects_3` FOREIGN KEY (`user_id`) REFERENCES `objects` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_menu_items_objects` (`user_id`),
  CONSTRAINT `FK_menu_items_objects` FOREIGN KEY (`user_id`) REFERENCES `objects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `meta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_id` int NOT NULL,
  `width` int DEFAULT NULL,
  `height` int DEFAULT NULL,
  `length` int DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `file_id` (`file_id`),
  CONSTRAINT `FK_meta_files` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `objects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_id` int DEFAULT NULL,
  `creation_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `edit_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_objects_objects` FOREIGN KEY (`user_id`) REFERENCES `objects` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `objects_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_id` int NOT NULL,
  `file_id` int NOT NULL,
  `title` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `downloads_count` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_objects_files_objects` (`object_id`),
  KEY `FK_objects_files_files` (`file_id`),
  CONSTRAINT `FK_objects_files_files` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`),
  CONSTRAINT `FK_objects_files_objects` FOREIGN KEY (`object_id`) REFERENCES `objects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `ip_address` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_independent` bit(1) NOT NULL,
  `permanent` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_sessions_objects` (`user_id`) USING BTREE,
  CONSTRAINT `FK_sessions_objects` FOREIGN KEY (`user_id`) REFERENCES `objects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `link_id` int DEFAULT NULL,
  `object_id` int DEFAULT NULL,
  `key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `edit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`),
  KEY `link_id` (`link_id`),
  KEY `object_id` (`object_id`) USING BTREE,
  CONSTRAINT `FK_settings_links` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`),
  CONSTRAINT `FK_settings_objects` FOREIGN KEY (`object_id`) REFERENCES `objects` (`id`),
  CONSTRAINT `FK_settings_objects_2` FOREIGN KEY (`user_id`) REFERENCES `objects` (`id`),
  CONSTRAINT `CC1` CHECK ((((case when (`link_id` is null) then 0 else 1 end) + (case when (`object_id` is null) then 0 else 1 end)) = 1))
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `visits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_id` int NOT NULL,
  `ip_address` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer_url` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_visits_objects` (`object_id`),
  CONSTRAINT `FK_visits_objects` FOREIGN KEY (`object_id`) REFERENCES `objects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=375 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
