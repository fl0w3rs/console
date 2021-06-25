-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июн 25 2021 г., 10:13
-- Версия сервера: 8.0.21
-- Версия PHP: 7.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `console`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bolo_peoples`
--

CREATE TABLE `bolo_peoples` (
  `id` int NOT NULL,
  `creator` int DEFAULT NULL,
  `target` varchar(256) DEFAULT NULL,
  `reason` varchar(256) DEFAULT NULL,
  `description` text,
  `last_location` varchar(256) DEFAULT NULL,
  `priority` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `bolo_vehicle`
--

CREATE TABLE `bolo_vehicle` (
  `id` int NOT NULL,
  `creator` int DEFAULT NULL,
  `target` varchar(256) DEFAULT NULL,
  `reason` varchar(256) DEFAULT NULL,
  `description` text,
  `last_location` varchar(256) DEFAULT NULL,
  `priority` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `casefiles`
--

CREATE TABLE `casefiles` (
  `id` int NOT NULL,
  `creator` int NOT NULL,
  `title` varchar(32) NOT NULL,
  `severity` int NOT NULL,
  `subject` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `evidence` text NOT NULL,
  `witnesses` text NOT NULL,
  `detectives` varchar(128) NOT NULL,
  `reasons` varchar(256) NOT NULL,
  `status` tinyint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `casefiles_comments`
--

CREATE TABLE `casefiles_comments` (
  `id` int NOT NULL,
  `creator` int NOT NULL,
  `text` text NOT NULL,
  `cf_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `characters`
--

CREATE TABLE `characters` (
  `id` int NOT NULL,
  `owner` int DEFAULT NULL,
  `name` varchar(65) DEFAULT NULL,
  `birth` varchar(16) NOT NULL,
  `sex` int NOT NULL,
  `race` int NOT NULL,
  `driving_license` int NOT NULL,
  `gun_license` int NOT NULL,
  `address` varchar(128) NOT NULL,
  `work` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `ssn` varchar(32) NOT NULL,
  `med_diseases` varchar(256) NOT NULL,
  `med_allergy` varchar(256) NOT NULL,
  `med_drugs` varchar(256) NOT NULL,
  `med_contact` varchar(256) NOT NULL,
  `med_height` varchar(65) NOT NULL,
  `med_weight` varchar(65) NOT NULL,
  `image` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `characters_vehicles`
--

CREATE TABLE `characters_vehicles` (
  `id` int NOT NULL,
  `owner` int NOT NULL,
  `char_id` int NOT NULL,
  `mark` varchar(64) NOT NULL,
  `model` varchar(64) NOT NULL,
  `plate` varchar(16) NOT NULL,
  `color` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `vin` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `characters_weapons`
--

CREATE TABLE `characters_weapons` (
  `id` int NOT NULL,
  `owner` int NOT NULL,
  `char_id` int NOT NULL,
  `model` varchar(64) NOT NULL,
  `serial` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `citations`
--

CREATE TABLE `citations` (
  `id` int NOT NULL,
  `char_id` int NOT NULL,
  `creator` int NOT NULL,
  `reasons` varchar(128) NOT NULL,
  `time` varchar(8) NOT NULL,
  `details` varchar(512) NOT NULL,
  `amount` int NOT NULL,
  `location` varchar(96) NOT NULL,
  `title` varchar(32) NOT NULL,
  `pay_time` int DEFAULT '0',
  `status` int DEFAULT '0',
  `created_time` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `departments`
--

CREATE TABLE `departments` (
  `id` int NOT NULL,
  `name` varchar(65) DEFAULT NULL,
  `groups` varchar(65) DEFAULT NULL,
  `training_field` varchar(65) DEFAULT NULL,
  `type` int DEFAULT NULL,
  `supervisor_group` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `report_blanks`
--

CREATE TABLE `report_blanks` (
  `id` int NOT NULL,
  `creator` int NOT NULL,
  `rb_from` varchar(32) NOT NULL,
  `rb_to` varchar(32) NOT NULL,
  `rank` varchar(32) NOT NULL,
  `time` varchar(16) NOT NULL,
  `description` text NOT NULL,
  `department` int NOT NULL,
  `additional` text NOT NULL,
  `status` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

CREATE TABLE `sessions` (
  `id` int NOT NULL,
  `hash` varchar(65) DEFAULT NULL,
  `useragent` varchar(255) DEFAULT NULL,
  `ip` varchar(65) DEFAULT NULL,
  `fid` int DEFAULT NULL,
  `active` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `s_key` varchar(65) DEFAULT NULL,
  `s_value` varchar(255) DEFAULT NULL,
  `s_api` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`id`, `s_key`, `s_value`, `s_api`) VALUES
(1, 'signal100', '0', 0),
(2, 'gamezone', 'San Andreas', 0),
(3, 'panic', '0', 0),
(4, 'priority', '0', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `situations`
--

CREATE TABLE `situations` (
  `id` int NOT NULL,
  `creator` int DEFAULT NULL,
  `street` varchar(65) DEFAULT NULL,
  `intersected_street` varchar(65) DEFAULT NULL,
  `block` varchar(65) DEFAULT NULL,
  `issuer_name` varchar(65) DEFAULT NULL,
  `description` text,
  `display_title` varchar(65) DEFAULT NULL,
  `display_type` varchar(65) DEFAULT NULL,
  `status` int DEFAULT '1',
  `code_4` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `situations_attached`
--

CREATE TABLE `situations_attached` (
  `id` int NOT NULL,
  `sit_id` int DEFAULT NULL,
  `unit_id` int DEFAULT NULL,
  `arrived` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `situations_logs`
--

CREATE TABLE `situations_logs` (
  `id` int NOT NULL,
  `sit_id` int DEFAULT NULL,
  `is_auto` int DEFAULT NULL,
  `creator_name` varchar(65) DEFAULT NULL,
  `message` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `units`
--

CREATE TABLE `units` (
  `id` int NOT NULL,
  `uid` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `dept` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `fid` int DEFAULT NULL,
  `department` int DEFAULT NULL,
  `steam_id` varchar(65) DEFAULT NULL,
  `muted_buttons` int DEFAULT '0',
  `notebook` text,
  `name` varchar(65) DEFAULT NULL,
  `wallpaper` varchar(128) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `warrants`
--

CREATE TABLE `warrants` (
  `id` int NOT NULL,
  `type` int NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `issued_by` int NOT NULL,
  `date` varchar(16) NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `additional` varchar(1024) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bolo_peoples`
--
ALTER TABLE `bolo_peoples`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `bolo_vehicle`
--
ALTER TABLE `bolo_vehicle`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `casefiles`
--
ALTER TABLE `casefiles`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `casefiles_comments`
--
ALTER TABLE `casefiles_comments`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `characters`
--
ALTER TABLE `characters`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `characters_vehicles`
--
ALTER TABLE `characters_vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vin` (`vin`);

--
-- Индексы таблицы `characters_weapons`
--
ALTER TABLE `characters_weapons`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `citations`
--
ALTER TABLE `citations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `report_blanks`
--
ALTER TABLE `report_blanks`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `s_key` (`s_key`);

--
-- Индексы таблицы `situations`
--
ALTER TABLE `situations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `situations_attached`
--
ALTER TABLE `situations_attached`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `situations_logs`
--
ALTER TABLE `situations_logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fid` (`fid`);

--
-- Индексы таблицы `warrants`
--
ALTER TABLE `warrants`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bolo_peoples`
--
ALTER TABLE `bolo_peoples`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `bolo_vehicle`
--
ALTER TABLE `bolo_vehicle`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `casefiles`
--
ALTER TABLE `casefiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `casefiles_comments`
--
ALTER TABLE `casefiles_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `characters`
--
ALTER TABLE `characters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `characters_vehicles`
--
ALTER TABLE `characters_vehicles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `characters_weapons`
--
ALTER TABLE `characters_weapons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `citations`
--
ALTER TABLE `citations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `report_blanks`
--
ALTER TABLE `report_blanks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `situations`
--
ALTER TABLE `situations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `situations_attached`
--
ALTER TABLE `situations_attached`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `situations_logs`
--
ALTER TABLE `situations_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `units`
--
ALTER TABLE `units`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `warrants`
--
ALTER TABLE `warrants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
