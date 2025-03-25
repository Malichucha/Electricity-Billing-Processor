-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2025 at 08:40 AM
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
-- Table structure for table `current_monthbill`
--

CREATE TABLE `current_monthbill` (
  `id` int(11) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `total_usage` int(11) NOT NULL,
  `tariff_rate` decimal(10,3) NOT NULL,
  `fixed_charge` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `bill_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `current_monthbill`
--

INSERT INTO `current_monthbill` (`id`, `customer_id`, `date`, `total_usage`, `tariff_rate`, `fixed_charge`, `discount_amount`, `bill_amount`) VALUES
(1, 'CUS1234', '2025-01-01', 470, 0.516, 3.00, 70.50, 175.02),
(2, 'CUS3456', '2025-04-26', 82, 0.218, 3.00, 4.10, 16.78),
(3, 'CUS5678', '2025-02-16', 115, 0.218, 3.00, 5.75, 22.32),
(4, 'CUS7890', '2025-07-12', 80, 0.218, 3.00, 4.00, 16.44),
(5, 'CUS9012', '2025-09-01', 421, 0.516, 3.00, 63.15, 157.09);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `current_monthbill`
--
ALTER TABLE `current_monthbill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `current_monthbill`
--
ALTER TABLE `current_monthbill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `current_monthbill`
--
ALTER TABLE `current_monthbill`
  ADD CONSTRAINT `current_monthbill_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
