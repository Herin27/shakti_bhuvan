-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2025 at 05:14 AM
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
-- Database: `shakti_bhuvan`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `guests` int(11) DEFAULT 1,
  `room_id` int(11) DEFAULT NULL,
  `checkin` date DEFAULT NULL,
  `checkout` date DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Confirmed','Pending','Checked-in','Checked-out','Cancelled') DEFAULT 'Pending',
  `payment_status` enum('Paid','Partial','Pending') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `checkin` date DEFAULT NULL,
  `checkout` date DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `fullname`, `email`, `phone`, `subject`, `checkin`, `checkout`, `message`, `created_at`) VALUES
(1, 'Herin Patel', 'herin7151@gmail.com', '452467', 'contect', '2025-08-19', '2025-08-19', 'i need room', '2025-08-19 11:17:38');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_percent` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `image_type` enum('Hotel View','Luxury Suite','Deluxe Room','Standard Room') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `image_url`, `image_type`, `created_at`) VALUES
(2, 'uploads/1758427814_WhatsApp Image 2025-09-20 at 18.57.58_b7a89080.jpg', 'Hotel View', '2025-09-21 04:10:14'),
(3, 'uploads/1758427814_WhatsApp Image 2025-09-20 at 18.57.59_23e0ed2d.jpg', 'Hotel View', '2025-09-21 04:10:14'),
(4, 'uploads/1758427814_DSC06359.JPG', 'Hotel View', '2025-09-21 04:10:14'),
(5, 'uploads/1759234491_DSC06391.JPG', 'Luxury Suite', '2025-09-30 12:14:51'),
(6, 'uploads/1759234491_DSC06390.JPG', 'Luxury Suite', '2025-09-30 12:14:51');

-- --------------------------------------------------------

--
-- Table structure for table `hero_section`
--

CREATE TABLE `hero_section` (
  `id` int(11) NOT NULL,
  `background_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_section`
--

INSERT INTO `hero_section` (`id`, `background_image`) VALUES
(3, 'uploads/DSC06359.JPG'),
(4, 'uploads/WhatsApp Image 2025-09-20 at 18.57.59_23e0ed2d.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `payment_date`) VALUES
(1, 'BK001', 25000.00, '2024-08-15'),
(2, 'BK002', 12000.00, '2024-08-16'),
(3, 'BK003', 40000.00, '2024-08-17');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `bed_type` varchar(100) DEFAULT NULL,
  `guests` varchar(50) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `reviews` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `features` text DEFAULT NULL,
  `policies` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Available','Occupied') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `price`, `discount_price`, `size`, `bed_type`, `guests`, `rating`, `reviews`, `image`, `amenities`, `features`, `policies`, `created_at`, `status`) VALUES
(3, 'Luxury Suite', 'Premium suite with separate living area, private balcony, and luxurious amenities for the ultimate comfort experience.', 6500.00, 5500.00, '600 sq ft', 'King Size Bed', '4', 4.9, 124, 'premium_photo-1661877303180-19a028c21048.avif', 'Free Wi-Fi,AC,Room Service,TV,Parking,Swimming Pool', 'Sea View,Balcony,Jacuzzi,Smart TV,Work Desk', 'No Smoking,Pet Friendly,Free Cancellation,Check-in after 12 PM,Check-out before 11 AM', '2025-08-23 08:58:34', 'Available'),
(4, 'Luxury Suite', 'best', 5000.00, 500.00, '350 sq ft', 'King Size Bed', '2', 4.8, 124, '1759143589_DSC06391.JPG,1759143589_DSC06390.JPG', 'Free Wi-Fi,AC,Room Service,TV,parking', 'Balcony,Smart TV', 'No Smoking', '2025-09-29 10:59:49', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `customer_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `member_since` date DEFAULT curdate(),
  `bookings` int(11) DEFAULT 0,
  `total_spent` decimal(10,2) DEFAULT 0.00,
  `rating` decimal(3,1) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE','VIP') DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`customer_id`, `name`, `email`, `phone`, `location`, `member_since`, `bookings`, `total_spent`, `rating`, `status`, `created_at`) VALUES
('CUST1953', 'herin', 'patelherin15@gmail.com', '9023897448', 'Gandhinagar ', '2025-09-01', 22, 68550.00, NULL, 'VIP', '2025-09-01 05:30:30'),
('CUST9430', 'Herin Patel', 'herin7151@gmail.com', '452467', 'Kherava , Mehsana ', '2025-09-01', 23, 79050.00, NULL, 'VIP', '2025-09-01 03:35:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hero_section`
--
ALTER TABLE `hero_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hero_section`
--
ALTER TABLE `hero_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
