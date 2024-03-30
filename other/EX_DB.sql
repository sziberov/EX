-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               8.0.30 - MySQL Community Server - GPL
-- Операционная система:         Win64
-- HeidiSQL Версия:              12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Дамп структуры базы данных ex
CREATE DATABASE IF NOT EXISTS `ex` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `ex`;

-- Дамп структуры для таблица ex.aliases
CREATE TABLE IF NOT EXISTS `aliases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `object_id` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `object_id` (`object_id`),
  CONSTRAINT `FK_aliases_objects` FOREIGN KEY (`object_id`) REFERENCES `objects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Дамп данных таблицы ex.aliases: ~0 rows (приблизительно)

-- Дамп структуры для таблица ex.files
CREATE TABLE IF NOT EXISTS `files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `size` int NOT NULL,
  `edit_time` timestamp NOT NULL,
  `md5` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы ex.files: ~4 rows (приблизительно)
INSERT INTO `files` (`id`, `size`, `edit_time`, `md5`) VALUES
	(1, 645704, '2019-09-11 07:58:48', '27794903d0aeefd1a2e145c5eb7afd3d'),
	(2, 16817272, '2019-02-16 04:31:04', '10342e221c366616663594ecdd5bb4dc'),
	(3, 6690, '2022-04-12 03:08:40', '8088add90fdc0af27eff6f755c2a5d6e'),
	(4, 291886, '2019-03-30 06:00:02', 'ed54356dd5c080deef50b77c4631718d');

-- Дамп структуры для таблица ex.fs
CREATE TABLE IF NOT EXISTS `fs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `domain` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы ex.fs: ~0 rows (приблизительно)
INSERT INTO `fs` (`id`, `domain`) VALUES
	(1, '127.0.0.1'),
	(2, '127.0.0.1');

-- Дамп структуры для таблица ex.fs_files
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

-- Дамп данных таблицы ex.fs_files: ~6 rows (приблизительно)
INSERT INTO `fs_files` (`id`, `fs_id`, `file_id`) VALUES
	(1, 1, 1),
	(2, 1, 2),
	(3, 1, 3),
	(4, 2, 3),
	(5, 2, 2),
	(6, 2, 4);

-- Дамп структуры для таблица ex.links
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
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы ex.links: ~41 rows (приблизительно)
INSERT INTO `links` (`id`, `from_id`, `to_id`, `user_id`, `creation_time`, `type_id`) VALUES
	(1, 2, 1, 2, '2022-04-08 01:07:02', 1),
	(2, 2, 3, 2, '2023-11-21 20:09:41', 1),
	(3, 58, 4, 2, '2023-11-19 18:08:31', 5),
	(4, 5, 4, 2, '2023-11-21 15:17:33', 4),
	(5, 2, 7, 7, '2024-03-17 12:02:04', 2),
	(7, 5, NULL, 7, '2024-03-20 01:52:17', 7),
	(17, 52, 1, 2, '2024-03-21 02:28:46', 1),
	(18, 52, 53, 52, '2024-03-21 02:28:46', 1),
	(19, 6, 2, 2, '2024-03-21 02:56:15', 4),
	(20, 53, 2, 2, '2024-03-21 03:00:19', 4),
	(22, 54, 7, 7, '2024-03-23 00:19:29', 4),
	(23, 54, 5, 7, '2024-03-23 01:01:16', 4),
	(24, 1, 6, 2, '2024-03-23 02:04:16', 1),
	(25, 3, 5, 2, '2024-03-23 02:28:35', 1),
	(26, 1, 54, 7, '2024-03-23 02:49:49', 1),
	(27, 7, 1, 7, '2024-03-24 20:26:55', 1),
	(28, 8, 54, 7, '2024-03-24 20:47:39', 1),
	(29, 7, 8, 7, '2024-03-24 20:48:30', 1),
	(30, 8, 8, 7, '2024-03-24 20:49:49', 1),
	(31, 1, 4, 2, '2024-03-24 21:47:57', 1),
	(32, 6, 7, 7, '2024-03-26 00:26:36', 3),
	(33, 6, 7, 7, '2024-03-26 00:26:36', 3),
	(34, 6, 7, 7, '2024-03-26 00:26:36', 3),
	(35, 6, 7, 7, '2024-03-26 00:26:36', 3),
	(36, 6, 7, 7, '2024-03-26 00:26:36', 3),
	(37, 1, 2, 2, '2024-03-28 02:19:55', 1),
	(38, 1, 56, 2, '2024-03-28 02:55:16', 1),
	(40, 1, 57, 2, '2024-03-28 02:55:16', 1),
	(41, 57, 56, 56, '2024-03-28 02:55:16', 1),
	(42, 56, 57, 56, '2024-03-28 02:55:16', 1),
	(43, 8, 7, 7, '2024-03-24 20:48:30', 1),
	(44, 59, 58, 2, '2024-03-29 13:25:07', 5),
	(45, 64, 58, 2, '2024-03-29 13:25:07', 5),
	(46, 60, 59, 2, '2024-03-29 13:25:07', 5),
	(47, 61, 59, 2, '2024-03-29 13:25:07', 5),
	(48, 62, 59, 2, '2024-03-29 13:25:07', 5),
	(49, 63, 59, 2, '2024-03-29 13:25:07', 5),
	(50, 65, 64, 2, '2024-03-29 13:25:07', 5),
	(51, 65, 4, 2, '2024-03-29 13:25:07', 5),
	(52, 6, 60, 2, '2024-03-29 17:39:18', 5),
	(53, 57, 57, 2, '2024-03-30 17:56:00', 1);

-- Дамп структуры для таблица ex.menu_items
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_menu_items_objects` (`user_id`),
  CONSTRAINT `FK_menu_items_objects` FOREIGN KEY (`user_id`) REFERENCES `objects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы ex.menu_items: ~0 rows (приблизительно)

-- Дамп структуры для таблица ex.objects
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

-- Дамп данных таблицы ex.objects: ~19 rows (приблизительно)
INSERT INTO `objects` (`id`, `title`, `description`, `user_id`, `creation_time`, `edit_time`, `type_id`) VALUES
	(1, 'Все', 'Общая группа для зарегистрированных пользователей и гостей.', NULL, '2022-03-04 21:03:14', '2024-03-27 23:47:42', 1),
	(2, 'Система', '', NULL, '2022-03-04 21:03:14', '2024-03-28 14:06:29', 2),
	(3, 'Group_system', NULL, 2, '2023-11-21 18:42:34', '2024-03-21 00:42:11', 1),
	(4, 'Общие разделы', NULL, 2, '2023-11-09 17:05:37', '2023-11-21 19:55:54', 3),
	(5, '', 'Идейные соображения высшего порядка, а также постоянное информационно-пропагандистское обеспечение нашей деятельности способствует подготовки и реализации дальнейших направлений развития. Идейные соображения высшего порядка, а также начало повседневной работы по формированию позиции представляет собой интересный эксперимент проверки систем массового участия. Равным образом дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации дальнейших направлений развития. Равным образом дальнейшее развитие различных форм деятельности обеспечивает широкому кругу (специалистов) участие в формировании направлений прогрессивного развития. С другой стороны дальнейшее развитие различных форм деятельности влечет за собой процесс внедрения и модернизации соответствующий условий активизации. Равным образом сложившаяся структура организации влечет за собой процесс внедрения и модернизации направлений прогрессивного развития.\r\n\r\nТоварищи! реализация намеченных плановых заданий представляет собой интересный эксперимент проверки модели развития. Повседневная практика показывает, что начало повседневной работы по формированию позиции требуют от нас анализа направлений прогрессивного развития. Не следует, однако забывать, что постоянный количественный рост и сфера нашей активности представляет собой интересный эксперимент проверки форм развития.', 2, '2022-03-04 21:05:32', '2024-03-22 22:15:08', 3),
	(6, 'EX', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean viverra vulputate maximus. Nam sed mi non velit dignissim rutrum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus et risus leo. Ut ac orci ipsum. Fusce tristique finibus elit, a consequat ante molestie vel. Praesent quis tincidunt orci. Donec at ullamcorper nisl. Sed vitae lectus non urna tincidunt ultrices. Pellentesque non justo magna. Quisque vehicula felis ante, at faucibus ante luctus at.\r\n\r\nPhasellus a neque nibh. Integer ultricies imperdiet lobortis. Suspendisse auctor leo ante, ac dapibus risus gravida in. Vivamus elementum sagittis augue at aliquam. Pellentesque finibus diam lorem, eget pellentesque elit accumsan eu. Quisque augue dui, vehicula in dui et, dictum fermentum urna. Vivamus euismod odio a nibh tempor facilisis. Aliquam pretium dolor quis consectetur vehicula. Vivamus fringilla et nisi auctor volutpat. Cras a mi vestibulum, hendrerit mauris vitae, molestie metus.\r\n\r\nSuspendisse sagittis augue vulputate consectetur posuere. Etiam faucibus arcu vel libero hendrerit, vel sollicitudin risus sollicitudin. Vivamus posuere sagittis egestas. Vivamus felis orci, facilisis non eleifend id, vehicula vitae ipsum. Quisque vulputate porta nulla vel vestibulum. Aenean tristique eget enim quis porttitor. Pellentesque ultrices, sapien a ornare elementum, dui tortor commodo est, a finibus magna ex ac mi. Nam vulputate nec lorem sed posuere. Maecenas ac auctor nibh. Praesent non nulla placerat, rutrum enim nec, pulvinar nulla. Vivamus vestibulum feugiat nulla, congue facilisis ex facilisis pharetra. Vestibulum rhoncus tincidunt justo nec suscipit. Maecenas vehicula mi a lectus cursus, a egestas lacus venenatis. Nam ullamcorper vel arcu a ullamcorper.\r\n\r\nMaecenas laoreet ut leo ac egestas. Fusce finibus suscipit cursus. Vivamus ac ligula vel sapien ornare bibendum ut quis ex. Nulla facilisi. Donec rutrum viverra ultricies. Fusce erat turpis, placerat vel tempus sed, iaculis sed libero. Maecenas ullamcorper tortor nibh, in aliquet diam suscipit sed. Phasellus posuere dolor placerat ultricies suscipit. Quisque tincidunt nunc ac nibh vestibulum, ac pulvinar mauris tempor. Sed nec molestie lorem, sit amet eleifend neque. Nam porta neque vitae interdum faucibus. Vivamus eget consequat nibh. In tempor magna a sagittis congue. Morbi maximus ipsum nec nulla aliquet pellentesque. Ut euismod metus ultrices consectetur mollis. Fusce mattis mattis nisi, id molestie nibh pharetra eu.\r\n\r\nDuis id mi eget leo facilisis molestie in nec turpis. Vivamus ac posuere erat. Morbi feugiat sem vehicula justo dignissim pulvinar. Morbi ut mauris risus. Donec quis ipsum vitae ipsum porta molestie. Nam quis dui id tortor facilisis mollis. Pellentesque vel tincidunt nunc. In id mi non justo cursus consectetur. Duis fermentum tortor sit amet eleifend ultricies. Integer suscipit justo vitae ex consequat, et volutpat magna tempor. Etiam dictum laoreet venenatis. Donec non urna magna.\r\n\r\nПример текста с [b]жирным[/b] и [color=red]красным[/color] шрифтом, а также ссылкой [url=https://example.com]на сайт[/url] и [lang=ru|en|ua]текстом на разных языках[/lang].', 2, '2023-11-19 18:03:56', '2024-03-22 22:15:17', 3),
	(7, '', '', NULL, '2022-03-04 21:03:14', '2024-03-28 14:06:23', 2),
	(8, 'Group_DIES', NULL, 7, '2024-03-17 18:10:00', '2024-03-17 18:10:00', 1),
	(52, '', NULL, NULL, '2024-03-21 02:28:46', '2024-03-28 14:07:02', 2),
	(53, 'Group_asdasd', NULL, 52, '2024-03-21 02:28:46', '2024-03-21 02:28:46', 1),
	(54, 'Настройки', 'В целях персонализации и расширения функционала в рамках уже существующей структуры, объекты, межобъектные связи и файлы имеют настройки. Некоторые из них являются рекомендуемыми или обязательными и назначаются сразу в момент создания, некоторые могут быть отредактированы только привилегированными пользователями.\r\n\r\nНастройки объектов по типам:\r\n- 2 : Пользователь [\r\n	login (string) - Логин\r\n	password_hash (string) - Хэш пароля\r\n	email (string) - Почта\r\n	hide_from_search (boolean) - Скрывать из поисковой выдачи (включая принадлежащие объекты)\r\n	use_personal_menu (boolean) - Использовать персональное меню\r\n	group_id (integer) - Основная группа (номер объекта)\r\n	editor_id (integer) - Редактор (номер файла)\r\n	avatar_id (integer) - Аватар по умолчанию (номер объекта)\r\n	max_upload_size (integer) - Максимальный размер загрузки\r\n	notify_friendship (boolean) - Уведомления о дружбе\r\n	notify_recommendations (boolean) - Уведомления о рекомендациях\r\n	notify_comments (boolean) - Уведомления о комментариях\r\n	notify_private_messages (boolean) - Уведомления о личных сообщениях\r\n	allow_any_upload_size (boolean) - Разрешить любой размер загрузки\r\n	allow_advanced_control (boolean) - Разрешить профессиональное управление\r\n	allow_max_access_ignoring_groups (boolean) - Разрешить максимальный доступ, игнорируя группы\r\n]\r\n- 3 : Простой [\r\n	hide_from_search (boolean) - Скрывать из поисковой выдачи\r\n	hide_default_referer (boolean) - Скрывать реферер по умолчанию\r\n	hide_title (boolean) - Скрывать название\r\n	hide_author_and_times (boolean) - Скрывать автора и времена\r\n	avatar_id (integer) - Аватар (номер объекта)\r\n	poster_id (integer) - Постер (номер файла)\r\n	display_search_bar (boolean) - Отображать строку поиска по включениям\r\n	display_amount (integer) - Количество отображаемых включений (на страницу, не менее 1)\r\n	display_mode_id (integer) - Режим отображения включений (0 - ячейками, 1 - списком)\r\n	sort_mode_id (integer) - Режим сортировки (0 - по времени включения, 1 - по времени редактирования, 2 - по времени создания, 3 - по названию, 4 - по популярности, 5 - по посещяемости, 6 - по обсуждаемости, 7 - по рекомендуемости)\r\n	hide_file_list (boolean) - Скрывать список файлов\r\n	deny_nonbookmark_inclusion (boolean) - Запретить включение извне закладок\r\n	deny_claims (boolean) - Запретить обжалование\r\n	allow_template_execution (boolean) - Разрешить выполнение шаблона\r\n	awaiting_save (boolean) - Ожидает сохранения (находится в черновиках)\r\n]\r\n\r\nНастройки межобъектных связей по типам:\r\n- 1 : Доступ [\r\n	access_level_id (integer)\r\n	prefer_higher_access_level (boolean)\r\n	allow_invites (boolean)\r\n	awaiting_accept (boolean)\r\n]\r\n- 6 : Шаблон [\r\n	display_mode_id (integer)\r\n]\r\n- 11 : Уведомление [\r\n	event_id (string)\r\n]\r\n\r\nНастройки файлов:\r\n[\r\n	format (string) - MIME-тип\r\n	length (integer) - Длина в секундах\r\n	width (integer) - Ширина в пикселях\r\n	height (integer) - Высота в пикселях\r\n]', 7, '2024-03-23 00:12:59', '2024-03-23 00:18:59', 3),
	(55, NULL, 'Тест', NULL, '2024-03-28 00:15:59', '2024-03-28 02:02:52', 4),
	(56, 'User_test', NULL, NULL, '2024-03-28 02:55:16', '2024-03-28 03:38:41', 2),
	(57, 'Group_test', NULL, 56, '2024-03-28 02:55:16', '2024-03-28 02:55:16', 1),
	(58, 'Тестовый комментарий', 'Тестовое содержание тестового комментария', 7, '2024-03-29 13:37:44', '2024-03-29 13:37:44', 3),
	(59, 'Второй тестовый комментарий', 'Второе тестовое содержание второго тестового комментария', 2, '2024-03-29 13:37:44', '2024-03-29 14:15:57', 3),
	(60, 'Третий тестовый комментарий', 'Третье содержание третьего тестового комментария', 56, '2024-03-29 13:37:44', '2024-03-29 14:56:38', 3),
	(61, '4', '4', 56, '2024-03-29 13:37:44', '2024-03-29 14:56:38', 3),
	(62, '5', '5', 56, '2024-03-29 13:37:44', '2024-03-29 14:56:38', 3),
	(63, '6', '6', 56, '2024-03-29 13:37:44', '2024-03-29 14:56:38', 3),
	(64, '7', '7', 56, '2024-03-29 13:37:44', '2024-03-29 15:56:34', 3),
	(65, '8', '8', 56, '2024-03-29 13:37:44', '2024-03-29 15:56:34', 3);

-- Дамп структуры для таблица ex.objects_files
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

-- Дамп данных таблицы ex.objects_files: ~7 rows (приблизительно)
INSERT INTO `objects_files` (`id`, `object_id`, `file_id`, `title`, `downloads_count`) VALUES
	(1, 4, 1, 'example.jpg', 0),
	(2, 4, 2, 'Krysiek & Marshall - Szara Piechota.mp4', 0),
	(3, 5, 1, 'example.jpg', 0),
	(4, 6, 3, 'ex.svg', 0),
	(5, 4, 4, '3546.jpg', 0),
	(6, 57, 4, '3546.jpg', 0),
	(7, 55, 4, '3546.jpg', 0);

-- Дамп структуры для таблица ex.sessions
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

-- Дамп данных таблицы ex.sessions: ~0 rows (приблизительно)

-- Дамп структуры для таблица ex.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_id` int DEFAULT NULL,
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
  KEY `file_id` (`file_id`) USING BTREE,
  CONSTRAINT `FK_settings_files` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`),
  CONSTRAINT `FK_settings_links` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`),
  CONSTRAINT `FK_settings_objects` FOREIGN KEY (`object_id`) REFERENCES `objects` (`id`),
  CONSTRAINT `FK_settings_objects_2` FOREIGN KEY (`user_id`) REFERENCES `objects` (`id`),
  CONSTRAINT `CC1` CHECK (((((case when (`file_id` is null) then 0 else 1 end) + (case when (`link_id` is null) then 0 else 1 end)) + (case when (`object_id` is null) then 0 else 1 end)) = 1))
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Дамп данных таблицы ex.settings: ~65 rows (приблизительно)
INSERT INTO `settings` (`id`, `file_id`, `link_id`, `object_id`, `key`, `value`, `user_id`, `edit_time`) VALUES
	(1, NULL, NULL, 2, 'login', 'system', 2, '2024-03-15 20:38:01'),
	(2, NULL, NULL, 2, 'password_hash', '$2y$10$oXLiWSZxM37CkzVOD0QHGumHpUNxYz70SS786sxHhWDsH5dfFVmZe', 2, '2024-03-15 20:38:01'),
	(3, NULL, NULL, 2, 'hide_from_search', 'true', 2, '2024-03-15 20:38:01'),
	(4, NULL, NULL, 2, 'allow_any_upload_size', 'true', 2, '2024-03-15 20:38:01'),
	(5, NULL, NULL, 2, 'allow_advanced_control', 'true', 2, '2024-03-15 20:38:01'),
	(6, NULL, NULL, 2, 'allow_max_access_ignoring_groups', 'true', 2, '2024-03-15 20:38:01'),
	(7, NULL, NULL, 4, 'hide_default_referer', 'true', 2, '2024-03-15 20:38:01'),
	(8, NULL, NULL, 4, 'hide_author_and_times', 'true', 2, '2024-03-15 20:38:01'),
	(9, NULL, NULL, 4, 'hide_file_list', 'true', 2, '2024-03-15 20:38:01'),
	(10, NULL, NULL, 4, 'deny_nonbookmark_inclusion', 'true', 2, '2024-03-15 20:38:01'),
	(11, NULL, NULL, 4, 'deny_claims', 'true', 2, '2024-03-15 20:38:01'),
	(12, NULL, NULL, 5, 'alias', 'test', 2, '2024-03-15 20:38:01'),
	(13, NULL, NULL, 5, 'poster_id', '5', 2, '2024-03-15 20:38:01'),
	(14, NULL, NULL, 5, 'display_search_bar', 'true', 2, '2024-03-15 20:38:01'),
	(15, NULL, 1, NULL, 'allow_invites', 'true', 2, '2024-03-15 20:38:01'),
	(16, NULL, 1, NULL, 'access_level_id', '5', 2, '2024-03-15 20:38:01'),
	(17, NULL, 1, NULL, 'allow_higher_access_preference', 'true', 2, '2024-03-15 20:38:01'),
	(18, NULL, NULL, 2, 'group_id', '3', 2, '2024-03-15 20:38:01'),
	(19, 1, NULL, NULL, 'format', 'image/jpeg', 2, '2024-03-15 20:38:01'),
	(20, 1, NULL, NULL, 'width', '3840', 2, '2024-03-15 20:38:01'),
	(21, 1, NULL, NULL, 'height', '2160', 2, '2024-03-15 20:38:01'),
	(22, 2, NULL, NULL, 'format', 'video/mp4', 2, '2024-03-15 20:38:01'),
	(23, 2, NULL, NULL, 'length', '182', 2, '2024-03-15 20:38:01'),
	(24, 2, NULL, NULL, 'width', '640', 2, '2024-03-15 20:38:01'),
	(25, 2, NULL, NULL, 'height', '360', 2, '2024-03-15 20:38:01'),
	(26, NULL, 2, NULL, 'access_level_id', '5', 2, '2024-03-15 20:38:01'),
	(27, NULL, 2, NULL, 'allow_invites', 'true', 2, '2024-03-15 20:38:01'),
	(28, NULL, 2, NULL, 'allow_higher_access_preference', 'true', 2, '2024-03-15 20:38:01'),
	(31, NULL, NULL, 4, 'deny_nonbookmark_inclusion', 'false', 2, '2024-03-15 20:38:01'),
	(32, NULL, NULL, 2, 'avatar_id', '5', 2, '2024-03-17 10:05:57'),
	(33, NULL, NULL, 7, 'login', 'DIES', 7, '2024-03-15 20:38:01'),
	(34, NULL, NULL, 7, 'password_hash', '$2y$10$iZJXj79dBwLA5tQrn8/JY.Z7pRoToQyJAHQlpqAQkH3g0eA0PM28i', 7, '2024-03-15 20:38:01'),
	(74, NULL, NULL, 52, 'login', 'asdasd', 52, '2024-03-21 02:28:46'),
	(75, NULL, NULL, 52, 'password_hash', '$2y$10$er08Io72nbMs7wNK92d3n.c3hsovItHOWRiH8UrzpyRjNHhKDQFPi', 52, '2024-03-21 02:28:46'),
	(76, NULL, NULL, 52, 'email', 'sziberov@gmail.com', 52, '2024-03-21 02:28:46'),
	(77, NULL, 17, NULL, 'access_level_id', '2', 52, '2024-03-21 02:28:46'),
	(78, NULL, 18, NULL, 'allow_invites', 'true', 52, '2024-03-21 02:28:46'),
	(79, NULL, 18, NULL, 'access_level_id', '5', 52, '2024-03-21 02:28:46'),
	(80, NULL, 18, NULL, 'allow_higher_access_preference', 'true', 52, '2024-03-21 02:28:46'),
	(81, NULL, NULL, 6, 'poster_id', '5', 2, '2024-03-22 20:51:17'),
	(82, NULL, 24, NULL, 'access_level_id', '2', 2, '2024-03-23 02:04:44'),
	(83, NULL, 25, NULL, 'access_level_id', '5', 2, '2024-03-23 02:29:03'),
	(84, NULL, 26, NULL, 'access_level_id', '2', 7, '2024-03-23 02:50:16'),
	(85, 3, NULL, NULL, 'format', 'image/svg+xml', 2, '2024-03-24 08:38:48'),
	(86, NULL, NULL, 54, 'hide_default_referrer', 'false', 7, '2024-03-24 20:03:50'),
	(87, NULL, 27, NULL, 'access_level_id', '2', 2, '2024-03-24 20:27:25'),
	(88, NULL, 28, NULL, 'access_level_id', '5', 7, '2024-03-24 20:47:58'),
	(89, NULL, 29, NULL, 'access_level_id', '5', 7, '2024-03-24 20:48:49'),
	(90, NULL, 30, NULL, 'access_level_id', '4', 7, '2024-03-24 20:50:10'),
	(91, 4, NULL, NULL, 'format', 'image/jpeg', 7, '2024-03-24 21:44:40'),
	(92, 4, NULL, NULL, 'width', '1280', 7, '2024-03-24 21:44:57'),
	(93, 4, NULL, NULL, 'height', '804', 7, '2024-03-24 21:45:06'),
	(94, NULL, 31, NULL, 'access_level_id', '2', 2, '2024-03-24 21:48:15'),
	(95, NULL, 29, NULL, 'allow_invites', 'true', 7, '2024-03-24 22:18:29'),
	(96, NULL, 29, NULL, 'allow_members_list_view', 'true', 7, '2024-03-24 22:18:55'),
	(97, NULL, 29, NULL, 'allow_higher_access_preference', 'true', 7, '2024-03-24 22:18:55'),
	(98, NULL, 1, NULL, 'allow_members_list_view', 'true', 2, '2024-03-25 22:13:43'),
	(99, NULL, 2, NULL, 'allow_members_list_view', 'true', 2, '2024-03-25 22:13:55'),
	(100, NULL, 37, NULL, 'access_level_id', '5', 2, '2024-03-15 20:38:01'),
	(101, NULL, NULL, 56, 'login', 'test', 56, '2024-03-28 02:55:16'),
	(102, NULL, NULL, 56, 'password_hash', '$2y$10$6aeHSy1l4xrtWQGQSc2loOzAaRqrt0slrXLYLIkg2kq6PMUeKZ43e', 56, '2024-03-28 02:55:16'),
	(103, NULL, NULL, 56, 'email', '123@gmail.com', 56, '2024-03-28 02:55:16'),
	(104, NULL, 38, NULL, 'access_level_id', '2', 56, '2024-03-28 02:55:16'),
	(106, NULL, 40, NULL, 'access_level_id', '2', 56, '2024-03-28 02:55:16'),
	(107, NULL, 41, NULL, 'access_level_id', '4', 56, '2024-03-28 02:55:16'),
	(108, NULL, 42, NULL, 'access_level_id', '5', 56, '2024-03-28 02:55:16'),
	(109, NULL, 42, NULL, 'allow_invites', 'true', 56, '2024-03-28 02:55:16'),
	(110, NULL, 42, NULL, 'allow_members_list_view', 'true', 56, '2024-03-28 02:55:16'),
	(111, NULL, 42, NULL, 'allow_higher_access_preference', 'true', 56, '2024-03-28 02:55:16'),
	(112, NULL, 43, NULL, 'access_level_id', '4', 7, '2024-03-28 02:55:16'),
	(113, NULL, 53, NULL, 'access_level_id', '2', 56, '2024-03-28 02:55:16');

-- Дамп структуры для таблица ex.visits
CREATE TABLE IF NOT EXISTS `visits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_id` int NOT NULL,
  `ip_address` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer_url` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_visits_objects` (`object_id`),
  CONSTRAINT `FK_visits_objects` FOREIGN KEY (`object_id`) REFERENCES `objects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы ex.visits: ~0 rows (приблизительно)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
