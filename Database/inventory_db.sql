-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2026 at 05:40 PM
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
(6, 'Connection Devices', '2026-05-23 08:08:21');

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
(1, 1, 'Wireless Mouse', 'Added', 0, 10, 'Admin', '2026-05-20 11:23:18'),
(2, 2, 'Wireless Keyboard', 'Added', 0, 10, 'Admin', '2026-05-20 11:23:35'),
(3, 3, 'Earphones', 'Added', 0, 10, 'Admin', '2026-05-20 11:23:50'),
(4, 4, 'Earbuds', 'Added', 0, 10, 'Admin', '2026-05-20 11:24:11'),
(5, 5, 'Speaker', 'Added', 0, 10, 'Admin', '2026-05-20 11:24:29'),
(6, 6, 'USB Flash Drive 256gb', 'Added', 0, 10, 'Admin', '2026-05-20 11:24:49'),
(7, 7, 'Micro SD Card 512gb', 'Added', 0, 10, 'Admin', '2026-05-20 11:25:03'),
(8, 8, 'Monitor', 'Added', 0, 10, 'Admin', '2026-05-20 11:25:18'),
(9, 1, 'Wireless Mouse', 'Edited', 6, 10, 'Admin', '2026-05-20 11:29:12'),
(10, 2, 'Wireless Keyboard', 'Edited', 6, 10, 'Admin', '2026-05-20 11:29:16'),
(11, 3, 'Earphones', 'Edited', 6, 10, 'Admin', '2026-05-20 11:29:19'),
(12, 4, 'Earbuds', 'Edited', 6, 10, 'Admin', '2026-05-20 11:29:21'),
(13, 5, 'Speaker', 'Edited', 6, 10, 'Admin', '2026-05-20 11:29:23'),
(14, 6, 'USB Flash Drive 256gb', 'Edited', 6, 10, 'Admin', '2026-05-20 11:29:25'),
(15, 7, 'Micro SD Card 512gb', 'Edited', 6, 10, 'Admin', '2026-05-20 11:29:27'),
(16, 8, 'Monitor', 'Edited', 6, 10, 'Admin', '2026-05-20 11:29:30'),
(17, 9, 'SSD 512gb', 'Added', 0, 10, 'Admin', '2026-05-23 08:09:06'),
(18, 10, 'Cooling Pad', 'Added', 0, 10, 'Admin', '2026-05-23 08:09:19'),
(19, 11, 'USB Ports', 'Added', 0, 10, 'Admin', '2026-05-23 08:09:35'),
(20, 12, 'HDMI Cable', 'Added', 0, 10, 'Admin', '2026-05-23 08:09:52'),
(21, 13, 'Webcam', 'Added', 0, 10, 'Admin', '2026-05-23 08:10:05'),
(22, 14, 'Headphones', 'Added', 0, 10, 'Admin', '2026-05-23 08:10:19');

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
  `change_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_no`, `total_amount`, `discount_amount`, `payment_method`, `created_at`, `cash_received`, `change_amount`) VALUES
(1, '0001', 5095.80, 566.20, 'Cash', '2026-05-20 11:25:53', 6000.00, 904.20),
(2, '0002', 16986.00, 0.00, 'Cash', '2026-05-20 11:28:21', 17000.00, 14.00),
(3, '0003', 28310.00, 0.00, 'Cash', '2026-05-20 11:31:33', 30000.00, 1690.00),
(4, '0004', 5395.50, 599.50, 'Cash', '2026-05-23 10:10:11', 5500.00, 104.50);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_sale` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_at_sale`) VALUES
(1, 1, 1, 1, 249.00),
(2, 1, 2, 1, 449.00),
(3, 1, 3, 1, 69.00),
(4, 1, 4, 1, 149.00),
(5, 1, 5, 1, 249.00),
(6, 1, 6, 1, 699.00),
(7, 1, 7, 1, 799.00),
(8, 1, 8, 1, 2999.00),
(9, 2, 1, 3, 249.00),
(10, 2, 2, 3, 449.00),
(11, 2, 3, 3, 69.00),
(12, 2, 4, 3, 149.00),
(13, 2, 5, 3, 249.00),
(14, 2, 6, 3, 699.00),
(15, 2, 7, 3, 799.00),
(16, 2, 8, 3, 2999.00),
(17, 3, 1, 5, 249.00),
(18, 3, 2, 5, 449.00),
(19, 3, 3, 5, 69.00),
(20, 3, 4, 5, 149.00),
(21, 3, 5, 5, 249.00),
(22, 3, 6, 5, 699.00),
(23, 3, 7, 5, 799.00),
(24, 3, 8, 5, 2999.00),
(25, 4, 9, 5, 1199.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT 'default_product.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `price`, `stock`, `created_at`, `image`) VALUES
(1, 'Wireless Mouse', 1, 249.00, 5, '2026-05-20 11:23:18', '1779276198_6a0d99a6adc07.png'),
(2, 'Wireless Keyboard', 1, 449.00, 5, '2026-05-20 11:23:35', '1779276215_6a0d99b79bb46.png'),
(3, 'Earphones', 2, 69.00, 5, '2026-05-20 11:23:50', '1779276230_6a0d99c6d1c94.png'),
(4, 'Earbuds', 2, 149.00, 5, '2026-05-20 11:24:11', '1779276251_6a0d99db8e841.png'),
(5, 'Speaker', 2, 249.00, 5, '2026-05-20 11:24:29', '1779276269_6a0d99edd1b29.png'),
(6, 'USB Flash Drive 256gb', 3, 699.00, 5, '2026-05-20 11:24:49', '1779276289_6a0d9a015ed59.png'),
(7, 'Micro SD Card 512gb', 3, 799.00, 5, '2026-05-20 11:25:03', '1779276303_6a0d9a0fb7c17.png'),
(8, 'Monitor', 4, 2999.00, 5, '2026-05-20 11:25:18', '1779276318_6a0d9a1e94b54.png'),
(9, 'SSD 512gb', 3, 1199.00, 5, '2026-05-23 08:09:06', '1779523746_392c55ef80499d2f.png'),
(10, 'Cooling Pad', 5, 349.00, 10, '2026-05-23 08:09:19', '1779523759_2c8ab3f73360125d.png'),
(11, 'USB Ports', 6, 149.00, 10, '2026-05-23 08:09:35', '1779523775_1c962aed8e2352c7.png'),
(12, 'HDMI Cable', 6, 249.00, 10, '2026-05-23 08:09:52', '1779523792_8c7616ff2fa7383a.png'),
(13, 'Webcam', 4, 349.00, 10, '2026-05-23 08:10:05', '1779523805_cd357c934fd843b5.png'),
(14, 'Headphones', 2, 299.00, 10, '2026-05-23 08:10:19', '1779523819_63b617c48fe219ee.png');

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
(1, 1, 10, 1, 249.00, '2026-05-20 11:23:18'),
(2, 2, 10, 1, 449.00, '2026-05-20 11:23:35'),
(3, 3, 10, 1, 69.00, '2026-05-20 11:23:50'),
(4, 4, 10, 1, 149.00, '2026-05-20 11:24:11'),
(5, 5, 10, 1, 249.00, '2026-05-20 11:24:29'),
(6, 6, 10, 1, 699.00, '2026-05-20 11:24:49'),
(7, 7, 10, 1, 799.00, '2026-05-20 11:25:03'),
(8, 8, 10, 1, 2999.00, '2026-05-20 11:25:18'),
(9, 1, 4, 4, 249.00, '2026-05-20 11:29:12'),
(10, 2, 4, 4, 449.00, '2026-05-20 11:29:16'),
(11, 3, 4, 4, 69.00, '2026-05-20 11:29:19'),
(12, 4, 4, 4, 149.00, '2026-05-20 11:29:21'),
(13, 5, 4, 4, 249.00, '2026-05-20 11:29:23'),
(14, 6, 4, 4, 699.00, '2026-05-20 11:29:25'),
(15, 7, 4, 4, 799.00, '2026-05-20 11:29:27'),
(16, 8, 4, 4, 2999.00, '2026-05-20 11:29:30'),
(17, 9, 10, 5, 1199.00, '2026-05-23 08:09:06'),
(18, 10, 10, 10, 349.00, '2026-05-23 08:09:19'),
(19, 11, 10, 10, 149.00, '2026-05-23 08:09:35'),
(20, 12, 10, 10, 249.00, '2026-05-23 08:09:52'),
(21, 13, 10, 10, 349.00, '2026-05-23 08:10:05'),
(22, 14, 10, 10, 299.00, '2026-05-23 08:10:19');

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
(1, 'sean', '$2y$10$dDxWCUvbpgNQGB7sx5aU0e8TJs3jkm18.my7RDzdFwqFyUZigtf5e', '2026-05-19 16:03:36', 'user'),
(2, 'Triggerwords', '$2y$10$ayUikVMiaU490.4FDuYlpeAvhmgVJvILlmJ7A1eJwr08o2Z0T8qju', '2026-05-20 02:02:47', 'user'),
(3, 'admin', '$2y$10$examplehashedpassword', '2026-05-20 04:08:33', 'admin'),
(4, 'kkkkk', '$2y$10$Q1FOZPGAu3UpLeCx5eF8ou1Hv.Q2ur9PZhFSkwm6IMTYXYJMCyFIa', '2026-05-20 04:30:48', 'user');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `product_batches`
--
ALTER TABLE `product_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
