-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2025 at 06:10 AM
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
  `notes` text DEFAULT NULL,
  `auto_checkout_done` tinyint(1) DEFAULT 0,
  `num_rooms` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_name`, `phone`, `email`, `guests`, `room_id`, `room_number`, `checkin`, `checkout`, `total_price`, `extra_bed_included`, `created_at`, `status`, `payment_status`, `razorpay_id`, `notes`, `auto_checkout_done`, `num_rooms`) VALUES
(88, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '11', '2025-12-20', '2025-12-21', 3150.00, 0, '2025-12-20 10:49:29', 'Checked-out', 'Paid', 'pay_RtRWorrYIWj7tN', '', 0, 1),
(91, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '12', '2025-12-20', '2025-12-21', 3150.00, 0, '2025-12-20 11:26:32', 'Checked-out', 'Paid', 'pay_RtSAFJqfxAer1E', '', 0, 1),
(93, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '4', '2025-12-20', '2025-12-21', 10030.00, 0, '2025-12-20 11:52:28', 'Checked-out', 'Paid', 'pay_RtSbRHiZKx8c2n', '', 0, 1),
(97, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '10', '2025-12-20', '2025-12-21', 900.00, 0, '2025-12-20 15:05:44', 'Checked-out', 'Paid', 'pay_RtVtsn9SJ8TFJy', '', 0, 1),
(102, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '10', '2025-12-21', '2025-12-22', 900.00, 0, '2025-12-21 03:49:56', 'Checked-out', 'Paid', 'pay_Rtiv8QTSyzbIhU', '', 0, 1),
(104, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '10', '2025-12-20', '2025-12-21', 900.00, 0, '2025-12-20 08:32:10', 'Checked-out', 'Paid', 'pay_RtnjhDf4aIs95c', '', 0, 1),
(106, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '11', '2025-12-20', '2025-12-21', 3150.00, 0, '2025-12-20 08:38:17', 'Checked-out', 'Paid', 'pay_RtnqDYw0nX1j1V', '', 0, 1),
(107, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '10', '2025-12-21', '2025-12-22', 900.00, 0, '2025-12-21 05:35:36', 'Checked-out', 'Paid', 'pay_RtoZDBvov5NmOx', '', 0, 1),
(108, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '10', '2025-12-22', '2025-12-23', 900.00, 0, '2025-12-22 05:44:32', 'Checked-out', 'Paid', 'pay_Rtoj1j3r7xl67z', '', 0, 1),
(110, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '10', '2025-12-20', '2025-12-21', 900.00, 0, '2025-12-20 05:39:18', 'Checked-out', 'Paid', 'pay_RtpH3iH2TjOf7h', '', 0, 1),
(111, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '10', '2025-12-21', '2025-12-22', 900.00, 0, '2025-12-21 05:58:43', 'Checked-out', 'Paid', 'pay_RtpblKwV6QE4A0', '', 0, 1),
(113, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '10', '2025-12-22', '2025-12-23', 900.00, 0, '2025-12-22 06:10:23', 'Checked-out', 'Paid', 'pay_RtpoKiW4oINv0L', '', 0, 1),
(117, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '11', '2025-12-23', '2025-12-24', 3150.00, 0, '2025-12-21 03:53:41', 'Confirmed', 'Paid', 'pay_Ru7VzEJ8mEcJcp', '', 0, 1),
(118, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '10', '2025-12-25', '2025-12-26', 900.00, 0, '2025-12-23 09:24:12', 'Confirmed', 'Paid', 'pay_RuDAjGhDE0tq9w', '', 0, 1),
(123, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '12', '2025-12-23', '2025-12-24', 3150.00, 0, '2025-12-23 09:48:04', 'Confirmed', 'Paid', 'pay_RuDYDno8nLeVeU', '', 0, 1),
(129, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '12', '2025-12-21', '2025-12-22', 3150.00, 0, '2025-12-21 10:18:52', 'Confirmed', 'Paid', 'pay_RuE4fFoqUA0R6c', '', 0, 1),
(130, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '4', '2025-12-21', '2025-12-22', 10030.00, 0, '2025-12-21 10:49:39', 'Confirmed', 'Paid', 'pay_RuEbL4oHsODwCJ', '', 0, 1),
(133, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '10', '2025-12-22', '2025-12-23', 900.00, 0, '2025-12-22 03:40:50', 'Confirmed', 'Paid', 'pay_RuVpOklDmGbuYg', '', 0, 1),
(134, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, NULL, '2025-12-23', '2025-12-24', 900.00, 0, '2025-12-22 03:42:25', 'Pending', 'Pending', NULL, '', 0, 1),
(135, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, NULL, '110', '2025-12-22', '2025-12-23', 1575.00, 5, '2025-12-22 10:24:35', 'Confirmed', 'Paid', 'pay_Ruci5zC5j70SrP', '', 0, 1),
(143, 'Herin Alkeshkumar Patel', '09023897448', 'herin7151@gmail.com', 0, 39, '115', '2025-12-23', '2025-12-24', 900.00, 0, '2025-12-23 04:47:34', 'Confirmed', 'Paid', 'pay_RuvWbwTYD5mOMK', '', 0, 1);

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
(1, 'Herin Patel', 'herin7151@gmail.com', '452467', 'contect', '2025-08-19', '2025-08-19', 'i need room', '2025-08-19 11:17:38'),
(2, 'fsdfsd', 'fsdf#@fsdfsf', '', '', '0000-00-00', '0000-00-00', 'dfsdfsd', '2025-12-17 06:15:01'),
(3, 'Herin Patel', 'herin7151@gmail.com', '452467', '', '0000-00-00', '0000-00-00', 'adsfgsd', '2025-12-19 03:45:44');

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
(10, 'uploads/1766406437_6949392510698.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(11, 'uploads/1766406437_6949392511e1e.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(12, 'uploads/1766406437_69493925126a6.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(13, 'uploads/1766406437_6949392512eab.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(14, 'uploads/1766406437_6949392513751.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(15, 'uploads/1766406437_6949392513fc9.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(16, 'uploads/1766406437_694939251473a.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(17, 'uploads/1766406437_6949392515045.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(18, 'uploads/1766406437_694939251573c.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(19, 'uploads/1766406437_6949392515ecf.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(20, 'uploads/1766406437_6949392516cd6.jpg', 'Hotel View', '2025-12-22 12:27:17'),
(21, 'uploads/1766406437_6949392517336.jpg', 'Hotel View', '2025-12-22 12:27:17'),
(22, 'uploads/1766406437_6949392517b52.JPG', 'Hotel View', '2025-12-22 12:27:17');

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
(7, 'uploads/1766399715_69491ee3b0038.jpg'),
(8, 'uploads/1766399736_69491ef8efe30.JPG'),
(9, 'uploads/1766399747_69491f03486b7.JPG'),
(10, 'uploads/1766466588_694a241c3f956.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `offline_booking`
--

CREATE TABLE `offline_booking` (
  `id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `checkin_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `checkout_date` date DEFAULT NULL,
  `payment_status` enum('Paid','Pending','Partial') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `max_extra_beds` int(11) DEFAULT 0,
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
  `status` enum('Available','Occupied','Cleaning') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `price`, `discount_price`, `extra_bed_price`, `max_extra_beds`, `size`, `bed_type`, `guests`, `floor`, `rating`, `reviews`, `image`, `ac_status`, `amenities`, `features`, `policies`, `created_at`, `status`) VALUES
(39, 'Standard Non-AC', 'Standard Non-AC Room offers a comfortable stay with basic amenities, ideal for budget-friendly travelers. The room is well-maintained and provides a peaceful environment for a pleasant stay.', 1080.00, 900.00, 200.00, 1, '350', 'Double Bed', '2', 'First Floor', 5.0, 119, '1766399476_0_DSC06392.JPG,1766399476_1_DSC06383.JPG,1766399476_2_DSC06380.JPG,1766399476_3_DSC06378.JPG,1766399476_4_DSC06376.JPG', 'Non-AC', 'Free Wi-Fi,Room Service,TV', 'Sea View,Smart TV', 'Free Cancellation', '2025-12-22 10:31:16', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `room_numbers`
--

CREATE TABLE `room_numbers` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `floor` varchar(50) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `status` enum('Available','Occupied','Maintenance','Cleaning') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_numbers`
--

INSERT INTO `room_numbers` (`id`, `room_type_id`, `floor`, `room_number`, `status`) VALUES
(47, 39, 'First Floor', '115', 'Available'),
(48, 39, 'First Floor', '116', 'Available'),
(49, 39, 'First Floor', '117', 'Available'),
(50, 39, 'First Floor', '118', 'Available'),
(51, 39, 'First Floor', '119', 'Available'),
(52, 39, 'First Floor', '120', 'Available'),
(53, 39, 'First Floor', '121', 'Available'),
(54, 39, 'First Floor', '122', 'Available');

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
('email_address', 'info@shaktibhuvan.com'),
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
('CUST1195', 'TEST', 'TEEFSDFSDS@FSDF', '1234567890', NULL, '2025-12-23', 1, 3150.00, NULL, 'ACTIVE', '2025-12-23 12:47:25'),
('CUST4392', 'Herin Patel', 'patelherin15@gmail.com', '6351193590', NULL, '2025-12-17', 1, 4725.00, NULL, 'ACTIVE', '2025-12-17 03:50:10'),
('CUST7900', 'fsdfsd', 'fsdfsd@fgsd.fasd', '1122334455', NULL, '2025-12-20', 1, 9450.00, NULL, 'ACTIVE', '2025-12-20 07:01:12'),
('CUST8151', 'Herin Alkeshkumar Patel', 'herin7151@gmail.com', '09023897448', NULL, '2025-12-16', 124, 545174.50, NULL, 'ACTIVE', '2025-12-16 09:23:36'),
('CUST9725', 'Herin Patel', '23012012027@gnu.ac.in', '9023897448', NULL, '2025-12-18', 1, 10030.00, NULL, 'ACTIVE', '2025-12-18 07:57:53');

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
-- Indexes for table `offline_booking`
--
ALTER TABLE `offline_booking`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `hero_section`
--
ALTER TABLE `hero_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `offline_booking`
--
ALTER TABLE `offline_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `room_numbers`
--
ALTER TABLE `room_numbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `room_numbers`
--
ALTER TABLE `room_numbers`
  ADD CONSTRAINT `fk_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
