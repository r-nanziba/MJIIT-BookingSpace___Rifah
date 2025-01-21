-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2025 at 03:48 AM
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
-- Database: `mjiitroommasterdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `email`) VALUES
(1, 'mjadmin', '$2a$12$zlUjzmhpEczv7sPA55PLqetZr8FpjQcotmMNTa9JDl.kPgVNnymcS', 'mjadmin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled','Completed') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `booking_date`, `start_time`, `end_time`, `status`) VALUES
(1, 1, 1, '2024-12-19', '09:00:00', '11:00:00', 'Confirmed'),
(2, 2, 3, '2024-12-19', '12:00:00', '14:00:00', 'Confirmed'),
(3, 16, 2, '2024-12-31', '13:10:00', '14:10:00', ''),
(5, 17, 3, '2025-01-29', '11:30:00', '12:30:00', 'Cancelled'),
(6, 17, 1, '2025-01-27', '09:38:00', '10:38:00', 'Cancelled'),
(7, 17, 5, '2025-01-30', '12:17:00', '15:17:00', 'Cancelled'),
(8, 17, 2, '2025-01-30', '15:24:00', '16:24:00', 'Cancelled'),
(9, 17, 6, '2025-01-31', '15:43:00', '16:43:00', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `guest_id` int(11) NOT NULL,
  `guestname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type_id` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`guest_id`, `guestname`, `password`, `email`, `created_at`, `type_id`) VALUES
(1, '', '$2y$10$vc8H4QRH13ld31/1VtJcD.BA.4jOKkQk3ePmCPbPGLVFu49UykH6u', 'farin@graduate.utm.my', '2025-01-17 06:28:36', 2),
(2, 'rayan', '$2y$10$LQoE93sHJOc/etf8.Q9D...mgHR0nMmdhP1aT62qDuMFHgTSRTCSe', 'rayan@gmail.com', '2025-01-17 06:28:36', 2),
(3, 'saya', '$2y$10$U7aYbRmMyj6Po7ZZxPkOtuZbDxVeD35DKjniPB8d0ZM3X74EMP0/W', 'saya@gmail.com', '2025-01-17 06:28:36', 2),
(4, 'rupa', '$2y$10$yG61Cc6riTViqeS5gSfc5uEli6tnJXjDcwfRlj0h98rmjcOOoy05W', 'rupa@gmail.com', '2025-01-17 06:28:36', 2),
(5, 'mama', '$2y$10$Xl54e83j9QqeNv48KLyCme.2xFsh1wdgfRUMcdAM5R51.6rWywH3a', 'mama@gmail.com', '2025-01-17 06:28:36', 2);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `equipment` text DEFAULT NULL,
  `pricing` decimal(10,2) DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('Available','Booked','Maintenance') NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_name`, `location`, `capacity`, `equipment`, `pricing`, `image`, `status`) VALUES
(1, 'Bilik Kuliah 1', '02.31.01 / 02', 40, 'Projector, Whiteboard', 150.00, 'bk2.png', 'Available'),
(2, 'Bilik Kuliah 2', '02.36.01 / 02', 40, 'Projector, Whiteboard', 160.00, 'bk8.png', 'Available'),
(3, 'Bilik Kuliah 3', '02.37.01 / 02', 40, 'Projector, Whiteboard', 170.00, 'bk20.png', 'Available'),
(4, 'Bilik Kuliah 4', '03.63.01 / 02', 40, 'Projector, Whiteboard', 180.00, 'bk2.png', 'Available'),
(5, 'Bilik Kuliah 5', '03.64.01 / 02', 40, 'Projector, Whiteboard', 190.00, 'bk8.png', 'Available'),
(6, 'Bilik Kuliah 6', '04.37.01 / 02', 40, 'Projector, Whiteboard', 200.00, 'bk20.png', 'Available'),
(7, 'Bilik Kuliah 7', '04.38.01 / 02', 40, 'Projector, Whiteboard', 210.00, 'bk20.png', 'Available'),
(8, 'Bilik Kuliah 8', '04.41.01 / 02', 40, 'Projector, Whiteboard', 220.00, 'bk20.png', 'Available'),
(9, 'Bilik Kuliah 9', '05.44.01 / 02', 40, 'Projector, Whiteboard', 230.00, 'bk20.png', 'Available'),
(10, 'Bilik Kuliah 10', '05.45.01 / 02', 40, 'Projector, Whiteboard', 240.00, 'bk20.png', 'Available'),
(11, 'Bilik Kuliah 12', '06.50.01 / 02', 40, 'Projector, Whiteboard', 250.00, 'bk20.png', 'Available'),
(12, 'Bilik Kuliah 13', '06.51.01 / 02', 40, 'Projector, Whiteboard', 260.00, 'bk20.png', 'Available'),
(13, 'Bilik Kuliah 14', '06.56.01 / 02', 40, 'Projector, Whiteboard', 270.00, 'bk20.png', 'Available'),
(14, 'Bilik Kuliah 15', '06.57.01 / 02', 40, 'Projector, Whiteboard', 280.00, 'bk20.png', 'Available'),
(15, 'Bilik Kuliah 16', '06.63.01 / 02', 40, 'Projector, Whiteboard', 290.00, 'bk20.png', 'Available'),
(16, 'Bilik Kuliah 17', '06.62.01 / 02', 40, 'Projector, Whiteboard', 300.00, 'bk20.png', 'Available'),
(17, 'Bilik Kuliah 19', '08.44.01 / 08.44.02', 40, 'Projector, Whiteboard', 310.00, 'bk20.png', 'Available'),
(18, 'Bilik Kuliah 20', '08.47.01 / 08.47.02', 40, 'Projector, Whiteboard', 320.00, 'bk20.png', 'Available'),
(19, 'Bilik Kuliah 21', '08.45.01 / 08.45.02', 40, 'Projector, Whiteboard', 330.00, 'bk20.png', 'Available'),
(20, 'Bilik Kuliah 22', '08.49.01 / 02 & 08.48.01 / 02', 40, 'Projector, Whiteboard', 340.00, 'bk20.png', 'Available'),
(21, 'Bilik Sindiket 1', '04.24.01 / 02', 20, 'Projector, Whiteboard', 100.00, 'seminarroom.png', 'Available'),
(22, 'Bilik Sindiket 2', '04.25.01', 20, 'Projector, Whiteboard', 0.00, 'meetingroom.png', 'Available'),
(23, 'Bilik Sindiket 3', '04.26.01', 20, 'Projector, Whiteboard', 0.00, 'seminarroom.png', 'Available'),
(24, 'Bilik Sindiket 4', '04.27.01', 20, 'Projector, Whiteboard', 0.00, 'seminarroom.png', 'Available'),
(25, 'Bilik Sindiket 5', '04.28.01', 20, 'Projector, Whiteboard', 0.00, 'seminarroom.png', 'Available'),
(26, 'Bilik Sindiket 6', '04.29.01', 20, 'Projector, Whiteboard', 0.00, 'seminarroom.png', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('student','teacher') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `created_at`, `type_id`) VALUES
(1, 'john_doe', '$2a$12$QM52meLqrq7VZnwTw8k58up5XXx0RZKNBrESwM9rH9aRg7vUvrzx', 'john.doe@example.com', 'student', '2025-01-17 06:28:36', 1),
(2, 'jane_doe', 'password123', 'jane.doe@example.com', 'student', '2025-01-17 06:28:36', 1),
(3, 'kayum', '$2y$10$abNWR3OJMkbXjac55eAITOt8kNMoXE/ggP/8XIiow8SrIdibKVa6e', 'kayum@gmail.com', 'student', '2025-01-17 06:28:36', 1),
(4, 'shabil', '$2y$10$l8MhFNdBD6S1NY1cWDZQmu1UhOz4W2qf2//QjpriYlDaFLCUZ3wg6', 'shabil@gmail.com', 'student', '2025-01-17 06:28:36', 1),
(5, 'maria', '$2y$10$xIQxzdyzRgEoll0qYelIdOHi1LpjxnCWAgsSHuRCKSlYRVjsS4xtG', 'maria@gmail.com', 'student', '2025-01-17 06:28:36', 1),
(6, 'qiri', '$2y$10$Pc2y7crjllDeQMeuLfakbOK5u6VYYAxyq5jFNN6juN3dWThrwwlqi', 'qiri@graduate.utm.my', 'student', '2025-01-17 06:28:36', 1),
(7, 'raya', '$2y$10$08XPF/lFFM5WZ6DN3kZpwu7W6TapqX8c..S2ITrNflArlBSpPErgK', 'raya@graduate.utm.my', 'student', '2025-01-17 06:28:36', 1),
(8, 'mini', '$2y$10$6dtf6BZWfmIk/lfJ.zOp6eMDtc4aaL01tfZwD3CXRvotlTULAKjau', 'mini@graduate.utm.my', 'teacher', '2025-01-17 06:28:36', 1),
(9, 'tina', '$2y$10$CfXiNSpJBKTK0Y.kyNHvGevdyOTHIRJmLZX1FfZZBQal/PVclT1Pm', 'tina@graduate.utm.my', 'student', '2025-01-17 06:28:36', 1),
(12, 'rant', '$2y$10$fyLOmq2ePtEQ.LiOfFgVX.Aj.yvwO.VXIAHs8yjgr.eViVKE8NpKG', 'rant@gmal.com', 'teacher', '2025-01-17 06:28:36', 1),
(16, 'rifah', '$2y$10$nRgwWVVnVbyMAWO8Uy8TceHgwTO0C17oxB852UvqzsGLDYek2wCgu', 'rifah@gmail.com', 'student', '2025-01-17 06:28:36', 1),
(17, 'ana', '$2a$12$BdVW21OEhgCdCK8Du2pTHuP2dGQaT8NOC9aE.uqLOcZgMsMhl4/6y', 'ana@gmail.com', 'student', '2025-01-18 16:49:20', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`type_id`, `type_name`) VALUES
(2, 'Guest'),
(1, 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guest_id`),
  ADD UNIQUE KEY `guestname` (`guestname`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_guests_type_id` (`type_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_type_id` (`type_id`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Constraints for table `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `fk_guests_type_id` FOREIGN KEY (`type_id`) REFERENCES `user_types` (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_type_id` FOREIGN KEY (`type_id`) REFERENCES `user_types` (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
