-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 12:20 PM
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
-- Database: `unibot`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'General Inquiry', 'General questions from students', '2025-12-07 10:12:28'),
(2, 'Technical Issue', 'Website, portal, login issues', '2025-12-07 10:12:28'),
(3, 'Course Registration', 'Problems related to course selection', '2025-12-07 10:12:28'),
(4, 'Payment & Fees', 'Billing, fees, and financial aid issues', '2025-12-07 10:12:28'),
(5, 'Hostel & Accommodation', 'Campus room and housing issues', '2025-12-07 10:12:28'),
(6, 'conception logiciel', 'software engineering course', '2025-12-19 09:41:06');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_logs`
--

CREATE TABLE `chatbot_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `user_message` text NOT NULL,
  `bot_response` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender` enum('student','admin') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `sender`, `message`, `created_at`) VALUES
(1, 9, 'student', 'hi', '2025-12-18 21:34:36'),
(2, 9, 'student', 'hi', '2025-12-18 21:40:46');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `created_at`) VALUES
(11, 'i don\'t have a score for exam , what is the procedure ?', 'kindly leave your request , in a ticket with proof of your attendance', '2025-12-19 09:42:47');

-- --------------------------------------------------------

--
-- Table structure for table `faq_questions`
--

CREATE TABLE `faq_questions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL,
  `status` enum('pending','answered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq_questions`
--

INSERT INTO `faq_questions` (`id`, `student_id`, `question`, `answer`, `status`, `created_at`) VALUES
(1, 7, 'gng', NULL, 'pending', '2025-12-18 19:53:48');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `system_name` varchar(100) DEFAULT 'Unibot',
  `support_email` varchar(100) DEFAULT 'support@unibot.com',
  `timezone` varchar(50) DEFAULT 'Africa/Nairobi',
  `default_language` varchar(50) DEFAULT 'English',
  `confidence_threshold` float DEFAULT 0.6,
  `auto_escalation` tinyint(1) DEFAULT 1,
  `require_admin_2fa` tinyint(1) DEFAULT 1,
  `session_timeout` int(11) DEFAULT 30,
  `maintenance_mode` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `system_name`, `support_email`, `timezone`, `default_language`, `confidence_threshold`, `auto_escalation`, `require_admin_2fa`, `session_timeout`, `maintenance_mode`) VALUES
(1, 'Unibot', 'support@unibot.com', 'Africa/Nairobi', 'English', 0.6, 1, 1, 30, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `assigned_to` varchar(255) DEFAULT NULL,
  `status` enum('pending','in_progress','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `student_id`, `student_name`, `subject`, `category`, `assigned_to`, `status`, `created_at`, `category_id`, `description`, `attachment`) VALUES
(1, 7, 'almadikoume ', 'dffdz', 'Administrative Documents', NULL, 'pending', '2025-12-18 19:28:25', NULL, NULL, NULL),
(2, 9, 'dimi turner', 'i lost my diploma', 'Administrative Documents', NULL, 'pending', '2025-12-18 21:09:33', NULL, NULL, NULL),
(3, 9, 'dimi turner', 'hhfbg', 'Library / Resources', NULL, 'pending', '2025-12-18 21:17:50', NULL, 'fbgh', '1766092670_diagramme de sequence.png'),
(4, 9, 'dimi turner', 'i dont have a score for exam', 'Exam Results / Grades', NULL, 'pending', '2025-12-19 09:38:18', NULL, 'i was present during exams but i have no score for it', '1766137098_prolog.png');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_messages`
--

CREATE TABLE `ticket_messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `sender` enum('student','admin') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_messages`
--

INSERT INTO `ticket_messages` (`id`, `ticket_id`, `sender`, `message`, `created_at`) VALUES
(1, 1, 'admin', 'aasasa', '2025-12-18 20:38:09'),
(2, 2, 'admin', 'hi', '2025-12-18 21:10:18'),
(3, 3, 'admin', 'hi', '2025-12-18 21:40:25'),
(4, 3, 'admin', 'hi', '2025-12-18 21:55:15'),
(5, 3, 'student', 'hi', '2025-12-18 22:00:44'),
(6, 4, 'student', 'pleasei need help', '2025-12-19 09:38:43'),
(7, 4, 'admin', 'how can i help ?', '2025-12-19 09:39:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `role`, `created_at`, `status`) VALUES
(4, 'Admin', 'User', 'admin@example.com', '$2y$10$DnUdSVNeFW0wDXyvZBhOeuaCihs/cGqqD4OQ6HA5bFEU5nSJROaqi', 'admin', '2025-12-06 18:00:57', 'active'),
(6, 'almadikoume', '', 'almadikoum@gmail.com', '$2y$10$GkesyKrCnjg70N5a68HZJO.zLBHP6XbwTBfmi8PAGbkgBynLC0A9y', 'student', '2025-12-18 16:08:27', 'active'),
(7, 'almadikoume', '', 'almadikou@gmail.com', '$2y$10$O6KtDCgT6xnvuAjsU7QhzudODzhhMYNngYmkKSab3PQneujfvZ7je', 'student', '2025-12-18 16:20:30', 'active'),
(8, NULL, NULL, 'admin@unibot.com', '$2y$10$UyKg4vKGlVbEE8O.qW2G7ua3BxN3VIaD6UMbdMF1/G3awZov8uOBm', 'admin', '2025-12-18 20:42:08', 'active'),
(9, 'dimi', 'turner', 'dimiturner@gmail.com', '$2y$10$H.nMqRk4neIDnkQtH4Y3C.6VnYVur3k4V.xpditfianu/tQ6FqiyW', 'student', '2025-12-18 21:08:40', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_questions`
--
ALTER TABLE `faq_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ticket_category` (`category_id`);

--
-- Indexes for table `ticket_messages`
--
ALTER TABLE `ticket_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chatbot_logs`
--
ALTER TABLE `chatbot_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `faq_questions`
--
ALTER TABLE `faq_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ticket_messages`
--
ALTER TABLE `ticket_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_ticket_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
