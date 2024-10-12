-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2024 at 04:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `all_products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`all_products`)),
  `payment_method` varchar(20) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT '''Pending''',
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_success` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `Username`, `email`, `address`, `all_products`, `payment_method`, `total`, `payment_status`, `status`, `created_at`, `date_success`) VALUES
(1728096293, 'thanhpha', '2230140008@sv.hotec.edu.vn', '2230140008@sv.hotec.edu.vn', '[{\"id\":6,\"quantity\":1,\"name\":\"Macbook Pro 14\"}]', 'momo', 60000.00, 'Completed', 'Pending', '2024-07-02 02:44:53', '2024-10-04 17:00:00'),
(1728096536, 'thanhpha', '2230140008@sv.hotec.edu.vn', '2230140008@sv.hotec.edu.vn', '[{\"id\":6,\"quantity\":1,\"name\":\"Macbook Pro 14\"},{\"id\":4,\"quantity\":13,\"name\":\"Macbook Air 13\"}]', 'momo', 580000.00, 'Pending', 'Completed', '2024-06-11 02:48:56', '2024-10-04 17:00:00'),
(1728097090, 'thanhpha', '2230140008@sv.hotec.edu.vn', '2230140008@sv.hotec.edu.vn', '[{\"id\":4,\"quantity\":1,\"name\":\"Macbook Air 13\"},{\"id\":9,\"quantity\":1,\"name\":\"Lenovo Slim 3\"}]', 'momo', 69190.00, 'Pending', 'Pending', '2024-09-11 02:58:10', '2024-10-04 17:00:00'),
(1728114732, 'thanhpha', 'phamducdang123@gmail.com', 'phamducdang123@gmail.com', '[{\"id\":6,\"quantity\":1,\"name\":\"Macbook Pro 14\"}]', 'momo', 60000.00, 'Pending', 'Completed', '2024-10-05 07:52:12', '2024-10-05 07:52:12');

-- --------------------------------------------------------

--
-- Table structure for table `productcategory`
--

CREATE TABLE `productcategory` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productcategory`
--

INSERT INTO `productcategory` (`id`, `name`, `created_at`) VALUES
(1, 'Macbook', '2024-09-28 06:50:52'),
(2, 'Acer', '2024-09-28 06:51:00'),
(3, 'HP', '2024-09-28 06:51:07'),
(4, 'MSI', '2024-09-28 06:55:13'),
(5, 'Lenovo', '2024-09-28 06:55:21'),
(6, 'Xiaomi', '2024-09-28 09:22:43');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(60,0) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `image`, `created_at`) VALUES
(2, 2, 'Acer Aspire A315', 'CPU: Ryzen 5, 7535HS,', 13000, 'acer-aspire-a315.jpg', '2024-08-22 16:58:53'),
(3, 3, 'HP Victus 15', 'CPU: Ryzen 5, 7535HS,', 10000, 'hp-victus-15.jpg', '2024-08-22 16:58:53'),
(4, 1, 'Macbook Air 13', 'CPU: Apple M3, 100GB/s', 40000, 'macbook-air-13.jpg', '2024-08-22 16:58:53'),
(5, 3, 'HP 15', 'CPU: Ryzen 5, 7535HS,', 12000, 'hp-15.jpg', '2024-08-22 16:58:53'),
(6, 1, 'Macbook Pro 14', 'CPU: Apple M3, 100GB/s', 60000, 'apple-macbook-pro-14.jpg', '2024-08-22 16:58:53'),
(7, 4, 'MSI Morden 15', 'CPU: i5, 1240P, 1.7GHz', 17400, 'msi-modern-15.jpg', '2024-08-22 16:58:53'),
(8, 3, 'HP Pavilion 15', 'CPU: i5, 12450H, 2GHz', 18500, 'hp-pavilion-15.jpg', '2024-08-22 16:58:53'),
(9, 5, 'Lenovo Slim 3', 'CPU: Ryzen 5, 7520U', 29190, 'lenovo-ideapad-slim-3.jpg', '2024-08-22 16:58:53'),
(10, 2, 'Acer Aspire Lite 14', 'Card: Intel Iris Xe', 13490, 'acer-aspire-lite-14.jpg', '2024-08-22 16:58:53'),
(14, 1, 'Macbook Air 14', '11111', 10000, 'macbook-air-14.jpg', '2024-09-28 16:35:01'),
(16, 1, 'Thanh Pha X', '11', -1, 'x.png', '2024-09-28 17:11:27'),
(17, 2, 'Thanh Pha XX', '1', 1, 'macbook-air-14.jpg', '2024-10-05 07:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastLogin` timestamp NULL DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `IsAdmin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `PasswordHash`, `Email`, `CreatedAt`, `LastLogin`, `IsActive`, `IsAdmin`) VALUES
(1, 'thanhpha', '$2y$10$.9hl.jY5HDiDPPTQdopuheaiNyUXZpcGBmP6zKQoeMHB6j16YZBty', '2230140008@sv.hotec.edu.vn', '2024-09-21 06:20:53', '2024-10-05 07:51:44', 1, 0),
(2, 'dang', '$2y$10$pKkp7JZUr03.uhh9BYtcFuFYOicHo.P8L1YU.tyewOpzuRSXdl2j.', 'sss@sss', '2024-09-28 06:40:34', '2024-09-28 07:54:41', 1, 0),
(5, 'admin', '$2y$10$aiXp4Bzr/sacg/cM4BIbUe3bhQ9OtnZ44maUrUYVVQRrw1rPeDWjC', 'phamducdang123@gmail.com', '2024-10-10 06:53:33', '2024-10-10 07:13:49', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_users` (`Username`);

--
-- Indexes for table `productcategory`
--
ALTER TABLE `productcategory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1728114733;

--
-- AUTO_INCREMENT for table `productcategory`
--
ALTER TABLE `productcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`Username`) REFERENCES `users` (`Username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `productcategory` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
