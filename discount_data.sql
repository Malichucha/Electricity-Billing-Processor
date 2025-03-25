-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2025 at 08:41 AM
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
-- Database: `electricity_billing`
--

-- --------------------------------------------------------

--
-- Table structure for table `discount_data`
--

CREATE TABLE `discount_data` (
  `id` int(11) NOT NULL,
  `customer_id` varchar(50) DEFAULT NULL,
  `discount_rate` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount_data`
--

INSERT INTO `discount_data` (`id`, `customer_id`, `discount_rate`, `discount_amount`, `date`) VALUES
(5, 'CUS1234', 0.15, 70.50, '2025-01-01'),
(6, 'CUS1234', 0.15, 70.50, '2025-01-01'),
(7, 'CUS3456', 0.05, 4.10, '2025-04-26'),
(8, 'CUS3456', 0.05, 4.10, '2025-04-26'),
(9, 'CUS5678', 0.05, 5.75, '2025-02-16'),
(10, 'CUS5678', 0.05, 5.75, '2025-02-16'),
(11, 'CUS7890', 0.05, 4.00, '2025-07-12'),
(12, 'CUS7890', 0.05, 4.00, '2025-07-12'),
(13, 'CUS9012', 0.15, 63.15, '2025-09-01'),
(14, 'CUS9012', 0.15, 63.15, '2025-09-01'),
(15, 'CUS1234', 0.15, 90.00, '2025-02-16'),
(16, 'CUS1234', 0.15, 90.00, '2025-02-16'),
(17, 'CUS3456', 0.10, 23.00, '2025-04-26'),
(18, 'CUS3456', 0.10, 23.00, '2025-04-26'),
(19, 'CUS5678', 0.20, 160.00, '2025-02-16'),
(20, 'CUS5678', 0.20, 160.00, '2025-02-16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `discount_data`
--
ALTER TABLE `discount_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `discount_data`
--
ALTER TABLE `discount_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `discount_data`
--
ALTER TABLE `discount_data`
  ADD CONSTRAINT `discount_data_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
