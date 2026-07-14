-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2026 at 11:54 AM
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
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Input Devices', '2026-05-18 05:44:33'),
(2, 'Audio Devices', '2026-05-18 05:44:33'),
(3, 'Storage Devices', '2026-05-18 05:44:33'),
(4, 'Output Devices', '2026-05-18 05:44:33'),
(5, 'Other Devices', '2026-05-18 05:50:19'),
(6, 'Connection Devices', '2026-05-23 08:08:21'),
(7, 'Projector Devices', '2026-05-18 05:53:33');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `old_stock` int(11) DEFAULT NULL,
  `new_stock` int(11) DEFAULT NULL,
  `changed_by` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `product_id`, `product_name`, `action_type`, `old_stock`, `new_stock`, `changed_by`, `created_at`) VALUES
(1, 1, 'Projector', 'Restocked', 0, 20, 'Admin', '2026-07-12 08:50:42'),
(2, 5, 'Speaker', 'Restocked', 0, 20, 'Admin', '2026-07-12 08:59:14'),
(3, 4, 'SSD', 'Restocked', 0, 20, 'Admin', '2026-07-12 08:59:16'),
(4, 3, 'Headphone', 'Restocked', 0, 20, 'Admin', '2026-07-12 08:59:18'),
(5, 2, 'SD Card', 'Restocked', 0, 20, 'Admin', '2026-07-12 08:59:21'),
(6, 6, 'USB Hub', 'Restocked', 0, 20, 'Admin', '2026-07-12 08:59:36'),
(7, 1, 'Projector', 'Restocked', 10, 15, 'Admin', '2026-07-12 09:42:25'),
(8, 1, 'Projector', 'Restocked', 15, 20, 'Admin', '2026-07-12 10:04:34'),
(9, 5, 'Speaker', 'Edited', 0, 10, 'Admin', '2026-07-12 13:06:46'),
(10, 7, 'ASUS Gaming Monitor', 'Added', NULL, 12, 'Admin', '2026-07-12 13:23:41'),
(11, NULL, 'SSD', 'Deleted', 3, NULL, 'Admin', '2026-07-12 15:42:15'),
(12, 8, 'SSD', 'Added', NULL, 19, 'Admin', '2026-07-12 15:42:58'),
(13, 1, 'Projector', 'Restocked', 2, 3, 'Admin', '2026-07-12 16:04:57'),
(14, 3, 'Headphone', 'Restocked', 0, 20, 'Admin', '2026-07-12 16:24:33'),
(15, 5, 'Speaker', 'Restocked', 3, 4, 'Admin', '2026-07-12 16:35:28'),
(16, 2, 'SD Card', 'Restocked', 2, 22, 'Admin', '2026-07-12 16:59:07'),
(17, 6, 'USB Hub', 'Edited', 20, 3, 'Admin', '2026-07-12 17:00:15'),
(18, 6, 'USB Hub', 'Edited', 3, 3, 'Admin', '2026-07-12 17:00:18'),
(19, 1, 'Projector', 'Restocked', 3, 6, 'Admin', '2026-07-12 17:11:15'),
(20, 2, 'SD Card', 'Edited', 22, 3, 'Admin', '2026-07-12 17:11:51'),
(21, 2, 'SD Card', 'Restocked', 3, 23, 'Admin', '2026-07-13 11:08:44'),
(22, 7, 'ASUS Gaming Monitor', 'Restocked', 3, 23, 'Admin', '2026-07-13 11:10:52'),
(23, 7, 'ASUS Gaming Monitor', 'Edited', 23, 3, 'Admin', '2026-07-13 11:12:02'),
(24, 2, 'SD Card', 'Edited', 2, 10, 'Admin', '2026-07-13 11:13:16'),
(25, 2, 'SD Card', 'Edited', 10, 6, 'Admin', '2026-07-13 11:13:20'),
(26, 7, 'ASUS Gaming Monitor', 'Restocked', 3, 23, 'Admin', '2026-07-13 11:19:07'),
(27, 1, 'Projector', 'Restocked', 2, 22, 'Admin', '2026-07-13 11:41:58'),
(28, 1, 'Projector', 'Restocked', 1, 21, 'Admin', '2026-07-13 12:06:02'),
(29, 2, 'SD Card', 'Restocked', 3, 23, 'Admin', '2026-07-13 12:06:02'),
(30, 3, 'Headphone', 'Restocked', 3, 23, 'Admin', '2026-07-13 12:06:02'),
(31, 5, 'Speaker', 'Restocked', 3, 23, 'Admin', '2026-07-13 12:06:02'),
(32, 6, 'USB Hub', 'Restocked', 3, 23, 'Admin', '2026-07-13 12:06:02'),
(33, 7, 'ASUS Gaming Monitor', 'Restocked', 3, 23, 'Admin', '2026-07-13 12:06:02'),
(34, 8, 'SSD', 'Restocked', 0, 20, 'Admin', '2026-07-13 12:06:02'),
(35, 1, 'Projector', 'Restocked', 2, 22, 'Admin', '2026-07-13 12:08:29'),
(36, 7, 'ASUS Gaming Monitor', 'Edited', 23, 3, 'Admin', '2026-07-13 12:09:08'),
(37, 7, 'ASUS Gaming Monitor', 'Restocked', 3, 23, 'Admin', '2026-07-13 12:09:47'),
(38, 2, 'SD Card', 'Edited', 23, 3, 'Admin', '2026-07-13 13:31:24'),
(39, 2, 'SD Card', 'Restocked', 3, 23, 'Admin', '2026-07-13 13:32:31'),
(40, 7, 'ASUS Gaming Monitor', 'Edited', 23, 3, 'Admin', '2026-07-13 14:01:46'),
(41, 7, 'ASUS Gaming Monitor', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:02:04'),
(42, 8, 'SSD', 'Edited', 20, 3, 'Admin', '2026-07-13 14:04:04'),
(43, 2, 'SD Card', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:07:57'),
(44, 2, 'SD Card', 'Edited', 23, 3, 'Admin', '2026-07-13 14:08:55'),
(45, 2, 'SD Card', 'Edited', 3, 3, 'Admin', '2026-07-13 14:09:10'),
(46, 1, 'Projector', 'Edited', 22, 3, 'Admin', '2026-07-13 14:09:39'),
(47, 1, 'Projector', 'Edited', 3, 3, 'Admin', '2026-07-13 14:09:56'),
(48, 5, 'Speaker', 'Edited', 23, 3, 'Admin', '2026-07-13 14:10:26'),
(49, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:27'),
(50, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:27'),
(51, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:27'),
(52, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:27'),
(53, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:27'),
(54, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:27'),
(55, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:27'),
(56, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:28'),
(57, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:28'),
(58, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:28'),
(59, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:28'),
(60, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:28'),
(61, 5, 'Speaker', 'Edited', 3, 3, 'Admin', '2026-07-13 14:10:28'),
(62, 7, 'ASUS Gaming Monitor', 'Edited', 23, 3, 'Admin', '2026-07-13 14:11:41'),
(63, 5, 'Speaker', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:13:20'),
(64, 7, 'ASUS Gaming Monitor', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:13:20'),
(65, 1, 'Projector', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:13:27'),
(66, 2, 'SD Card', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:13:27'),
(67, 3, 'Headphone', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:13:27'),
(68, 6, 'USB Hub', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:13:27'),
(69, 8, 'SSD', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:13:27'),
(70, 1, 'Projector', 'Edited', 23, 3, 'Admin', '2026-07-13 14:13:48'),
(71, 1, 'Projector', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:20:24'),
(72, 2, 'SD Card', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:20:24'),
(73, 3, 'Headphone', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:20:24'),
(74, 5, 'Speaker', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:20:24'),
(75, 6, 'USB Hub', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:20:24'),
(76, 7, 'ASUS Gaming Monitor', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:20:24'),
(77, 8, 'SSD', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:20:24'),
(78, 1, 'Projector', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:22:20'),
(79, 2, 'SD Card', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:22:20'),
(80, 3, 'Headphone', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:22:20'),
(81, 5, 'Speaker', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:22:20'),
(82, 6, 'USB Hub', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:22:20'),
(83, 7, 'ASUS Gaming Monitor', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:22:20'),
(84, 8, 'SSD', 'Restocked', 3, 23, 'Admin', '2026-07-13 14:22:20'),
(85, 1, 'Projector', 'Restocked', 0, 20, 'Admin', '2026-07-13 16:10:41'),
(86, 2, 'SD Card', 'Restocked', 0, 20, 'Admin', '2026-07-13 16:10:41'),
(87, 3, 'Headphone', 'Restocked', 0, 20, 'Admin', '2026-07-13 16:10:41'),
(88, 5, 'Speaker', 'Restocked', 3, 23, 'Admin', '2026-07-13 16:10:41'),
(89, 6, 'USB Hub', 'Restocked', 3, 23, 'Admin', '2026-07-13 16:10:41'),
(90, 7, 'ASUS Gaming Monitor', 'Restocked', 3, 23, 'Admin', '2026-07-13 16:10:41'),
(91, 8, 'SSD', 'Restocked', 3, 23, 'Admin', '2026-07-13 16:10:41'),
(92, 1, 'Projector', 'Restocked', 3, 23, 'Admin', '2026-07-13 16:24:21'),
(93, 2, 'SD Card', 'Restocked', 0, 20, 'Admin', '2026-07-13 16:24:21'),
(94, 1, 'Projector', 'Restocked', 0, 20, 'Admin', '2026-07-13 16:27:36'),
(95, 2, 'SD Card', 'Restocked', 0, 20, 'Admin', '2026-07-13 16:27:36'),
(96, 3, 'Headphone', 'Restocked', 0, 20, 'Admin', '2026-07-13 16:27:36'),
(97, 5, 'Speaker', 'Restocked', 0, 20, 'Admin', '2026-07-13 16:27:36'),
(98, 6, 'USB Hub', 'Restocked', 0, 20, 'Admin', '2026-07-13 16:27:36');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_no` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('Cash','Card','GCash','Maya') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cash_received` decimal(10,2) DEFAULT 0.00,
  `change_amount` decimal(10,2) DEFAULT 0.00,
  `cost_of_goods_sold` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_no`, `user_id`, `total_amount`, `discount_amount`, `payment_method`, `created_at`, `cash_received`, `change_amount`, `cost_of_goods_sold`) VALUES
(1, '0001', 1, 6000.00, 0.00, 'Cash', '2026-07-06 08:50:55', 6000.00, 0.00, 5000.00),
(2, '0002', 1, 9000.00, 0.00, 'Cash', '2026-07-08 08:51:01', 9000.00, 0.00, 7500.00),
(3, '0003', 1, 3000.00, 0.00, 'Cash', '2026-07-11 08:51:07', 3000.00, 0.00, 2500.00),
(4, '0004', 1, 12000.00, 0.00, 'Cash', '2026-07-12 08:51:14', 12000.00, 0.00, 10000.00),
(5, '0005', 1, 500.00, 0.00, 'Cash', '2026-07-12 10:26:26', 500.00, 0.00, 350.00),
(6, '0006', 1, 3000.00, 0.00, 'Cash', '2026-07-12 10:26:34', 3000.00, 0.00, 2200.00),
(7, '0007', 1, 1200.00, 0.00, 'Cash', '2026-07-12 10:26:39', 1200.00, 0.00, 800.00),
(8, '0008', 1, 4000.00, 0.00, 'Cash', '2026-07-12 10:26:53', 4000.00, 0.00, 3000.00),
(9, '0009', 1, 36000.00, 0.00, 'Card', '2026-07-12 12:29:25', 36000.00, 0.00, 27000.00),
(10, '0010', 1, 48000.00, 0.00, 'Cash', '2026-07-12 15:40:56', 49000.00, 1000.00, 35200.00),
(12, '0011', 1, 204.00, 0.00, 'Maya', '2026-07-12 15:43:33', 204.00, 0.00, 204.00),
(13, '0012', 1, 12.00, 0.00, 'Cash', '2026-07-12 15:50:17', 15.00, 3.00, 12.00),
(14, '0013', 1, 2012.00, 0.00, 'Cash', '2026-07-12 15:53:11', 2100.00, 88.00, 2012.00),
(15, '0014', 1, 51000.00, 0.00, 'Cash', '2026-07-12 15:59:42', 52000.00, 1000.00, 42500.00),
(16, '0015', 1, 3000.00, 0.00, 'Cash', '2026-07-12 16:02:28', 40000.00, 37000.00, 2500.00),
(17, '0016', 1, 8500.00, 0.00, 'Cash', '2026-07-12 16:05:57', 9000.00, 500.00, 5950.00),
(18, '0017', 1, 108.00, 0.00, 'Cash', '2026-07-12 16:06:43', 200.00, 92.00, 108.00),
(19, '0018', 1, 12000.00, 0.00, 'Cash', '2026-07-12 16:15:33', 12000.00, 0.00, 12000.00),
(20, '0019', 1, 22800.00, 0.00, 'Cash', '2026-07-12 16:16:25', 23000.00, 200.00, 15200.00),
(21, '0020', 1, 20400.00, 0.00, 'Cash', '2026-07-12 16:40:39', 21000.00, 600.00, 13600.00),
(22, '0021', 1, 2000.00, 0.00, 'Cash', '2026-07-12 17:01:09', 2000.00, 0.00, 2000.00),
(23, '0022', 1, 12000.00, 0.00, 'Cash', '2026-07-12 17:12:17', 12000.00, 0.00, 10000.00),
(24, '0023', 1, 10500.00, 0.00, 'Cash', '2026-07-13 11:10:16', 11000.00, 500.00, 7350.00),
(25, '0024', 1, 1500.00, 0.00, 'Maya', '2026-07-13 11:14:48', 1500.00, 0.00, 1500.00),
(26, '0025', 1, 240.00, 0.00, 'Cash', '2026-07-13 11:20:14', 250.00, 10.00, 240.00),
(27, '0026', 1, 63000.00, 0.00, 'Cash', '2026-07-13 11:51:06', 63000.00, 0.00, 52500.00),
(28, '0027', 1, 57000.00, 0.00, 'GCash', '2026-07-13 12:06:42', 57000.00, 0.00, 47500.00),
(29, '0028', 1, 44000.00, 0.00, 'Cash', '2026-07-13 14:04:57', 44000.00, 0.00, 29000.00),
(31, '0029', 1, 84480.00, 0.00, 'Maya', '2026-07-13 14:17:15', 84480.00, 0.00, 59480.00),
(32, '0030', 1, 144480.00, 0.00, 'GCash', '2026-07-13 14:21:14', 144480.00, 0.00, 109480.00),
(33, '0031', 1, 158580.00, 0.00, 'Maya', '2026-07-13 14:24:26', 158580.00, 0.00, 120430.00),
(34, '0032', 1, 51000.00, 0.00, 'Cash', '2026-07-13 16:11:06', 51000.00, 0.00, 42500.00),
(36, '0033', 1, 10000.00, 0.00, 'Cash', '2026-07-13 16:23:19', 10000.00, 0.00, 7000.00),
(39, '0034', 1, 90100.00, 0.00, 'Cash', '2026-07-13 16:26:12', 100000.00, 9900.00, 68400.00),
(40, '0035', 1, 70400.00, 0.00, 'Cash', '2026-07-13 16:26:59', 100000.00, 29600.00, 53500.00),
(41, '0036', 5, 6000.00, 0.00, 'Cash', '2026-07-14 04:53:43', 6000.00, 0.00, 5000.00),
(42, '0037', 5, 2000.00, 0.00, 'Cash', '2026-07-14 05:03:37', 2000.00, 0.00, 1500.00),
(43, '0038', 1, 36.00, 0.00, 'Cash', '2026-07-14 05:03:54', 36.00, 0.00, 36.00),
(44, '0039', 5, 2400.00, 0.00, 'Cash', '2026-07-14 05:06:58', 2500.00, 100.00, 1600.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_sale` decimal(10,2) NOT NULL,
  `cost_of_goods_sold` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_at_sale`, `cost_of_goods_sold`) VALUES
(1, 1, 1, 2, 6000.00, 5000.00),
(2, 2, 1, 3, 9000.00, 7500.00),
(3, 3, 1, 1, 3000.00, 2500.00),
(4, 4, 1, 4, 12000.00, 10000.00),
(5, 5, 2, 1, 500.00, 350.00),
(7, 7, 3, 1, 1200.00, 800.00),
(8, 8, 5, 2, 4000.00, 3000.00),
(9, 9, 5, 18, 36000.00, 27000.00),
(11, 12, 8, 17, 204.00, 204.00),
(12, 13, 8, 1, 12.00, 12.00),
(13, 14, 5, 1, 2000.00, 2000.00),
(14, 14, 8, 1, 12.00, 12.00),
(15, 15, 1, 17, 51000.00, 42500.00),
(16, 16, 1, 1, 3000.00, 2500.00),
(17, 17, 2, 17, 8500.00, 5950.00),
(18, 18, 7, 9, 108.00, 108.00),
(19, 19, 5, 6, 12000.00, 12000.00),
(20, 20, 3, 19, 22800.00, 15200.00),
(21, 21, 3, 17, 20400.00, 13600.00),
(22, 22, 5, 1, 2000.00, 2000.00),
(23, 23, 1, 4, 12000.00, 10000.00),
(24, 24, 2, 21, 10500.00, 7350.00),
(25, 25, 2, 3, 1500.00, 1500.00),
(26, 26, 7, 20, 240.00, 240.00),
(27, 27, 1, 21, 63000.00, 52500.00),
(28, 28, 1, 19, 57000.00, 47500.00),
(29, 29, 2, 20, 10000.00, 7000.00),
(30, 29, 3, 20, 24000.00, 16000.00),
(31, 29, 6, 20, 10000.00, 6000.00),
(32, 31, 2, 20, 10000.00, 7000.00),
(33, 31, 3, 20, 24000.00, 16000.00),
(34, 31, 5, 20, 40000.00, 30000.00),
(35, 31, 6, 20, 10000.00, 6000.00),
(36, 31, 7, 20, 240.00, 240.00),
(37, 31, 8, 20, 240.00, 240.00),
(38, 32, 1, 20, 60000.00, 50000.00),
(39, 32, 2, 20, 10000.00, 7000.00),
(40, 32, 3, 20, 24000.00, 16000.00),
(41, 32, 5, 20, 40000.00, 30000.00),
(42, 32, 6, 20, 10000.00, 6000.00),
(43, 32, 7, 20, 240.00, 240.00),
(44, 32, 8, 20, 240.00, 240.00),
(45, 33, 1, 23, 69000.00, 57500.00),
(46, 33, 2, 23, 11500.00, 8050.00),
(47, 33, 3, 23, 27600.00, 18400.00),
(48, 33, 5, 20, 40000.00, 30000.00),
(49, 33, 6, 20, 10000.00, 6000.00),
(50, 33, 7, 20, 240.00, 240.00),
(51, 33, 8, 20, 240.00, 240.00),
(52, 34, 1, 17, 51000.00, 42500.00),
(53, 36, 2, 20, 10000.00, 7000.00),
(54, 39, 1, 9, 27000.00, 22500.00),
(55, 39, 2, 10, 5000.00, 3500.00),
(56, 39, 3, 8, 9600.00, 6400.00),
(57, 39, 5, 23, 46000.00, 34500.00),
(58, 39, 6, 5, 2500.00, 1500.00),
(59, 40, 1, 14, 42000.00, 35000.00),
(60, 40, 2, 10, 5000.00, 3500.00),
(61, 40, 3, 12, 14400.00, 9600.00),
(62, 40, 6, 18, 9000.00, 5400.00),
(63, 41, 1, 2, 6000.00, 5000.00),
(64, 42, 5, 1, 2000.00, 1500.00),
(65, 43, 7, 3, 36.00, 36.00),
(66, 44, 3, 2, 2400.00, 1600.00);

-- --------------------------------------------------------

--
-- Table structure for table `po_items`
--

CREATE TABLE `po_items` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_qty` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `po_items`
--

INSERT INTO `po_items` (`id`, `po_id`, `product_id`, `order_qty`, `unit_cost`) VALUES
(1, 1, 1, 20, 2500.00),
(2, 2, 2, 20, 350.00),
(3, 3, 3, 20, 800.00),
(5, 5, 5, 20, 1500.00),
(6, 6, 6, 20, 300.00),
(7, 7, 1, 5, 2500.00),
(8, 8, 1, 5, 2500.00),
(9, 9, 1, 1, 2500.00),
(10, 10, 3, 20, 800.00),
(11, 11, 5, 1, 1500.00),
(12, 12, 2, 20, 350.00),
(13, 13, 1, 3, 2500.00),
(14, 14, 2, 20, 350.00),
(15, 15, 7, 20, 12.00),
(16, 16, 7, 20, 12.00),
(17, 17, 1, 20, 2500.00),
(18, 18, 1, 20, 2500.00),
(19, 18, 2, 20, 350.00),
(20, 18, 3, 20, 800.00),
(21, 18, 5, 20, 1500.00),
(22, 18, 6, 20, 300.00),
(23, 18, 7, 20, 12.00),
(24, 18, 8, 20, 12.00),
(25, 19, 1, 20, 2500.00),
(26, 20, 7, 20, 12.00),
(27, 21, 2, 20, 350.00),
(28, 22, 7, 20, 12.00),
(29, 23, 2, 20, 350.00),
(30, 24, 1, 20, 2500.00),
(31, 24, 2, 20, 350.00),
(32, 24, 3, 20, 800.00),
(33, 24, 6, 20, 300.00),
(34, 24, 8, 20, 12.00),
(35, 25, 5, 20, 1500.00),
(36, 25, 7, 20, 12.00),
(37, 26, 1, 20, 2500.00),
(38, 26, 2, 20, 350.00),
(39, 26, 3, 20, 800.00),
(40, 26, 5, 20, 1500.00),
(41, 26, 6, 20, 300.00),
(42, 26, 7, 20, 12.00),
(43, 26, 8, 20, 12.00),
(44, 27, 1, 20, 2500.00),
(45, 27, 2, 20, 350.00),
(46, 27, 3, 20, 800.00),
(47, 27, 5, 20, 1500.00),
(48, 27, 6, 20, 300.00),
(49, 27, 7, 20, 12.00),
(50, 27, 8, 20, 12.00),
(51, 28, 1, 20, 2500.00),
(52, 28, 2, 20, 350.00),
(53, 28, 3, 20, 800.00),
(54, 28, 5, 20, 1500.00),
(55, 28, 6, 20, 300.00),
(56, 28, 7, 20, 12.00),
(57, 28, 8, 20, 12.00),
(58, 29, 1, 20, 2500.00),
(59, 29, 2, 20, 350.00),
(60, 30, 1, 20, 2500.00),
(61, 30, 2, 20, 350.00),
(62, 30, 3, 20, 800.00),
(63, 30, 5, 20, 1500.00),
(64, 30, 6, 20, 300.00),
(65, 31, 5, 20, 1500.00),
(66, 32, 8, 20, 12.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price_bought` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT 'default_product.png',
  `model_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `capacity_size` varchar(100) DEFAULT NULL,
  `resolution` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `price_bought`, `price`, `stock`, `created_at`, `image`, `model_path`, `description`, `brand`, `color`, `type`, `capacity_size`, `resolution`) VALUES
(1, 'Projector', 4, 2500.00, 3000.00, 18, '2026-07-12 08:01:09', '1783843269_e9b13a5fad644396.png', '../Models/1783843269_577cace3.glb', 'A device that displays images and videos onto a larger screen for presentations and entertainment.', 'Epson', 'White', 'Display Device', '', '1920x1080'),
(2, 'SD Card', 3, 350.00, 500.00, 20, '2026-07-12 08:02:24', '1783843344_5246dc872c12a8ef.png', '../Models/1783843344_a5149ef9.glb', 'A portable storage device used for saving photos, videos, and digital files.', 'SanDisk', 'Black', 'Storage Device', '128GB', ''),
(3, 'Headphone', 2, 800.00, 1200.00, 18, '2026-07-12 08:04:02', '1783843442_e12aa7fd7e6dfef6.png', '../Models/1783843442_6db03d66.glb', 'An audio device that provides private listening for music, calls, and multimedia.', 'Logitech', 'Black', 'Audio Device', '', ''),
(5, 'Speaker', 2, 1500.00, 2000.00, 19, '2026-07-12 08:05:46', '1783843546_2aefdb601eaee67b.png', '../Models/1783843546_e9305ae2.glb', 'An audio output device used to play music, videos, and other sounds clearly.', 'Logitech', 'Black', 'Audio Device', '', ''),
(6, 'USB Hub', 6, 300.00, 500.00, 20, '2026-07-12 08:06:48', '1783843608_7f332d1bf9258b08.png', '../Models/1783843608_736a9825.glb', 'A device that expands a computer\'s USB ports for connecting multiple peripherals.', 'UGREEN', 'Gray', 'Connection Device', '', ''),
(7, 'ASUS Gaming Monitor', 4, 12.00, 12.00, 20, '2026-07-12 13:23:41', '1783862621_2c5f849f226107f8.png', '../Models/1783862621_d94db461.glb', 'nigga', 'ASUS', 'Black', 'Gaming Monitor', '', '3840 x 2160'),
(8, 'SSD', 3, 12.00, 12.00, 23, '2026-07-12 15:42:58', '1783870978_59dbcb815e1ae84f.png', '../Models/1783870978_e20c2f94.glb', 'nigga', 'ASUS', 'Black', 'niga', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `product_batches`
--

CREATE TABLE `product_batches` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_received` int(11) NOT NULL,
  `quantity_remaining` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_batches`
--

INSERT INTO `product_batches` (`id`, `product_id`, `quantity_received`, `quantity_remaining`, `unit_cost`, `created_at`) VALUES
(1, 1, 20, 0, 2500.00, '2026-07-12 08:50:42'),
(2, 5, 20, 0, 1500.00, '2026-07-12 08:59:14'),
(4, 3, 20, 0, 800.00, '2026-07-12 08:59:18'),
(5, 2, 20, 0, 350.00, '2026-07-12 08:59:21'),
(6, 6, 20, 0, 300.00, '2026-07-12 08:59:36'),
(7, 1, 5, 0, 2500.00, '2026-07-12 09:42:25'),
(8, 1, 5, 0, 2500.00, '2026-07-12 10:04:34'),
(9, 5, 10, 0, 2000.00, '2026-07-12 13:06:46'),
(10, 7, 12, 0, 12.00, '2026-07-12 13:23:41'),
(11, 8, 19, 0, 12.00, '2026-07-12 15:42:58'),
(12, 1, 1, 0, 2500.00, '2026-07-12 16:04:57'),
(13, 3, 20, 0, 800.00, '2026-07-12 16:24:33'),
(14, 5, 1, 0, 1500.00, '2026-07-12 16:35:28'),
(15, 2, 20, 0, 350.00, '2026-07-12 16:59:07'),
(16, 1, 3, 0, 2500.00, '2026-07-12 17:11:15'),
(17, 2, 20, 0, 350.00, '2026-07-13 11:08:44'),
(18, 7, 20, 0, 12.00, '2026-07-13 11:10:52'),
(19, 2, 8, 0, 500.00, '2026-07-13 11:13:16'),
(20, 7, 20, 0, 12.00, '2026-07-13 11:19:07'),
(21, 1, 20, 0, 2500.00, '2026-07-13 11:41:58'),
(22, 1, 20, 0, 2500.00, '2026-07-13 12:06:02'),
(23, 2, 20, 0, 350.00, '2026-07-13 12:06:02'),
(24, 3, 20, 0, 800.00, '2026-07-13 12:06:02'),
(25, 5, 20, 0, 1500.00, '2026-07-13 12:06:02'),
(26, 6, 20, 0, 300.00, '2026-07-13 12:06:02'),
(27, 7, 20, 0, 12.00, '2026-07-13 12:06:02'),
(28, 8, 20, 0, 12.00, '2026-07-13 12:06:02'),
(29, 1, 20, 0, 2500.00, '2026-07-13 12:08:29'),
(30, 7, 20, 0, 12.00, '2026-07-13 12:09:47'),
(31, 2, 20, 0, 350.00, '2026-07-13 13:32:31'),
(32, 7, 20, 0, 12.00, '2026-07-13 14:02:04'),
(33, 2, 20, 0, 350.00, '2026-07-13 14:07:57'),
(34, 5, 20, 0, 1500.00, '2026-07-13 14:13:20'),
(35, 7, 20, 0, 12.00, '2026-07-13 14:13:20'),
(36, 1, 20, 0, 2500.00, '2026-07-13 14:13:27'),
(37, 2, 20, 0, 350.00, '2026-07-13 14:13:27'),
(38, 3, 20, 0, 800.00, '2026-07-13 14:13:27'),
(39, 6, 20, 0, 300.00, '2026-07-13 14:13:27'),
(40, 8, 20, 0, 12.00, '2026-07-13 14:13:27'),
(41, 1, 20, 0, 2500.00, '2026-07-13 14:20:24'),
(42, 2, 20, 0, 350.00, '2026-07-13 14:20:24'),
(43, 3, 20, 0, 800.00, '2026-07-13 14:20:24'),
(44, 5, 20, 0, 1500.00, '2026-07-13 14:20:24'),
(45, 6, 20, 0, 300.00, '2026-07-13 14:20:24'),
(46, 7, 20, 0, 12.00, '2026-07-13 14:20:24'),
(47, 8, 20, 0, 12.00, '2026-07-13 14:20:24'),
(48, 1, 20, 0, 2500.00, '2026-07-13 14:22:20'),
(49, 2, 20, 0, 350.00, '2026-07-13 14:22:20'),
(50, 3, 20, 0, 800.00, '2026-07-13 14:22:20'),
(51, 5, 20, 0, 1500.00, '2026-07-13 14:22:20'),
(52, 6, 20, 0, 300.00, '2026-07-13 14:22:20'),
(53, 7, 20, 0, 12.00, '2026-07-13 14:22:20'),
(54, 8, 20, 3, 12.00, '2026-07-13 14:22:20'),
(55, 1, 20, 0, 2500.00, '2026-07-13 16:10:41'),
(56, 2, 20, 0, 350.00, '2026-07-13 16:10:41'),
(57, 3, 20, 0, 800.00, '2026-07-13 16:10:41'),
(58, 5, 20, 0, 1500.00, '2026-07-13 16:10:41'),
(59, 6, 20, 0, 300.00, '2026-07-13 16:10:41'),
(60, 7, 20, 20, 12.00, '2026-07-13 16:10:41'),
(61, 8, 20, 20, 12.00, '2026-07-13 16:10:41'),
(62, 1, 20, 0, 2500.00, '2026-07-13 16:24:21'),
(63, 2, 20, 0, 350.00, '2026-07-13 16:24:21'),
(64, 1, 20, 18, 2500.00, '2026-07-13 16:27:36'),
(65, 2, 20, 20, 350.00, '2026-07-13 16:27:36'),
(66, 3, 20, 18, 800.00, '2026-07-13 16:27:36'),
(67, 5, 20, 19, 1500.00, '2026-07-13 16:27:36'),
(68, 6, 20, 20, 300.00, '2026-07-13 16:27:36');

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
(8, 'PO-20260712-0008', 'Received', NULL, 0.00, '2026-07-12 10:04:27', 'Admin'),
(9, 'PO-20260712-0009', 'Received', NULL, 0.00, '2026-07-12 16:04:52', 'Admin'),
(10, 'PO-20260712-0010', 'Received', NULL, 0.00, '2026-07-12 16:24:28', 'Admin'),
(11, 'PO-20260712-0011', 'Received', NULL, 0.00, '2026-07-12 16:35:23', 'Admin'),
(12, 'PO-20260712-0012', 'Received', NULL, 0.00, '2026-07-12 16:58:47', 'Admin'),
(13, 'PO-20260712-0013', 'Received', NULL, 0.00, '2026-07-12 17:11:08', 'Admin'),
(14, 'PO-20260713-0014', 'Received', NULL, 0.00, '2026-07-13 11:08:40', 'Admin'),
(15, 'PO-20260713-0015', 'Received', NULL, 0.00, '2026-07-13 11:10:46', 'Admin'),
(16, 'PO-20260713-0016', 'Received', NULL, 0.00, '2026-07-13 11:19:00', 'Admin'),
(17, 'PO-20260713-0017', 'Received', NULL, 0.00, '2026-07-13 11:41:47', 'Admin'),
(18, 'PO-20260713-0018', 'Received', NULL, 0.00, '2026-07-13 12:05:58', 'Admin'),
(19, 'PO-20260713-0019', 'Received', NULL, 0.00, '2026-07-13 12:08:07', 'Admin'),
(20, 'PO-20260713-0020', 'Received', NULL, 0.00, '2026-07-13 12:09:22', 'Admin'),
(21, 'PO-20260713-0021', 'Received', NULL, 0.00, '2026-07-13 13:32:23', 'Admin'),
(22, 'PO-20260713-0022', 'Received', NULL, 0.00, '2026-07-13 14:02:00', 'Admin'),
(23, 'PO-20260713-0023', 'Received', NULL, 0.00, '2026-07-13 14:07:54', 'Admin'),
(24, 'PO-20260713-0024', 'Received', NULL, 0.00, '2026-07-13 14:10:19', 'Admin'),
(25, 'PO-20260713-0025', 'Received', NULL, 0.00, '2026-07-13 14:13:15', 'Admin'),
(26, 'PO-20260713-0026', 'Received', NULL, 0.00, '2026-07-13 14:20:20', 'Admin'),
(27, 'PO-20260713-0027', 'Received', NULL, 0.00, '2026-07-13 14:22:12', 'Admin'),
(28, 'PO-20260713-0028', 'Received', NULL, 0.00, '2026-07-13 16:09:34', 'Admin'),
(29, 'PO-20260713-0029', 'Received', NULL, 0.00, '2026-07-13 16:24:17', 'Admin'),
(30, 'PO-20260713-0030', 'Received', NULL, 0.00, '2026-07-13 16:27:32', 'Admin'),
(31, 'PO-20260714-0031', 'Pending', NULL, 0.00, '2026-07-14 08:23:25', NULL),
(32, 'PO-20260714-0032', 'Pending', NULL, 0.00, '2026-07-14 09:00:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('admin_alert_email', 'earl.limo2013@gmail.com', '2026-07-13 16:05:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `role`) VALUES
(1, 'Triggerwords', '$2y$10$GOUoglsoJi6hJ7jxBg5OJuCC.lo.txE87TJOI3MkOMg5fd4uLOHgm', '2026-06-02 15:03:07', 'user'),
(2, 'sean', '$2y$10$wniVyrbtsPkIoWI591WFcuHK0wDmnuGWBKSyGogkgurbh4QxBcYQq', '2026-06-02 15:03:18', 'user'),
(3, 'pogi', '$2y$10$ixFOkjMMCru1eMUIQ3MQIOAMuOfEv8C4jh6FvnAkRhzw0LMtfIFTK', '2026-06-02 17:38:39', 'admin'),
(4, 'cerbo', '$2y$10$3YI1T0E0MwACVk8.M01xyOiNdHpVGT3i9VdgVuzAvsgCl9tT20G9G', '2026-06-03 03:44:48', 'user'),
(5, 'Earl', '$2y$10$FWdzp/hRNVq4Wo19mbaiW.qWxJJm129NoaCT0Ie/cmyJfir8WHqza', '2026-07-14 04:53:35', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_no` (`order_no`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `po_items`
--
ALTER TABLE `po_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_batches`
--
ALTER TABLE `product_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_batches`
--
ALTER TABLE `product_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `po_items`
--
ALTER TABLE `po_items`
  ADD CONSTRAINT `po_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `po_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_batches`
--
ALTER TABLE `product_batches`
  ADD CONSTRAINT `product_batches_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
