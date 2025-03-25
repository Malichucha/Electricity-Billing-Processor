-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2025 at 01:52 AM
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
-- Table structure for table `outstanding`
--

CREATE TABLE `outstanding` (
  `id` int(11) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `current_month_bill` decimal(10,2) NOT NULL,
  `outstanding_balance` decimal(10,2) NOT NULL,
  `final_bill_with_outstanding` decimal(10,2) NOT NULL,
  `final_bill_without_outstanding` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `outstanding`
--

INSERT INTO `outstanding` (`id`, `customer_id`, `date`, `current_month_bill`, `outstanding_balance`, `final_bill_with_outstanding`, `final_bill_without_outstanding`) VALUES
(1, 'CUS1234', '2025-01-01', 175.02, 50.00, 225.02, 175.02),
(2, 'CUS3456', '2025-04-01', 16.78, 0.00, 16.78, 16.78),
(3, 'CUS7890', '2025-07-01', 16.44, 10.00, 26.44, 16.44),
(4, 'CUS9012', '2025-09-01', 157.09, 20.00, 177.09, 157.09),
(5, 'CUS5678', '2025-02-01', 22.32, 40.00, 62.32, 22.32),
(6, 'CUS1234', '2025-03-01', 455.62, 0.00, 455.62, 455.62),
(7, 'CUS3456', '2025-06-01', 21.48, 50.00, 71.48, 21.48),
(8, 'CUS7890', '2025-09-01', 33.24, 0.00, 33.24, 33.24),
(9, 'CUS9012', '2025-11-01', 399.97, 0.00, 399.97, 399.97),
(10, 'CUS5678', '2025-04-01', 36.60, 0.00, 36.60, 36.60);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `outstanding`
--
ALTER TABLE `outstanding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `outstanding`
--
ALTER TABLE `outstanding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `outstanding`
--
ALTER TABLE `outstanding`
  ADD CONSTRAINT `outstanding_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
