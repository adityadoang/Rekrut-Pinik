-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 27, 2025 at 03:04 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phoenix_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `phoenix`
--

CREATE TABLE `phoenix` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `element` enum('fire','water','ice','wind','earth') NOT NULL,
  `req_element_power` int DEFAULT '0',
  `req_intelligence` int DEFAULT '0',
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phoenix`
--

INSERT INTO `phoenix` (`id`, `name`, `element`, `req_element_power`, `req_intelligence`, `description`) VALUES
(1, 'Ice Phoenix', 'ice', 5, 3, 'Phoenix penguasa elemen es.'),
(2, 'Fire Phoenix', 'fire', 5, 2, 'Phoenix bersayap api membara.'),
(3, 'Phoenix kuah soto', 'water', 1000, 1000, 'KOYA');

-- --------------------------------------------------------

--
-- Table structure for table `quests`
--

CREATE TABLE `quests` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` enum('A','B','C','D') NOT NULL,
  `reward_fire` int DEFAULT '0',
  `reward_water` int DEFAULT '0',
  `reward_ice` int DEFAULT '0',
  `reward_wind` int DEFAULT '0',
  `reward_earth` int DEFAULT '0',
  `reward_intelligence` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quests`
--

INSERT INTO `quests` (`id`, `title`, `description`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `reward_fire`, `reward_water`, `reward_ice`, `reward_wind`, `reward_earth`, `reward_intelligence`) VALUES
(1, 'Latihan Api Dasar', 'Melatih kontrol elemen api tingkat dasar.', 'Sifat apa yang paling menggambarkan elemen api?', 'Stabil dan tidak berubah', 'Cepat menyebar dan sulit dikontrol', 'Selalu dingin', 'Tidak memiliki energi', 'B', 3, 0, 0, 0, 0, 1),
(2, 'Meditasi di Sungai', 'Melatih koneksi dengan elemen air.', 'Apa sifat utama elemen air?', 'Kaku dan padat', 'Fleksibel dan mengalir', 'Tidak bisa berubah bentuk', 'Selalu panas', 'B', 0, 3, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `element_fire` int DEFAULT '0',
  `element_water` int DEFAULT '0',
  `element_ice` int DEFAULT '0',
  `element_wind` int DEFAULT '0',
  `element_earth` int DEFAULT '0',
  `intelligence` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `element_fire`, `element_water`, `element_ice`, `element_wind`, `element_earth`, `intelligence`, `created_at`) VALUES
(2, 'admin', '$2y$10$bwWwtNzddwONDoLqgJgs.u3ke9rzi93DgAQkk7Ci8WhXPmhU/hxgC', 'admin', 0, 0, 0, 0, 0, 0, '2025-11-25 20:10:20'),
(3, 'a', 'a', 'user', 10, 11, 11, 11, 11, 11, '2025-11-25 20:25:55'),
(4, 'q', '$2y$10$7zQ5htuReP4.e5uf78W1SO1VJyd7LYWiqkHXruW/xLmmahsr0FlJi', 'user', 6, 6, 0, 0, 0, 4, '2025-11-25 20:27:38');

-- --------------------------------------------------------

--
-- Table structure for table `user_phoenix`
--

CREATE TABLE `user_phoenix` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `phoenix_id` int NOT NULL,
  `recruited_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_phoenix`
--

INSERT INTO `user_phoenix` (`id`, `user_id`, `phoenix_id`, `recruited_at`) VALUES
(1, 4, 2, '2025-11-25 20:29:46');

-- --------------------------------------------------------

--
-- Table structure for table `user_quests`
--

CREATE TABLE `user_quests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `quest_id` int NOT NULL,
  `is_correct` tinyint(1) DEFAULT '0',
  `answered_option` enum('A','B','C','D') DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_quests`
--

INSERT INTO `user_quests` (`id`, `user_id`, `quest_id`, `is_correct`, `answered_option`, `completed_at`) VALUES
(1, 4, 1, 0, 'A', '2025-11-25 20:28:00'),
(2, 4, 2, 1, 'B', '2025-11-25 20:28:13'),
(3, 4, 2, 1, 'B', '2025-11-25 20:28:28'),
(4, 4, 1, 0, 'A', '2025-11-25 20:28:46'),
(5, 4, 1, 1, 'B', '2025-11-25 20:29:33'),
(6, 4, 1, 1, 'B', '2025-11-25 20:29:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `phoenix`
--
ALTER TABLE `phoenix`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quests`
--
ALTER TABLE `quests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_phoenix`
--
ALTER TABLE `user_phoenix`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `phoenix_id` (`phoenix_id`);

--
-- Indexes for table `user_quests`
--
ALTER TABLE `user_quests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quest_id` (`quest_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `phoenix`
--
ALTER TABLE `phoenix`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quests`
--
ALTER TABLE `quests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_phoenix`
--
ALTER TABLE `user_phoenix`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_quests`
--
ALTER TABLE `user_quests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_phoenix`
--
ALTER TABLE `user_phoenix`
  ADD CONSTRAINT `user_phoenix_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_phoenix_ibfk_2` FOREIGN KEY (`phoenix_id`) REFERENCES `phoenix` (`id`);

--
-- Constraints for table `user_quests`
--
ALTER TABLE `user_quests`
  ADD CONSTRAINT `user_quests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_quests_ibfk_2` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
