-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 31, 2025 at 11:57 AM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `otp` varchar(10) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`, `otp`, `otp_expiry`) VALUES
(1, 'admin123@gmail.com', '11', NULL, NULL);

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
  `extra_bed_included` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Confirmed','Pending','Checked-in','Checked-out','Cancelled') DEFAULT 'Pending',
  `payment_status` enum('Paid','Partial','Pending') DEFAULT 'Pending',
  `razorpay_id` varchar(50) DEFAULT NULL,
  `bank_rrn` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `auto_checkout_done` tinyint(1) DEFAULT 0,
  `num_rooms` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_name`, `phone`, `email`, `guests`, `room_id`, `room_number`, `checkin`, `checkout`, `total_price`, `extra_bed_included`, `created_at`, `status`, `payment_status`, `razorpay_id`, `bank_rrn`, `notes`, `auto_checkout_done`, `num_rooms`) VALUES
(168, 'Herin Alkeshkumar Patel', '9023897448', 'herin7151@gmail.com', 0, 43, NULL, '2025-12-31', '2026-01-01', 900.00, 0, '2025-12-31 10:49:37', 'Pending', 'Pending', NULL, NULL, '', 0, 1);

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
  `image_type` enum('Hotel View','Luxury Suite','Deluxe Room','Standard Room','Standard Non-AC') NOT NULL,
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
(22, 'uploads/1766406437_6949392517b52.JPG', 'Hotel View', '2025-12-22 12:27:17'),
(29, 'uploads/1767170385_6954e15144bc6.jpg', 'Standard Non-AC', '2025-12-31 08:39:45'),
(30, 'uploads/1767170385_6954e15147dd3.jpg', 'Standard Non-AC', '2025-12-31 08:39:45'),
(31, 'uploads/1767170385_6954e151484de.jpg', 'Standard Non-AC', '2025-12-31 08:39:45'),
(32, 'uploads/1767170531_6954e1e35366a.jpg', 'Deluxe Room', '2025-12-31 08:42:11'),
(33, 'uploads/1767170531_6954e1e3541f5.jpg', 'Deluxe Room', '2025-12-31 08:42:11'),
(34, 'uploads/1767170531_6954e1e35484e.jpg', 'Deluxe Room', '2025-12-31 08:42:11'),
(35, 'uploads/1767170531_6954e1e3550c1.jpg', 'Deluxe Room', '2025-12-31 08:42:11'),
(36, 'uploads/1767170531_6954e1e3556ed.jpg', 'Deluxe Room', '2025-12-31 08:42:11'),
(37, 'uploads/1767170553_6954e1f920b1e.jpg', 'Deluxe Room', '2025-12-31 08:42:33'),
(38, 'uploads/1767170553_6954e1f921851.jpg', 'Deluxe Room', '2025-12-31 08:42:33'),
(39, 'uploads/1767170553_6954e1f921f47.jpg', 'Deluxe Room', '2025-12-31 08:42:33'),
(40, 'uploads/1767170553_6954e1f922560.jpg', 'Deluxe Room', '2025-12-31 08:42:33'),
(41, 'uploads/1767170553_6954e1f922b0b.jpg', 'Deluxe Room', '2025-12-31 08:42:33'),
(42, 'uploads/1767170569_6954e2093d3b6.jpg', 'Deluxe Room', '2025-12-31 08:42:49'),
(43, 'uploads/1767170569_6954e2093dc0f.jpg', 'Deluxe Room', '2025-12-31 08:42:49'),
(44, 'uploads/1767170569_6954e2093e18d.jpg', 'Deluxe Room', '2025-12-31 08:42:49'),
(45, 'uploads/1767170569_6954e2093ea87.jpg', 'Deluxe Room', '2025-12-31 08:42:49'),
(47, 'uploads/1767170620_6954e23c11fb5.jpg', 'Standard Room', '2025-12-31 08:43:40'),
(48, 'uploads/1767170620_6954e23c12bfa.jpg', 'Standard Room', '2025-12-31 08:43:40'),
(49, 'uploads/1767170620_6954e23c1382e.jpg', 'Standard Room', '2025-12-31 08:43:40'),
(50, 'uploads/1767170620_6954e23c14476.jpg', 'Standard Room', '2025-12-31 08:43:40');

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
(9, 'uploads/1766399747_69491f03486b7.JPG');

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
  `payment_status` enum('Paid','Pending','Partial') DEFAULT 'Pending',
  `status` varchar(20) DEFAULT 'Checked-in'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `razorpay_payment_id` varchar(100) DEFAULT NULL,
  `bank_rrn` varchar(100) DEFAULT NULL,
  `payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `razorpay_payment_id`, `bank_rrn`, `payment_date`) VALUES
(5, '147', 1155.00, NULL, NULL, '2025-12-26'),
(6, '149', 900.00, NULL, NULL, '2025-12-29'),
(7, '150', 3465.00, NULL, NULL, '2025-12-29'),
(8, '151', 900.00, NULL, NULL, '2025-12-29'),
(9, '152', 900.00, NULL, NULL, '2025-12-29'),
(13, '156', 1.00, 'pay_Rxn8g9wyLQ2hdf', NULL, '2025-12-30'),
(16, '162', 1.00, 'pay_Ry6gbaMWtOpuy3', NULL, '2025-12-31'),
(17, '163', 1.00, 'pay_Ry6oG7fdyO9ABo', NULL, '2025-12-31'),
(18, '164', 1.00, 'pay_Ry6vtq8B0xqKbE', NULL, '2025-12-31'),
(19, '165', 1.00, 'pay_Ry769d0CgLYdBp', NULL, '2025-12-31'),
(20, '166', 1.00, 'pay_Ry7Cnf1gnvjMGl', NULL, '2025-12-31'),
(21, '167', 1837.50, 'pay_Ry7cu4X7MpDIPD', NULL, '2025-12-31');

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
(43, 'Standard Non-AC (Double Bed)', 'A comfortable and budget-friendly room designed for a pleasant stay. The Standard Non-AC Double Bed room features a spacious double bed with clean linens, ensuring a good night’s rest. The room is well-ventilated and naturally lit, offering a calm and relaxing atmosphere. It includes essential amenities such as a private bathroom with hot and cold water, a wardrobe, a study table, and basic toiletries. Ideal for couples or solo travelers looking for comfort at an affordable price without air conditioning.', 1080.00, 900.00, 200.00, 1, '600 sq ft', 'Double Bed', '2', '1st Floor', 4.8, 195, '1767156333_0_621256.jpg,1767156333_1_621258.jpg,1767156333_2_621257.jpg', 'Non-AC', 'Free Wi-Fi,Room Service', '', 'No Smoking,Free Cancellation', '2025-12-31 04:45:33', 'Available'),
(44, 'Family Room (First Floor)', 'A spacious and comfortable room specially designed for families. The Family Room Non-AC offers ample space with multiple beds to accommodate family members comfortably. The room is well-ventilated with natural light, creating a pleasant and homely environment. It includes essential amenities such as a private bathroom with hot and cold water, clean linens, seating space, a wardrobe, and basic toiletries. Ideal for families seeking a comfortable and economical stay without air conditioning.', 3300.00, 2750.00, 0.00, 0, '900sq', 'Single Bed', '8', '1st Floor', 4.5, 156, '1767157363_0_646467.jpg,1767157363_1_646468.jpg', 'Non-AC', 'Free Wi-Fi,Room Service', '', 'Free Cancellation', '2025-12-31 05:02:43', 'Available'),
(45, 'Delux AC (Ground Floor)', 'A premium and comfortable room designed for a relaxing stay. The Deluxe AC Double Bed room features a spacious double bed with soft linens, ensuring maximum comfort. Equipped with air conditioning, the room offers a cool and pleasant atmosphere throughout your stay. It includes modern amenities such as a private bathroom with hot and cold water, wardrobe, study table, seating area, television, and complimentary toiletries. Ideal for couples or business travelers looking for extra comfort and a touch of luxury.', 1440.00, 1200.00, 300.00, 1, '300sq', 'Double Bed', '7', 'Ground Floor', 5.0, 256, '1767157499_0_482710.jpg,1767157499_1_482709.jpg,1767157499_2_482712.jpg,1767157499_3_482713.jpg,1767157499_4_482711.jpg', 'AC', 'Free Wi-Fi,AC,Room Service', '', 'No Smoking,Free Cancellation', '2025-12-31 05:04:59', 'Available'),
(46, 'Super Delux AC (Ground Floor)', 'A spacious and premium room ideal for families or small groups. The Super Deluxe AC room features three comfortable beds with fresh linens, providing ample space for a relaxed stay. Fully air-conditioned, the room ensures a cool and pleasant environment at all times. It is well-furnished with modern amenities including a private bathroom with hot and cold water, wardrobe, seating area, study table, television, and complimentary toiletries. Perfect for guests seeking extra space, comfort, and a luxurious stay experience.', 2700.00, 2250.00, 300.00, 2, '450sq', 'Single Bed', '3', 'Ground Floor', 4.2, 578, '1767157712_0_625448.jpg,1767157712_1_625447.jpg,1767157712_2_625450.jpg,1767157712_3_625451.jpg', 'AC', 'Free Wi-Fi,AC,Room Service', 'Sea View', 'No Smoking,Free Cancellation', '2025-12-31 05:08:32', 'Available'),
(47, 'Super Delux AC (First Floor)', 'A large and luxurious room designed for families or groups traveling together. The Super Deluxe AC Four Bed room offers four comfortable beds with clean, soft linens, ensuring a restful stay for all guests. Fully air-conditioned, the room provides a cool and refreshing atmosphere. It comes equipped with modern amenities including a private bathroom with hot and cold water, spacious seating area, wardrobe, study table, television, and complimentary toiletries. Ideal for guests who need extra space, comfort, and a premium stay experience.', 3300.00, 2750.00, 300.00, 3, '600 sq ft', 'Double Bed', '4', '1st Floor', 4.3, 315, '1767157912_0_646461.jpg,1767157912_1_646458.jpg,1767157912_2_646440.jpg,1767157912_3_646459.jpg,1767157912_4_646439.jpg', 'AC', 'Free Wi-Fi,AC,Room Service,TV', 'Mountain View,Smart TV,Work Desk', 'No Smoking,Free Cancellation', '2025-12-31 05:11:52', 'Available'),
(48, 'Executive AC (First Floor)', 'A stylish and well-appointed room designed for comfort and convenience. The Executive AC Double Bed room features a spacious double bed with premium linens for a restful sleep. Equipped with air conditioning, the room maintains a pleasant and relaxing ambiance throughout your stay. It includes modern amenities such as a private bathroom with hot and cold water, wardrobe, work desk, comfortable seating area, television, and complimentary toiletries. Ideal for business travelers and couples seeking enhanced comfort with a touch of elegance.', 2100.00, 1750.00, 400.00, 1, '300sq', 'Double Bed', '2', '1st Floor', 4.8, 627, '1767158850_2_287647.jpg,1767158850_3_287632.jpg,1767158850_4_287635.jpg,1767158850_1_287650.jpg', 'AC', 'Free Wi-Fi,AC,Room Service,TV', 'Mountain View,Smart TV', 'No Smoking,Free Cancellation', '2025-12-31 05:27:30', 'Available'),
(49, 'Executive AC (Second Floor) ', 'A stylish and well-appointed room designed for comfort and convenience. The Executive AC Double Bed room features a spacious double bed with premium linens for a restful sleep. Equipped with air conditioning, the room maintains a pleasant and relaxing ambiance throughout your stay. It includes modern amenities such as a private bathroom with hot and cold water, wardrobe, work desk, comfortable seating area, television, and complimentary toiletries. Ideal for business travelers and couples seeking enhanced comfort with a touch of elegance.', 2100.00, 1750.00, 400.00, 1, '300sq', 'Double Bed', '2', '2nd Floor', 4.3, 320, '1767159050_2_287647.jpg,1767159050_3_287632.jpg,1767159050_4_287635.jpg,1767159050_0_287587.jpg,1767159050_1_287650.jpg', 'AC', 'Free Wi-Fi,AC,Room Service,TV', 'Mountain View,Smart TV', 'No Smoking,Free Cancellation', '2025-12-31 05:30:50', 'Available'),
(50, 'Benquet Hall ', 'A spacious and well-equipped banquet hall ideal for weddings, receptions, conferences, meetings, and social gatherings. Spanning 3000 square feet, the hall offers ample space to comfortably accommodate a large number of guests. It features a clean, elegant interior with flexible seating arrangements to suit different event needs. The hall is well-ventilated and supported with essential facilities such as lighting, power backup, and restrooms. Available for a duration of 10 hours, it is perfect for hosting memorable events in a comfortable and convenient setting.', 30000.00, 25000.00, 0.00, 0, '3000 sq ft', 'N/A', '300', 'Ground Floor', 4.0, 250, '1767159216_0_287644.jpg,1767159216_1_287641.jpg', 'Non-AC', 'Room Service', 'Mountain View', 'No Smoking,Free Cancellation', '2025-12-31 05:33:36', 'Available');

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
(59, 43, '1st Floor', '115', 'Available'),
(60, 43, '1st Floor', '116', 'Available'),
(61, 43, '1st Floor', '117', 'Available'),
(62, 43, '1st Floor', '118', 'Available'),
(63, 43, '1st Floor', '119', 'Available'),
(64, 43, '1st Floor', '120', 'Available'),
(65, 43, '1st Floor', '121', 'Available'),
(66, 43, '1st Floor', '122', 'Available'),
(67, 44, '1st Floor', '123', 'Available'),
(68, 45, 'Ground Floor', '2', 'Available'),
(69, 45, 'Ground Floor', '3', 'Available'),
(70, 45, 'Ground Floor', '4', 'Available'),
(71, 45, 'Ground Floor', '5', 'Available'),
(72, 45, 'Ground Floor', '6', 'Available'),
(73, 45, 'Ground Floor', '7', 'Available'),
(74, 45, 'Ground Floor', '8', 'Available'),
(75, 46, 'Ground Floor', '11', 'Available'),
(76, 46, 'Ground Floor', '12', 'Available'),
(77, 46, 'Ground Floor', '13', 'Available'),
(78, 47, '1st Floor', '124', 'Available'),
(79, 47, '1st Floor', '125', 'Available'),
(80, 47, '1st Floor', '126', 'Available'),
(81, 48, '1st Floor', '1', 'Available'),
(82, 48, '1st Floor', '2', 'Available'),
(83, 48, '1st Floor', '3', 'Available'),
(84, 48, '1st Floor', '4', 'Available'),
(85, 48, '1st Floor', '5', 'Available'),
(86, 48, '1st Floor', '6', 'Available'),
(87, 48, '1st Floor', '7', 'Available'),
(88, 48, '1st Floor', '8', 'Available'),
(89, 48, '1st Floor', '9', 'Available'),
(90, 48, '1st Floor', '10', 'Available'),
(91, 48, '1st Floor', '11', 'Available'),
(92, 48, '1st Floor', '12', 'Available'),
(93, 48, '1st Floor', '13', 'Available'),
(94, 48, '1st Floor', '14', 'Available'),
(95, 48, '1st Floor', '15', 'Available'),
(96, 48, '1st Floor', '16', 'Available'),
(97, 49, '2nd Floor', '1', 'Available'),
(98, 49, '2nd Floor', '2', 'Available'),
(99, 49, '2nd Floor', '3', 'Available'),
(100, 49, '2nd Floor', '4', 'Available'),
(101, 49, '2nd Floor', '5', 'Available'),
(102, 49, '2nd Floor', '6', 'Available'),
(103, 49, '2nd Floor', '7', 'Available'),
(104, 49, '2nd Floor', '8', 'Available'),
(105, 49, '2nd Floor', '9', 'Available'),
(106, 49, '2nd Floor', '10', 'Available'),
(107, 49, '2nd Floor', '11', 'Available'),
(108, 49, '2nd Floor', '12', 'Available'),
(109, 49, '2nd Floor', '13', 'Available'),
(110, 49, '2nd Floor', '14', 'Available'),
(111, 49, '2nd Floor', '15', 'Available'),
(112, 49, '2nd Floor', '16', 'Available'),
(113, 50, 'Ground Floor', '1', 'Available');

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
('CUST2013', 'Herin Alkeshkumar Patel', 'herin7151@gmail.com', '9023897448', NULL, '2025-12-31', 1, 900.00, NULL, 'ACTIVE', '2025-12-31 10:49:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `unique_room_floor` (`room_number`,`floor`),
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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `hero_section`
--
ALTER TABLE `hero_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `offline_booking`
--
ALTER TABLE `offline_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `room_numbers`
--
ALTER TABLE `room_numbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

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
