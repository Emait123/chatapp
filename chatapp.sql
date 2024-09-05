-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2024 at 10:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chatapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_history`
--

CREATE TABLE `chat_history` (
  `id` int(11) NOT NULL,
  `tel_user_id` bigint(20) NOT NULL COMMENT 'telegram user id',
  `add_date` datetime NOT NULL,
  `message` varchar(255) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `birth` date DEFAULT NULL,
  `gender` tinyint(4) DEFAULT NULL,
  `telegram_id` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `user_id`, `name`, `birth`, `gender`, `telegram_id`) VALUES
(1, 1, 'Nhân viên 1', '1997-03-24', 1, ''),
(2, 3, 'Lê Diệu Thúy', NULL, NULL, '5304040462'),
(3, 4, 'Trương Tiến Tùng', NULL, NULL, '5580139045'),
(4, 5, 'Trương Tiến Đạt', NULL, NULL, '7050621296');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Nhân viên');

-- --------------------------------------------------------

--
-- Table structure for table `timeoff`
--

CREATE TABLE `timeoff` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `request_date` datetime NOT NULL DEFAULT current_timestamp(),
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `duration` float DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT current_timestamp(),
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timeoff`
--

INSERT INTO `timeoff` (`id`, `employee_id`, `request_date`, `month`, `year`, `start_date`, `end_date`, `duration`, `reason`, `approved`, `approved_by`, `approved_date`, `deleted`) VALUES
(1, 1, '2024-08-04 14:27:20', NULL, NULL, '2024-08-07 12:00:00', '2024-08-07 17:00:00', NULL, 'nhà có việc bận', 0, NULL, '2024-08-04 14:27:20', 0),
(2, 1, '2024-08-05 09:18:16', NULL, NULL, '2024-08-13 00:00:00', '2024-08-13 23:59:59', NULL, 'nhà có việc bận', 0, NULL, '2024-08-05 09:18:16', 0),
(3, 1, '2024-08-05 16:05:47', NULL, NULL, '2024-08-09 00:00:00', '2024-08-09 23:59:59', NULL, 'có lịch đi khám bệnh', 0, NULL, '2024-08-05 16:05:47', 0),
(4, 1, '2024-08-15 13:48:58', NULL, NULL, '2024-08-16 00:00:00', NULL, NULL, 'xin nghỉ', 0, NULL, '2024-08-15 13:48:58', 0),
(5, 4, '2024-08-27 09:13:02', NULL, NULL, '1970-01-01 08:00:00', '1970-01-01 08:00:00', 1, 'khám bệnh', 0, NULL, '2024-08-27 09:13:02', 0),
(6, 4, '2024-08-27 09:19:33', NULL, NULL, '2024-08-29 08:00:00', '2024-08-29 17:00:00', 1, 'khám bệnh', 0, NULL, '2024-08-27 09:19:45', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tool`
--

CREATE TABLE `tool` (
  `id` int(11) NOT NULL,
  `img_name` varchar(128) DEFAULT NULL,
  `msv` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tool`
--

INSERT INTO `tool` (`id`, `img_name`, `msv`) VALUES
(1, 'file_0', '20A10010394_org'),
(2, 'file_1', '20A100_org_1'),
(3, 'file_1_bright', '20A_brigh'),
(4, 'file_1_flip', '20A1_flip'),
(5, 'file_1_noise', '24Anoise'),
(6, 'file_1_rotate', '24Arotate'),
(7, 'file_1_translate', '24translatr'),
(8, 'file_1_zoom', '24zoom');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `createdate` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `phone`, `createdate`, `last_login`, `role_id`) VALUES
(1, 'user', '$2y$10$uK3U.RcWI1m5Zv6HZh1vcOHww6CPqvOeu7LSfNiLqUIpfgspr61A6', 'emait123@gmail.com', '0123456', '2024-08-04 13:50:12', '2024-08-27 14:44:52', 2),
(2, 'admin', '$2y$10$dKbUqy7aakcmDXf3VzX8ROYxO605Wg7ecChgUbpBiPf2hym1x7MDK', 'tetra.dragon197@gmail.com', '0123456', '2024-08-25 08:46:54', '2024-08-25 09:19:24', 1),
(3, 'ledieuthuy', '$2y$10$DC.p3obB96Ssv5ivwxxBWedJB3JjMpsTvLZrs2YYrotBnYdfEUgIq', NULL, NULL, '2024-08-25 10:00:30', '2024-08-25 10:08:14', NULL),
(4, 'truongtientung', '$2y$10$QOWZ.HzhJqSJuarr/6NLQuH.1I7gw7ux4V.ZWFmZKINyJxLsj8Hk2', NULL, NULL, '2024-08-25 10:01:13', '2024-08-25 10:08:02', NULL),
(5, 'truongtiendat', '$2y$10$5pNJ8CoXXjFMwFpiaXYHCu.zoGRbh8wPpGtHZ87ZpL6FRPITtpQZe', NULL, NULL, '2024-08-25 10:07:08', '2024-08-25 10:08:21', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_history`
--
ALTER TABLE `chat_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timeoff`
--
ALTER TABLE `timeoff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `tool`
--
ALTER TABLE `tool`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_history`
--
ALTER TABLE `chat_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `timeoff`
--
ALTER TABLE `timeoff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tool`
--
ALTER TABLE `tool`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `timeoff`
--
ALTER TABLE `timeoff`
  ADD CONSTRAINT `timeoff_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`),
  ADD CONSTRAINT `timeoff_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
