-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2026 at 01:56 PM
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
(5, 'Projector Devices', '2026-05-18 05:50:19'),
(6, 'Connection Devices', '2026-05-23 08:08:21'),
(7, 'Other Devices', '2026-05-18 05:53:33');

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
(1, 1, 'AirPods Pro (2nd Generation) White', 'Added', NULL, 30, 'Admin', '2026-07-14 14:14:27'),
(2, 1, 'AirPods Pro (2nd Generation)', 'Added', NULL, 30, 'Admin', '2026-07-14 14:15:57'),
(3, 2, 'Cooler Master NotePal Cooling Pad', 'Added', NULL, 30, 'Admin', '2026-07-14 14:17:19'),
(4, 3, 'JVC Wired Earphones', 'Added', NULL, 30, 'Admin', '2026-07-14 14:18:16'),
(5, 4, 'SanDisk Ultra Flair USB Flash Drive', 'Added', NULL, 30, 'Admin', '2026-07-14 14:20:23'),
(6, 5, 'Vention HDMI Cable', 'Added', NULL, 30, 'Admin', '2026-07-14 14:21:35'),
(7, 6, 'JBL Tune 760NC Headphones', 'Added', NULL, 30, 'Admin', '2026-07-14 14:22:22'),
(8, 7, 'Razer BlackWidow V4 Wireless Keyboard', 'Added', NULL, 30, 'Admin', '2026-07-14 14:23:23'),
(9, 8, 'Epson EcoTank L3250 Printer', 'Added', NULL, 30, 'Admin', '2026-07-14 14:24:09'),
(10, 9, 'Epson Home Cinema Projector', 'Added', NULL, 30, 'Admin', '2026-07-14 14:25:12'),
(11, 10, 'SanDisk Ultra SD Card', 'Added', NULL, 30, 'Admin', '2026-07-14 14:26:26'),
(12, 11, 'JBL Flip 6 Portable Speaker', 'Added', NULL, 30, 'Admin', '2026-07-14 14:27:15'),
(13, 12, 'Samsung 970 EVO Plus SSD', 'Added', NULL, 30, 'Admin', '2026-07-14 14:28:28'),
(14, 13, 'Vention USB-C Hub', 'Added', NULL, 30, 'Admin', '2026-07-14 14:29:29'),
(15, 14, 'Logitech C920 HD Pro Webcam', 'Added', NULL, 30, 'Admin', '2026-07-14 14:30:44'),
(16, 15, 'ASUS ROG Strix XG32VQ Curved Gaming Monitor', 'Added', NULL, 30, 'Admin', '2026-07-14 14:32:37'),
(17, 1, 'AirPods Pro (2nd Generation)', 'Edited', 55, 25, 'Admin', '2026-07-14 14:49:42');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `login_time` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`log_id`, `user_id`, `username`, `login_time`, `ip_address`, `user_agent`) VALUES
(1, 5, 'Earl', '2026-07-22 22:26:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(2, 5, 'Earl', '2026-07-22 22:31:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(3, 0, 'Admin', '2026-07-22 22:31:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(4, 5, 'Earl', '2026-07-22 22:31:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(5, 0, 'Admin', '2026-07-22 22:32:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(6, 1, 'Triggerwords', '2026-07-22 22:32:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(7, 0, 'Admin', '2026-07-22 22:36:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(8, 5, 'Earl', '2026-07-22 22:51:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(9, 0, 'Admin', '2026-07-22 22:51:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(10, 2, 'sean', '2026-07-23 00:07:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(11, 0, 'Admin', '2026-07-23 00:08:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(12, 0, 'Admin', '2026-07-23 01:54:51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(13, 0, 'Admin', '2026-07-23 01:59:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(14, 0, 'Admin', '2026-07-23 02:01:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(15, 2, 'sean', '2026-07-23 16:35:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(16, 2, 'sean', '2026-07-23 16:36:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(17, 0, 'Admin', '2026-07-23 16:46:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(18, 2, 'sean', '2026-07-23 17:04:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(19, 2, 'sean', '2026-07-23 17:33:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(20, 0, 'Admin', '2026-07-23 17:36:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(21, 2, 'sean', '2026-07-23 17:36:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(22, 0, 'Admin', '2026-07-23 17:48:17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(23, 2, 'sean', '2026-07-23 17:55:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(24, 0, 'Admin', '2026-07-23 17:56:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(25, 0, 'Admin', '2026-07-23 17:56:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(26, 2, 'sean', '2026-07-23 18:01:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(27, 0, 'Admin', '2026-07-23 18:01:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(28, 0, 'Admin', '2026-07-23 18:10:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(29, 0, 'Admin', '2026-07-23 19:15:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(30, 0, 'Admin', '2026-07-23 19:15:30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(31, 2, 'sean', '2026-07-23 19:19:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(32, 0, 'Admin', '2026-07-23 19:28:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(33, 0, 'Admin', '2026-07-23 19:33:41', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1'),
(34, 0, 'Admin', '2026-07-23 19:37:38', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1');

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
  `discount_type` varchar(50) DEFAULT NULL,
  `payment_method` enum('Cash','Card','GCash','Maya') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cash_received` decimal(10,2) DEFAULT 0.00,
  `change_amount` decimal(10,2) DEFAULT 0.00,
  `cost_of_goods_sold` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_no`, `user_id`, `total_amount`, `discount_amount`, `discount_type`, `payment_method`, `created_at`, `cash_received`, `change_amount`, `cost_of_goods_sold`) VALUES
(1, '0001', 2, 218175.00, 0.00, NULL, 'Cash', '2026-07-14 14:49:01', 219000.00, 825.00, 131425.00),
(2, '0002', 2, 43635.00, 0.00, NULL, 'GCash', '2026-05-12 14:50:53', 43635.00, 0.00, 26285.00),
(3, '0003', 2, 43635.00, 0.00, NULL, 'Maya', '2026-02-24 14:51:18', 43635.00, 0.00, 26285.00),
(4, '0004', 2, 130905.00, 0.00, NULL, 'Card', '2026-04-05 14:53:12', 130905.00, 0.00, 78855.00),
(5, '0005', 2, 5829.84, 1457.46, 'Senior', 'Maya', '2026-07-23 09:48:07', 5829.84, 0.00, 4197.00),
(6, '0006', 2, 4399.20, 1099.80, 'Senior', 'Cash', '2026-07-23 09:56:14', 5000.00, 600.80, 2499.00),
(7, '0007', 2, 699.00, 0.00, NULL, 'Cash', '2026-07-23 11:22:24', 1000.00, 301.00, 399.00);

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
  `item_discount_amount` decimal(10,2) DEFAULT 0.00,
  `item_discount_type` varchar(10) DEFAULT NULL,
  `cost_of_goods_sold` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_at_sale`, `item_discount_amount`, `item_discount_type`, `cost_of_goods_sold`) VALUES
(1, 1, 1, 5, 54995.00, 0.00, NULL, 39995.00),
(2, 1, 2, 5, 11495.00, 0.00, NULL, 6495.00),
(3, 1, 3, 5, 1495.00, 0.00, NULL, 995.00),
(4, 1, 4, 5, 6995.00, 0.00, NULL, 4495.00),
(5, 1, 5, 5, 3495.00, 0.00, NULL, 1995.00),
(6, 1, 6, 5, 6995.00, 0.00, NULL, 3995.00),
(7, 1, 7, 5, 13495.00, 0.00, NULL, 6995.00),
(8, 1, 8, 5, 11495.00, 0.00, NULL, 7495.00),
(9, 1, 9, 5, 21995.00, 0.00, NULL, 11995.00),
(10, 1, 10, 5, 6495.00, 0.00, NULL, 3995.00),
(11, 1, 11, 5, 7495.00, 0.00, NULL, 4495.00),
(12, 1, 12, 5, 34995.00, 0.00, NULL, 19995.00),
(13, 1, 13, 5, 2495.00, 0.00, NULL, 1745.00),
(14, 1, 14, 5, 6745.00, 0.00, NULL, 4245.00),
(15, 1, 15, 5, 27495.00, 0.00, NULL, 12495.00),
(16, 2, 1, 1, 10999.00, 0.00, NULL, 7999.00),
(17, 2, 2, 1, 2299.00, 0.00, NULL, 1299.00),
(18, 2, 3, 1, 299.00, 0.00, NULL, 199.00),
(19, 2, 4, 1, 1399.00, 0.00, NULL, 899.00),
(20, 2, 5, 1, 699.00, 0.00, NULL, 399.00),
(21, 2, 6, 1, 1399.00, 0.00, NULL, 799.00),
(22, 2, 7, 1, 2699.00, 0.00, NULL, 1399.00),
(23, 2, 8, 1, 2299.00, 0.00, NULL, 1499.00),
(24, 2, 9, 1, 4399.00, 0.00, NULL, 2399.00),
(25, 2, 10, 1, 1299.00, 0.00, NULL, 799.00),
(26, 2, 11, 1, 1499.00, 0.00, NULL, 899.00),
(27, 2, 12, 1, 6999.00, 0.00, NULL, 3999.00),
(28, 2, 13, 1, 499.00, 0.00, NULL, 349.00),
(29, 2, 14, 1, 1349.00, 0.00, NULL, 849.00),
(30, 2, 15, 1, 5499.00, 0.00, NULL, 2499.00),
(31, 3, 1, 1, 10999.00, 0.00, NULL, 7999.00),
(32, 3, 2, 1, 2299.00, 0.00, NULL, 1299.00),
(33, 3, 3, 1, 299.00, 0.00, NULL, 199.00),
(34, 3, 4, 1, 1399.00, 0.00, NULL, 899.00),
(35, 3, 5, 1, 699.00, 0.00, NULL, 399.00),
(36, 3, 6, 1, 1399.00, 0.00, NULL, 799.00),
(37, 3, 7, 1, 2699.00, 0.00, NULL, 1399.00),
(38, 3, 8, 1, 2299.00, 0.00, NULL, 1499.00),
(39, 3, 9, 1, 4399.00, 0.00, NULL, 2399.00),
(40, 3, 10, 1, 1299.00, 0.00, NULL, 799.00),
(41, 3, 11, 1, 1499.00, 0.00, NULL, 899.00),
(42, 3, 12, 1, 6999.00, 0.00, NULL, 3999.00),
(43, 3, 13, 1, 499.00, 0.00, NULL, 349.00),
(44, 3, 14, 1, 1349.00, 0.00, NULL, 849.00),
(45, 3, 15, 1, 5499.00, 0.00, NULL, 2499.00),
(46, 4, 1, 3, 32997.00, 0.00, NULL, 23997.00),
(47, 4, 2, 3, 6897.00, 0.00, NULL, 3897.00),
(48, 4, 3, 3, 897.00, 0.00, NULL, 597.00),
(49, 4, 4, 3, 4197.00, 0.00, NULL, 2697.00),
(50, 4, 5, 3, 2097.00, 0.00, NULL, 1197.00),
(51, 4, 6, 3, 4197.00, 0.00, NULL, 2397.00),
(52, 4, 7, 3, 8097.00, 0.00, NULL, 4197.00),
(53, 4, 8, 3, 6897.00, 0.00, NULL, 4497.00),
(54, 4, 9, 3, 13197.00, 0.00, NULL, 7197.00),
(55, 4, 10, 3, 3897.00, 0.00, NULL, 2397.00),
(56, 4, 11, 3, 4497.00, 0.00, NULL, 2697.00),
(57, 4, 12, 3, 20997.00, 0.00, NULL, 11997.00),
(58, 4, 13, 3, 1497.00, 0.00, NULL, 1047.00),
(59, 4, 14, 3, 4047.00, 0.00, NULL, 2547.00),
(60, 4, 15, 3, 16497.00, 0.00, NULL, 7497.00),
(61, 5, 7, 3, 8097.00, 809.70, '%', 4197.00),
(62, 6, 15, 1, 5499.00, 0.00, NULL, 2499.00),
(63, 7, 5, 1, 699.00, 0.00, NULL, 399.00);

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
(1, 'AirPods Pro (2nd Generation)', 2, 7999.00, 10999.00, 20, '2026-07-14 14:15:57', '1784038557_98082bf0dc20fbeb.png', '../Models/1784038557_82685acb.glb', 'Experience immersive sound with Active Noise Cancellation that blocks outside distractions and Transparency mode that lets you hear your surroundings when needed. Featuring the H2 chip for richer audio, up to 6 hours of listening time per charge, and a sweat/water-resistant design (IPX4), these earbuds are perfect for workouts, commutes, or calls on the go. The MagSafe charging case adds another 24 hours of battery life, while Adaptive Audio automatically adjusts to your environment for a seamless listening experience.', 'Apple', 'White', 'Wireless Earbuds', '', ''),
(2, 'Cooler Master NotePal Cooling Pad', 7, 1299.00, 2299.00, 20, '2026-07-14 14:17:19', '1784038639_a3798e433eb444e1.png', '../Models/1784038639_fb3477b8.glb', 'Keep your laptop running cool during intense gaming or heavy multitasking sessions with this dual-fan cooling pad, designed to reduce internal temperatures and prevent thermal throttling. It features an adjustable height stand for ergonomic typing angles, a mesh metal surface for optimal airflow, and quiet operation that won\'t distract you during late-night work or gaming marathons. Compatible with laptops up to 17 inches, it\'s a must-have accessory for anyone who pushes their machine to the limit.', 'Cooler Master', 'Black', 'Laptop Cooling Pad', '', ''),
(3, 'JVC Wired Earphones', 2, 199.00, 299.00, 20, '2026-07-14 14:18:16', '1784038696_d9c94ca0aec98f77.png', '../Models/1784038696_b9416d9e.glb', 'Enjoy crisp, balanced sound on a budget with these lightweight in-ear earphones featuring 0.4-inch neodymium drivers for punchy bass and clear highs. The tangle-resistant cable and comfortable silicone ear tips make them ideal for daily commutes, workouts, or study sessions, while the 3.5mm universal jack ensures compatibility with most devices. A simple, no-fuss audio solution for anyone who wants reliable sound without breaking the bank.', 'JVC', 'White', 'Wired Earphones', '', ''),
(4, 'SanDisk Ultra Flair USB Flash Drive', 3, 899.00, 1399.00, 20, '2026-07-14 14:20:23', '1784038823_8b224463f9bf6aec.png', '../Models/1784038823_47afa6d9.glb', 'Transfer files up to 15x faster than standard USB 2.0 drives thanks to USB 3.0 technology, making it easy to move large photos, videos, and documents in seconds. The sleek metal casing offers durability for everyday carry in a bag or pocket, while the retractable design protects the connector from damage. Backward compatible with USB 2.0 ports, it\'s a reliable storage companion for students, professionals, and anyone needing extra space on the go.', 'SanDisk', 'Silver', 'USB Flash Drive', '256GB', ''),
(5, 'Vention HDMI Cable', 6, 399.00, 699.00, 19, '2026-07-14 14:21:35', '1784038895_33626002a495fd30.png', '../Models/1784038895_67700e76.glb', 'Deliver stunning 4K@60Hz visuals and immersive audio with this high-speed HDMI 2.0 cable, built with gold-plated connectors for a stable, corrosion-resistant connection. The braided nylon jacket adds durability and tangle resistance, making it perfect for connecting gaming consoles, laptops, or streaming devices to your TV or monitor. Available in multiple lengths, it\'s a dependable choice for home theaters, offices, and gaming setups alike.', 'Vention', 'Black', 'HDMI Cable', '', ''),
(6, 'JBL Tune 760NC Headphones', 2, 799.00, 1399.00, 20, '2026-07-14 14:22:22', '1784038942_315624736bd31d61.png', '../Models/1784038942_6a644721.glb', 'Immerse yourself in JBL\'s signature Pure Bass Sound with Active Noise Cancelling that quiets the world around you, whether you\'re on a flight or working in a noisy office. With up to 35 hours of battery life on a single charge and fast charging that delivers hours of playback in minutes, these over-ear headphones are built for all-day use. Foldable and lightweight with plush ear cushions, they\'re a comfortable, feature-packed choice for music lovers and remote workers alike.', 'JBL', 'Black', 'Wireless Headphones', '', ''),
(7, 'Razer BlackWidow V4 Wireless Keyboard', 1, 1399.00, 2699.00, 17, '2026-07-14 14:23:23', '1784039003_cceeedd4e9528b61.png', '../Models/1784039003_14e58370.glb', 'Built for gamers who demand precision, this mechanical keyboard features hot-swappable switches, per-key RGB Chroma lighting, and a durable aluminum top plate for a premium feel. It offers seamless connectivity via 2.4GHz wireless, Bluetooth, or wired USB-C, giving you flexibility across multiple devices without sacrificing responsiveness. With dedicated media controls and a comfortable magnetic wrist rest, it\'s equally suited for competitive gaming and everyday productivity.', 'Razer', 'Black', 'Wireless Mechanical Keyboard', '', ''),
(8, 'Epson EcoTank L3250 Printer', 4, 1499.00, 2299.00, 20, '2026-07-14 14:24:09', '1784039049_667dd614ea5ed1b3.png', '../Models/1784039049_0c8db4e3.glb', 'This cartridge-free printer uses refillable ink tanks to deliver ultra-low-cost, high-volume printing—ideal for home offices, students, and small businesses. It combines printing, scanning, and copying in one compact unit, with built-in Wi-Fi for convenient wireless printing straight from your phone or laptop. Each ink bottle set can produce thousands of pages, drastically cutting the cost per print compared to traditional cartridge printers.', 'Epson', 'Black', 'All-in-One Printer', '', ''),
(9, 'Epson Home Cinema Projector', 5, 2399.00, 4399.00, 20, '2026-07-14 14:25:12', '1784039112_900d0c19e35ab837.png', '../Models/1784039112_9ebe2d25.glb', 'Turn any room into a home theater with this projector\'s crisp 1080p resolution and up to 3,400 lumens of brightness, delivering vivid images even in moderately lit rooms. It supports large-screen projection up to 300 inches, making it perfect for movie nights, gaming sessions, or business presentations. With HDMI connectivity and built-in speakers, setup is quick and hassle-free for both casual users and home theater enthusiasts.', 'Epson', 'White', 'Home Cinema Projector', '', '1080p Full HD'),
(10, 'SanDisk Ultra SD Card', 3, 799.00, 1299.00, 20, '2026-07-14 14:26:26', '1784039186_405a1deb03678aa6.png', '../Models/1784039186_a9cc92a4.glb', 'Capture and store high-resolution photos and Full HD video with read speeds up to 140MB/s, ensuring smooth performance in cameras, drones, and camcorders. Rated Class 10 and UHS-I for reliable recording without dropped frames, it\'s built to withstand shockproof, waterproof, and temperature-resistant conditions for adventurous shooters. Whether you\'re a hobbyist photographer or a content creator on the move, this card offers dependable storage where it counts.', 'SanDisk', 'Black', 'SD Memory Card', '64GB', ''),
(11, 'JBL Flip 6 Portable Speaker', 2, 899.00, 1499.00, 20, '2026-07-14 14:27:15', '1784039235_e12bae550bfc5906.png', '../Models/1784039235_e7101469.glb', 'Take the party anywhere with this portable speaker delivering powerful JBL Original Pro Sound and punchy bass in a compact, rugged design. It\'s IP67 waterproof and dustproof, so it\'s ready for pool days, beach trips, or outdoor gatherings without worry, and offers up to 12 hours of playtime on a single charge. PartyBoost lets you pair it with other compatible JBL speakers for even bigger sound, making it a versatile choice for solo listening or group events.', 'JBL', 'Black', 'Portable Bluetooth Speaker', '', ''),
(12, 'Samsung 970 EVO Plus SSD', 3, 3999.00, 6999.00, 20, '2026-07-14 14:28:28', '1784039308_88d89dfc2787b254.png', '../Models/1784039308_3e885c1f.glb', 'Boost your system\'s speed dramatically with read speeds up to 3,500MB/s, cutting boot times, load screens, and file transfers to a fraction of what a traditional hard drive requires. Built on NVMe M.2 technology, it\'s ideal for gamers, video editors, and professionals working with large files or demanding applications. Backed by Samsung\'s reliable V-NAND technology and a durable design, it\'s a smart upgrade for any desktop or laptop with an available M.2 slot.', 'Samsung', 'Black', 'Internal Solid State Drive', '1TB', ''),
(13, 'Vention USB-C Hub', 6, 349.00, 499.00, 20, '2026-07-14 14:29:29', '1784039369_45e92449b2dc844d.png', '../Models/1784039369_5b56a8d5.glb', 'Expand your laptop\'s connectivity instantly with this compact hub, offering USB 3.0 ports, HDMI output, and card reader slots all through a single USB-C connection. It\'s perfect for professionals and students who need to connect multiple peripherals, external displays, or storage devices while working from a slim ultrabook or MacBook. Sturdy aluminum construction and plug-and-play functionality make it a convenient, reliable travel companion for work or presentations.', 'Vention', 'Black', 'USB Hub / Docking Adapter', '', ''),
(14, 'Logitech C920 HD Pro Webcam', 1, 849.00, 1349.00, 20, '2026-07-14 14:30:44', '1784039444_c5a3fad4d5784af2.png', '../Models/1784039444_ebf06416.glb', 'Look sharp on every video call with Full HD 1080p video and built-in stereo autofocus that keeps you in crisp focus even as you move. Dual omnidirectional microphones capture clear audio, reducing background noise for professional-quality video conferencing, streaming, or content creation. Compatible with most video calling apps and easy to clip onto any monitor or laptop, it\'s a plug-and-play upgrade for remote work, online teaching, or streaming setups.', 'Logitech', 'Black', 'HD Webcam', '', '3840 x 2160 4K HD'),
(15, 'ASUS ROG Strix XG32VQ Curved Gaming Monitor', 4, 2499.00, 5499.00, 19, '2026-07-14 14:32:37', '1784039557_e78dd9ae8bcb2225.png', '../Models/1784039557_e9d4ccb3.glb', 'A 32\" QHD curved display with 165Hz refresh rate, 1ms response time, FreeSync Premium, HDR-ready color, and an ergonomic ROG-styled stand, built for gamers who demand speed and immersion.', 'ASUS', 'Black', 'Gaming Monitor', '', '3840 x 2160 4K HD');

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
(1, 1, 30, 0, 7999.00, '2026-07-14 14:14:27'),
(2, 1, 30, 20, 7999.00, '2026-07-14 14:15:57'),
(3, 2, 30, 20, 1299.00, '2026-07-14 14:17:19'),
(4, 3, 30, 20, 199.00, '2026-07-14 14:18:16'),
(5, 4, 30, 20, 899.00, '2026-07-14 14:20:23'),
(6, 5, 30, 19, 399.00, '2026-07-14 14:21:35'),
(7, 6, 30, 20, 799.00, '2026-07-14 14:22:22'),
(8, 7, 30, 17, 1399.00, '2026-07-14 14:23:23'),
(9, 8, 30, 20, 1499.00, '2026-07-14 14:24:09'),
(10, 9, 30, 20, 2399.00, '2026-07-14 14:25:12'),
(11, 10, 30, 20, 799.00, '2026-07-14 14:26:26'),
(12, 11, 30, 20, 899.00, '2026-07-14 14:27:15'),
(13, 12, 30, 20, 3999.00, '2026-07-14 14:28:28'),
(14, 13, 30, 20, 349.00, '2026-07-14 14:29:29'),
(15, 14, 30, 20, 849.00, '2026-07-14 14:30:44'),
(16, 15, 30, 19, 2499.00, '2026-07-14 14:32:37');

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
('admin_alert_email', 'ilsymeunice@gmail.com', '2026-07-14 10:34:39');

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
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`log_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_batches`
--
ALTER TABLE `product_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
