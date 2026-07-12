-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2026 at 12:28 PM
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
-- Database: `inventory_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `reference_no` varchar(20) NOT NULL,
  `status` enum('Pending','Received') DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `received_by` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `reference_no`, `status`, `payment_method`, `amount_paid`, `created_at`, `received_by`) VALUES
(1, 'PO-20260712-0001', 'Received', NULL, 0.00, '2026-07-01 08:50:37', 'Admin'),
(2, 'PO-20260712-0002', 'Received', NULL, 0.00, '2026-07-02 08:58:29', 'Admin'),
(3, 'PO-20260712-0003', 'Received', NULL, 0.00, '2026-07-06 08:58:39', 'Admin'),
(4, 'PO-20260712-0004', 'Received', NULL, 0.00, '2026-07-06 08:58:55', 'Admin'),
(5, 'PO-20260712-0005', 'Received', NULL, 0.00, '2026-07-08 08:59:06', 'Admin'),
(6, 'PO-20260712-0006', 'Received', NULL, 0.00, '2026-07-08 08:59:28', 'Admin'),
(7, 'PO-20260712-0007', 'Received', NULL, 0.00, '2026-07-09 09:42:05', 'Admin'),
(8, 'PO-20260712-0008', 'Received', NULL, 0.00, '2026-07-12 10:04:27', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
