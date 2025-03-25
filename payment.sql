SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create payment table
CREATE TABLE `payment` (
  `payment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_id` VARCHAR(50) NOT NULL,
  `outstanding_id` INT NOT NULL,
  `payment_amount` DECIMAL(10,2) NOT NULL,
  `payment_date` DATE NOT NULL,
  `status` ENUM('Paid', 'Pending', 'Failed') NOT NULL DEFAULT 'Pending',
  CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`outstanding_id`) REFERENCES `outstanding` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `payment` (`customer_id`, `outstanding_id`, `payment_amount`, `payment_date`, `status`) VALUES
('CUS1234', 1, 50.00, '2025-01-02', 'Paid'),
('CUS3456', 4, 50.00, '2025-06-05', 'Paid'),
('CUS5678', 5, 0.0, '2025-02-02', 'Pending'),
('CUS7890', 7, 10.00, '2025-07-02', 'Paid'),
('CUS9012', 9, 0.0, '2025-09-02', 'Pending');


COMMIT;