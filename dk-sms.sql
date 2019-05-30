-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2019 at 01:34 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dk-sms`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `phone` varchar(12) NOT NULL,
  `body` text NOT NULL,
  `sent` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

-- INSERT INTO `messages` (`id`, `phone`, `body`, `sent`) VALUES
-- (8, '09123456789', 'salam', 1),
-- (9, '09123456789', 'salam', 0),
-- (10, '09123456789', 'salam', 1),
-- (11, '09123456789', 'salam', 1),
-- (12, '09123456789', 'salam', 0),
-- (13, '09123456789', 'salam', 1),
-- (14, '09123456789', 'salam', 1),
-- (15, '989123456789', 'salam', 0),
-- (16, '989123456789', 'salam', 1),
-- (17, '989123456789', 'salam', 1),
-- (18, '09213456789', 'salam', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sent_log`
--

CREATE TABLE `sent_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `message_id` int(10) UNSIGNED NOT NULL,
  `sent` int(11) NOT NULL,
  `api` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sent_log`
--

-- INSERT INTO `sent_log` (`id`, `message_id`, `sent`, `api`, `date`) VALUES
-- (1, 8, 0, 0, '2019-05-27 21:59:13'),
-- (2, 8, 1, 1, '2019-05-27 21:59:13'),
-- (3, 9, 0, 0, '2019-05-27 21:59:57'),
-- (4, 9, 0, 1, '2019-05-27 21:59:57'),
-- (5, 10, 1, 0, '2019-05-27 22:08:14'),
-- (6, 11, 0, 0, '2019-05-27 22:08:15'),
-- (7, 11, 1, 1, '2019-05-27 22:08:15'),
-- (8, 12, 0, 0, '2019-05-27 22:08:16'),
-- (9, 12, 0, 1, '2019-05-27 22:08:16'),
-- (10, 13, 1, 0, '2019-05-27 22:46:08'),
-- (11, 14, 0, 0, '2019-05-27 22:46:09'),
-- (12, 14, 1, 1, '2019-05-27 22:46:09'),
-- (13, 15, 0, 0, '2019-05-27 22:54:04'),
-- (14, 15, 0, 1, '2019-05-27 22:54:04'),
-- (15, 16, 0, 0, '2019-05-27 22:54:06'),
-- (16, 16, 1, 1, '2019-05-27 22:54:06'),
-- (17, 17, 1, 0, '2019-05-27 22:54:07'),
-- (18, 18, 1, 0, '2019-05-27 22:54:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `sent_log`
--
ALTER TABLE `sent_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sent_log`
--
ALTER TABLE `sent_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sent_log`
--
ALTER TABLE `sent_log`
  ADD CONSTRAINT `sent_log_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
