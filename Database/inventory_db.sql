-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2026 at 04:01 PM
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
(10, 7, 'ASUS Gaming Monitor', 'Added', NULL, 12, 'Admin', '2026-07-12 13:23:41');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_no` varchar(20) NOT NULL,
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

INSERT INTO `orders` (`id`, `order_no`, `total_amount`, `discount_amount`, `payment_method`, `created_at`, `cash_received`, `change_amount`, `cost_of_goods_sold`) VALUES
(1, '0001', 6000.00, 0.00, 'Cash', '2026-07-06 08:50:55', 6000.00, 0.00, 5000.00),
(2, '0002', 9000.00, 0.00, 'Cash', '2026-07-08 08:51:01', 9000.00, 0.00, 7500.00),
(3, '0003', 3000.00, 0.00, 'Cash', '2026-07-11 08:51:07', 3000.00, 0.00, 2500.00),
(4, '0004', 12000.00, 0.00, 'Cash', '2026-07-12 08:51:14', 12000.00, 0.00, 10000.00),
(5, '0005', 500.00, 0.00, 'Cash', '2026-07-12 10:26:26', 500.00, 0.00, 350.00),
(6, '0006', 3000.00, 0.00, 'Cash', '2026-07-12 10:26:34', 3000.00, 0.00, 2200.00),
(7, '0007', 1200.00, 0.00, 'Cash', '2026-07-12 10:26:39', 1200.00, 0.00, 800.00),
(8, '0008', 4000.00, 0.00, 'Cash', '2026-07-12 10:26:53', 4000.00, 0.00, 3000.00),
(9, '0009', 36000.00, 0.00, 'Card', '2026-07-12 12:29:25', 36000.00, 0.00, 27000.00);

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
(6, 6, 4, 1, 3000.00, 2200.00),
(7, 7, 3, 1, 1200.00, 800.00),
(8, 8, 5, 2, 4000.00, 3000.00),
(9, 9, 5, 18, 36000.00, 27000.00);

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
(4, 4, 4, 20, 2200.00),
(5, 5, 5, 20, 1500.00),
(6, 6, 6, 20, 300.00),
(7, 7, 1, 5, 2500.00),
(8, 8, 1, 5, 2500.00);

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
(1, 'Projector', 4, 2500.00, 3000.00, 20, '2026-07-12 08:01:09', '1783843269_e9b13a5fad644396.png', '../Models/1783843269_577cace3.glb', 'A device that displays images and videos onto a larger screen for presentations and entertainment.', 'Epson', 'White', 'Display Device', '', '1920x1080'),
(2, 'SD Card', 3, 350.00, 500.00, 19, '2026-07-12 08:02:24', '1783843344_5246dc872c12a8ef.png', '../Models/1783843344_a5149ef9.glb', 'A portable storage device used for saving photos, videos, and digital files.', 'SanDisk', 'Black', 'Storage Device', '128GB', ''),
(3, 'Headphone', 2, 800.00, 1200.00, 19, '2026-07-12 08:04:02', '1783843442_e12aa7fd7e6dfef6.png', '../Models/1783843442_6db03d66.glb', 'An audio device that provides private listening for music, calls, and multimedia.', 'Logitech', 'Black', 'Audio Device', '', ''),
(4, 'SSD', 3, 2200.00, 3000.00, 19, '2026-07-12 08:05:07', '1783843507_1131a8fce5bb5810.png', '../Models/1783843507_ec9e7464.glb', 'A high-speed storage device used to improve computer performance and file access.', 'Kingston', 'Red', 'Storage Device', '512GB', ''),
(5, 'Speaker', 2, 1500.00, 2000.00, 10, '2026-07-12 08:05:46', '1783843546_2aefdb601eaee67b.png', '../Models/1783843546_e9305ae2.glb', 'An audio output device used to play music, videos, and other sounds clearly.', 'Logitech', 'Black', 'Audio Device', '', ''),
(6, 'USB Hub', 6, 300.00, 500.00, 20, '2026-07-12 08:06:48', '1783843608_7f332d1bf9258b08.png', '../Models/1783843608_736a9825.glb', 'A device that expands a computer\'s USB ports for connecting multiple peripherals.', 'UGREEN', 'Gray', 'Connection Device', '', ''),
(7, 'ASUS Gaming Monitor', 4, 12.00, 12.00, 12, '2026-07-12 13:23:41', '1783862621_2c5f849f226107f8.png', '../Models/1783862621_d94db461.glb', 'nigga', 'ASUS', 'Black', 'Gaming Monitor', '', '3840 x 2160');

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
(1, 1, 20, 10, 2500.00, '2026-07-12 08:50:42'),
(2, 5, 20, 0, 1500.00, '2026-07-12 08:59:14'),
(3, 4, 20, 19, 2200.00, '2026-07-12 08:59:16'),
(4, 3, 20, 19, 800.00, '2026-07-12 08:59:18'),
(5, 2, 20, 19, 350.00, '2026-07-12 08:59:21'),
(6, 6, 20, 20, 300.00, '2026-07-12 08:59:36'),
(7, 1, 5, 5, 2500.00, '2026-07-12 09:42:25'),
(8, 1, 5, 5, 2500.00, '2026-07-12 10:04:34'),
(9, 5, 10, 10, 2000.00, '2026-07-12 13:06:46'),
(10, 7, 12, 12, 12.00, '2026-07-12 13:23:41');

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
(4, 'cerbo', '$2y$10$3YI1T0E0MwACVk8.M01xyOiNdHpVGT3i9VdgVuzAvsgCl9tT20G9G', '2026-06-03 03:44:48', 'user');

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
  ADD UNIQUE KEY `order_no` (`order_no`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product_batches`
--
ALTER TABLE `product_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

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
