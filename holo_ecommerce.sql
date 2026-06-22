-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2026 at 02:00 AM
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
-- Database: `holo_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(10) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `created_at`) VALUES
(1, 'Smartphones', '📱', '2026-05-20 19:10:54'),
(2, 'Laptops', '💻', '2026-05-20 19:10:54'),
(3, 'Audio', '🎧', '2026-05-20 19:10:54'),
(4, 'Accessories', '⌨️', '2026-05-20 19:10:54'),
(5, 'Gaming', '🕹️', '2026-05-20 19:10:54');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `full_name`, `email`, `phone`, `address`, `city`, `state`, `zip`, `payment_method`, `date`, `updated_at`) VALUES
(1, 3, 4999.00, 'completed', 'ilyes', 'ilyes@gmail.com', '12456789', 'sousse', 'sousse', 'tunis', '1245', 'card', '2026-05-20 20:39:36', '2026-05-20 19:40:42');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, 4999.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `features` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `price`, `description`, `features`, `image`, `featured`, `stock`, `created_at`, `updated_at`) VALUES
(1, 'Apple iPhone 15 Pro Max (Red)', 1, 4299.00, 'The iPhone 15 Pro Max in red brings Apple\'s most powerful mobile platform to life with the A17 Pro chip, a stunning 6.7-inch Super Retina XDR display, and pro-level camera performance in a premium titanium frame.', '6.7\" Super Retina XDR display\nA17 Pro chip\n48MP triple camera\nTitanium frame\nAction Button\nUSB-C connectivity', 'photos/phone.jpg', 1, 24, '2026-05-20 19:10:54', '2026-05-20 19:10:54'),
(2, 'Apple iPhone 17', 1, 4999.00, 'The iPhone 17 delivers sleek design and strong performance with an A16 Bionic chip, a 6.1-inch OLED display, and advanced camera features for crisp photos and smooth video.', '6.1\" OLED display\nA16 Bionic chip\n48MP main camera\nDynamic Island\nUSB-C charging\nCinematic video', 'photos/phone2.jpeg', 1, 28, '2026-05-20 19:10:54', '2026-05-20 19:10:54'),
(3, 'Apple iPhone 15 Pro (Gold)', 1, 3899.00, 'The iPhone 15 Pro in gold combines precision engineering with a lightweight titanium design, a fast A17 Pro chip, and a professional-grade camera system for creatives and power users.', '6.1\" Super Retina XDR display\nA17 Pro chip\n48MP triple camera\nProMotion 120Hz\nTitanium finish\nAdvanced video modes', 'photos/phone3.jpeg', 0, 20, '2026-05-20 19:10:54', '2026-05-20 19:20:23'),
(4, 'Apple iPhone 15 Pro Max (Space Black)', 1, 4299.00, 'The iPhone 15 Pro Max in space black delivers flagship power with long battery life, a vivid Super Retina XDR display, and a professional camera system for stunning low-light photography.', '6.7\" Super Retina XDR display\nA17 Pro chip\n48MP triple camera\nProRAW support\nTitanium body\nAction Button', 'photos/phone4.jpg', 0, 18, '2026-05-20 19:10:54', '2026-05-20 19:20:23'),
(5, 'Apple iPhone SE (3rd Gen)', 1, 1899.00, 'The iPhone SE (3rd Gen) offers iconic design, a powerful A15 Bionic chip, and reliable performance in a compact package with a single-camera setup and strong battery life.', '4.7\" Retina HD display\nA15 Bionic chip\nFace ID\nSingle 12MP camera\nCompact design\nWireless charging', 'photos/phone5.jpeg', 0, 32, '2026-05-20 19:10:54', '2026-05-20 19:20:23'),
(6, 'Dell XPS 15', 2, 8599.00, 'The Dell XPS 15 pairs premium materials with a bright 15.6-inch OLED display, Intel Core i9 performance, and dedicated RTX graphics for creators and professionals on the move.', '15.6\" OLED display\nIntel Core i9\nNVIDIA RTX 4060\n32GB RAM\n1TB SSD\nInfinityEdge touchscreen', 'photos/laptop.jpg', 1, 12, '2026-05-20 19:10:54', '2026-05-20 19:20:23'),
(7, 'ASUS ROG Strix G16', 2, 7599.00, 'Built for gaming and content creation, the ASUS ROG Strix G16 features a high-refresh display, powerful Intel CPU, and advanced cooling to keep performance steady during marathon sessions.', '16\" QHD 240Hz display\nIntel Core i7\nNVIDIA RTX 4070\nDDR5 memory\nRGB keyboard\nComprehensive cooling', 'photos/laptop 2.jpeg', 0, 14, '2026-05-20 19:10:54', '2026-05-20 19:20:23'),
(8, 'Lenovo Legion 9i', 2, 8999.00, 'The Lenovo Legion 9i delivers premium gaming performance with a crisp display, Intel Alder Lake processor, and advanced thermal design for sustained frame rates under load.', '16\" QHD display\nIntel Core i9\nNVIDIA RTX 4080\nLiquid metal cooling\nRGB lighting\nThunderbolt 4', 'photos/laptop 3.jpeg', 0, 10, '2026-05-20 19:10:54', '2026-05-20 19:20:23'),
(9, 'MSI Raider GE78', 2, 8299.00, 'The MSI Raider GE78 brings desktop-class power to gamers with a fast display, cutting-edge RTX graphics, and a bold thermal chassis for high-performance play.', '17.3\" 240Hz display\nIntel Core i9\nNVIDIA RTX 4080\nRGB light bar\n5 heat pipes\nThunderbolt 4', 'photos/laptop4.jpeg', 0, 9, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(10, 'MSI Stealth 16', 2, 6999.00, 'The MSI Stealth 16 blends a slim chassis with strong gaming hardware, delivering portability without sacrificing performance or display quality.', '16\" QHD display\nIntel Core i7\nNVIDIA RTX 4070\nNVMe SSD\nRGB keyboard\nLong battery life', 'photos/laptop5.jpeg', 0, 16, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(11, 'ASUS TUF Gaming A15', 2, 5299.00, 'The ASUS TUF Gaming A15 is built for value gamers, offering a rugged chassis, reliable performance, and a high-refresh display for smooth play.', '15.6\" FHD 144Hz display\nAMD Ryzen 9\nNVIDIA RTX 4060\nMIL-STD durability\nWi-Fi 6\nRGB keyboard', 'photos/laptop6.jpeg', 0, 18, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(12, 'Sony WH-1000XM5', 3, 1249.00, 'The Sony WH-1000XM5 offers industry-leading noise cancellation, plush comfort, and detailed sound tuning for premium listening at home or on the go.', 'Industry-leading ANC\n30-hour battery\nPrecise Voice Pickup\nWireless Bluetooth\nAdaptive Sound Control\nComfort fit', 'photos/headphones.jpg', 1, 24, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(13, 'Nothing Ear (2)', 3, 699.00, 'Nothing Ear (2) delivers transparent sound with active noise cancellation, a tactile charging case, and long-lasting wireless listening in a modern design.', 'Active Noise Cancellation\nWireless charging case\n11.6mm drivers\nUp to 34-hour battery\nBluetooth 5.3\nFast pairing', 'photos/headphone2.jpg', 0, 30, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(14, 'Sony MDR-EX155AP', 3, 149.00, 'Sony MDR-EX155AP wired earbuds offer clear audio and in-line controls for reliable everyday listening with a compact, comfortable design.', 'Wired in-ear design\nIn-line mic\nLightweight fit\nClear audio\nTangle-resistant cable\nUniversal compatibility', 'photos/headphone3.jpg', 0, 45, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(15, 'JBL Live 660NC', 3, 699.00, 'JBL Live 660NC combines over-ear comfort with adaptive noise cancellation and JBL Signature Sound, making it ideal for travel and daily commutes.', 'Adaptive Noise Cancellation\nUp to 50-hour battery\nMulti-device pairing\nVoice assistant support\nComfort fit\nDetachable cable', 'photos/headphone4.jpg', 0, 28, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(16, 'Beats Solo3 Wireless', 3, 799.00, 'Beats Solo3 Wireless delivers rich sound, comfortable on-ear design, and up to 40 hours of battery life for music lovers who need long-lasting performance.', '40-hour battery\nWireless Bluetooth\nFast Fuel charging\nComfortable ear cushions\nOn-ear controls\nBuilt-in microphone', 'photos/headphone5.jpg', 0, 35, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(17, 'Razer DeathAdder V2', 4, 319.00, 'The Razer DeathAdder V2 is a high-precision gaming mouse with optical switches, ergonomic comfort, and customizable RGB lighting for competitive gameplay.', '20K DPI optical sensor\nOptical mouse switches\nRGB lighting\nErgonomic design\n6 programmable buttons\nSpeedflex cable', 'photos/mouse.jpeg', 1, 40, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(18, 'Redragon M711', 4, 199.00, 'The Redragon M711 is a feature-packed wired gaming mouse with adjustable weights, RGB lighting, and high-precision tracking for competitive accuracy.', 'RGB lighting\nAdjustable weights\n16K DPI\n6 programmable buttons\nErgonomic shape\nDurable switches', 'photos/mouse2.jpg', 0, 52, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(19, 'Logitech M185', 4, 129.00, 'The Logitech M185 wireless mouse offers simple plug-and-play connectivity, comfortable ambidextrous design, and long battery life for everyday productivity.', 'Wireless USB receiver\nCompact design\nLong battery life\nAmbidextrous\nPlug-and-play\nSmooth tracking', 'photos/mouse3.jpg', 0, 60, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(20, 'HP Wireless Mouse', 4, 149.00, 'The HP Wireless Mouse delivers reliable wireless performance with a comfortable shape and easy navigation for home and office use.', 'Wireless connectivity\nComfort grip\nPlug-and-play\nCompact size\nLong battery life\nHigh precision', 'photos/mouse4.jpg', 0, 48, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(21, 'HP USB Mouse', 4, 99.00, 'The HP USB Mouse is a simple wired solution for reliable daily computing with smooth cursor control and a comfortable form factor.', 'Wired USB connection\nComfortable shape\nPrecise tracking\nPlug-and-play\nLightweight design\nDurable buttons', 'photos/mouse5.jpg', 0, 54, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(22, 'Corsair iCUE Gaming PC', 5, 12999.00, 'The Corsair iCUE Gaming PC combines an RGB-lit tower, high-end components, and premium cooling to deliver a stunning gaming experience for desktop enthusiasts.', 'Custom RGB cooling\nHigh airflow case\nPremium build quality\nAdvanced cable management\nGaming-ready performance\nQuick access front panel', 'photos/desktop.jpeg', 1, 8, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(23, 'NZXT H510 Gaming PC', 5, 11999.00, 'The NZXT H510 Gaming PC delivers clean design, effective airflow, and powerful hardware in a modern chassis built for both gaming and content creation.', 'Tempered glass side panel\nRGB front fans\nCable management\nHigh-performance cooling\nStylish interior\nReady for upgrades', 'photos/desktop2.jpeg', 0, 10, '2026-05-20 19:20:23', '2026-05-20 19:20:23'),
(24, 'Thermaltake RGB Desktop', 5, 10999.00, 'The Thermaltake RGB Desktop pairs bold lighting with a tempered glass chassis to create a stunning centerpiece for gaming setups and productivity workstations.', 'RGB fan lighting\nTempered glass side\nHigh airflow\nTool-free design\nGaming-grade cooling\nUpgrade-ready interior', 'photos/desktop3.jpeg', 0, 12, '2026-05-20 19:20:23', '2026-05-20 19:20:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@holo.com', '$2y$10$7HIrIl6plCn4dZOKpjukGeDyE27Y/fRW6Evao5wXSU0mg4qrpznqW', 'Admin', 'User', 'admin', '2023-12-31 23:00:00', '2026-05-20 19:10:54'),
(3, 'ilyes', 'ilyes@gmail.com', '$2y$10$tY4bRBALNZYt2lBdhnrIq.oVNL1PjON2busV1Cq97mCZqkAV6cyqK', 'ilyes', 'bo', 'customer', '2026-05-20 19:38:30', '2026-05-20 19:38:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
