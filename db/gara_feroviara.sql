-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 05 2025 г., 22:11
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `gara_feroviara`
--

-- --------------------------------------------------------

--
-- Структура таблицы `authentication_logs`
--

CREATE TABLE `authentication_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `actiune` varchar(50) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `data_actiune` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `authentication_logs`
--

INSERT INTO `authentication_logs` (`log_id`, `user_id`, `actiune`, `ip_address`, `data_actiune`) VALUES
(1, 1, 'Autentificare', '::1', '2025-05-05 23:01:24'),
(2, 1, 'Autentificare', '::1', '2025-05-05 23:02:42'),
(3, 1, 'Autentificare', '::1', '2025-05-05 23:08:38');

-- --------------------------------------------------------

--
-- Структура таблицы `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `train_id` int(11) NOT NULL,
  `mesaj` text NOT NULL,
  `data_emitere` datetime DEFAULT current_timestamp(),
  `tip_notificare` enum('întârziere','modificare traseu','anulare') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `notifications`
--

INSERT INTO `notifications` (`notification_id`, `train_id`, `mesaj`, `data_emitere`, `tip_notificare`) VALUES
(1, 1, 'Trenul IR 1600 va avea o întârziere de aproximativ 15 minute din cauza condițiilor meteorologice.', '2025-05-05 22:16:59', 'întârziere'),
(2, 3, 'Trenul RE 7890 și-a modificat traseul și nu va mai opri în stația Snagov.', '2025-05-05 22:16:59', 'modificare traseu'),
(3, 5, 'Trenul EX 5678 circulă conform programului, fără întârzieri.', '2025-05-05 22:16:59', 'întârziere');

-- --------------------------------------------------------

--
-- Структура таблицы `routes`
--

CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `start_station` varchar(50) NOT NULL,
  `end_station` varchar(50) NOT NULL,
  `distanta` int(11) NOT NULL,
  `tip_ruta` enum('directă','cu opriri') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `routes`
--

INSERT INTO `routes` (`route_id`, `start_station`, `end_station`, `distanta`, `tip_ruta`) VALUES
(1, 'București Nord', 'Brașov', 166, 'directă'),
(2, 'București Nord', 'Cluj Napoca', 445, 'cu opriri'),
(3, 'București Nord', 'Constanța', 225, 'directă'),
(4, 'București Nord', 'Timișoara', 562, 'cu opriri'),
(5, 'București Nord', 'Ploiești', 59, 'directă');

-- --------------------------------------------------------

--
-- Структура таблицы `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `nume_serviciu` varchar(100) NOT NULL,
  `descriere` text NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `disponibilitate` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `services`
--

INSERT INTO `services` (`service_id`, `nume_serviciu`, `descriere`, `cost`, `disponibilitate`) VALUES
(1, 'Depozitare bagaje', 'Serviciu de depozitare bagaje în gara', 10.00, 1),
(2, 'WiFi gratuit', 'Acces gratuit la rețeaua WiFi în toată gara', NULL, 1),
(3, 'Sala de așteptare Business', 'Sala de așteptare premium cu facilități', 25.00, 1),
(4, 'Închirieri auto', 'Punct de închirieri auto în gară', NULL, 1),
(5, 'Asistență turistică', 'Informații și bilete pentru atracții turistice', NULL, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_id` int(11) NOT NULL,
  `nume_setare` varchar(100) NOT NULL,
  `valoare` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `site_settings`
--

INSERT INTO `site_settings` (`setting_id`, `nume_setare`, `valoare`) VALUES
(1, 'nume_gara', 'Gara Centrală București'),
(2, 'adresa_gara', 'Piața Gării de Nord, București'),
(3, 'telefon_gara', '+40212345678'),
(4, 'email_gara', 'contact@gara-bucuresti.ro');

-- --------------------------------------------------------

--
-- Структура таблицы `station_map`
--

CREATE TABLE `station_map` (
  `map_id` int(11) NOT NULL,
  `numar_peron` varchar(10) NOT NULL,
  `detalii` text DEFAULT NULL,
  `latitudine` decimal(10,8) DEFAULT NULL,
  `longitudine` decimal(11,8) DEFAULT NULL,
  `tip_locatie` enum('peron','ghiseu','toaleta','restaurant') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `station_map`
--

INSERT INTO `station_map` (`map_id`, `numar_peron`, `detalii`, `latitudine`, `longitudine`, `tip_locatie`) VALUES
(1, 'Peron 1', 'Trenuri rapide și intercity', 44.44692600, 26.07540000, 'peron'),
(2, 'Peron 2', 'Trenuri regionale și de proximitate', 44.44712000, 26.07485000, 'peron'),
(3, 'Ghișeu 1', 'Vânzare bilete internaționale', 44.44680000, 26.07620000, 'ghiseu'),
(4, 'Ghișeu 2', 'Vânzare bilete interne', 44.44675000, 26.07650000, 'ghiseu'),
(5, 'Restaurant', 'Restaurant cu bucătărie tradițională', 44.44730000, 26.07590000, 'restaurant'),
(6, 'Toalete', 'Toalete publice la parter', 44.44695000, 26.07600000, 'toaleta');

-- --------------------------------------------------------

--
-- Структура таблицы `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `train_id` int(11) NOT NULL,
  `clasa` enum('1','2','3') NOT NULL,
  `pret` decimal(10,2) NOT NULL,
  `data_achizitie` datetime DEFAULT current_timestamp(),
  `status` enum('achiziționat','anulat') DEFAULT 'achiziționat'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `trains`
--

CREATE TABLE `trains` (
  `train_id` int(11) NOT NULL,
  `numar_tren` varchar(10) NOT NULL,
  `tip_tren` enum('rapid','intercity','regio','express') NOT NULL,
  `plecare` varchar(50) NOT NULL,
  `sosire` varchar(50) NOT NULL,
  `rute` text NOT NULL,
  `durata_traseu` int(11) NOT NULL,
  `status` enum('în circulație','întârziat','anulat') DEFAULT 'în circulație'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `trains`
--

INSERT INTO `trains` (`train_id`, `numar_tren`, `tip_tren`, `plecare`, `sosire`, `rute`, `durata_traseu`, `status`) VALUES
(1, 'IR 1600', 'intercity', '06:00', '12:30', 'București Nord - Brașov - Sighișoara - Cluj Napoca', 390, 'în circulație'),
(2, 'R 3456', 'rapid', '07:15', '10:45', 'București Nord - Ploiești - Sinaia - Brașov', 210, 'în circulație'),
(3, 'RE 7890', 'regio', '08:30', '09:45', 'București Nord - Otopeni - Snagov - Ploiești Vest', 75, 'în circulație'),
(4, 'IC 1234', 'intercity', '09:00', '14:20', 'București Nord - Pitești - Craiova - Timișoara', 320, 'în circulație'),
(5, 'EX 5678', 'express', '10:15', '11:30', 'București Nord - Constanța', 75, 'în circulație');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `nume` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `parola` varchar(255) NOT NULL,
  `telefon` varchar(15) DEFAULT NULL,
  `data_inregistrare` datetime DEFAULT current_timestamp(),
  `rol` enum('utilizator','administrator') DEFAULT 'utilizator'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `nume`, `email`, `parola`, `telefon`, `data_inregistrare`, `rol`) VALUES
(1, 'Admin', 'admin@gara.ro', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2025-05-05 22:16:59', 'administrator');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `authentication_logs`
--
ALTER TABLE `authentication_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `train_id` (`train_id`);

--
-- Индексы таблицы `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`);

--
-- Индексы таблицы `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Индексы таблицы `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Индексы таблицы `station_map`
--
ALTER TABLE `station_map`
  ADD PRIMARY KEY (`map_id`);

--
-- Индексы таблицы `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `train_id` (`train_id`);

--
-- Индексы таблицы `trains`
--
ALTER TABLE `trains`
  ADD PRIMARY KEY (`train_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `authentication_logs`
--
ALTER TABLE `authentication_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `station_map`
--
ALTER TABLE `station_map`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `trains`
--
ALTER TABLE `trains`
  MODIFY `train_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `authentication_logs`
--
ALTER TABLE `authentication_logs`
  ADD CONSTRAINT `authentication_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`train_id`) REFERENCES `trains` (`train_id`);

--
-- Ограничения внешнего ключа таблицы `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`train_id`) REFERENCES `trains` (`train_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
