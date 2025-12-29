-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 11:45 AM
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
  `email` varchar(100) DEFAULT NULL,
  `guests` int(11) DEFAULT 1,
  `room_id` int(11) DEFAULT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  `checkin` date DEFAULT NULL,
  `checkout` date DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `extra_bed_included` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Confirmed','Pending','Checked-in','Checked-out','Cancelled') DEFAULT 'Pending',
  `payment_status` enum('Paid','Partial','Pending') DEFAULT 'Pending',
  `razorpay_id` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_name`, `phone`, `email`, `guests`, `room_id`, `room_number`, `checkin`, `checkout`, `total_price`, `extra_bed_included`, `created_at`, `status`, `payment_status`, `razorpay_id`, `notes`) VALUES
(2, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 2, 21, '2', '2025-12-16', '2025-12-17', 4935.00, 1, '2025-12-16 09:23:36', 'Pending', 'Pending', NULL, ''),
(3, 'Herin Patel', '452467', '', 1, 21, '3', '2025-12-19', '2025-12-20', 4725.00, 0, '2025-12-19 09:43:20', 'Pending', 'Pending', NULL, ''),
(4, 'Herin Alkeshkumar Patel', '09023897448', '', 1, 21, '4', '2025-12-17', '2025-12-18', 4935.00, 1, '2025-12-17 09:49:59', 'Pending', 'Pending', NULL, ''),
(5, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 1, 21, '5', '2025-12-16', '2025-12-17', 4935.00, 1, '2025-12-16 10:11:14', 'Pending', 'Pending', NULL, '');

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
(7, 'uploads/1765083424_69350920a8b73.JPG', 'Luxury Suite', '2025-12-07 04:57:04'),
(9, 'uploads/1765083424_69350920ad626.JPG', 'Luxury Suite', '2025-12-07 04:57:04');

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
(4, 'uploads/WhatsApp Image 2025-09-20 at 18.57.59_23e0ed2d.jpg'),
(5, 'uploads/DSC06366.JPG'),
(6, 'uploads/1765082060_693503cc9dc32.JPG');

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
  `extra_bed_price` decimal(10,2) DEFAULT 0.00,
  `size` varchar(50) DEFAULT NULL,
  `bed_type` varchar(100) DEFAULT NULL,
  `guests` varchar(50) DEFAULT NULL,
  `floor` varchar(50) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `reviews` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `ac_status` enum('AC','Non-AC') DEFAULT 'AC',
  `amenities` text DEFAULT NULL,
  `features` text DEFAULT NULL,
  `policies` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Available','Occupied') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `price`, `discount_price`, `extra_bed_price`, `size`, `bed_type`, `guests`, `floor`, `rating`, `reviews`, `image`, `ac_status`, `amenities`, `features`, `policies`, `created_at`, `status`) VALUES
(19, 'Luxury Suite', 'A well-organized room ideal for two guests, featuring ample space, clean beds, cupboards, and a peaceful atmosphere for a comfortable long stay.', 7000.00, 6000.00, 0.00, '350sq', '2', '2', 'Ground Floor', 4.9, 299, '1765083695_DSC06391.JPG,1765083695_DSC06390.JPG', 'AC', 'Free Wi-Fi,AC,Room Service,TV', 'Smart TV,Work Desk', 'No Smoking,Pet Friendly,Free Cancellation', '2025-12-07 05:01:35', 'Available'),
(21, 'Luxury Suite', 'best room', 5000.00, 4500.00, 200.00, '350 sq ft', '2', '2', 'Ground Floor', 4.9, 124, '1765876899_0_DSC06391.JPG,1765876899_1_DSC06390.JPG', 'AC', 'Free Wi-Fi,AC,Room Service,TV', 'Balcony', 'No Smoking,Pet Friendly,Free Cancellation', '2025-12-16 09:21:39', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `room_numbers`
--

CREATE TABLE `room_numbers` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `floor` varchar(50) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `status` enum('Available','Occupied','Maintenance') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_numbers`
--

INSERT INTO `room_numbers` (`id`, `room_type_id`, `floor`, `room_number`, `status`) VALUES
(2, 21, 'Ground Floor', '2', 'Occupied'),
(3, 21, 'Ground Floor', '3', 'Occupied'),
(4, 21, 'Ground Floor', '4', 'Occupied'),
(5, 21, 'Ground Floor', '5', 'Occupied'),
(6, 21, 'Ground Floor', '6', 'Available'),
(7, 21, 'Ground Floor', '7', 'Available'),
(8, 21, 'Ground Floor', '8', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('email_address', 'shaktibhuvanambaji@gmail.com'),
('phone_number', '+91 98765 43210'),
('physical_address', 'Shakti bhuvan, GJ SH 56, Shaktidhara Society, Ambaji, Gujarat 385110');

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
('CUST1322', 'Herin Patel', '', '452467', NULL, '2025-12-19', 1, 4725.00, NULL, 'ACTIVE', '2025-12-19 09:43:20'),
('CUST8151', 'Herin Alkeshkumar Patel', 'herin7151@gmail.com', '09023897448', NULL, '2025-12-16', 3, 14805.00, NULL, 'ACTIVE', '2025-12-16 09:23:36');

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
-- Indexes for table `room_numbers`
--
ALTER TABLE `room_numbers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_room_number` (`room_number`),
  ADD KEY `fk_room_type` (`room_type_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `idx_phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hero_section`
--
ALTER TABLE `hero_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `room_numbers`
--
ALTER TABLE `room_numbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `room_numbers`
--
ALTER TABLE `room_numbers`
  ADD CONSTRAINT `fk_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
