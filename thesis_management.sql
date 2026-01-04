-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2026 at 04:27 PM
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
-- Database: `thesis_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `created_at`) VALUES
(105, 8, 'Registered as Admin', '2026-01-04 14:58:55'),
(106, 8, 'Logged in', '2026-01-04 14:59:17'),
(107, 9, 'Registered as Faculty', '2026-01-04 15:00:43'),
(108, 9, 'Logged in', '2026-01-04 15:00:51'),
(109, 9, 'Logged out', '2026-01-04 15:01:02'),
(110, 9, 'Logged in', '2026-01-04 15:01:15'),
(111, 10, 'Registered as Student', '2026-01-04 15:03:03'),
(112, 10, 'Logged in', '2026-01-04 15:03:38'),
(113, 8, 'Logged out', '2026-01-04 15:04:00'),
(114, 10, 'Logged out', '2026-01-04 15:04:03'),
(115, 10, 'Logged in', '2026-01-04 15:06:46'),
(116, 10, 'Logged out', '2026-01-04 15:07:09'),
(117, 8, 'Logged in', '2026-01-04 15:11:36'),
(118, 9, 'Logged in', '2026-01-04 15:14:30'),
(119, 8, 'Updated profile picture and signature', '2026-01-04 15:15:39'),
(120, 9, 'Updated profile picture and signature', '2026-01-04 15:16:03'),
(121, 10, 'Logged in', '2026-01-04 15:16:58'),
(122, 10, 'Updated profile picture and signature', '2026-01-04 15:17:24'),
(123, 10, 'Uploaded thesis: Crime ', '2026-01-04 15:19:27'),
(124, 10, 'Logged out', '2026-01-04 15:20:42'),
(125, 9, 'Logged in', '2026-01-04 15:20:55'),
(126, 9, 'Approved thesis ID 4 with comment: great, keep it up', '2026-01-04 15:22:20'),
(127, 9, 'Logged out', '2026-01-04 15:22:53'),
(128, 10, 'Logged in', '2026-01-04 15:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(3, 'BSIT Department');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `department_id`, `name`) VALUES
(2, 3, 'Crime');

-- --------------------------------------------------------

--
-- Table structure for table `review_logs`
--

CREATE TABLE `review_logs` (
  `id` int(11) NOT NULL,
  `thesis_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `action` enum('Approved','Rejected','Commented') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_logs`
--

INSERT INTO `review_logs` (`id`, `thesis_id`, `reviewer_id`, `comment`, `action`, `created_at`) VALUES
(3, 4, 9, 'great, keep it up', 'Approved', '2026-01-04 15:22:20');

-- --------------------------------------------------------

--
-- Table structure for table `thesis`
--

CREATE TABLE `thesis` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `abstract` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `adviser_id` int(11) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thesis`
--

INSERT INTO `thesis` (`id`, `student_id`, `title`, `abstract`, `keywords`, `adviser_id`, `course`, `department_id`, `file`, `status`, `created_at`) VALUES
(4, 10, 'Crime ', 'CRIME PLEASE SO THIS IS ABSTRACT', 'Cybercrime, Online Security, College Students, Internet Behavior, Digital Safety', 9, 'Bachelor of Science in Information Technology (BSIT)', 3, 'uploads/1767539967_Upload_Thesis_Sample.docx', 'Approved', '2026-01-04 15:19:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Student','Faculty','Admin') NOT NULL DEFAULT 'Student',
  `profile_pic` varchar(255) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `username`, `password`, `role`, `profile_pic`, `signature`, `created_at`) VALUES
(8, 'System Administrator', 'systemads@gmail.com', 'systemadmin', '$2y$10$oWPbCyUv8Xq8YBSeTtuK4estJRn2U8m4lcgajOwqebxeWCVH3lQym', 'Admin', 'uploads/profile_8_1767539739.jpg', 'uploads/sign_8_1767539739.png', '2026-01-04 14:58:55'),
(9, 'Leo Gabrielle', 'leog@gmail.com', 'sirleogab', '$2y$10$4iJI7ekfDqWdFxmZx1Zg6eBTb0i4cgwMJV1KhsG4L/Urk6AYxAKge', 'Faculty', 'uploads/profile_9_1767539763.jpg', 'uploads/sign_9_1767539763.png', '2026-01-04 15:00:43'),
(10, 'Bryan Calimlim', 'bryan@gmail.com', 'calmbryan', '$2y$10$6kYdVCvJQL23cTUENaut8uk8bIbXQPM4U2zIlawA9lBE1esmyF9lW', 'Student', 'uploads/profile_10_1767539844.jpg', 'uploads/sign_10_1767539844.png', '2026-01-04 15:03:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `review_logs`
--
ALTER TABLE `review_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thesis_id` (`thesis_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `thesis`
--
ALTER TABLE `thesis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `adviser_id` (`adviser_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `review_logs`
--
ALTER TABLE `review_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `thesis`
--
ALTER TABLE `thesis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `programs_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_logs`
--
ALTER TABLE `review_logs`
  ADD CONSTRAINT `review_logs_ibfk_1` FOREIGN KEY (`thesis_id`) REFERENCES `thesis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_logs_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thesis`
--
ALTER TABLE `thesis`
  ADD CONSTRAINT `thesis_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `thesis_ibfk_2` FOREIGN KEY (`adviser_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `thesis_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
