-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Mar 24, 2025 at 06:02 PM
-- Server version: 8.0.41
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `maxmed`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('maksibowersen54@gmail.com|128.201.76.200:timer', 'i:1742139830;', 1742139830),
('maksibowersen54@gmail.com|128.201.76.200', 'i:2;', 1742139830),
('williamskatie227133@yahoo.com|36.94.183.5:timer', 'i:1742209005;', 1742209005),
('williamskatie227133@yahoo.com|36.94.183.5', 'i:2;', 1742209005),
('mohanad.babi@gmail.com|151.253.234.100:timer', 'i:1742324507;', 1742324507),
('mohanad.babi@gmail.com|151.253.234.100', 'i:2;', 1742324507),
('einslib6@gmail.com|123.19.30.94:timer', 'i:1742445070;', 1742445070),
('einslib6@gmail.com|123.19.30.94', 'i:2;', 1742445070),
('rozadickso@gmail.com|117.213.249.125:timer', 'i:1742553148;', 1742553148),
('rozadickso@gmail.com|117.213.249.125', 'i:2;', 1742553148),
('acogregnb@gmail.com|110.44.116.161:timer', 'i:1742615716;', 1742615716),
('acogregnb@gmail.com|110.44.116.161', 'i:2;', 1742615716),
('stephenvogel778692@yahoo.com|116.212.156.68:timer', 'i:1742661407;', 1742661407),
('stephenvogel778692@yahoo.com|116.212.156.68', 'i:2;', 1742661407),
('joinerchu183459@yahoo.com|191.52.225.153:timer', 'i:1742782288;', 1742782288),
('joinerchu183459@yahoo.com|191.52.225.153', 'i:2;', 1742782288),
('aabraxasay22chime96@gmail.com|139.5.71.84:timer', 'i:1742838200;', 1742838200),
('aabraxasay22chime96@gmail.com|139.5.71.84', 'i:2;', 1742838200);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `created_at`, `updated_at`, `image_url`) VALUES
(39, NULL, 'MaxTest© Rapid Tests IVD', '2025-03-23 16:39:03', '2025-03-23 16:39:03', 'https://maxmedme.com/storage/categories/PiUL2buwehJ7tTnyF0dbfY1dfdABGXh6Ka9RaA1a.png'),
(40, NULL, 'MaxWare© Plasticware', '2025-03-23 16:42:09', '2025-03-23 16:42:09', 'https://maxmedme.com/storage/categories/oOMWXJ16peCPSQ8PN1vocgqaOwYlnzJnTg0kkdMD.jpg'),
(43, NULL, 'Laboratory Equipments', '2025-03-23 16:43:30', '2025-03-23 16:43:30', 'https://maxmedme.com/storage/categories/Qmd5oOWrhNbSjFUlbSN7SqrjNozUEgayO1s8L94n.jpg'),
(44, 43, 'Analytical Chemistry', '2025-03-23 16:44:06', '2025-03-23 16:44:06', 'https://maxmedme.com/storage/categories/Z5ij53wwsUD4ckFPvhiO0cwDDlUvgdorMaJDyAQQ.jpg'),
(45, 43, 'Microbiology', '2025-03-23 16:44:22', '2025-03-23 16:44:22', 'https://maxmedme.com/storage/categories/cE80pE3WoLGKncozg50Wxmkw6goFyo0fOjioSxnm.jpg'),
(46, 43, 'Molecular Biology', '2025-03-23 16:44:54', '2025-03-23 16:44:54', 'https://maxmedme.com/storage/categories/fjQsygEqx7EExOY4KSifOYWy60YXXcwbQNp58XXM.jpg'),
(41, NULL, 'Medical Consumables', '2025-03-23 16:42:26', '2025-03-23 16:42:26', 'https://maxmedme.com/storage/categories/xDMXS75KOpeCvxuRmg8AkYHNSJ9i45rn2sa3gtGy.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventories`
--

INSERT INTO `inventories` (`id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(30, 32, 4, '2025-03-20 17:53:02', '2025-03-20 17:53:02'),
(31, 33, 4, '2025-03-20 17:56:16', '2025-03-20 17:56:16'),
(32, 34, 3, '2025-03-20 17:58:03', '2025-03-20 17:58:03'),
(33, 35, 3, '2025-03-20 18:01:12', '2025-03-24 16:52:37'),
(29, 31, 3, '2025-03-20 17:47:43', '2025-03-20 17:47:43'),
(28, 30, 4, '2025-03-17 18:01:31', '2025-03-18 19:02:27');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(5, '2025_02_15_084645_create_products_table', 2),
(7, '2025_02_16_090801_create_categories_table', 4),
(8, '2025_02_16_090827_add_category_id_to_products_table', 4),
(9, '2023_10_10_000002_create_inventories_table', 5),
(17, '2025_02_23_083107_create_order_items_table', 8),
(16, '2025_02_23_083107_create_orders_table', 7),
(18, '2025_02_23_083107_create_transactions_table', 9),
(20, '2024_03_20_create_product_reservations_table', 10),
(21, 'xxxx_xx_xx_add_is_admin_to_users_table', 11),
(22, 'xxxx_xx_xx_create_news_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `shipping_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_zipcode` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `status`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_zipcode`, `shipping_phone`, `notes`, `created_at`, `updated_at`) VALUES
(21, 7, 'ORD-67D9C331CD1EC', 100.00, 'processing', 'Default Address', 'Default City', 'Default State', '12345', '1234567890', NULL, '2025-03-18 19:02:09', '2025-03-18 19:02:27'),
(22, 7, 'ORD-67D9C3F6406D3', 200.00, 'pending', 'Default Address', 'Default City', 'Default State', '12345', '1234567890', NULL, '2025-03-18 19:05:26', '2025-03-18 19:05:26'),
(23, 7, 'ORD-67DEED1F0F104', 100.00, 'pending', 'Default Address', 'Default City', 'Default State', '12345', '1234567890', NULL, '2025-03-22 17:02:23', '2025-03-22 17:02:23'),
(24, 7, 'ORD-67E18D0857D88', 100.00, 'pending', 'Default Address', 'Default City', 'Default State', '12345', '1234567890', NULL, '2025-03-24 16:49:12', '2025-03-24 16:49:12'),
(25, 7, 'ORD-67E18D5B71CA7', 100.00, 'pending', 'Default Address', 'Default City', 'Default State', '12345', '1234567890', NULL, '2025-03-24 16:50:35', '2025-03-24 16:50:35'),
(26, 7, 'ORD-67E18D80B2F66', 1.00, 'processing', 'Default Address', 'Default City', 'Default State', '12345', '1234567890', NULL, '2025-03-24 16:51:12', '2025-03-24 16:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(24, 20, 4, 1, 300.00, '2025-02-28 11:24:07', '2025-02-28 11:24:07'),
(23, 19, 4, 3, 300.00, '2025-02-25 15:50:10', '2025-02-25 15:50:10'),
(22, 19, 1, 3, 300.00, '2025-02-25 15:50:10', '2025-02-25 15:50:10'),
(21, 18, 4, 1, 300.00, '2025-02-25 15:32:40', '2025-02-25 15:32:40'),
(20, 17, 4, 5, 300.00, '2025-02-25 15:05:40', '2025-02-25 15:05:40'),
(19, 16, 4, 5, 300.00, '2025-02-25 15:05:32', '2025-02-25 15:05:32'),
(18, 15, 7, 3, 300.00, '2025-02-25 14:48:30', '2025-02-25 14:48:30'),
(17, 15, 5, 6, 300.00, '2025-02-25 14:48:30', '2025-02-25 14:48:30'),
(25, 21, 30, 1, 100.00, '2025-03-18 19:02:09', '2025-03-18 19:02:09'),
(26, 22, 30, 2, 100.00, '2025-03-18 19:05:26', '2025-03-18 19:05:26'),
(27, 23, 32, 1, 100.00, '2025-03-22 17:02:23', '2025-03-22 17:02:23'),
(28, 24, 32, 1, 100.00, '2025-03-24 16:49:12', '2025-03-24 16:49:12'),
(29, 25, 35, 1, 100.00, '2025-03-24 16:50:35', '2025-03-24 16:50:35'),
(30, 26, 35, 1, 1.00, '2025-03-24 16:51:12', '2025-03-24 16:51:12');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(8,2) NOT NULL,
  `price_aed` decimal(8,2) DEFAULT NULL,
  `image_url` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `price_aed`, `image_url`, `category_id`, `created_at`, `updated_at`) VALUES
(32, 'MaxTest Rotavirus Rapid Test 20 Tests/Kit', 'Rapid qualitative detection of stool Rotavirus antigen for in vitro diagnostic use.\r\n\r\nFEATURES\r\n\r\nMethod: Monoclonal immunochromatographic assay\r\n\r\nSpecimen: Stool\r\n\r\nDetection device: Dipstick\r\n\r\nTime to Result: 10 minutes\r\n\r\nExternal Quality Control: Positive control swab\r\n\r\nPackage: 20 tests/kit\r\n\r\nStorage temperature: 15-30℃\r\n \r\n\r\nPERFORMANCE CHARACTERISTICS\r\n\r\nThe analytical sensitivity was evaluated using 2 Rotavirus strains.\r\n\r\nLowest detectable limit:\r\n\r\nRotavirus /Hu/Australia/102510/77/L : 8.89X102 TCID50/mL\r\n\r\nRotavirus /HRV 89-12C2 : 1.58X103 TCID50/mL\r\n\r\n \r\nANALYTICAL SPECIFICITY\r\n\r\nThe cross reactivity study was evaluated with a total of 15 bacteria strains and 20 viruses strains. None of these tested microorganisms gave a positive result.', 100.00, NULL, 'https://maxmedme.com/storage/products/0qQKsGz9rKs6SOg5iikJStIsBqAMUMLxZmqN6UFC.jpg', 39, '2025-03-20 17:53:02', '2025-03-20 17:53:02'),
(31, 'MaxTest Strep A Rapid Test 20 Tests/kit', 'Rapid qualitative detection of Group A streptococcus antigen in human throat specimens for in vitro diagnostic use.\r\n\r\nFEATURES\r\n\r\nMethod: Polyclonal immunochromatographic assay\r\n\r\nSpecimen: Throat swab\r\n\r\nDetection device: Dipstick\r\n\r\nTime to Result: 5 minutes\r\n\r\nExternal Quality Control: Positive and negative control reagent\r\n\r\nPackage: 20 tests/kit\r\n\r\nStorage temperature: 15-30℃\r\n\r\n \r\nPERFORMANCE CHARACTERISTICS\r\n\r\nThe analytical sensitivity was evaluated using 3 Group A Streptococcus strains.\r\n\r\nLowest detectable limit:\r\n\r\nGroup A Streptococcus/ATCC 19615 : 1x104 org/swab or 2.5x 105 org/ml\r\n\r\nGroup A Streptococcus/ATCC 14289 : 1x106 org/swab or 2.5x 107 org/ml\r\n\r\nGroup A Streptococcus/ATCC 12344 : 1x106 org/swab or 2.5x 107 org/ml\r\n\r\n\r\nANALYTICAL SPECIFICITY\r\n\r\nThe cross reactivity study was evaluated with a total of 14 bacteria strains and 20 viruses strains. None of these tested microorganisms gave a positive result.', 100.00, NULL, 'https://maxmedme.com/storage/products/IG2rhPjbnvhd8ViLLum8S4QIwQvbIoDcbzRdwjQH.jpg', 39, '2025-03-20 17:47:43', '2025-03-20 17:50:10'),
(30, 'MaxTest H. pylori Antigen Rapid Test 20 test / Kit', 'Vstrip H. pylori Antigen Rapid Test is an immunochromatographic assay for the rapid detection of H. pylori antigen in human stool specimens.', 200.00, 0.00, 'https://maxmedme.com/storage/products/tjZn0fc4zaNSEFRrsAPoWjINY2kAb5qiejXoE8pF.jpg', 39, '2025-03-17 18:01:31', '2025-03-24 16:54:29'),
(33, 'MaxTest Flu A&B Rapid Test 20 Tests/kit', 'Rapid qualitative detection of influenza type A and type B antigens in human nasal specimens for in vitro diagnostic use.\r\n\r\nFEATURES\r\n\r\nMethod: Monoclonal immunochromatographic assay\r\n\r\nSpecimen: Nasopharyngeal swab\r\n\r\nDetection Device: Dipstick\r\n\r\nTime to Result: 10 minutes\r\n\r\nExternal Quality Control: Positive influenza A and influenza B control swab\r\n\r\nPackage: 20 tests/kit\r\n\r\nStorage temperature: 15-30℃\r\n \r\n\r\nCLINICAL PERFORMANCE \r\n\r\nComparison study was conducted in 132 patients with Flu-like symptoms and compared the results of Vstrip® Flu A&B Rapid Test to a FDA-cleared commercial kit.\r\n                               Influenza A      Influenza B       \r\nSensitivity:                94.7%               91.3%\r\nSpecificity:                97.9%               100%\r\nAccuracy:                 97.0%               98.5%\r\nPPV:                         94.7%               100%\r\nNPV:                         97.9%               98.2%\r\n\r\nANALYTICAL SENSITIVITY\r\n\r\nAmong the 14 influenza A strains (H1N1, H3N2, etc.) and 3 influenza B viruses (Victoria, Yamagata), the minimum detectable concentration was follows:\r\nFlu A H1N1 subtype: 1.0x104 TCID50/ml\r\nFlu A H3N2 subtype: 5.0x103 TCID50/ml\r\nFlu B virus                : 1.25x105 TCID50/ml\r\n\r\nANALYTICAL SPECIFICITY\r\n\r\nThe cross reactivity study was evaluated with a total of 15 bacteria strains and 5 viruses strains. None of these tested microorganisms gave a positive result.', 100.00, NULL, 'https://maxmedme.com/storage/products/noTuQphheHHTbbcr57PTMCiGmL08W8n348xo4b0w.jpg', 39, '2025-03-20 17:56:16', '2025-03-20 17:56:16'),
(34, 'MaxTest RSV Rapid Test 20 Tests/kit', 'Rapid qualitative detection of respiratory syncytial virus(RSV) antigen in human nasal specimens for in vitro diagnostic use.\r\n\r\nFEATURES\r\n\r\nMethod: Monoclonal immunochromatographic assay\r\n\r\nSpecimen: Nasopharyngeal swab, Nasopharyngeal wash/aspirate\r\n\r\nDetection device: Cassette\r\n\r\nTime to Result: 10 minutes\r\n\r\nExternal Quality Control: Positive and Negative control reagent\r\n\r\nPackage: 20 tests/kit\r\n\r\nStorage temperature: 15-30℃\r\n\r\n \r\nPERFORMANCE CHARACTERISTICS\r\n\r\nThe analytical sensitivity was evaluated using 4 RSV strains.\r\n\r\nLowest detectable limit:\r\n\r\nRSV/ A2 (ATCC VR-1540): 2.24x103 TCID50/mL\r\n\r\nRSV/ 18537 (ATCC VR-1582): 8.89 x103TCID50/mL\r\n\r\n\r\nANALYTICAL SPECIFICITY\r\n\r\nThe cross reactivity study was evaluated with a total of 15 bacteria strains and 20 viruses strains. None of these tested microorganisms gave a positive result.', 100.00, NULL, 'https://maxmedme.com/storage/products/rYpY3yDukYzt7kCAQx8vWhhzgwhQT23BfzgybJss.jpg', 39, '2025-03-20 17:58:03', '2025-03-20 17:58:03'),
(35, 'MaxTest COVID-19 Antigen Rapid Test 20 Test/Kit', 'Rapid qualitative detection of SARS-CoV-2 antigen in human nasopharyngeal specimens for in vitro diagnostic use.', 100.00, 0.00, 'https://maxmedme.com/storage/products/jzPjCUev4ZQq6TVMc8FJvGjr9GyZ16ri1epsunL1.jpg', 39, '2025-03-20 18:01:12', '2025-03-24 16:53:06');

-- --------------------------------------------------------

--
-- Table structure for table `product_reservations`
--

CREATE TABLE `product_reservations` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL,
  `session_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `status` enum('pending','confirmed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_reservations`
--

INSERT INTO `product_reservations` (`id`, `product_id`, `user_id`, `quantity`, `session_id`, `expires_at`, `status`, `created_at`, `updated_at`) VALUES
(27, 35, 7, 1, 'T8LBFh6W0ctzBcr2BNwLUd6LkDV07yGxyfIoyqA2', '2025-03-24 16:52:08', 'confirmed', '2025-03-24 16:51:08', '2025-03-24 16:52:37'),
(23, 32, 7, 1, 'AX9JEuIKqivaZSi16X7yP5vh1zHZnR52QMlpGGoW', '2025-03-22 17:03:18', 'pending', '2025-03-22 17:02:18', '2025-03-22 17:02:18'),
(22, 30, 7, 2, 'wNwfFZm7uxdT7pTPrtqyiJvbJC6XqxqVY5r5W9Vi', '2025-03-18 19:06:19', 'pending', '2025-03-18 19:05:19', '2025-03-18 19:05:19'),
(21, 30, 7, 1, 'wNwfFZm7uxdT7pTPrtqyiJvbJC6XqxqVY5r5W9Vi', '2025-03-18 19:03:05', 'confirmed', '2025-03-18 19:02:05', '2025-03-18 19:02:27'),
(20, 29, 7, 1, 'M580QHuBo2jYvEOuHotjyqhJHSen1CXB7i4nQRBT', '2025-03-09 16:32:28', 'pending', '2025-03-09 16:31:28', '2025-03-09 16:31:28');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('5R2q3y2f0RgjjMvTXvMLoYVoYKcS29R3YOvrFsI1', NULL, '104.238.221.120', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 Edg/126.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMVFRTWRrM1ZGRGhTcXN2bHNxN2VnbU9XQ2ZaaUsxQzFvTzQyOHFrZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vbWF4bWVkbWUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742828866),
('t5MbbI3GNbLB7QvmxKqaVDMj21Lkk1NTkhJeghXl', NULL, '3.144.76.107', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ1FYZlN6aFJxMENaajFSNndmbElicTBaTmkwRGU5aXFQNm1reGhaMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vMTMuNTAuOTguMjAxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742829311),
('cWIRew93c6AIwF6rNeotlwa10wCxo6zieFDJIclz', 11, '151.253.234.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicXd4OFN1YzFVc05oczNPV0UyMG1DRHRVdUV4RHFnWGNVWVNrTE04VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vbWF4bWVkbWUuY29tL3Byb2R1Y3RzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTE7fQ==', 1742837975),
('kQlQcjnIprD5Gms7jaLjNBQD4vyehhZ6DQA1ik6D', NULL, '216.244.66.227', 'Mozilla/5.0 (compatible; DotBot/1.2; +https://opensiteexplorer.org/dotbot; help@moz.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY3FiQ0hEZ0JmRU9sekJmcXFZeHJIZzZXVWdGTVdNalo4UVlNTlBRRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzQ6Imh0dHBzOi8vMTMuNTAuOTguMjAxL3Byb2R1Y3RzP2NhdGVnb3J5PU1heFRlc3QlQzIlQTklMjBSYXBpZCUyMFRlc3RzJTIwSVZEIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742831593),
('YNSvGfhfILQPBcq9zPT0RwEtiI3yrWUfDvrrMAPk', NULL, '43.130.228.73', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVUl4OWxHaWFNakc5bkpRbHRjclJPQ0xaNDBjSXhhYzh3dXhtUGY5MSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHBzOi8vd3d3Lm1heG1lZG1lLmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742832029),
('8uF0KWcphuywqXOyc3z187k8IkgsWabDGyrtXq8j', NULL, '151.253.234.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieFJqSXZsT2VJU2hMbFE0SVA1Mjk4M1VFY3NJcjBnRnhYVzhYTkswVyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vbWF4bWVkbWUuY29tL3Byb2R1Y3RzIjt9fQ==', 1742832836),
('kBy8jAZpEqW4P6FDKKbtioGMDusG5gVlHiKAyaOt', NULL, '50.117.73.87', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN2tFWXdCS21PbGxKcFZLRHZPVjIzdzVPTkZyaG5UZjVqNkoya3FjRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHBzOi8vd3d3Lm1heG1lZG1lLmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742833870),
('iJErSdCnq5wtLeunTEnN0jw1x2zWWpxmcsIMGDuy', NULL, '115.55.63.130', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVnR3TWU2VUJlb2FYdDVBc29wS0JFRTJZd3RCaW5WZlR1N3RoNXdPUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vbWF4bWVkbWUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742834061),
('8IDKnHIXDb5D5EySB2ZdZBMg1cpTLzUkoAchBP9d', NULL, '216.244.66.227', 'Mozilla/5.0 (compatible; DotBot/1.2; +https://opensiteexplorer.org/dotbot; help@moz.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS2pNT3ROeHllMXFSOFNpamQ1OWtsMzJDdWhwWmxSZTFDRkhzQ3ZwTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjA6Imh0dHBzOi8vMTMuNTAuOTguMjAxL3Byb2R1Y3RzP2NhdGVnb3J5PU1lZGljYWwlMjBDb25zdW1hYmxlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742834379),
('fW8AXWK51EAdEtA796shzut6Hc1HVMKIVLLwPG2Z', NULL, '43.130.53.252', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiclZkVmVkRVZoS01PN09YbVdVRTNSR2tTOVdIVGdmMzd0bXRoRXF2OCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vbWF4bWVkbWUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742834390),
('9FSz8bAx6ZJI5BT2Zb8ukMBZBBFfNg4AWUJAqeer', NULL, '157.55.39.7', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVGh5RjQ5elRMdE9ERjhEY0VLang5TGdBbmJRb1dkNDZpOHdld2cyWCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMzoiaHR0cHM6Ly9tYXhtZWRtZS5jb20vcXVvdGF0aW9uLzM0Ijt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vbWF4bWVkbWUuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742834416),
('TGHPUwoJAhbAyCEw1J50XWVCwdnTZeYDTGUUO2Qw', NULL, '157.55.39.7', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNnI2bTNJbDQ3Y1NmT3ZIR1c5MEZSUnRMaTVvSFNROW5iaTZBbXNHbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vbWF4bWVkbWUuY29tL2NhdGVnb3JpZXMvNDYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1742834417),
('oXbNKGMI0H5mF35YMx4Iik1VjVHINTjCf0dnR6cz', NULL, '40.77.167.45', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWkh6b2dFRVlrN0wzVW01SjdlTERIOFpadXpEbTFlTmpLUXI4VW9GbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vbWF4bWVkbWUuY29tL3Byb2R1Y3QvMzMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1742834419),
('GzOWmuBerLWCs5rXdZLSqhTQ5HCTvFN1tLecEdbD', NULL, '157.55.39.7', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiamtzMllpanZDdk1PMVBGQkRQekVyT1FzazFmR3JITUVKejU4MnROdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vbWF4bWVkbWUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742834420),
('4xNSZLDiPo7KcLN1IMLbXg4D2z9CF2sYLGMDfdSx', NULL, '157.55.39.7', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibm9VOElPUFZSZUFTR3VORUNxT2NFcDRabTlrTkw3bldERlNsT2UxTSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMzoiaHR0cHM6Ly9tYXhtZWRtZS5jb20vcXVvdGF0aW9uLzM0Ijt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vbWF4bWVkbWUuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742834426),
('6Xw6fuGAXnlnlJQFJbKgqc2zma84vz3DIXgFPXk7', NULL, '223.113.128.174', 'curl/7.29.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ2JtT1JyVHllS09FdWpuV3h3U3F1VDJMNFhkVGFXNkpXZTRDNlNaTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vbWF4bWVkbWUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742834527),
('T8LBFh6W0ctzBcr2BNwLUd6LkDV07yGxyfIoyqA2', 7, '87.201.178.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiSGNLTWRBdlVhTUFiRk5rWDFCcmtVTmZvYk5uZ01iaFoxM1paV3czaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vbWF4bWVkbWUuY29tL3F1b3RhdGlvbi8zMiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O3M6NDoiY2FydCI7YTowOnt9fQ==', 1742835572),
('L7Kfd6jf07Zz7LU9VLgKzqtGa6qcieYR7doP4yBG', NULL, '207.46.13.127', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWkVwVXJ5bDFsTE1NZVJMdG1PeUt4WVZ3UXNSanp6SlJUVXQxVHhXNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vbWF4bWVkbWUuY29tL3Byb2R1Y3RzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742835797),
('eFtcFX9BuIefzXxy8hom6ya7aFsv55lHQ2preOL0', NULL, '157.55.39.9', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU1BwSjdZR2J4QUtCVERwSTBFdFJaanVlRWF2RENlRTFLRWRyanNXVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vbWF4bWVkbWUuY29tL2NhdGVnb3JpZXMvMzkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1742835301),
('AUhnsHmARhoDVxibTKbp0o0WMtv50ErgYMoyRSI4', NULL, '207.46.13.127', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicVpHM2RtV0ZLMHFoUkNuejFkeW53TFE4RnRwZTlBNFpHMk9aQVdVUCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNToiaHR0cHM6Ly9tYXhtZWRtZS5jb20vY2FydCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI2OiJodHRwczovL21heG1lZG1lLmNvbS9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742835949),
('SbdvdZnJdahM93ZrftFQEh2W3NKndd2Ow8STNhxU', NULL, '209.126.83.235', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiODNDS2lCZVpLR1lYellSNXBiOHE5T2pGSnl3MndzajVab1RXdDlEQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vMTMuNTAuOTguMjAxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742836190),
('YpI3xdXr41FI35p8g9GCb17nB9OMDYissjHiy6l7', NULL, '157.55.39.202', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib2xvb2NrRG5IUFZ2OEdHZE1yRUpCbWR4YjZlZllOc2djN2YzSXlBQiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMzoiaHR0cHM6Ly9tYXhtZWRtZS5jb20vcXVvdGF0aW9uLzMyIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vbWF4bWVkbWUuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742836194),
('IHMQjXdfjPU7Aw84SQiJ6GtNNZI1i0gCSQPJZ03x', NULL, '66.249.68.6', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.6998.165 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWFM1a2JYeTU5cTlETGF4czNWeDRtQ01ORFlFN0RFV3BhZFNWR1Y2ZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vbWF4bWVkbWUuY29tL2NhdGVnb3JpZXMvNDUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1742836398),
('gQ7FEFnVH0SWbRdjHFArjJk6ialg4czKh5RyGqln', NULL, '52.167.144.225', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVHR3MU1nZjV5MVVIeUZ1T2QycWNMdUc1TXZ5Wk9aNjUzd1ljTnF3QyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vbWF4bWVkbWUuY29tL3JlZ2lzdGVyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742836421),
('8u4gk9qWQfZCJCh3jAfaJ6rqT2InTi1lGKoNT1Ld', NULL, '18.192.68.74', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieHpsazRDVjJRS1lVb3hPWVJRcDY5eFRPdldnSGRXcm4wMG9qRTFlWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vbWF4bWVkbWUuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742836802),
('owfsQ0FSRfR1lmSLIGogOlixdLQyxJCdGgmqWjTc', NULL, '157.55.39.13', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ2hzenR4bXdLN1N4RFBpVFVUbUNxVmlYSGM0QkhuUnpucUVxTDdlMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vbWF4bWVkbWUuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742837109),
('wl0nU1NXBOtf23TrPOShTu4MUBkmqDorJIrYKDC1', 17, '139.5.71.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMU5Kd1p1WmVoZmlNMzU2ajRxRmZLSnRVVVQ3QTE1RkpOSXJjUVliVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vbWF4bWVkbWUuY29tL2Rhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE3O30=', 1742838156),
('6REYxYVq2d6puQQjVbWpXrqSCjMfAVHPkbduILXo', NULL, '52.167.144.138', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFBXUUZpOHFoZFFIVDk0bW9PZTRHbnFxRFpNcTNMVGo1dEwwN2pZRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHBzOi8vbWF4bWVkbWUuY29tL3Byb2R1Y3RzP2NhdGVnb3J5PU1pY3JvYmlvbG9neSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742838204),
('5s8WWQilJaVZX4kghJHy9vTyHP4DTpUDZewDp2yY', NULL, '157.55.39.203', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieVpoa2RGTEc1eThNZ29YRmZydm5JcHJuSDQ1eWtTV2dkaldDOE1CaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vbWF4bWVkbWUuY29tL25ld3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1742838813),
('LJw3sloRRfSAZmzEUWjpJFkhnF0PoVsMChq3LZUi', NULL, '157.55.39.203', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT1FVdm55NTJFaHBOV25pQWt0Q2dFaU45YlE2QjMyMHhXRUZtQ2hUbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vbWF4bWVkbWUuY29tL3BhcnRuZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742839093);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `order_id`, `user_id`, `amount`, `payment_method`, `status`, `transaction_id`, `created_at`, `updated_at`) VALUES
(4, 15, 7, 2700.00, 'stripe', 'completed', 'cs_test_b11soVAGy4DMZbORgQe2jQZIUedFjA11f9vvOMzrhOZFMD21fslkpsMgNj', '2025-02-25 14:48:55', '2025-02-25 14:48:55'),
(5, 19, 7, 1800.00, 'stripe', 'completed', 'cs_test_b1YP2HzIbEAFa4xtHOGUgXpvBEmRlKCeiKnCxffoOk2QQtBTA2rpNNnTJK', '2025-02-25 15:50:24', '2025-02-25 15:50:24'),
(6, 21, 7, 100.00, 'stripe', 'completed', 'cs_test_a1DVW3yWLq95znJ99PbFd9HQ4DAne2sIDkei8FWDD5om41mjXcQlexLxDg', '2025-03-18 19:02:27', '2025-03-18 19:02:27'),
(7, 26, 7, 1.00, 'stripe', 'completed', 'cs_live_a1FcDauj6V6uiY2mMmIvSNhrro6tuJ8krFfVabszRcCI43hY1jDMFBMzmQ', '2025-03-24 16:52:37', '2025-03-24 16:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `is_admin`) VALUES
(3, 'Walid', 'walid.babi.du@gmail.com', NULL, '$2y$12$hm/6G63r0ogNnO0/g1DCNeJ2S.6KFjPjqaqwE/sy6BILiyoW3pRFy', 'gnhDoBhcMnjm5d6sXMX68XT0Adbi4TgoanUxtr2avQe6eAPSwSvV5h34PqCZ', '2025-02-15 02:57:16', '2025-02-15 04:39:37', 0),
(8, 'liWvASSYh', 'maksibowersen54@gmail.com', NULL, '$2y$12$kCCdpHHDfqvB6E.jQ/ZlO.oksO4/KvLMLcg6bqTXQu.tN1P7Imo2a', NULL, '2025-03-16 15:43:15', '2025-03-16 15:43:15', 0),
(7, 'Walid', 'walidbabi@localhost.com', NULL, '$2y$12$8DoIT4/hNspvjOv7UbhmtePNdwR6pM3N4G0IOT6vFGt3.byEMPpJ6', NULL, '2025-02-16 03:13:54', '2025-02-16 03:13:54', 1),
(9, 'GyqhYnGJ', 'williamskatie227133@yahoo.com', NULL, '$2y$12$2PLum73tsv.868YPwdg7JuWs0/b3hkd/m3wseAQU69TAz5XxqakgW', NULL, '2025-03-17 10:56:19', '2025-03-17 10:56:19', 0),
(10, '* * * Snag Your Free Gift: https://errandsservices.com/uploads/eis2xo.php?egsyc1m * * * hs=59a4e499e224c8201918190118accfe3*', 'pazapz@mailbox.in.ua', NULL, '$2y$12$UqGA6RjjTkwDMo9A1Mf5/e8mkdIoQM9Xh8VQFDR9IOYXkIZsJ/zvm', NULL, '2025-03-17 14:09:11', '2025-03-17 14:09:11', 0),
(11, 'Mohanad', 'sales@maxmedme.com', NULL, '$2y$12$tdv7HDIH14IWTVYaCqQn7uF3LofJT6QjhH9Cl0mDf8ftszAJ4w.xi', NULL, '2025-03-17 17:54:45', '2025-03-17 17:54:45', 1),
(12, 'ZDZwNzUhtQ', 'einslib6@gmail.com', NULL, '$2y$12$2hz4Jj3IQkeabemu7smVi.ztCAV0xefXZC1qd.HaVve.uoYvS7pCG', NULL, '2025-03-20 04:30:44', '2025-03-20 04:30:44', 0),
(13, 'mZSdBSUazZo', 'rozadickso@gmail.com', NULL, '$2y$12$IDGucvJxA8osJ9fCWCeKqeO56YPvK4FANTBvpY3qOaOphdNhAhSdS', NULL, '2025-03-21 10:31:47', '2025-03-21 10:31:47', 0),
(14, 'LVsuaEuDkPymdL', 'acogregnb@gmail.com', NULL, '$2y$12$XGh2a/hSYbetAih87EQIBODjyS./Ymtqj.vTS2zs30DEHwfujsLoq', NULL, '2025-03-22 03:54:28', '2025-03-22 03:54:28', 0),
(15, 'emaOwWeDPJ', 'stephenvogel778692@yahoo.com', NULL, '$2y$12$7TDIoyx3oA7z/LoEuSiG1epTYU7PSXkxyj3aEmu/KKmsYtCmXgP3i', NULL, '2025-03-22 16:36:06', '2025-03-22 16:36:06', 0),
(16, 'lUwIfwjUUdUlqG', 'joinerchu183459@yahoo.com', NULL, '$2y$12$TxYLAmgg2sZwopPJnRrNLOrfxDLnv52OdJYIUK7aIrN39OMV6xJI6', NULL, '2025-03-24 02:10:41', '2025-03-24 02:10:41', 0),
(17, 'iTnUjCiyea', 'aabraxasay22chime96@gmail.com', NULL, '$2y$12$J.ud95MSiSmlho7MO/a8ienHeqnyArt09w3YT9BtLw8TMhAFUWY9q', NULL, '2025-03-24 17:42:32', '2025-03-24 17:42:32', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventories_product_id_foreign` (`product_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_reservations`
--
ALTER TABLE `product_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_reservations_product_id_foreign` (`product_id`),
  ADD KEY `product_reservations_user_id_foreign` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_transaction_id_unique` (`transaction_id`),
  ADD KEY `transactions_order_id_foreign` (`order_id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `product_reservations`
--
ALTER TABLE `product_reservations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
