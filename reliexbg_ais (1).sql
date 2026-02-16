-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 17, 2025 at 12:07 AM
-- Server version: 8.3.0
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reliexbg_aiss`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `note` text,
  `type` enum('asset','liability','equity','income','expense') NOT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `initial_balance` decimal(15,2) DEFAULT '0.00',
  `current_balance` decimal(15,2) DEFAULT '0.00',
  `currency_code` int DEFAULT '1',
  `parent_id` int DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `code`, `name`, `note`, `type`, `account_number`, `initial_balance`, `current_balance`, `currency_code`, `parent_id`, `status`, `created_at`, `updated_at`) VALUES
(1, '1000', 'Cash', NULL, 'asset', '0550204206', 1000.00, 1146.32, 1, NULL, 'active', '2025-08-10 06:23:22', '2025-08-12 17:01:41'),
(8, '93322', 'DAVIDO OBO BADDEST', 'IT IS FOR TESTING', 'asset', '0550204207', 100000.00, 99850.00, 1, NULL, 'active', '2025-08-12 15:43:54', '2025-08-12 17:32:01');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `status` enum('active','inactive') NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(2, 'Pharmacy', 'Medicine and health products', 'active', '2025-07-25 17:22:13', 1, '2025-07-26 17:34:13', 1),
(3, 'Groceries', 'Daily food and consumables', 'active', '2025-07-25 17:22:13', 1, '2025-07-26 17:34:13', 1),
(4, 'Stationery', 'Books, pens, papers', 'active', '2025-07-25 17:22:13', 1, '2025-07-26 17:34:13', 1),
(5, 'Furniture', 'Chairs, tables, shelves', 'active', '2025-07-25 17:22:13', 1, '2025-07-26 17:34:13', 1),
(6, 'Electronics', 'Devices like laptops, phones, etc.', 'inactive', '2025-07-25 17:22:42', 1, '2025-07-26 16:35:45', 1),
(12, 'Food', 'It is for food', 'inactive', '2025-07-26 16:01:32', 1, '2025-07-26 17:34:13', 1),
(13, 'Foodstuff', 'It is for food', 'active', '2025-08-16 21:29:45', 1, '2025-08-16 22:29:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `company_name` varchar(150) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `customergroup`
--

DROP TABLE IF EXISTS `customergroup`;
CREATE TABLE IF NOT EXISTS `customergroup` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `customergroup`
--

INSERT INTO `customergroup` (`id`, `name`, `description`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 'group A', 'it is for seller and buyers', '0000-00-00 00:00:00', 1, '2025-08-07 09:19:47', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postbox` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `taxid` varchar(100) DEFAULT NULL,
  `group_id` int DEFAULT NULL,
  `shipping_billing` varchar(11) NOT NULL DEFAULT 'no',
  `shipping_name` varchar(100) DEFAULT NULL,
  `shipping_phone` varchar(50) DEFAULT NULL,
  `shipping_email` varchar(100) DEFAULT NULL,
  `shipping_address` text,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_region` varchar(100) DEFAULT NULL,
  `shipping_country` varchar(100) DEFAULT NULL,
  `shipping_postbox` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `email`, `address`, `city`, `region`, `country`, `postbox`, `company`, `taxid`, `group_id`, `shipping_billing`, `shipping_name`, `shipping_phone`, `shipping_email`, `shipping_address`, `shipping_city`, `shipping_region`, `shipping_country`, `shipping_postbox`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 'CUS54716', 'DAMILARE DAVID ADEBESIN', '08115336762', 'adebesindamilare39@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', '12345', 'Damilare Adebesin', '234n', 1, 'no', 'DAMILARE DAVID ADEBESIN', '08115336762', 'adebesindamilare39@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', '12345', '2025-07-03 08:58:10', 1, '0000-00-00 00:00:00', 1),
(2, 'CUS90069', 'DAMILARE DAVID ADEBESIN', '08115336762', 'adebesindamilare39@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', '12345', 'Damilare Adebesin', '234n', 1, 'no', 'DAMILARE DAVID ADEBESIN', '08115336762', 'adebesindamilare39@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', '12345', '2025-07-03 09:01:40', 1, '0000-00-00 00:00:00', 1),
(3, 'CUS77061', 'DAMILARE DAVID ADEBESIN', '08115336762', 'adebesindamilare39@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', '12345', 'Damilare Adebesin', '234n', 1, 'no', 'DAMILARE DAVID ADEBESIN', '08115336762', 'adebesindamilare39@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', '12345', '2025-07-03 09:17:24', 1, '0000-00-00 00:00:00', 1),
(5, 'CUS39310', 'Damilare Adebesin', '08115336762', 'adebesindamilare750@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', '12345', 'Damilare Adebesin', '234n', 1, 'yes', 'Damilare Adebesin', '08115336762', 'adebesindamilare750@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', '12345', '2025-08-06 11:46:42', 1, '2025-08-06 12:30:20', 1),
(6, 'CUS57733', 'DAMILARE DAVID ADEBESIN', '08115336762', 'adebesindamilare39@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', 'TEST', 'Damilare Adebesin', '234n', 1, 'yes', 'DAMILARE DAVID ADEBESIN', '08115336762', 'adebesindamilare39@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', 'TEST', '2025-08-16 20:31:10', 1, '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `estimates`
--

DROP TABLE IF EXISTS `estimates`;
CREATE TABLE IF NOT EXISTS `estimates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `estimate_number` varchar(50) DEFAULT NULL,
  `client_id` int DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `status` enum('Draft','Sent','Accepted','Rejected') DEFAULT 'Draft',
  `subtotal` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `notes` text,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estimate_number` (`estimate_number`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `estimate_items`
--

DROP TABLE IF EXISTS `estimate_items`;
CREATE TABLE IF NOT EXISTS `estimate_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `estimate_id` int DEFAULT NULL,
  `description` text,
  `quantity` int DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estimate_id` (`estimate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(150) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `expense_date` date DEFAULT NULL,
  `notes` text,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `tax_format` varchar(20) DEFAULT NULL,
  `discount_format` varchar(20) DEFAULT NULL,
  `recurring_times` int NOT NULL DEFAULT '0',
  `notes` text,
  `subtotal` decimal(15,2) DEFAULT NULL,
  `shipping` decimal(15,2) DEFAULT NULL,
  `grand_total` decimal(15,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `public_token` varchar(255) NOT NULL,
  `payment_status` varchar(150) NOT NULL,
  `payment_method` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `recurring_period` varchar(50) NOT NULL DEFAULT '0 day',
  `end_date` date DEFAULT NULL,
  `status` enum('active','paused','cancelled') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'active',
  `type` enum('invoices','recurring') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'invoices',
  `created_by` int NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `customer_id`, `invoice_number`, `reference`, `invoice_date`, `due_date`, `tax_format`, `discount_format`, `recurring_times`, `notes`, `subtotal`, `shipping`, `grand_total`, `currency`, `payment_terms`, `created_at`, `public_token`, `payment_status`, `payment_method`, `recurring_period`, `end_date`, `status`, `type`, `created_by`, `updated_at`, `updated_by`) VALUES
(13, 1, '1084', '', '2025-07-07', '2025-07-07', '%', '%', 0, '', 3.20, 9.00, 12.20, '1', '1', '2025-07-07 13:16:38', '1eef41f18bc0131294e2560e882325db7bdcce9b', 'due', 'Stripe', '0 day', NULL, 'active', 'invoices', 0, '0000-00-00 00:00:00', 0),
(14, 1, '1084', '#345', '2025-07-07', '2025-07-07', '%', '%', 0, 'it is testing', 7.45, 9.00, 7.46, '1', '1', '2025-07-07 13:19:29', '44e50e059f7e7f31d6daa73b89def7346fac28ca', 'due', 'Stripe', '0 day', NULL, 'active', 'invoices', 0, '0000-00-00 00:00:00', 0),
(15, 1, '1084', '', '2025-07-07', '2025-07-07', '%', '%', 0, '', 7.46, 9.00, 16.46, '0', '1', '2025-07-07 13:41:07', '37b99a2de06255c9e7d70e4efc4a086f33328b2f', 'paid', 'Stripe', '0 day', NULL, 'active', 'invoices', 0, '0000-00-00 00:00:00', 0),
(16, 1, '1084', '#345', '2025-07-07', '2025-07-07', '%', '%', 0, 'it is testing', 7.07, 9.00, 16.07, '1', '1', '2025-07-07 15:11:14', '9938ec38c73aac3fb42ae2c3ea7d771769564e82', 'Paid', 'Stripe', '0 day', NULL, 'active', 'invoices', 0, '0000-00-00 00:00:00', 0),
(17, 2, '1078', '#346', '2025-07-10', '2025-07-26', '%', '%', 0, 'IT IS FOR TESTING', 13.46, 10.00, 23.46, '1', '1', '2025-07-08 08:45:38', '0f3aeea3da1e547e10aec200e0cad851e88ec87c', 'partial', 'Stripe', '0 day', NULL, 'active', 'invoices', 0, '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int NOT NULL,
  `tax_percent` decimal(5,2) DEFAULT NULL,
  `tax_amount` decimal(15,2) DEFAULT NULL,
  `discount` decimal(15,2) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL,
  `product_description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `product_id` int DEFAULT '0',
  `type` enum('invoices','recurring') NOT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `fk_invoice_items_product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `product_name`, `quantity`, `tax_percent`, `tax_amount`, `discount`, `subtotal`, `product_description`, `created_at`, `product_id`, `type`, `price`, `discount_amount`) VALUES
(7, 13, 'Paracetamol 500mg', 1, 5.00, 0.18, 13.00, 3.20, 'Used for mild to moderate pain relief.', '2025-07-07 13:16:38', 2, 'invoices', 3.50, 0.48),
(8, 14, 'Amoxicillin 250mg', 1, 7.50, 0.53, 1.00, 7.45, 'Broad-spectrum antibiotic.', '2025-07-07 13:19:29', 2, 'invoices', 7.00, 0.08),
(9, 15, 'Amoxicillin 250mg', 1, 7.50, 0.53, 1.00, 7.46, 'Broad-spectrum antibiotic.', '2025-07-07 13:41:07', 2, 'invoices', 7.00, 0.07),
(10, 16, 'Metformin 500mg', 1, 4.00, 0.27, 0.00, 7.07, 'Used for treating type 2 diabetes.', '2025-07-07 15:11:14', 7, 'invoices', 6.80, 0.00),
(11, 17, 'Paracetamol 500mg', 1, 5.00, 0.18, 0.00, 3.67, 'Used for mild to moderate pain relief.', '2025-07-08 08:45:38', 2, 'invoices', 3.50, 0.00),
(12, 17, 'Vitamin C 1000mg', 1, 0.00, 0.00, 2.00, 9.79, 'Boosts immune system.', '2025-07-08 08:45:38', 4, 'invoices', 9.99, 0.20),
(13, 17, 'Amoxicillin 250mg', 1, 7.50, 0.53, 1.00, 7.45, 'Broad-spectrum antibiotic.', '2025-07-08 08:45:38', 2, 'invoices', 7.00, 0.08);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `paid_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `invoice_id`, `payment_method`, `amount`, `status`, `transaction_id`, `currency`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 16, 'Stripe', 16.07, 'completed', 'manual-686d3edbc6832', '1', '2025-07-08 15:52:59', '2025-07-08 15:52:59', '2025-07-08 15:52:59'),
(2, 18, 'Stripe', 3.68, 'due', 'manual-686d4219a97f2', '1', '2025-07-08 16:06:49', '2025-07-08 16:06:49', '2025-07-22 11:01:53'),
(3, 18, 'Stripe', 3.68, 'paid', 'manual-686d43a54a344', '1', '2025-07-08 16:13:25', '2025-07-08 16:13:25', '2025-07-14 11:52:57'),
(4, 18, 'Stripe', 3.68, 'paid', 'manual-686d47f16980a', '1', '2025-07-08 16:31:45', '2025-07-08 16:31:45', '2025-07-14 11:52:57'),
(5, 18, 'Stripe', 3.68, 'paid', 'manual-686d48fd09b6c', '1', '2025-07-08 16:36:13', '2025-07-08 16:36:13', '2025-07-14 11:52:57'),
(6, 18, 'Stripe', 3.68, 'paid', 'manual-686d4938292e7', '1', '2025-07-08 16:37:12', '2025-07-08 16:37:12', '2025-07-14 11:52:57'),
(7, 18, 'Stripe', 3.68, 'paid', 'manual-686d4ab358aa7', '1', '2025-07-08 16:43:31', '2025-07-08 16:43:31', '2025-07-14 11:52:57'),
(8, 18, 'Stripe', 3.68, 'paid', 'manual-686e2e8db2611', '1', '2025-07-09 08:55:41', '2025-07-09 08:55:41', '2025-07-14 11:52:57'),
(9, 18, 'Stripe', 3.68, 'paid', 'manual-686e2ef8d1a80', '1', '2025-07-09 08:57:28', '2025-07-09 08:57:28', '2025-07-14 11:52:57'),
(10, 18, 'Stripe', 3.68, 'paid', 'manual-686e2f6a1e87f', '1', '2025-07-09 08:59:22', '2025-07-09 08:59:22', '2025-07-14 11:52:57'),
(11, 18, 'Stripe', 3.68, 'paid', 'manual-686e2f85d0ee7', '1', '2025-07-09 08:59:49', '2025-07-09 08:59:49', '2025-07-14 11:52:57'),
(12, 18, 'Stripe', 3.68, 'paid', 'manual-686e2f9d4ea49', '1', '2025-07-09 09:00:13', '2025-07-09 09:00:13', '2025-07-14 11:52:57'),
(13, 18, 'Stripe', 3.68, 'paid', 'manual-686e3089cb737', '1', '2025-07-09 09:04:09', '2025-07-09 09:04:09', '2025-07-14 11:52:57'),
(14, 18, 'Paystack', 3.68, 'paid', 'uyh017lo0s', '1', '2025-07-09 12:46:43', '2025-07-09 12:46:43', '2025-07-14 11:52:57'),
(15, 18, 'Paystack', 3.68, 'paid', 'c3h7fta228', '1', '2025-07-09 12:54:26', '2025-07-09 12:54:26', '2025-07-14 11:52:57'),
(16, 18, 'Paystack', 3.68, 'paid', 'jdn6x8mmjs', '1', '2025-07-09 12:56:12', '2025-07-09 12:56:12', '2025-07-14 11:52:57'),
(17, 18, 'Paystack', 3.68, 'paid', 'akjs7cimfq', '1', '2025-07-09 14:22:57', '2025-07-09 14:22:57', '2025-07-14 11:52:57'),
(18, 18, 'Stripe', 3.68, 'paid', 'manual-686fe481e41ae', '1', '2025-07-10 16:04:17', '2025-07-10 16:04:17', '2025-07-14 11:52:57');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `category_id` int DEFAULT NULL,
  `warehouse_id` int DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `wholesale_price` decimal(10,2) DEFAULT '0.00',
  `tax_percent` decimal(5,2) DEFAULT '0.00',
  `discount` decimal(5,2) DEFAULT '0.00',
  `quantity` int DEFAULT '0',
  `alert_quantity` int DEFAULT '0',
  `description` text,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int NOT NULL DEFAULT '0',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `stock_by_warehouse` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_category` (`category_id`),
  KEY `fk_warehouse` (`warehouse_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `code`, `category_id`, `warehouse_id`, `price`, `wholesale_price`, `tax_percent`, `discount`, `quantity`, `alert_quantity`, `description`, `updated_at`, `updated_by`, `created_at`, `created_by`, `status`, `stock_by_warehouse`) VALUES
(2, 'Amoxicillin 250mg', 'AMX250', NULL, 0, 7.00, 0.00, 7.50, 1.00, 0, 0, 'Broad-spectrum antibiotic.', NULL, 1, '2025-07-03 15:57:59', 0, 'active', NULL),
(3, 'Ibuprofen 200mg', 'IBF200', NULL, NULL, 5.25, 0.00, 6.00, 0.50, 0, 0, 'Anti-inflammatory painkiller.', NULL, 1, '2025-07-03 15:57:59', 0, 'active', NULL),
(4, 'Vitamin C 1000mg', 'VTC1000', NULL, NULL, 9.99, 0.00, 0.00, 2.00, 0, 0, 'Boosts immune system.', NULL, 1, '2025-07-03 15:57:59', 0, 'active', NULL),
(5, 'Cough Syrup 100ml', 'CS100', NULL, NULL, 4.75, 0.00, 5.00, 0.25, 0, 0, 'For dry cough relief.', NULL, 1, '2025-07-03 15:57:59', 0, 'active', NULL),
(6, 'Cetirizine 10mg', 'CTZ10', NULL, NULL, 2.40, 0.00, 0.00, 0.00, 0, 0, 'Antihistamine for allergy relief.', NULL, 1, '2025-07-03 15:57:59', 0, 'active', NULL),
(7, 'Metformin 500mg', 'MTF500', NULL, NULL, 6.80, 0.00, 4.00, 0.00, 2, 0, 'Used for treating type 2 diabetes.', NULL, 1, '2025-07-03 15:57:59', 0, 'active', NULL),
(8, 'Loratadine 10mg', 'LRT10', NULL, NULL, 3.20, 0.00, 0.00, 0.10, 3, 0, 'Non-drowsy allergy medication.', NULL, 1, '2025-07-03 15:57:59', 0, 'active', NULL),
(9, 'Diclofenac Gel 30g', 'DCG30', NULL, NULL, 8.25, 0.00, 2.50, 0.00, 6, 0, 'Topical anti-inflammatory gel.', NULL, 1, '2025-07-03 15:57:59', 0, 'active', NULL),
(10, 'ORS Sachet', 'ORS01', 2, 2, 1.00, 0.00, 0.00, 0.00, 80, 0, 'Oral rehydration solution.', NULL, 1, '2025-07-03 15:57:59', 1, 'active', '{\"2\":80,\"1\":10}'),
(12, 'Paracetamol', '123456', 2, 2, 10.00, 50.00, 5.00, 3.00, 20, 5, 'it is testing', '2025-07-26 17:21:07', 1, '2025-07-25 23:44:16', 1, 'active', '{\"2\":20,\"1\":15}');

-- --------------------------------------------------------

--
-- Table structure for table `product_warehouse`
--

DROP TABLE IF EXISTS `product_warehouse`;
CREATE TABLE IF NOT EXISTS `product_warehouse` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `product_warehouse`
--

INSERT INTO `product_warehouse` (`id`, `product_id`, `warehouse_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 12, 2, 20, '2025-07-28 17:00:26', '2025-07-29 10:44:50'),
(2, 7, 3, 2, '2025-07-28 17:01:30', '2025-07-28 17:02:17'),
(3, 8, 4, 3, '2025-07-28 17:01:30', '2025-07-28 17:02:13'),
(4, 9, 4, 6, '2025-07-28 17:01:30', '2025-07-28 17:02:08'),
(5, 10, 2, 80, '2025-07-28 17:01:30', '2025-07-29 10:44:50'),
(6, 12, 2, 20, '2025-07-28 17:01:30', '2025-07-29 10:44:50'),
(7, 7, 4, 2, '2025-07-28 17:01:30', '2025-07-28 17:01:55'),
(8, 8, 3, 3, '2025-07-28 17:01:30', '2025-07-28 17:01:49'),
(9, 9, 6, 6, '2025-07-28 17:01:30', '2025-07-28 17:02:00'),
(10, 10, 2, 80, '2025-07-28 17:01:30', '2025-07-29 10:44:50'),
(11, 12, 2, 20, '2025-07-28 17:01:30', '2025-07-29 10:44:50'),
(12, 12, 1, 10, '2025-07-29 10:44:50', '2025-07-29 10:44:50'),
(13, 10, 1, 10, '2025-07-29 10:44:50', '2025-07-29 10:44:50');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int DEFAULT NULL,
  `warehouse_id` int DEFAULT NULL,
  `category_id` int NOT NULL DEFAULT '2',
  `invoice_no` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `tax_format` varchar(10) DEFAULT NULL,
  `discount_format` varchar(10) DEFAULT NULL,
  `notes` text,
  `currency` int NOT NULL DEFAULT '1',
  `subtotal` decimal(10,2) DEFAULT NULL,
  `shipping` decimal(10,2) DEFAULT NULL,
  `grand_total` decimal(10,2) DEFAULT NULL,
  `update_stock` enum('yes','no') DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `payment_status` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(150) NOT NULL DEFAULT 'None',
  `status` varchar(150) NOT NULL DEFAULT 'pending',
  `created_by` int NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `supplier_id`, `warehouse_id`, `category_id`, `invoice_no`, `reference`, `invoice_date`, `due_date`, `tax_format`, `discount_format`, `notes`, `currency`, `subtotal`, `shipping`, `grand_total`, `update_stock`, `payment_terms`, `created_at`, `payment_status`, `payment_method`, `status`, `created_by`, `updated_at`, `updated_by`) VALUES
(3, 1, 1, 2, '1046', '#305', '2025-02-08', '2025-02-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-02 23:16:27', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(2, 1, 1, 2, '1046', '#305', '2025-02-08', '2025-02-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-02 17:02:23', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(7, 1, 1, 2, '1046', '#305', '2025-03-08', '2025-03-08', '%', '%', 'it is testing', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-03 08:09:10', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(8, 1, 1, 2, '1046', '#305', '2025-03-08', '2025-03-08', '%', '%', 'it is testing', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-03 15:07:35', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(9, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:42:24', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(10, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:43:23', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(11, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:44:11', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(12, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:45:27', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(13, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:45:55', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(14, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:46:59', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(15, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:47:14', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(16, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'it is testing', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 11:24:28', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(19, 1, 2, 2, '1046', '#305', '2025-05-08', '2025-05-08', 'on', '%', 'it is testing', 1, 21.00, 9.00, 30.00, 'yes', '1', '2025-08-04 11:26:15', 'pending', 'None', 'active', 0, '2025-08-05 15:17:37', 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
CREATE TABLE IF NOT EXISTS `purchase_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `tax_percent` decimal(5,2) DEFAULT '0.00',
  `tax_amount` decimal(10,2) DEFAULT '0.00',
  `discount` decimal(5,2) DEFAULT '0.00',
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL,
  `product_description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_purchase` (`purchase_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `product_name`, `quantity`, `price`, `tax_percent`, `tax_amount`, `discount`, `discount_amount`, `subtotal`, `product_description`, `created_at`) VALUES
(1, 2, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-02 17:02:23'),
(2, 3, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-02 23:16:27'),
(6, 7, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-03 08:09:10'),
(7, 8, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-03 15:07:35'),
(8, 9, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:42:24'),
(9, 10, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:43:23'),
(10, 11, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:44:11'),
(11, 12, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:45:27'),
(12, 13, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:45:55'),
(13, 14, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:46:59'),
(14, 15, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:47:14'),
(15, 16, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 11:24:28'),
(27, 19, 12, 'Paracetamol', 2.00, 10.00, 5.00, 1.00, 3.00, 3.00, 21.00, 'it is testing', '2025-08-05 16:17:37');

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

DROP TABLE IF EXISTS `quotes`;
CREATE TABLE IF NOT EXISTS `quotes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quote_number` varchar(100) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `customer_id` int NOT NULL,
  `quote_date` date NOT NULL,
  `due_date` date NOT NULL,
  `tax_format` varchar(10) DEFAULT NULL,
  `discount_format` varchar(10) DEFAULT NULL,
  `notes` text,
  `proposal` text,
  `subtotal` varchar(255) NOT NULL,
  `shipping` decimal(10,2) DEFAULT '0.00',
  `total_tax` decimal(10,2) DEFAULT NULL,
  `total_discount` decimal(10,2) DEFAULT NULL,
  `grand_total` decimal(10,2) DEFAULT NULL,
  `payment_terms` varchar(50) DEFAULT NULL,
  `currency` tinyint DEFAULT '0',
  `public_token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `quotes`
--

INSERT INTO `quotes` (`id`, `quote_number`, `reference`, `customer_id`, `quote_date`, `due_date`, `tax_format`, `discount_format`, `notes`, `proposal`, `subtotal`, `shipping`, `total_tax`, `total_discount`, `grand_total`, `payment_terms`, `currency`, `public_token`, `created_at`, `updated_at`) VALUES
(4, '3371', '#305', 1, '2025-07-19', '2025-07-30', '%', '%', 'IT IS WORKING FINE', '&lt;p&gt;I WANT TO PROPOSE A PHONE&lt;/p&gt;', '7.46', 9.00, 0.53, 0.07, 16.46, '1', 1, '69a9cae021842089890f109a1867e1cf21e6b613', '2025-07-16 14:12:22', '2025-07-17 12:00:32'),
(5, '3371', '#305', 1, '2025-07-19', '2025-07-30', '%', '%', 'IT IS WORKING FINE', '&lt;p&gt;I WANT TO PROPOSE A PHONE&lt;/p&gt;', '7.46', 9.00, 0.53, 0.07, 16.46, '1', 1, 'b8780df8a85cc453b4cacf43aebeff6ee127047c', '2025-07-16 14:14:05', '2025-07-17 12:00:53'),
(6, '3371', '#305', 1, '2025-07-19', '2025-07-30', '%', '%', 'IT IS WORKING FINE', 'I WANT TO PROPOSE A PHONE&lt', '7.46', 9.00, 0.53, 0.07, 16.46, '1', 1, 'fa6d78adedc9ea5507e8d338a090605dc36044fe', '2025-07-16 14:14:17', '2025-07-17 12:01:16'),
(7, '45003', '#305', 1, '2025-07-19', '2025-07-30', '%', '%', 'IT IS WORKING FINE', 'IT FOR A PHONE', '3.67', 9.00, 0.18, 0.00, 12.67, '1', 1, '40e53d9784735289f428e2052d35b1defc2f9149', '2025-07-16 14:20:40', '2025-07-17 12:01:36'),
(8, '45003', '#305', 1, '2025-07-19', '2025-07-30', '%', '%', 'IT IS WORKING FINE', 'IT FOR A PHONE', '3.67', 9.00, 0.18, 0.00, 12.67, '1', 1, '1eef41f18bc0131294e2560e882325db7bdcce9b', '2025-07-16 14:20:52', '2025-07-17 12:01:49'),
(11, '22400', '#305', 1, '2025-07-22', '2025-07-22', '%', '%', 'IT IS WORKING FINE', '                                                                i am propsing iphone                                                            ', '0', 9.00, 21.11, 10.00, 46.89, '1', 2, '97960e74070ec7370d8ddf434a514dd052ef4ff2', '2025-07-16 14:36:25', '2025-07-22 14:54:06'),
(12, '69506', '#355', 1, '1970-01-01', '2025-07-26', '%', '%', 'IT IS FOR TESTING', 'I AM PROPOSING NEW IPHONE AND SAMSUNG', '7.46', 9.00, 0.53, 0.07, 16.46, '1', 1, '', '2025-07-22 11:53:02', '2025-07-22 11:53:02'),
(13, '69506', '#355', 1, '2025-07-22', '2025-07-26', '%', '%', 'IT IS FOR TESTING', 'I AM PROPOSING NEW IPHONE AND SAMSUNG', '7.46', 9.00, 0.53, 0.07, 16.46, '1', 1, '', '2025-07-22 11:54:19', '2025-07-22 11:54:19');

-- --------------------------------------------------------

--
-- Table structure for table `quote_items`
--

DROP TABLE IF EXISTS `quote_items`;
CREATE TABLE IF NOT EXISTS `quote_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quote_id` int NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `quantity` int DEFAULT '1',
  `rate` decimal(10,2) DEFAULT NULL,
  `tax_percent` decimal(5,2) DEFAULT NULL,
  `tax_amount` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `product_description` text,
  `product_id` int NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `quote_items`
--

INSERT INTO `quote_items` (`id`, `quote_id`, `product_name`, `quantity`, `rate`, `tax_percent`, `tax_amount`, `discount`, `subtotal`, `product_description`, `product_id`, `status`, `created_at`) VALUES
(1, 2, 'Paracetamol 500mg', 1, 3.50, 5.00, 0.18, 0.00, 1.00, '3.67', 0, '', '2025-07-16 13:29:33'),
(3, 4, 'Amoxicillin 250mg', 1, 7.00, 7.50, 0.53, 1.00, 2.00, '7.46', 0, '', '2025-07-16 14:12:22'),
(4, 5, 'Amoxicillin 250mg', 1, 7.00, 7.50, 0.53, 1.00, 2.00, '7.46', 0, '', '2025-07-16 14:14:05'),
(5, 6, 'Amoxicillin 250mg', 1, 7.00, 7.50, 0.53, 1.00, 2.00, '7.46', 0, '', '2025-07-16 14:14:17'),
(6, 7, 'Paracetamol 500mg', 1, 3.50, 5.00, 0.18, 0.00, 3.67, 'Used for mild to moderate pain relief.', 1, '', '2025-07-16 14:20:40'),
(7, 8, 'Paracetamol 500mg', 1, 3.50, 5.00, 0.18, 0.00, 3.67, 'Used for mild to moderate pain relief.', 1, '', '2025-07-16 14:20:52'),
(23, 11, 'Amoxicillin 250mg', 3, 7.00, 100.50, 21.11, 10.00, 37.89, 'Broad-spectrum antibiotic.', 21, 'Pending', '2025-07-22 14:54:06'),
(11, 0, 'Amoxicillin 250mg', 3, 7.00, 10.50, 0.53, 10.00, 7.46, 'Broad-spectrum antibiotic.', 10, 'Pending', '2025-07-22 11:37:47'),
(15, 12, 'Amoxicillin 250mg', 1, 7.00, 7.50, 0.53, 1.00, 7.46, 'Broad-spectrum antibiotic.', 2, 'Pending', '2025-07-22 11:53:02'),
(16, 13, 'Amoxicillin 250mg', 1, 7.00, 7.50, 0.53, 1.00, 7.46, 'Broad-spectrum antibiotic.', 2, 'Pending', '2025-07-22 11:54:19');

-- --------------------------------------------------------

--
-- Table structure for table `recurring_invoices`
--

DROP TABLE IF EXISTS `recurring_invoices`;
CREATE TABLE IF NOT EXISTS `recurring_invoices` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` int UNSIGNED NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `tax_format` varchar(50) DEFAULT NULL,
  `discount_format` varchar(50) DEFAULT NULL,
  `recurring_times` int NOT NULL,
  `notes` text,
  `subtotal` decimal(10,2) DEFAULT '0.00',
  `shipping` decimal(10,2) DEFAULT '0.00',
  `grand_total` decimal(10,2) DEFAULT '0.00',
  `currency` varchar(10) DEFAULT 'USD',
  `payment_terms` varchar(100) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'unpaid',
  `payment_method` varchar(100) DEFAULT NULL,
  `public_token` varchar(255) DEFAULT NULL,
  `recurring_period` varchar(50) NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','paused','cancelled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `public_token` (`public_token`),
  KEY `customer_id` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recurring_invoices`
--

INSERT INTO `recurring_invoices` (`id`, `customer_id`, `invoice_number`, `reference`, `invoice_date`, `due_date`, `tax_format`, `discount_format`, `recurring_times`, `notes`, `subtotal`, `shipping`, `grand_total`, `currency`, `payment_terms`, `payment_status`, `payment_method`, `public_token`, `recurring_period`, `end_date`, `status`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 1, '875', '#355', '2025-06-03', '2025-07-24', '%', '%', 5, 'it is tesying', 3.34, 9.00, 12.34, '1', '1', 'Pending', NULL, NULL, '45 day', '2026-01-20', 'active', '2025-07-24 10:11:26', 1, NULL),
(2, 1, '65494', '#355', '2025-06-03', '2025-07-24', '%', '%', 5, 'it is testing', 7.46, 9.00, 16.46, '1', '1', 'Pending', NULL, '142cc906d6b29c9eed960551af44dfe558642c94', '7 day', '2025-08-22', 'active', '2025-07-24 10:26:08', 1, '2025-07-24 11:26:08');

-- --------------------------------------------------------

--
-- Table structure for table `recurring_invoice_items`
--

DROP TABLE IF EXISTS `recurring_invoice_items`;
CREATE TABLE IF NOT EXISTS `recurring_invoice_items` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `recurring_invoice_id` int UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int UNSIGNED DEFAULT '1',
  `rate` decimal(10,2) NOT NULL,
  `tax_percent` decimal(5,2) DEFAULT '0.00',
  `tax_amount` decimal(10,2) DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `product_description` text,
  `product_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recurring_invoice_id` (`recurring_invoice_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recurring_invoice_items`
--

INSERT INTO `recurring_invoice_items` (`id`, `recurring_invoice_id`, `product_name`, `quantity`, `rate`, `tax_percent`, `tax_amount`, `discount`, `discount_amount`, `subtotal`, `product_description`, `product_id`, `created_at`) VALUES
(1, 1, 'Paracetamol 500mg', 1, 3.50, 5.00, 0.18, 9.00, 0.33, 3.34, 'Used for mild to moderate pain relief.', 1, '2025-07-24 10:11:26'),
(2, 2, 'Amoxicillin 250mg', 1, 7.00, 7.50, 0.53, 1.00, 0.07, 7.46, 'Broad-spectrum antibiotic.', 2, '2025-07-24 10:26:08');

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

DROP TABLE IF EXISTS `returns`;
CREATE TABLE IF NOT EXISTS `returns` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int DEFAULT NULL,
  `warehouse_id` int DEFAULT NULL,
  `category_id` int NOT NULL DEFAULT '2',
  `invoice_no` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `tax_format` varchar(10) DEFAULT NULL,
  `discount_format` varchar(10) DEFAULT NULL,
  `notes` text,
  `currency` int NOT NULL DEFAULT '1',
  `subtotal` decimal(10,2) DEFAULT NULL,
  `shipping` decimal(10,2) DEFAULT NULL,
  `grand_total` decimal(10,2) DEFAULT NULL,
  `update_stock` enum('yes','no') DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `payment_status` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(150) NOT NULL DEFAULT 'None',
  `status` varchar(150) NOT NULL DEFAULT 'pending',
  `created_by` int NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `returns`
--

INSERT INTO `returns` (`id`, `supplier_id`, `warehouse_id`, `category_id`, `invoice_no`, `reference`, `invoice_date`, `due_date`, `tax_format`, `discount_format`, `notes`, `currency`, `subtotal`, `shipping`, `grand_total`, `update_stock`, `payment_terms`, `created_at`, `payment_status`, `payment_method`, `status`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 1, 1, 2, '1046', '#305', '2025-02-08', '2025-02-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-02 23:16:27', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(2, 1, 1, 2, '1046', '#305', '2025-02-08', '2025-02-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-02 17:02:23', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(3, 1, 1, 2, '1046', '#305', '2025-03-08', '2025-03-08', '%', '%', 'it is testing', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-03 08:09:10', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(4, 1, 1, 2, '1046', '#305', '2025-03-08', '2025-03-08', '%', '%', 'it is testing', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-03 15:07:35', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(5, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:42:24', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(6, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:43:23', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(7, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:44:11', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(8, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:45:27', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(9, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:45:55', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(10, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:46:59', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(11, 1, 2, 2, '1046', '#305', '2025-04-08', '2025-04-08', '%', '%', 'IT IS TESTING', 0, 10.20, 9.00, 19.20, 'yes', '1', '2025-08-04 10:47:14', '', 'None', 'pending', 0, '0000-00-00 00:00:00', 0),
(13, 1, 2, 2, '1046', '#305', '2025-06-08', '2025-06-08', 'on', '%', 'it is testing', 1, 21.00, 9.00, 30.00, 'yes', '1', '2025-08-04 11:26:15', 'pending', 'None', 'accepted', 0, '2025-08-06 09:38:26', 1);

-- --------------------------------------------------------

--
-- Table structure for table `return_items`
--

DROP TABLE IF EXISTS `return_items`;
CREATE TABLE IF NOT EXISTS `return_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `return_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `tax_percent` decimal(5,2) DEFAULT '0.00',
  `tax_amount` decimal(10,2) DEFAULT '0.00',
  `discount` decimal(5,2) DEFAULT '0.00',
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL,
  `product_description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `return_items`
--

INSERT INTO `return_items` (`id`, `return_id`, `product_id`, `product_name`, `quantity`, `price`, `tax_percent`, `tax_amount`, `discount`, `discount_amount`, `subtotal`, `product_description`, `created_at`) VALUES
(1, 2, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-02 17:02:23'),
(2, 3, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-02 23:16:27'),
(3, 7, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-03 08:09:10'),
(4, 8, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-03 15:07:35'),
(5, 9, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:42:24'),
(6, 10, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:43:23'),
(7, 11, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:44:11'),
(14, 13, 12, 'Paracetamol', 2.00, 10.00, 5.00, 1.00, 3.00, 0.00, 21.00, 'it is testing', '2025-08-06 10:38:27'),
(10, 14, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:46:59'),
(11, 15, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 10:47:14'),
(12, 16, 12, 'Paracetamol', 1.00, 10.00, 5.00, 0.50, 3.00, 0.30, 10.20, 'it is testing', '2025-08-04 11:24:28'),
(13, 19, 12, 'Paracetamol', 2.00, 10.00, 5.00, 1.00, 3.00, 3.00, 21.00, 'it is testing', '2025-08-05 16:17:37');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'SuperAdmin', '2025-08-15 15:17:48', '2025-08-15 15:17:48'),
(2, 'SalesManager', '2025-08-15 15:17:48', '2025-08-15 15:17:48'),
(3, 'SalesPerson', '2025-08-15 15:18:19', '2025-08-15 15:18:19'),
(4, 'Accountant', '2025-08-15 15:18:19', '2025-08-15 15:18:19'),
(5, 'Manager', '2025-08-16 18:03:55', '2025-08-16 18:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

DROP TABLE IF EXISTS `stock_transfers`;
CREATE TABLE IF NOT EXISTS `stock_transfers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_warehouse_id` int NOT NULL,
  `to_warehouse_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `from_warehouse_id` (`from_warehouse_id`),
  KEY `to_warehouse_id` (`to_warehouse_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `stock_transfers`
--

INSERT INTO `stock_transfers` (`id`, `from_warehouse_id`, `to_warehouse_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 2, 1, 12, 10, '2025-07-29 09:44:50'),
(2, 2, 1, 10, 10, '2025-07-29 09:44:50'),
(3, 2, 1, 12, 9, '2025-07-29 11:27:32'),
(4, 2, 1, 12, 5, '2025-07-29 11:28:26'),
(5, 2, 1, 12, 5, '2025-07-29 12:44:40'),
(6, 2, 1, 12, 10, '2025-07-29 12:45:31'),
(7, 2, 1, 10, 10, '2025-07-29 12:45:31');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfer_items`
--

DROP TABLE IF EXISTS `stock_transfer_items`;
CREATE TABLE IF NOT EXISTS `stock_transfer_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transfer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_id` (`transfer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `stripe_sessions`
--

DROP TABLE IF EXISTS `stripe_sessions`;
CREATE TABLE IF NOT EXISTS `stripe_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `stripe_sessions`
--

INSERT INTO `stripe_sessions` (`id`, `invoice_id`, `session_id`, `created_at`) VALUES
(1, 16, 'cs_test_a1ZfdJAZCX8P5F2X8GCiPhsbHQDidaELv8E48RVXpTXbmJAAU3yBSNHqbG', '2025-07-08 15:42:40'),
(2, 18, 'cs_test_a1OYapTvOTY9Qj2tykgQE8F9LlyM44cO3QaApVBP5X7r2uSOQZJluaDn11', '2025-07-08 16:00:58'),
(3, 18, 'cs_test_a1gWjr6ZZfJoav2rYjPQVTbUkb1AMlS3oIqrSyyM8brWR7wW8pzfLkutJf', '2025-07-08 16:13:07'),
(4, 18, 'cs_test_a1GNOjmx0jSco0cwChspmgWQQ9GtBOAXyX6fZuLpUWcKfYIxbZlMtfUTlJ', '2025-07-08 16:31:28'),
(5, 18, 'cs_test_a1uvtaNI8kjLjAnboe2fbkFSAOyaK82wse4Oh0czSg5A9AAtNOdcYJS5OA', '2025-07-08 16:35:59'),
(6, 18, 'cs_test_a1dGDezbiEVRLPeO2Kl2xThoAyJn6txtBOHVUYdl2sMwtEM4t7KerHkcmy', '2025-07-08 16:36:58'),
(7, 18, 'cs_test_a1VLeNnvkQkVG0dpmyOsSojZOXAtgWG7PGHtZnWIx2DIyDY9RqYgPDszNC', '2025-07-08 16:42:34'),
(8, 18, 'cs_test_a1flVeF6usLB1JGY3SE3e5UevKqGVNCOmNWftDbDQPMneEsiFyNqE3XOnk', '2025-07-09 08:55:16'),
(9, 18, 'cs_test_a173qPiG1DSmhLJhaOxxUszc1Eib1ggdqKlirdr9I3eIfnysEtmDKPRn3y', '2025-07-09 08:57:10'),
(10, 18, 'cs_test_a1TYpzDinrQc7JU8D0BpUicuTSxIpK8qGm8Y4qsN2dgvW3ORq9WE72ALl2', '2025-07-09 08:58:44'),
(11, 18, 'cs_test_a1998ajdm4blDEtTZ6iPdLqBlSOX842psoiXxatHE2N8AxkVtYRTair3gw', '2025-07-09 09:03:38'),
(12, 18, 'cs_test_a1SsBDjUCZzJS2sXzvHnE4eh2t9oFlAVASiEsSLUS3QGe0yNg0zDsknxre', '2025-07-10 16:03:50'),
(13, 19, 'cs_test_a1j6qz3pYv4pL6YFCq47uJmLikIvTeEt6KeHbr4mr5KzvjDEgEbgqhuD1p', '2025-07-11 10:04:47'),
(14, 19, 'cs_test_a18FedKshfU6z6Gu5XP2AJ3JF4YLgw5Emk6gaT2yFSM8DAGN9tHyCdSryl', '2025-07-11 10:05:46'),
(15, 19, 'cs_test_a1z1vi9gWvfxOssWZVTIuSwFoKIzWw7z4iPA9LT4H4ecS0OdSVaWBjctXl', '2025-07-11 10:06:33');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postbox` varchar(100) DEFAULT NULL,
  `taxid` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_code`, `name`, `phone`, `email`, `address`, `city`, `region`, `country`, `postbox`, `taxid`, `created_at`) VALUES
(1, 'SUP96173', 'DAMILARE DAVID ADEBESIN', '08115336762', 'adebesindamilare39@gmail.com', '2, adewusi street itoki road og', 'Agege (Iju Road)', 'Lagos', 'Nigeria', '12345', '234n', '2025-07-29 15:05:48');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('solved','processing','waiting','closed') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'processing',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_attachments`
--

DROP TABLE IF EXISTS `ticket_attachments`;
CREATE TABLE IF NOT EXISTS `ticket_attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reply_id` int NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reply_id` (`reply_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `ticket_attachments`
--

INSERT INTO `ticket_attachments` (`id`, `reply_id`, `file_path`, `file_name`, `created_at`) VALUES
(1, 2, 'uploads/ticket_attachments/1754764378_code.png', 'code.png', '2025-08-09 19:32:58');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_replies`
--

DROP TABLE IF EXISTS `ticket_replies`;
CREATE TABLE IF NOT EXISTS `ticket_replies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `sent_via` enum('web','email') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'web',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `ticket_replies`
--

INSERT INTO `ticket_replies` (`id`, `ticket_id`, `sender_type`, `message`, `sent_via`, `created_at`) VALUES
(1, 1, 'user', 'I AM JUST TESTING', 'web', '2025-08-07 13:05:57'),
(2, 1, 'admin', 'it is for testing', 'web', '2025-08-09 19:32:58');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_users`
--

DROP TABLE IF EXISTS `ticket_users`;
CREATE TABLE IF NOT EXISTS `ticket_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `ticket_users`
--

INSERT INTO `ticket_users` (`id`, `name`, `email`, `created_at`) VALUES
(1, 'DAMILARE DAVID ADEBESIN', 'adebesindamilare39@gmail.com', '2025-08-07 13:05:57');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_id` int UNSIGNED NOT NULL,
  `payment_id` int DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payer_id` varchar(15) DEFAULT NULL,
  `category_id` int NOT NULL DEFAULT '0',
  `description` text,
  `payment_method` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `fk_transactions_account` (`account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `account_id`, `payment_id`, `type`, `amount`, `payer_id`, `category_id`, `description`, `payment_method`, `created_at`) VALUES
(1, 1, 1, 'credit', 16.07, 'CUS54716', 2, 'Stripe payment for invoice #1084', 'Web', '2025-07-08 15:52:59'),
(2, 1, 2, 'credit', 3.68, 'CUS54716', 1, 'Stripe payment for invoice #1084', '', '2025-07-08 16:06:49'),
(3, 1, 3, 'credit', 3.68, 'CUS54716', 2, 'Stripe payment for invoice #1084', '', '2025-07-08 16:13:25'),
(4, 1, 4, 'credit', 3.68, 'CUS54716', 2, 'Stripe payment for invoice #1084', '', '2025-07-08 16:31:45'),
(6, 1, 6, 'credit', 3.68, 'CUS54716', 0, 'Stripe payment for invoice #1084', '', '2025-07-08 16:37:12'),
(7, 1, 7, 'credit', 3.68, 'CUS54716', 0, 'Stripe payment for invoice #1084', '', '2025-07-08 16:43:31'),
(8, 1, 8, 'credit', 3.68, 'CUS54716', 0, 'Stripe payment for invoice #1084', '', '2025-07-09 08:55:41'),
(9, 1, 9, 'credit', 3.68, 'CUS54716', 0, 'Stripe payment for invoice #1084', '', '2025-07-09 08:57:28'),
(10, 1, 10, 'credit', 3.68, 'CUS54716', 2, 'Stripe payment for invoice #1084', '', '2025-07-09 08:59:22'),
(11, 1, 11, 'credit', 3.68, 'CUS54716', 0, 'Stripe payment for invoice #1084', '', '2025-07-09 08:59:49'),
(12, 1, 12, 'credit', 3.68, 'CUS54716', 0, 'Stripe payment for invoice #1084', '', '2025-07-09 09:00:13'),
(13, 1, 13, 'credit', 3.68, 'CUS54716', 0, 'Stripe payment for invoice #1084', '', '2025-07-09 09:04:09'),
(14, 0, 14, 'credit', 3.68, 'CUS54716', 0, 'Paystack payment for invoice #1084', '', '2025-07-09 12:46:43'),
(15, 0, 15, 'credit', 3.68, 'CUS54716', 0, 'Paystack payment for invoice #1084', '', '2025-07-09 12:54:26'),
(16, 0, 16, 'credit', 3.68, 'CUS54716', 0, 'Paystack payment for invoice #1084', '', '2025-07-09 12:56:12'),
(17, 0, 17, 'credit', 3.68, 'CUS54716', 2, 'Paystack payment for invoice #1084', '', '2025-07-09 14:22:57'),
(18, 1, 18, 'debit', 3.68, 'CUS54716', 2, 'Stripe payment for invoice #1084', '', '2025-07-10 16:04:17'),
(19, 1, NULL, 'credit', 50.00, 'CUS54716', 3, 'IT IS FOR GROCERIES', 'Cash', '0000-00-00 00:00:00'),
(20, 8, 0, 'debit', 100.00, '1', 0, 'Transfer to account ID 1', 'Web', '2025-08-12 14:59:36'),
(21, 8, 0, 'debit', 100.00, '1', 0, 'Transfer to account ID 1', 'Web', '2025-08-12 15:01:16'),
(22, 8, 0, 'debit', 100.00, '1', 0, 'Transfer to account ID 1', 'Transfer', '2025-08-12 15:01:27'),
(23, 1, 0, 'debit', 100.00, '1', 0, 'Transfer to account ID 8', 'Transfer', '2025-08-12 15:01:27'),
(24, 8, 0, 'debit', 50.00, '1', 0, 'Transfer to account ID 1', 'Transfer', '2025-08-12 15:04:12'),
(25, 1, 0, 'credit', 50.00, 'SUP96173', 0, 'Transfer to account ID 8', 'Transfer', '2025-08-12 15:04:12'),
(27, 1, 0, 'credit', 50.00, 'SUP96173', 0, 'Transfer to account DAVIDO OBO BADDEST with ID of 8', 'Web', '2025-08-12 16:01:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_type` int DEFAULT NULL,
  `block` enum('Y','N') DEFAULT 'N',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_user_type_roles` (`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `user_type`, `block`, `created_at`) VALUES
(1, 'Demo', 'Owner', 'owner@demo.com', '$2y$10$.7OKXo/EcYTO.ZyvIjIsseP6zrmDOSDjkD3nLUSGQvyoEnpDvwcyy', 1, 'N', '2025-07-01 15:07:47'),
(2, 'Demo', 'SaleManager', 'salesmanager@demo.com', '$2y$10$.7OKXo/EcYTO.ZyvIjIsseP6zrmDOSDjkD3nLUSGQvyoEnpDvwcyy', 2, 'N', '2025-07-01 15:07:47'),
(3, 'Demo', 'SalesPerson', 'salesperson@demo.com', '$2y$10$.7OKXo/EcYTO.ZyvIjIsseP6zrmDOSDjkD3nLUSGQvyoEnpDvwcyy', 3, 'N', '2025-07-01 15:07:47'),
(4, 'Demo', 'Accountant', 'accountant@demo.com', '$2y$10$.7OKXo/EcYTO.ZyvIjIsseP6zrmDOSDjkD3nLUSGQvyoEnpDvwcyy', 4, 'N', '2025-07-01 15:07:47'),
(7, 'Demo', 'Manager', 'manager@demo.com', '$2y$10$.7OKXo/EcYTO.ZyvIjIsseP6zrmDOSDjkD3nLUSGQvyoEnpDvwcyy', 5, 'N', '2025-08-16 19:33:34');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `location`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 'Main Warehouse', 'Accra', 'active', '2025-07-25 17:23:03', 1, '2025-07-27 00:22:24', 1),
(2, 'Backup Warehouse', 'Kumasi', 'active', '2025-07-25 17:23:03', 1, '2025-07-26 23:29:50', 1),
(3, 'Pharmacy Store', 'Cape Coast', 'active', '2025-07-25 17:23:03', 1, '2025-07-27 00:22:24', 1),
(4, 'Warehouse D', 'Tamale', 'active', '2025-07-25 17:23:03', 1, '2025-07-27 00:22:24', 1),
(5, 'Office Store', 'Takoradi', 'active', '2025-07-25 17:23:03', 1, '2025-07-27 00:22:24', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_type_roles` FOREIGN KEY (`user_type`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
