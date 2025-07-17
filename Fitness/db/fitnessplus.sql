-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2025 at 05:26 AM
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
-- Database: `fitnessplus`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(20) DEFAULT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`, `fullname`, `username`, `password`) VALUES
(0, 'admin', 'admin', '$2y$10$TSSlz/biITlXAQAvEBQkse0IGnlhL7edkUQ4ic58wnN1OgHKieGTG');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `member_id`, `time_in`, `time_out`) VALUES
(19, 29, '2025-06-12 19:12:39', '2025-06-12 19:12:55'),
(25, 6, '2025-06-13 18:10:30', '2025-06-13 18:10:57'),
(26, 39, '2025-06-13 18:10:44', '2025-06-13 18:28:47'),
(27, 39, '2025-07-08 09:19:02', '2025-07-13 12:39:07');

-- --------------------------------------------------------

--
-- Table structure for table `class_members`
--

CREATE TABLE `class_members` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `class_members`
--

INSERT INTO `class_members` (`id`, `class_id`, `member_id`) VALUES
(29, 3, 44),
(36, 3, 23),
(37, 3, 46);

-- --------------------------------------------------------

--
-- Table structure for table `class_schedule`
--

CREATE TABLE `class_schedule` (
  `id` int(11) NOT NULL,
  `coach_id` int(20) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_schedule`
--

INSERT INTO `class_schedule` (`id`, `coach_id`, `class_name`, `day_of_week`, `start_time`, `end_time`, `location`) VALUES
(3, 57, 'Boxing', 'Monday', '16:00:00', '17:00:00', '2nd floor(cage)'),
(4, 61, 'Judo', 'Wednesday', '11:39:00', '02:40:00', '2nd floor');

-- --------------------------------------------------------

--
-- Table structure for table `coach`
--

CREATE TABLE `coach` (
  `id` int(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `specialization` varchar(20) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `email` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coach`
--

INSERT INTO `coach` (`id`, `name`, `birth_date`, `specialization`, `contact_number`, `email`) VALUES
(57, 'Wyn Michael James Rizon', '2004-03-08', 'Boxer', '09176412713', 'mjrizon23@gmail.com'),
(61, 'Eloisa Maribao', '2004-04-12', 'Boxing', '065095215612', 'eloisa@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(20) DEFAULT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `dor` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `services` varchar(50) DEFAULT NULL,
  `amount` int(100) DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `p_year` int(11) DEFAULT NULL,
  `plan` varchar(100) DEFAULT NULL,
  `address` varchar(20) DEFAULT NULL,
  `contact` varchar(10) DEFAULT NULL,
  `attendance_count` int(100) DEFAULT NULL,
  `ini_weight` int(100) DEFAULT NULL,
  `curr_weight` int(100) DEFAULT NULL,
  `ini_bodytype` varchar(50) DEFAULT NULL,
  `curr_bodytype` varchar(50) DEFAULT NULL,
  `progress_date` date DEFAULT NULL,
  `reminder` int(11) DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `height` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`user_id`, `fullname`, `username`, `password`, `gender`, `dor`, `expiration_date`, `services`, `amount`, `paid_date`, `p_year`, `plan`, `address`, `contact`, `attendance_count`, `ini_weight`, `curr_weight`, `ini_bodytype`, `curr_bodytype`, `progress_date`, `reminder`, `weight`, `height`) VALUES
(6, 'Harry Denn', 'harry', '$2y$10$z6inmXq72vxtVCuXtosK6.Ld7RiDEIDnk2Y5H1J2Uq5a0PoatxvzC', 'Male', '2025-06-13', '2025-09-13', 'Gym', 5000, '2025-05-07', 2025, '3', '64 Mulberry Lane', '8545878545', 4, 54, 62, 'Slim', 'Buffed', '2020-04-22', 0, 80, 170),
(8, 'Charles Anderson', 'charles', 'cac29d7a34687eb14b37068ee4708e7b', 'Male', '2025-06-13', NULL, 'Gym', 950, '2025-06-13', 2025, '1', '99 Heron Way', '8520258520', 14, 92, 85, 'Fat', 'Bulked', '2020-04-22', 1, NULL, NULL),
(11, 'Justin Schexnayder', 'justin', 'cac29d7a34687eb14b37068ee4708e7b', 'Male', '2025-06-13', NULL, 'Gym', 950, '2025-06-13', 2025, '1', '14 Blair Court', '7535752220', 9, 0, 0, '', '', '0000-00-00', 0, NULL, NULL),
(14, 'Ryan Crowl', 'ryan', 'cac29d7a34687eb14b37068ee4708e7b', 'Male', '2025-06-13', NULL, 'Gym', 5000, '2025-06-13', 2025, '6', '34 Twin Oaks Drive', '1578880010', 13, 59, 63, 'Slim', 'Slim', '2020-04-23', 0, NULL, NULL),
(17, 'Karen McGray', 'karen', 'cac29d7a34687eb14b37068ee4708e7b', 'Female', '2025-06-13', NULL, 'Cardio', 120, '2022-05-31', 2020, '1', '23 Rubaiyat Road', '7441002540', 12, 0, 0, '', '', '0000-00-00', 0, NULL, NULL),
(18, 'Jeanne Pratt', 'prattj', '$2y$10$62bmTHPrJlRuiKLHjlKzkugawwGQAaJQycIwx0qHHyxCYDPyas8QW', 'Female', '2020-04-04', '2020-05-04', 'Fitness', 55, '2021-06-11', 2021, '1', '86 Hilltop Street', '7854445410', 11, 0, 0, '', '', '0000-00-00', 0, NULL, NULL),
(21, 'Patrick Wilson', 'patrick', 'cac29d7a34687eb14b37068ee4708e7b', 'Male', '2020-04-02', NULL, 'Cardio', 120, '2022-06-01', 2021, '3', '24 Cody Ridge Road', '9874568520', 11, 0, 0, '', '', '0000-00-00', 0, NULL, NULL),
(23, 'Keith Martin', 'martin', '$2y$10$r2Zqjq4T6t/khsZ3KfTSTe4ulXSdFbLg.ilePrYbqqKaQMYjz6raS', 'Male', '2020-04-02', '2020-07-02', 'Cardio', 120, '2022-06-02', 2021, '3', '89 Smithfield Avenue', '7895456250', 24, 51, 68, 'Slim', 'Muscular', '2022-06-02', 0, NULL, NULL),
(24, 'Richard G Langston', 'richard', 'cac29d7a34687eb14b37068ee4708e7b', 'Male', '2025-06-18', NULL, 'Gym', 1800, '2025-06-13', 2025, '2', '541  Raoul Wallenber', '7012545580', 1, 0, 0, '', '', '0000-00-00', 0, NULL, NULL),
(25, 'Raymond Ledesma', 'raymond', 'cac29d7a34687eb14b37068ee4708e7b', 'Male', '2025-06-13', NULL, 'Cardio', 480, '2022-06-02', 2022, '2', '2954  Robinson Lane', '4785450002', 2, 0, 0, '', '', '0000-00-00', 0, NULL, NULL),
(29, 'Kathy J. Glennon', 'kathy', 'cac29d7a34687eb14b37068ee4708e7b', 'Female', '2025-06-11', '2026-06-11', 'Fitness', 330, '2022-06-02', 0, '12', '87 Harry Place', '7896587458', 0, 0, 0, '', '', '0000-00-00', 0, NULL, NULL),
(38, 'lebron james', 'king', '$2y$10$H0avM4xGU4zR0eY0Uh.2weNBsoyXe9IxkMEG4LdhCqUf0kUpyqW5O', 'Male', '2025-06-10', '2025-08-10', NULL, NULL, NULL, NULL, '2', NULL, '1156130320', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'Wyn Michael James Ri', 'mjrizon8', '$2y$10$2sf2bWQ1SHIBdu8fe2YpTeFzEiQi0cmqJCjQJqXKHh3lNebqQLVvG', 'Male', '2025-07-16', '2026-02-16', NULL, NULL, NULL, NULL, '7', NULL, '8545878545', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 80, 170),
(44, 'Marlon Bernal', 'marlon', '$2y$10$2KsBWSPzk.83.dG5OblVceAZ9ytc5fkgpUEmgEH31J2PVA/Eu.W3y', 'Male', '2025-06-18', '2025-08-18', NULL, NULL, NULL, NULL, '2', NULL, '0976451', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 200, 175),
(45, 'bill Bansil', 'admin', '$2y$10$Tyn693CnzvyIOS81ko8LIemb1Ifjf9UsU./tCgWA.CPlbY51maVXi', 'Male', '2025-06-18', '2025-07-18', NULL, NULL, NULL, NULL, '1', NULL, '1293849023', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 120, 190),
(46, 'abdullah', 'Abdullah1212', '$2y$10$uT6leE4m1kaa1iHB0KkjruJeNu7CAdIRDZlKtvkeea2zIvQ.sBtyO', 'Male', '2025-07-08', '2025-10-08', NULL, NULL, NULL, NULL, '3', NULL, '0975016209', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 90, 181),
(47, 'Eloisa Maribao', 'Eloisa', '$2y$10$OGs3NRCXkPuoE1h/LTfJhed0BrA.mpe8Xd3GMNEdyVqRTEXYLCYOq', 'Female', '2025-07-16', '2025-09-16', NULL, NULL, NULL, NULL, '2', NULL, '0456126026', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 60, 160),
(48, 'Connie Feir', 'connie', '$2y$10$6eJzUjOs8kYFhmDrK/Z3.O.Kj5kjQH5wWmjN7fIc3T0pVk61yI26a', 'Female', '2025-07-16', '2025-12-16', NULL, NULL, NULL, NULL, '5', NULL, '123123`', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `monthlypayments`
--

CREATE TABLE `monthlypayments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan` varchar(20) NOT NULL,
  `amount` int(100) NOT NULL,
  `paid_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `monthlypayments`
--

INSERT INTO `monthlypayments` (`id`, `user_id`, `plan`, `amount`, `paid_date`) VALUES
(1, 17, '1', 950, '2025-06-13'),
(2, 17, '7', 5000, '2025-06-13'),
(3, 6, '3', 5000, '2025-06-13'),
(4, 17, '1', 950, '2025-06-13'),
(5, 25, '2', 1800, '2025-06-13'),
(6, 24, '2', 1800, '2025-06-18'),
(7, 39, '1', 900, '2025-07-16'),
(8, 39, '2', 1800, '2025-07-16'),
(9, 39, '1', 900, '2025-06-16'),
(10, 39, '1', 900, '2025-05-16'),
(11, 39, '1', 900, '2025-07-16'),
(12, 39, '1', 5000, '2025-05-16'),
(13, 39, '1', 900, '2025-07-16'),
(14, 39, '1', 5000, '2025-05-16'),
(15, 39, '1', 1000, '2025-07-16'),
(16, 48, '3', 5000, '2025-07-16'),
(17, 48, '5', 5000, '2025-07-16'),
(18, 39, '1', 900, '2025-07-16'),
(19, 39, '7', 5000, '2025-07-16');

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `charge` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rates`
--

INSERT INTO `rates` (`id`, `name`, `charge`) VALUES
(1, 'Boxing', '300'),
(2, 'Muay Thai', '350'),
(3, 'MMA', '350'),
(4, 'GYM Session', '80');

-- --------------------------------------------------------

--
-- Table structure for table `walkin_payments`
--

CREATE TABLE `walkin_payments` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `service` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `paid_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `walkin_payments`
--

INSERT INTO `walkin_payments` (`id`, `fullname`, `service`, `amount`, `paid_date`) VALUES
(1, 'Wyn Michael James Rizon', 'Boxing', 300, '2025-06-13'),
(2, 'Harry Denn', 'Boxing', 280, '2025-06-13'),
(3, 'Harry Denn', 'Boxing', 300, '2025-06-05'),
(4, 'Eloisa Maribao', 'Boxing', 250, '2025-07-16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `class_members`
--
ALTER TABLE `class_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `class_schedule`
--
ALTER TABLE `class_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coach_id` (`coach_id`);

--
-- Indexes for table `coach`
--
ALTER TABLE `coach`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `monthlypayments`
--
ALTER TABLE `monthlypayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `walkin_payments`
--
ALTER TABLE `walkin_payments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `class_members`
--
ALTER TABLE `class_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `class_schedule`
--
ALTER TABLE `class_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `coach`
--
ALTER TABLE `coach`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `monthlypayments`
--
ALTER TABLE `monthlypayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `walkin_payments`
--
ALTER TABLE `walkin_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `class_members`
--
ALTER TABLE `class_members`
  ADD CONSTRAINT `class_members_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class_schedule` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_members_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `class_schedule`
--
ALTER TABLE `class_schedule`
  ADD CONSTRAINT `class_schedule_ibfk_1` FOREIGN KEY (`coach_id`) REFERENCES `coach` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monthlypayments`
--
ALTER TABLE `monthlypayments`
  ADD CONSTRAINT `monthlypayments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
