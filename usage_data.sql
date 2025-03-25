-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2025 at 01:05 AM
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
-- Table structure for table `usage_data`
--

CREATE TABLE `usage_data` (
  `id` int(11) NOT NULL,
  `customer_id` varchar(50) DEFAULT NULL,
  `start_reading` int(11) DEFAULT NULL,
  `end_reading` int(11) DEFAULT NULL,
  `total_usage` int(11) DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usage_data`
--

INSERT INTO `usage_data` (`id`, `customer_id`, `start_reading`, `end_reading`, `total_usage`, `date`) VALUES
(1, 'CUS1234', 30, 500, 470, '2025-01-01'),
(2, 'CUS3456', 8, 90, 82, '2025-04-26'),
(3, 'CUS7890', 15, 95, 80, '2025-07-12'),
(4, 'CUS9012', 12, 433, 421, '2025-09-01'),
(5, 'CUS5678', 20, 135, 115, '2025-02-16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `usage_data`
--
ALTER TABLE `usage_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `usage_data`
--
ALTER TABLE `usage_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `usage_data`
--
ALTER TABLE `usage_data`
  ADD CONSTRAINT `usage_data_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
