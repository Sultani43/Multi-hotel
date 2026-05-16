-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2026 at 01:11 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `multi_hotel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bill_items`
--

CREATE TABLE `bill_items` (
  `bill_item_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `item_type` enum('room','food','other') DEFAULT 'room',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled') DEFAULT 'pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `check_in_time` time DEFAULT '14:00:00' COMMENT 'د ننوتلو وخت (عموماً ۱۴:۰۰)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `hotel_id`, `guest_id`, `room_id`, `check_in_date`, `check_out_date`, `total_price`, `status`, `booking_date`, `check_in_time`) VALUES
(30, 12, 25, 31, '2026-04-12', '2026-04-30', '21816.00', 'confirmed', '2026-04-12 19:03:59', '14:00:00'),
(31, 12, 27, 32, '2026-07-15', '2026-07-24', '10800.00', 'confirmed', '2026-07-12 21:57:53', '19:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `booking_occupants`
--

CREATE TABLE `booking_occupants` (
  `occupant_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `full_name` varchar(100) NOT NULL,
  `id_card_number` varchar(50) DEFAULT NULL COMMENT 'د تذکرې یا پاسپورټ نمبر',
  `age` int(11) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL COMMENT 'اصلي ولایت',
  `permanent_address` text DEFAULT NULL COMMENT 'اصلي استوګنځای',
  `current_address` text DEFAULT NULL COMMENT 'اوسنی استوګنځای',
  `gender` enum('male','female','child') DEFAULT 'male',
  `travel_purpose` varchar(100) DEFAULT NULL COMMENT 'د سفر موخه (سوداګري، ګرځندوي، ...)',
  `relation_to_primary` varchar(50) DEFAULT NULL COMMENT 'اړیکه له اصلي میلمه سره',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_occupants`
--

INSERT INTO `booking_occupants` (`occupant_id`, `booking_id`, `is_primary`, `full_name`, `id_card_number`, `age`, `province`, `permanent_address`, `current_address`, `gender`, `travel_purpose`, `relation_to_primary`, `created_at`) VALUES
(16, 30, 0, 'احمد', '۸۷۸۷', 22, 'زابل', 'سنتب', 'نیمسبت', 'male', 'نسیتبنم', 'نسیتبن', '2026-04-12 19:03:59'),
(17, 30, 0, 'خان محمد', '۹۸۷۶۹۸۷۶۹', 14, 'یبنمستب', '', 'سیبتسمن', 'male', 'منسیتبنم', 'نسیتبنت', '2026-04-12 19:03:59'),
(19, 31, 1, 'Mohammad Kabir \"Afghan\" ', '54656', 23, 'KDR', 'kjkjh', 'vbcvbvb', 'male', 'cvbcvb', 'خپل ځان', '2026-07-12 21:57:53'),
(20, 31, 0, 'Haqmal', '`98709876897', 45, 'کندهار', 'gjhjh', 'lkdfjkdls', 'male', 'fvhgfhgf', 'مور', '2026-07-12 21:57:53');

-- --------------------------------------------------------

--
-- Table structure for table `daily_closing`
--

CREATE TABLE `daily_closing` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `close_date` date NOT NULL,
  `booking_revenue` decimal(10,2) DEFAULT 0.00,
  `food_revenue` decimal(10,2) DEFAULT 0.00,
  `other_revenue` decimal(10,2) DEFAULT 0.00,
  `total_revenue` decimal(10,2) DEFAULT 0.00,
  `closed_by` int(11) DEFAULT NULL,
  `closed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_closing`
--

INSERT INTO `daily_closing` (`id`, `hotel_id`, `close_date`, `booking_revenue`, `food_revenue`, `other_revenue`, `total_revenue`, `closed_by`, `closed_at`) VALUES
(2, 12, '2026-05-11', '0.00', '0.00', '0.00', '0.00', 19, '2026-05-11 17:17:02'),
(3, 12, '2026-07-12', '0.00', '0.00', '0.00', '0.00', 19, '2026-07-12 00:47:05'),
(4, 12, '2026-10-12', '16788.00', '0.00', '16788.00', '33576.00', 19, '2026-10-12 06:31:52'),
(5, 12, '2026-01-12', '0.00', '0.00', '0.00', '0.00', 19, '2026-01-12 07:38:12'),
(6, 12, '2026-04-12', '0.00', '36.00', '0.00', '36.00', 19, '2026-04-12 17:36:31');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `feedback_type` enum('bug','suggestion','feature_request','improvement','other') DEFAULT 'other',
  `subject` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `screenshot` varchar(255) DEFAULT NULL,
  `status` enum('pending','reviewed','implemented','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `hotel_id`, `admin_name`, `feedback_type`, `subject`, `description`, `rating`, `screenshot`, `status`, `created_at`) VALUES
(7, 12, 'Nazifullah \"Hisam\"', 'improvement', 'dklfjsdldjf', 'hjgkg', 4, 'feedback_1776016026_6098.jpg', 'pending', '2026-04-12 17:47:06');

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `item_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_items`
--

INSERT INTO `food_items` (`item_id`, `hotel_id`, `item_name`, `description`, `price`, `category`, `is_available`, `created_at`) VALUES
(8, 12, 'ساده کباب', 'wefsdf', '12.00', 'غرمه', 1, '2026-05-11 17:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `food_orders`
--

CREATE TABLE `food_orders` (
  `order_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','preparing','served','cancelled') DEFAULT 'pending',
  `payment_status` enum('paid','unpaid') DEFAULT 'unpaid',
  `special_instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_orders`
--

INSERT INTO `food_orders` (`order_id`, `hotel_id`, `guest_id`, `item_id`, `quantity`, `order_date`, `status`, `payment_status`, `special_instructions`) VALUES
(57, 12, 26, 8, 4, '2026-07-12 12:07:00', 'served', 'unpaid', 'مالګه باید ورسره وي'),
(58, 12, 28, 8, 4, '2026-07-13 03:35:55', 'served', 'unpaid', 'مالګه باید ورسره وي');

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `guest_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `id_card_number` varchar(50) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `gender` enum('male','female','child') DEFAULT 'male',
  `travel_purpose` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`guest_id`, `hotel_id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `country`, `id_card_number`, `permanent_address`, `current_address`, `age`, `gender`, `travel_purpose`) VALUES
(25, 12, NULL, 'Hamidullah', NULL, NULL, '۹۷۳۶۴۸۳۷۸', 'Afghanistan', '۹۸۴۳۷۵۹۸۳۴۵۷۹۳۸۴', 'انتان', 'کابل', NULL, 'male', NULL),
(26, 12, 22, 'Hamidullah', NULL, 'hamid@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL),
(27, 12, 20, 'Mohammad Kabir \"Afghan\"', NULL, 'kabir@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL),
(28, 12, 23, 'Ahmad Jan', NULL, 'ahmadjan@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `hotel_id` int(11) NOT NULL,
  `hotel_name` varchar(100) NOT NULL,
  `hotel_address` text DEFAULT NULL,
  `hotel_phone` varchar(20) DEFAULT NULL,
  `hotel_email` varchar(100) DEFAULT NULL,
  `about_us` text DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`hotel_id`, `hotel_name`, `hotel_address`, `hotel_phone`, `hotel_email`, `about_us`, `facilities`, `gallery_images`, `created_by`, `created_at`, `is_active`) VALUES
(12, 'Asia', 'شهیدانو چوک', '۰۷۰۰۰۳۴۳۴۳۴', 'asia@gmail.com', 'lksdjf;klsdjf\r\nfdskldf;jksdf\r\ndfkjs;dfk', 'wifi\r\nحوض\r\nښه چاپیریال\r\nکلب', '[\"hotel_12_1783893264_7884.jpg\",\"hotel_12_1783893264_8143.jpg\",\"hotel_12_1783893264_9870.jpg\",\"hotel_12_1783893264_3262.jpg\",\"hotel_12_1783893264_9910.jpg\",\"hotel_12_1783893264_3736.jpg\"]', 1, '2026-05-11 17:04:31', 1),
(13, 'نور جهان هوټل', 'شهیدانو چوک', '۰۷۰۰۰۳۴۳۴۳۴', 'asia@gmail.com', '', '', NULL, 1, '2026-10-12 03:28:26', 1),
(14, 'یارانه هوټل', 'شهیدانو چوک', '۰۷۰۰۰۳۴۳۴۳۴', 'asia@gmail.com', '', '', NULL, 1, '2026-10-12 03:28:39', 1);

-- --------------------------------------------------------

--
-- Table structure for table `hotel_expenses`
--

CREATE TABLE `hotel_expenses` (
  `expense_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `expense_date` date NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','bank_transfer','other') DEFAULT 'cash',
  `receipt_no` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_expenses`
--

INSERT INTO `hotel_expenses` (`expense_id`, `hotel_id`, `expense_date`, `category`, `description`, `amount`, `payment_method`, `receipt_no`, `created_at`) VALUES
(5, 12, '2026-07-12', 'خوراکي توکي', 'jhgjgjh', '1200.00', 'cash', '', '2026-07-12 00:33:20');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_staff`
--

CREATE TABLE `hotel_staff` (
  `staff_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `position` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_staff`
--

INSERT INTO `hotel_staff` (`staff_id`, `hotel_id`, `full_name`, `position`, `phone`, `email`, `hire_date`, `salary`, `status`, `created_at`) VALUES
(6, 12, 'Tareen Khan', 'لوښي پریولونکي', '984579847598', 'tareen@gmail.com', '2026-10-21', '0.48', 'active', '2026-10-12 03:37:55');

-- --------------------------------------------------------

--
-- Table structure for table `manual_expenses`
--

CREATE TABLE `manual_expenses` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `expense_date` date NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manual_expenses`
--

INSERT INTO `manual_expenses` (`id`, `hotel_id`, `expense_date`, `category`, `description`, `amount`, `created_by`, `created_at`) VALUES
(1, 12, '0000-00-00', '', '', '0.00', 19, '2026-07-12 00:33:39'),
(2, 12, '0000-00-00', '', '', '0.00', 19, '2026-07-12 00:34:43'),
(3, 12, '0000-00-00', '', '', '0.00', 19, '2026-07-12 00:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('cash','card','bank_transfer','online','other') DEFAULT 'cash',
  `payment_status` enum('paid','pending','partial','refunded') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `guest_id`, `payment_date`, `amount`, `discount`, `payment_method`, `payment_status`, `transaction_id`, `description`) VALUES
(121, NULL, NULL, '2026-04-27', '14.00', '0.00', 'cash', 'paid', NULL, 'خواړه امر '),
(122, NULL, NULL, '2026-05-05', '0.56', '0.00', 'cash', 'paid', NULL, ''),
(123, NULL, NULL, '2026-05-05', '200.00', '0.00', 'cash', 'paid', NULL, ''),
(127, NULL, NULL, '2026-05-07', '0.39', '0.00', 'cash', 'partial', NULL, ''),
(128, NULL, NULL, '2026-05-07', '0.00', '0.00', 'cash', 'paid', NULL, 'بنتلمنبلتیکبمنلت'),
(129, NULL, NULL, '2026-05-07', '0.90', '0.00', 'cash', 'paid', NULL, 'خواړه امر #49 - بنتلمنبلتیکبمنلت'),
(131, NULL, NULL, '2026-05-07', '1.20', '0.00', 'cash', 'paid', NULL, 'خواړه امر '),
(133, NULL, NULL, '2026-05-08', '2.16', '0.00', 'cash', 'paid', NULL, 'خواړه امر '),
(135, NULL, NULL, '2026-05-10', '1200.00', '0.00', 'cash', 'paid', NULL, ''),
(136, NULL, NULL, '2026-05-10', '1200.00', '0.00', 'cash', 'paid', NULL, ''),
(144, NULL, NULL, '2026-10-12', '1200.00', '0.00', 'cash', 'paid', NULL, ''),
(145, NULL, NULL, '2026-01-12', '0.22', '0.00', 'cash', 'paid', NULL, 'بنتلمنبلتیکبمنلت'),
(148, NULL, NULL, '2026-04-12', '0.26', '0.00', 'cash', 'paid', NULL, ''),
(149, NULL, NULL, '2026-04-12', '36.00', '0.00', 'cash', 'paid', NULL, 'خواړه امر #56 - '),
(150, NULL, NULL, '2026-04-12', '4800.00', '0.00', 'cash', 'paid', NULL, ''),
(152, 30, NULL, '2026-07-12', '21816.00', '0.00', 'cash', 'paid', NULL, ''),
(153, NULL, 26, '2026-07-12', '48.00', '0.00', 'cash', 'paid', NULL, 'خواړه امر '),
(154, NULL, 26, '2026-07-12', '1200.00', '0.00', 'cash', 'paid', NULL, ''),
(155, NULL, 26, '2026-07-13', '48.00', '0.00', 'cash', 'paid', NULL, 'خواړه امر '),
(156, NULL, 28, '2026-07-13', '48.00', '0.00', 'cash', 'paid', NULL, 'خواړه امر ');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `room_type` varchar(50) DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `capacity` int(11) DEFAULT 1,
  `status` enum('available','occupied','maintenance') DEFAULT 'available',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `hotel_id`, `room_number`, `room_type`, `price_per_night`, `capacity`, `status`, `description`, `image`) VALUES
(31, 12, '11', 'علی درجه', '1212.00', 3, 'occupied', '', 'room_1776020530_2430.jpg'),
(32, 12, '119', 'علی درجه', '1200.00', 2, 'occupied', '', 'room_1783893361_4394.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','hotel_admin','user') DEFAULT 'user',
  `hotel_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `profile_pic`, `password`, `full_name`, `role`, `hotel_id`, `created_at`, `last_login`, `is_active`) VALUES
(1, 'superadmin', 'super@admin.com', 'user_1_1778169855.png', 'c93ccd78b2076528346216b3b2f701e6', 'Noor Ahmad (Supper Admin)', 'super_admin', NULL, '2026-03-11 07:22:04', '2026-07-12 22:54:13', 1),
(19, 'asia', 'hesam@gmail.com', 'user_19_1791786964.jpg', 'ef9455e7352fc6711fd9452f30802349', 'Nazifullah \"Hisam\"', 'hotel_admin', 12, '2026-05-11 17:05:00', '2026-07-12 23:06:37', 1),
(20, 'hisam', 'kabir@gmail.com', NULL, 'a8d2dabeb566ec07c901d838a9968ede', 'Mohammad Kabir \"Afghan\"', 'user', 12, '2026-05-11 17:16:00', '2026-07-12 21:56:26', 1),
(21, 'Tareen', 'tareen@gmail.com', 'user_21_1791775893.jpg', '05660adcbb83fc380ee2c2262e35625c', 'Tareen Khan', 'user', 12, '2026-10-12 03:30:41', '2026-10-12 06:24:11', 1),
(22, 'hamid', 'hamid@gmail.com', NULL, 'dfb8e2bec9362a4e99e0cc79af77f123', 'Hamidullah', 'user', 12, '2026-07-12 19:06:26', '2026-07-12 19:06:47', 1),
(23, 'ahmad', 'ahmadjan@gmail.com', NULL, '8de13959395270bf9d6819f818ab1a00', 'Ahmad Jan', 'user', 12, '2026-07-12 22:51:34', '2026-07-12 23:05:46', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_messages`
--

CREATE TABLE `user_messages` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hotel_admin_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `reply` text DEFAULT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `replied_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD PRIMARY KEY (`bill_item_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `booking_occupants`
--
ALTER TABLE `booking_occupants`
  ADD PRIMARY KEY (`occupant_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `daily_closing`
--
ALTER TABLE `daily_closing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_hotel_date` (`hotel_id`,`close_date`),
  ADD KEY `closed_by` (`closed_by`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `food_orders`
--
ALTER TABLE `food_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guest_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`hotel_id`);

--
-- Indexes for table `hotel_expenses`
--
ALTER TABLE `hotel_expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `hotel_staff`
--
ALTER TABLE `hotel_staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `manual_expenses`
--
ALTER TABLE `manual_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `guest_id` (`guest_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `unique_room_per_hotel` (`hotel_id`,`room_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `user_messages`
--
ALTER TABLE `user_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hotel_admin_id` (`hotel_admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bill_items`
--
ALTER TABLE `bill_items`
  MODIFY `bill_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `booking_occupants`
--
ALTER TABLE `booking_occupants`
  MODIFY `occupant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `daily_closing`
--
ALTER TABLE `daily_closing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `food_orders`
--
ALTER TABLE `food_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `hotel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hotel_expenses`
--
ALTER TABLE `hotel_expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hotel_staff`
--
ALTER TABLE `hotel_staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `manual_expenses`
--
ALTER TABLE `manual_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user_messages`
--
ALTER TABLE `user_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD CONSTRAINT `bill_items_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_occupants`
--
ALTER TABLE `booking_occupants`
  ADD CONSTRAINT `booking_occupants_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_closing`
--
ALTER TABLE `daily_closing`
  ADD CONSTRAINT `daily_closing_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `daily_closing_ibfk_2` FOREIGN KEY (`closed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Constraints for table `food_items`
--
ALTER TABLE `food_items`
  ADD CONSTRAINT `food_items_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Constraints for table `food_orders`
--
ALTER TABLE `food_orders`
  ADD CONSTRAINT `food_orders_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `food_orders_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `food_orders_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `food_items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `guests_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guests_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `hotel_expenses`
--
ALTER TABLE `hotel_expenses`
  ADD CONSTRAINT `hotel_expenses_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Constraints for table `hotel_staff`
--
ALTER TABLE `hotel_staff`
  ADD CONSTRAINT `hotel_staff_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Constraints for table `manual_expenses`
--
ALTER TABLE `manual_expenses`
  ADD CONSTRAINT `manual_expenses_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `manual_expenses_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_4` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`) ON DELETE SET NULL;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_messages`
--
ALTER TABLE `user_messages`
  ADD CONSTRAINT `user_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_messages_ibfk_2` FOREIGN KEY (`hotel_admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
