-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2025 at 04:55 PM
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
-- Database: `billing_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(255) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer','billing processor','manager') DEFAULT NULL,
  `status` enum('Active','Inactive','','') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `password`, `role`, `status`, `created_at`) VALUES
('CUS1234', 'john.doe@example.com', 'JOHN DOE', '$2y$10$fyXD4IX2dUeZVzinELYHA.0FMZIvR5KqgQXAgrnQR9V7KBGL1AJdS', 'customer', 'Inactive', '2025-01-10 17:31:41'),
('CUS3456', 'jivenraaj.gobal@example.com', 'JIVENRAAJ GOBAL', '$2y$10$zhsJRO/1gba/MW4jSAa5KOlRiaDBja3BGgAx5eUQ.UkFlK9WDhj.K', 'customer', 'Inactive', '2025-02-08 15:49:01'),
('CUS5678', 'hari.varsan@example.com', 'HARI VARSAN', '$2y$10$NgQr6xaaF8fPe8FGmcVOd.cf1X7ExhZuxMhnZpU2C4wNByJIrd.gS', 'customer', 'Active', '2025-01-10 17:32:52'),
('CUS7890', 'rajahmae.sotta@example.com', 'RAJAHMAE SOTTA', '$2y$10$VCrdYKcYgl0a2SmsBC0Kku2t7AZ3zdxDDEPq/QcULtfhlXmct.7We', 'customer', 'Inactive', '2025-02-08 15:49:35'),
('CUS9012', 'amirah.shahul@example.com', 'AMIRAH SHAHUL', '$2y$10$rr2pRlTPsN2SP/hKc2HHVeZCYo29.e0ivWl2m89WbgoDMuyb.DvTa', 'customer', 'Inactive', '2025-01-10 17:34:00'),
('STAFF01', 'zeti.wiyada@example.com', 'ZETI WIYADA ', '$2y$10$cm9zRzS14pHOUOzSSn3SI.yE/SqaEDzrX46UahHYsp46yPmlAe6Uy', 'admin', 'Active', '2025-02-04 02:05:33'),
('STAFF02', 'amalia.sorfina@example.com', 'AMALIA SORFINA', '$2y$10$EIdlPhZT6RNreVuJ7dRwGevCxZx1KvOcSkZe/7rtdO2RS2yMB8yN2', 'admin', 'Active', '2025-01-10 17:53:42'),
('STAFF03', 'narmithaa.sureesh@example.com', 'NARMITHAA SUREESH', '$2y$10$yfkdzbtRmM83bvgeA3rHDu8WXUzXD/3FOXLuYrx2Jjm/0/w5B5ZsO', 'manager', 'Active', '2025-01-10 17:22:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
