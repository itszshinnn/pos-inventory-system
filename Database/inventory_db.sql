-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2026 at 04:38 PM
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
(16, 15, 'ASUS ROG Strix XG32VQ Curved Gaming Monitor', 'Added', NULL, 30, 'Admin', '2026-07-14 14:32:37');

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
(1, 'AirPods Pro (2nd Generation)', 2, 7999.00, 10999.00, 30, '2026-07-14 14:15:57', '1784038557_98082bf0dc20fbeb.png', '../Models/1784038557_82685acb.glb', 'Experience immersive sound with Active Noise Cancellation that blocks outside distractions and Transparency mode that lets you hear your surroundings when needed. Featuring the H2 chip for richer audio, up to 6 hours of listening time per charge, and a sweat/water-resistant design (IPX4), these earbuds are perfect for workouts, commutes, or calls on the go. The MagSafe charging case adds another 24 hours of battery life, while Adaptive Audio automatically adjusts to your environment for a seamless listening experience.', 'Apple', 'White', 'Wireless Earbuds', '', ''),
(2, 'Cooler Master NotePal Cooling Pad', 7, 1299.00, 2299.00, 30, '2026-07-14 14:17:19', '1784038639_a3798e433eb444e1.png', '../Models/1784038639_fb3477b8.glb', 'Keep your laptop running cool during intense gaming or heavy multitasking sessions with this dual-fan cooling pad, designed to reduce internal temperatures and prevent thermal throttling. It features an adjustable height stand for ergonomic typing angles, a mesh metal surface for optimal airflow, and quiet operation that won\'t distract you during late-night work or gaming marathons. Compatible with laptops up to 17 inches, it\'s a must-have accessory for anyone who pushes their machine to the limit.', 'Cooler Master', 'Black', 'Laptop Cooling Pad', '', ''),
(3, 'JVC Wired Earphones', 2, 199.00, 299.00, 30, '2026-07-14 14:18:16', '1784038696_d9c94ca0aec98f77.png', '../Models/1784038696_b9416d9e.glb', 'Enjoy crisp, balanced sound on a budget with these lightweight in-ear earphones featuring 0.4-inch neodymium drivers for punchy bass and clear highs. The tangle-resistant cable and comfortable silicone ear tips make them ideal for daily commutes, workouts, or study sessions, while the 3.5mm universal jack ensures compatibility with most devices. A simple, no-fuss audio solution for anyone who wants reliable sound without breaking the bank.', 'JVC', 'White', 'Wired Earphones', '', ''),
(4, 'SanDisk Ultra Flair USB Flash Drive', 3, 899.00, 1399.00, 30, '2026-07-14 14:20:23', '1784038823_8b224463f9bf6aec.png', '../Models/1784038823_47afa6d9.glb', 'Transfer files up to 15x faster than standard USB 2.0 drives thanks to USB 3.0 technology, making it easy to move large photos, videos, and documents in seconds. The sleek metal casing offers durability for everyday carry in a bag or pocket, while the retractable design protects the connector from damage. Backward compatible with USB 2.0 ports, it\'s a reliable storage companion for students, professionals, and anyone needing extra space on the go.', 'SanDisk', 'Silver', 'USB Flash Drive', '256GB', ''),
(5, 'Vention HDMI Cable', 6, 399.00, 699.00, 30, '2026-07-14 14:21:35', '1784038895_33626002a495fd30.png', '../Models/1784038895_67700e76.glb', 'Deliver stunning 4K@60Hz visuals and immersive audio with this high-speed HDMI 2.0 cable, built with gold-plated connectors for a stable, corrosion-resistant connection. The braided nylon jacket adds durability and tangle resistance, making it perfect for connecting gaming consoles, laptops, or streaming devices to your TV or monitor. Available in multiple lengths, it\'s a dependable choice for home theaters, offices, and gaming setups alike.', 'Vention', 'Black', 'HDMI Cable', '', ''),
(6, 'JBL Tune 760NC Headphones', 2, 799.00, 1399.00, 30, '2026-07-14 14:22:22', '1784038942_315624736bd31d61.png', '../Models/1784038942_6a644721.glb', 'Immerse yourself in JBL\'s signature Pure Bass Sound with Active Noise Cancelling that quiets the world around you, whether you\'re on a flight or working in a noisy office. With up to 35 hours of battery life on a single charge and fast charging that delivers hours of playback in minutes, these over-ear headphones are built for all-day use. Foldable and lightweight with plush ear cushions, they\'re a comfortable, feature-packed choice for music lovers and remote workers alike.', 'JBL', 'Black', 'Wireless Headphones', '', ''),
(7, 'Razer BlackWidow V4 Wireless Keyboard', 1, 1399.00, 2699.00, 30, '2026-07-14 14:23:23', '1784039003_cceeedd4e9528b61.png', '../Models/1784039003_14e58370.glb', 'Built for gamers who demand precision, this mechanical keyboard features hot-swappable switches, per-key RGB Chroma lighting, and a durable aluminum top plate for a premium feel. It offers seamless connectivity via 2.4GHz wireless, Bluetooth, or wired USB-C, giving you flexibility across multiple devices without sacrificing responsiveness. With dedicated media controls and a comfortable magnetic wrist rest, it\'s equally suited for competitive gaming and everyday productivity.', 'Razer', 'Black', 'Wireless Mechanical Keyboard', '', ''),
(8, 'Epson EcoTank L3250 Printer', 4, 1499.00, 2299.00, 30, '2026-07-14 14:24:09', '1784039049_667dd614ea5ed1b3.png', '../Models/1784039049_0c8db4e3.glb', 'This cartridge-free printer uses refillable ink tanks to deliver ultra-low-cost, high-volume printing—ideal for home offices, students, and small businesses. It combines printing, scanning, and copying in one compact unit, with built-in Wi-Fi for convenient wireless printing straight from your phone or laptop. Each ink bottle set can produce thousands of pages, drastically cutting the cost per print compared to traditional cartridge printers.', 'Epson', 'Black', 'All-in-One Printer', '', ''),
(9, 'Epson Home Cinema Projector', 5, 2399.00, 4399.00, 30, '2026-07-14 14:25:12', '1784039112_900d0c19e35ab837.png', '../Models/1784039112_9ebe2d25.glb', 'Turn any room into a home theater with this projector\'s crisp 1080p resolution and up to 3,400 lumens of brightness, delivering vivid images even in moderately lit rooms. It supports large-screen projection up to 300 inches, making it perfect for movie nights, gaming sessions, or business presentations. With HDMI connectivity and built-in speakers, setup is quick and hassle-free for both casual users and home theater enthusiasts.', 'Epson', 'White', 'Home Cinema Projector', '', '1080p Full HD'),
(10, 'SanDisk Ultra SD Card', 3, 799.00, 1299.00, 30, '2026-07-14 14:26:26', '1784039186_405a1deb03678aa6.png', '../Models/1784039186_a9cc92a4.glb', 'Capture and store high-resolution photos and Full HD video with read speeds up to 140MB/s, ensuring smooth performance in cameras, drones, and camcorders. Rated Class 10 and UHS-I for reliable recording without dropped frames, it\'s built to withstand shockproof, waterproof, and temperature-resistant conditions for adventurous shooters. Whether you\'re a hobbyist photographer or a content creator on the move, this card offers dependable storage where it counts.', 'SanDisk', 'Black', 'SD Memory Card', '64GB', ''),
(11, 'JBL Flip 6 Portable Speaker', 2, 899.00, 1499.00, 30, '2026-07-14 14:27:15', '1784039235_e12bae550bfc5906.png', '../Models/1784039235_e7101469.glb', 'Take the party anywhere with this portable speaker delivering powerful JBL Original Pro Sound and punchy bass in a compact, rugged design. It\'s IP67 waterproof and dustproof, so it\'s ready for pool days, beach trips, or outdoor gatherings without worry, and offers up to 12 hours of playtime on a single charge. PartyBoost lets you pair it with other compatible JBL speakers for even bigger sound, making it a versatile choice for solo listening or group events.', 'JBL', 'Black', 'Portable Bluetooth Speaker', '', ''),
(12, 'Samsung 970 EVO Plus SSD', 3, 3999.00, 6999.00, 30, '2026-07-14 14:28:28', '1784039308_88d89dfc2787b254.png', '../Models/1784039308_3e885c1f.glb', 'Boost your system\'s speed dramatically with read speeds up to 3,500MB/s, cutting boot times, load screens, and file transfers to a fraction of what a traditional hard drive requires. Built on NVMe M.2 technology, it\'s ideal for gamers, video editors, and professionals working with large files or demanding applications. Backed by Samsung\'s reliable V-NAND technology and a durable design, it\'s a smart upgrade for any desktop or laptop with an available M.2 slot.', 'Samsung', 'Black', 'Internal Solid State Drive', '1TB', ''),
(13, 'Vention USB-C Hub', 6, 349.00, 499.00, 30, '2026-07-14 14:29:29', '1784039369_45e92449b2dc844d.png', '../Models/1784039369_5b56a8d5.glb', 'Expand your laptop\'s connectivity instantly with this compact hub, offering USB 3.0 ports, HDMI output, and card reader slots all through a single USB-C connection. It\'s perfect for professionals and students who need to connect multiple peripherals, external displays, or storage devices while working from a slim ultrabook or MacBook. Sturdy aluminum construction and plug-and-play functionality make it a convenient, reliable travel companion for work or presentations.', 'Vention', 'Black', 'USB Hub / Docking Adapter', '', ''),
(14, 'Logitech C920 HD Pro Webcam', 1, 849.00, 1349.00, 30, '2026-07-14 14:30:44', '1784039444_c5a3fad4d5784af2.png', '../Models/1784039444_ebf06416.glb', 'Look sharp on every video call with Full HD 1080p video and built-in stereo autofocus that keeps you in crisp focus even as you move. Dual omnidirectional microphones capture clear audio, reducing background noise for professional-quality video conferencing, streaming, or content creation. Compatible with most video calling apps and easy to clip onto any monitor or laptop, it\'s a plug-and-play upgrade for remote work, online teaching, or streaming setups.', 'Logitech', 'Black', 'HD Webcam', '', '3840 x 2160 4K HD'),
(15, 'ASUS ROG Strix XG32VQ Curved Gaming Monitor', 4, 2499.00, 5499.00, 30, '2026-07-14 14:32:37', '1784039557_e78dd9ae8bcb2225.png', '../Models/1784039557_e9d4ccb3.glb', 'A 32\" QHD curved display with 165Hz refresh rate, 1ms response time, FreeSync Premium, HDR-ready color, and an ergonomic ROG-styled stand, built for gamers who demand speed and immersion.', 'ASUS', 'Black', 'Gaming Monitor', '', '3840 x 2160 4K HD');

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
(1, 1, 30, 30, 7999.00, '2026-07-14 14:14:27'),
(2, 1, 30, 30, 7999.00, '2026-07-14 14:15:57'),
(3, 2, 30, 30, 1299.00, '2026-07-14 14:17:19'),
(4, 3, 30, 30, 199.00, '2026-07-14 14:18:16'),
(5, 4, 30, 30, 899.00, '2026-07-14 14:20:23'),
(6, 5, 30, 30, 399.00, '2026-07-14 14:21:35'),
(7, 6, 30, 30, 799.00, '2026-07-14 14:22:22'),
(8, 7, 30, 30, 1399.00, '2026-07-14 14:23:23'),
(9, 8, 30, 30, 1499.00, '2026-07-14 14:24:09'),
(10, 9, 30, 30, 2399.00, '2026-07-14 14:25:12'),
(11, 10, 30, 30, 799.00, '2026-07-14 14:26:26'),
(12, 11, 30, 30, 899.00, '2026-07-14 14:27:15'),
(13, 12, 30, 30, 3999.00, '2026-07-14 14:28:28'),
(14, 13, 30, 30, 349.00, '2026-07-14 14:29:29'),
(15, 14, 30, 30, 849.00, '2026-07-14 14:30:44'),
(16, 15, 30, 30, 2499.00, '2026-07-14 14:32:37');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
