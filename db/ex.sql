-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 16 2022 г., 06:16
-- Версия сервера: 10.4.19-MariaDB
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `ex`
--

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `edit_time` timestamp NOT NULL,
  `md5` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `files`
--

INSERT INTO `files` (`id`, `size`, `edit_time`, `md5`, `settings`) VALUES
(1, 645704, '2019-09-11 07:58:48', '27794903d0aeefd1a2e145c5eb7afd3d', '{\"format\":\"image/jpeg\",\"resolution\":[3840,2160]}'),
(2, 16817272, '2019-02-16 04:31:04', '10342e221c366616663594ecdd5bb4dc', '{\"length\":182,\"format\":\"video/mp4\",\"resolution\":[640,360]}');

-- --------------------------------------------------------

--
-- Структура таблицы `fs`
--

CREATE TABLE `fs` (
  `id` int(11) NOT NULL,
  `domain` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `fs`
--

INSERT INTO `fs` (`id`, `domain`) VALUES
(1, '127.0.0.1');

-- --------------------------------------------------------

--
-- Структура таблицы `fs_files`
--

CREATE TABLE `fs_files` (
  `id` int(11) NOT NULL,
  `fs_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `fs_files`
--

INSERT INTO `fs_files` (`id`, `fs_id`, `file_id`) VALUES
(1, 1, 1),
(2, 1, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `objects`
--

CREATE TABLE `objects` (
  `id` int(11) NOT NULL,
  `title` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creation_time` timestamp NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `objects`
--

INSERT INTO `objects` (`id`, `title`, `description`, `creation_time`, `edit_time`, `settings`, `type_id`) VALUES
(1, 'Все', NULL, '2022-03-04 21:03:14', '2022-03-04 21:03:25', '', 1),
(2, 'system', NULL, '2022-03-04 21:05:32', '2022-03-04 21:07:16', '{\"notifications\":{\"friendship\":1,\"recommendations\":1,\"comments\":1,\"private_messages\":1},\"password_hash\":\"system\",\"mail\":\"system@ex.ru\"}', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `objects_bridges`
--

CREATE TABLE `objects_bridges` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `creation_time` timestamp NOT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`settings`)),
  `type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `objects_bridges`
--

INSERT INTO `objects_bridges` (`id`, `user_id`, `parent_id`, `child_id`, `creation_time`, `settings`, `type_id`) VALUES
(1, 2, 1, 2, '2022-04-08 01:07:02', '{\"access_level_id\":5,\"prefer_higher_access_level\":true,\"allow_invites\":true}', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `objects_files`
--

CREATE TABLE `objects_files` (
  `id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `objects_files`
--

INSERT INTO `objects_files` (`id`, `object_id`, `file_id`, `title`) VALUES
(1, 1, 1, 'example.jpg'),
(2, 1, 2, 'Krysiek & Marshall - Szara Piechota.mp4');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `fs`
--
ALTER TABLE `fs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `fs_files`
--
ALTER TABLE `fs_files`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `objects`
--
ALTER TABLE `objects`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `objects_bridges`
--
ALTER TABLE `objects_bridges`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `objects_files`
--
ALTER TABLE `objects_files`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `fs`
--
ALTER TABLE `fs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `fs_files`
--
ALTER TABLE `fs_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `objects`
--
ALTER TABLE `objects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `objects_bridges`
--
ALTER TABLE `objects_bridges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `objects_files`
--
ALTER TABLE `objects_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
