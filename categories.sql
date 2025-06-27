-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Jun 25, 2025 at 07:49 AM
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
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `created_at`, `updated_at`, `image_url`) VALUES
(81, 39, 'Women\'s Health Rapid Tests', 'rapid-test-kits-rdt-womens-health-rapid-tests', '2025-04-14 16:35:17', '2025-04-14 20:23:37', 'https://maxmedme.com/storage/categories/weUWLyqC7SfW06F5epnC9Qav1s36K2qDKvMzKKKx.png'),
(39, 51, 'Rapid Test Kits RDT', 'molecular-clinical-diagnostics-rapid-test-kits-rdt', '2025-04-02 17:07:31', '2025-04-19 03:20:23', 'https://maxmedme.com/storage/categories/x8SzL2S6kwZAmn8GhVTcVxwveasUbShjVOLAmGg6.png'),
(51, NULL, 'Molecular & Clinical Diagnostics', 'molecular-clinical-diagnostics', '2025-04-02 17:05:46', '2025-04-11 17:07:38', 'https://maxmedme.com/storage/categories/r29cdfJRiesz8Y9K5GTEDGXin9lDn9uchcvGrmSX.jpg'),
(79, NULL, 'Technology & AI Solutions', 'technology-ai-solutions', '2025-04-11 17:03:58', '2025-04-11 17:13:02', 'https://maxmedme.com/storage/categories/VvpmPZSHW9XpzFi12wsPPaxB5tXPDvtOzhm8waEp.jpg'),
(57, NULL, 'Medical Consumables', 'medical-consumables', '2025-04-02 18:35:57', '2025-04-03 17:03:47', 'https://maxmedme.com/storage/categories/M6i4LWMqpzaBAqDuxYrX9oyLq9cCOhzU9QsGMK6y.jpg'),
(50, 66, 'Mixing & Shaking Equipment', 'lab-equipment-mixing-shaking-equipment', '2025-04-03 17:12:14', '2025-04-11 16:39:26', 'https://maxmedme.com/storage/categories/5cLqitWR52vTaFuCnNBwz6VQWfK8cQQn2DKwxLxw.png'),
(59, 50, 'Centrifuges', 'mixing-shaking-equipment-centrifuges', '2025-04-03 18:13:04', '2025-04-11 16:39:44', 'https://maxmedme.com/storage/categories/uLdHw8tbVvMyTd0Vm9WSy5mUrWVWDIukrypzPATL.jpg'),
(60, NULL, 'Life Science & Research', 'life-science-research', '2025-04-05 11:21:15', '2025-04-11 17:05:11', 'https://maxmedme.com/storage/categories/aF11WNkmE50ePEfUMIyAqzWy6aDIbgiN7rrCRmSx.jpg'),
(61, 68, 'Electrochemistry Equipment', 'analytical-instruments-electrochemistry-equipment', '2025-04-11 16:47:09', '2025-04-11 16:47:09', 'https://maxmedme.com/storage/categories/a79yxAGQfMWcMMBf5dyFlTsDdTqceZxORb2545XL.png'),
(62, 50, 'Mixers', 'mixing-shaking-equipment-mixers', '2025-04-11 12:49:51', '2025-04-11 16:39:38', 'https://maxmedme.com/storage/categories/fvTf0z4kKyic8INH0OSbPuwILWBFkD1yFowGZiph.png'),
(63, 51, 'PCR & Molecular Analysis', 'molecular-clinical-diagnostics-pcr-molecular-analysis', '2025-04-11 15:17:57', '2025-04-11 16:32:31', 'https://maxmedme.com/storage/categories/69kEDp28lfImZcsdwplBmUNJ5z1aqmTanx11HaI6.png'),
(80, NULL, 'Lab Consumables', 'lab-consumables', '2025-04-14 12:46:35', '2025-04-14 12:48:21', 'https://maxmedme.com/storage/categories/vZg2LJtbBdfq1Pd6QSjwLNiSlpVsxzPDhTgShjVb.jpg'),
(71, 66, 'Thermal & Process Equipment', 'lab-equipment-thermal-process-equipment', '2025-04-11 16:49:56', '2025-04-11 16:49:56', 'https://maxmedme.com/storage/categories/xLpWf4hHKg92fZEbQsvhF7ZHAorT7IPYs1A34dZq.png'),
(66, NULL, 'Lab Equipment', 'lab-equipment', '2025-04-11 16:37:02', '2025-04-11 16:37:02', 'https://maxmedme.com/storage/categories/d5RpTGEQ9ArJamGA3WncHJygx3TnrQwA6ZHQRpvB.png'),
(58, 50, 'Shakers', 'mixing-shaking-equipment-shakers', '2025-04-11 16:38:08', '2025-04-11 16:38:08', 'https://maxmedme.com/storage/categories/sSjMKtT2NQwufXv03iyIcS1ENwgHCC8IEltFA4yU.jpg'),
(68, 66, 'Analytical Instruments', 'lab-equipment-analytical-instruments', '2025-04-11 16:40:52', '2025-04-11 16:40:52', 'https://maxmedme.com/storage/categories/zI3GF6oRsdi95gHjmi92EgaWRe7rt18LVYDWXHQP.jpg'),
(64, 68, 'UV-Vis Spectrophotometers', 'analytical-instruments-uv-vis-spectrophotometers', '2025-04-11 16:41:33', '2025-04-11 16:41:33', 'https://maxmedme.com/storage/categories/RfWABpu9U4GqHI8l2S5UOwpnFmBUzfbCYx6LVIpH.png'),
(72, 71, 'Distillation Systems', 'thermal-process-equipment-distillation-systems', '2025-04-11 16:51:00', '2025-04-11 16:51:00', 'https://maxmedme.com/storage/categories/yFzWYVsL9GzGOQ5SAFxaubFyto4TIkvzpArjgjWv.jpg'),
(73, 71, 'Incubators & Ovens', 'thermal-process-equipment-incubators-ovens', '2025-04-11 16:52:40', '2025-04-11 16:52:40', 'https://maxmedme.com/storage/categories/pjCnE1baDjIJkN7bqrAt3xJbGrlnKcynRORcXYsy.png'),
(74, 57, 'PPE & Safety Gear', 'medical-consumables-ppe-safety-gear', '2025-04-11 16:55:02', '2025-04-11 16:55:02', 'https://maxmedme.com/storage/categories/AVtXLgIHlwraEcTbiRC0e2TtqQRpp4aA5qeSyxrW.jpg'),
(75, 80, 'Lab Essentials (Tubes, Pipettes, Glassware)', 'lab-consumables-lab-essentials-tubes-pipettes-glassware', '2025-04-11 16:56:20', '2025-05-03 19:37:37', 'https://maxmedme.com/storage/categories/oBXXS0kyPFPuFuKC7pRY2xFOA22zssVdaLPlfrm9.png'),
(76, 80, 'Chemical & Reagents', 'lab-consumables-chemical-reagents', '2025-04-11 16:57:11', '2025-04-14 14:06:17', 'https://maxmedme.com/storage/categories/F5QQm6SraaWzNX8saOPrXpbcNgjVyjU4qa21Co8d.jpg'),
(102, 60, 'Recombinant Monoclonal Antibodies (recmAbTM)', 'life-science-research-recombinant-monoclonal-antibodies-recmabtm', '2025-06-17 10:43:10', '2025-06-17 10:43:10', 'https://maxmedme.com/storage/categories/ulXxLsWTtx00jonOXKA7V7TgvRB6CYu32mgttjvB.jpg'),
(101, 60, 'Recombinant Proteins', 'life-science-research-recombinant-proteins', '2025-06-17 10:42:03', '2025-06-17 10:42:03', 'https://maxmedme.com/storage/categories/yff0mePS1pfMSuEwv0a3Cg49hWHCxDJlpilQsqow.jpg'),
(82, 39, 'Infectious Disease Rapid Tests', 'rapid-test-kits-rdt-infectious-disease-rapid-tests', '2025-04-14 20:25:01', '2025-04-14 20:25:01', 'https://maxmedme.com/storage/categories/y0JuzbDgkAlsoqNs2olVmjGJA0csFMABjQCgMrq3.png'),
(83, 39, 'Drugs of Abuse Rapid Tests', 'rapid-test-kits-rdt-drugs-of-abuse-rapid-tests', '2025-04-14 20:25:27', '2025-04-14 20:25:27', 'https://maxmedme.com/storage/categories/gzhwXH1UnDUIrvukl6RLCPiSCipzfloIxFaxIQUM.png'),
(84, 39, 'Tumor Markers Rapid Tests', 'rapid-test-kits-rdt-tumor-markers-rapid-tests', '2025-04-14 20:25:39', '2025-04-14 20:25:39', 'https://maxmedme.com/storage/categories/oq06KnXsZgAs0cueD2OlHtKw86yz7ElLFabFs37I.png'),
(85, 39, 'Cardiac Markers Rapid Tests', 'rapid-test-kits-rdt-cardiac-markers-rapid-tests', '2025-04-14 20:25:53', '2025-04-14 20:26:32', 'https://maxmedme.com/storage/categories/dJQxlh4jdwLKdnfuhH18zvbJBSjRoC0aheqTZQRD.png'),
(87, 66, 'Microbiology Equipment', 'lab-equipment-microbiology-equipment', '2025-04-15 14:00:59', '2025-05-01 05:41:46', 'https://maxmedme.com/storage/categories/iFeKyFy8W7HOGiNz8ZjkXp2Gyneklbv8VKBOOGww.jpg'),
(86, 39, 'Other Rapid Tests', 'rapid-test-kits-rdt-other-rapid-tests', '2025-04-14 20:26:13', '2025-04-19 20:19:15', 'https://maxmedme.com/storage/categories/LD4OOsVZnZTRUG2UCjPDkklmwQFDFoKlvcXiN4ad.png'),
(88, 68, 'Chromatography Consumables', 'analytical-instruments-chromatography-consumables', '2025-04-17 05:56:30', '2025-04-17 05:56:30', 'https://maxmedme.com/storage/categories/FML7HvySGKashP4GkHyglYFQ0JGZJ3xTC66zXKM1.jpg'),
(89, 88, 'Ion Chromatography(IC)', 'chromatography-consumables-ion-chromatographyic', '2025-04-17 18:47:52', '2025-04-17 18:47:52', 'https://maxmedme.com/storage/categories/0z0b4dCwrLzkrfNJkA3nohm3wwfEfSHQYa97Gw1L.jpg'),
(90, 88, 'Liquid Chromatograph (HPLC)', 'chromatography-consumables-liquid-chromatograph-hplc', '2025-04-17 19:21:53', '2025-04-17 19:23:18', 'https://maxmedme.com/storage/categories/B76iIvo2Tsa74o9UJF9yR4JuGxNj8nyCntWDRBly.png'),
(91, 51, 'Point of Care Testing Platform (POCT)', 'molecular-clinical-diagnostics-point-of-care-testing-platform-poct', '2025-04-22 09:48:00', '2025-04-22 18:43:01', 'https://maxmedme.com/storage/categories/6UeXLG1rZiDrTrchKFL6CFXA8bXf9YJRj1CNbeQi.png'),
(92, NULL, 'Veterinary', 'veterinary', '2025-04-23 10:50:28', '2025-04-23 10:50:28', 'https://maxmedme.com/storage/categories/6qylJ26hjFQNdqu7iuVYEYmatfm63evG44HpZRT3.jpg'),
(93, 92, 'Veterinary Diagnostics', 'veterinary-veterinary-diagnostics', '2025-04-23 10:51:58', '2025-04-23 10:51:58', 'https://maxmedme.com/storage/categories/bpfHZB4ekVGsT2JVNCIYMueqoF2BEapKuAyQCUnL.jpg'),
(94, 57, 'Dental Consumables', 'medical-consumables-dental-consumables', '2025-04-24 04:55:21', '2025-04-24 04:55:21', 'https://maxmedme.com/storage/categories/EmPQksAFyCghECLkTKS4O77IvN5xOJBc5qeqqG10.jpg'),
(95, 71, 'Disinfection And Sterilization Equipment', 'thermal-process-equipment-disinfection-and-sterilization-equipment', '2025-04-28 18:01:46', '2025-04-29 16:37:03', 'https://maxmedme.com/storage/categories/nyw9UOCUlYu40fWhNYBcwIbsecG5TjXX7HNyLF44.jpg'),
(96, 71, 'Air Protection Equipment', 'thermal-process-equipment-air-protection-equipment', '2025-04-28 18:04:38', '2025-04-28 18:04:38', 'https://maxmedme.com/storage/categories/bZGj1XWIGDSJunPx4dB1JCaWghK7saqLMK7yvhbj.jpg'),
(97, 66, 'Pathology Equipment', 'lab-equipment-pathology-equipment', '2025-05-01 05:42:08', '2025-05-01 05:42:08', 'https://maxmedme.com/storage/categories/5yBxRl8pRWENeOYfzyjseXmkIkH9ZPChvi4esKYi.jpg'),
(98, 71, 'Cold Chain Products', 'thermal-process-equipment-cold-chain-products', '2025-05-02 16:35:14', '2025-05-02 16:35:14', 'https://maxmedme.com/storage/categories/cpOIJzcjU9awVohNAQmustP5lPLsJeDzhGFmr2cx.jpg'),
(99, 60, 'Antibodies', 'life-science-research-antibodies', '2025-06-17 10:34:44', '2025-06-17 10:41:06', 'https://maxmedme.com/storage/categories/NSwjbbmd6XMvPinsWJ2BmbNwoe7BYoLgGKaTp4s5.jpg'),
(100, 60, 'Cell Lines', 'life-science-research-cell-lines', '2025-06-17 10:38:35', '2025-06-17 10:48:39', 'https://maxmedme.com/storage/categories/KtrfAwS9qmhhYZfuz0rCmSr1qgZGrjj8f4gZsiku.jpg'),
(103, 60, 'Ligands and Inhibitors', 'life-science-research-ligands-and-inhibitors', '2025-06-17 10:44:13', '2025-06-17 10:44:13', 'https://maxmedme.com/storage/categories/YO1GSoIl2H4S9TWYRQyMhM3NEmSMXYr9NsXSYkhW.jpg'),
(104, 60, 'Immune-Check Point', 'life-science-research-immune-check-point', '2025-06-17 10:46:04', '2025-06-17 10:46:04', 'https://maxmedme.com/storage/categories/8b5Tf4uWzfHnjK7dtgVG69uCjg5ob88XNXJubPcE.jpg'),
(105, 60, 'Research Kits and Reagents', 'life-science-research-research-kits-and-reagents', '2025-06-17 10:46:54', '2025-06-17 10:46:54', 'https://maxmedme.com/storage/categories/gwburMS5NLubmt8t9zoPwM6Eo4PeOcdRBWzZ5CsK.jpg'),
(106, 60, 'sdMAB ™ (Single Domain Monoclonal Antibody)', 'life-science-research-sdmab-single-domain-monoclonal-antibody', '2025-06-17 10:47:45', '2025-06-17 10:47:45', 'https://maxmedme.com/storage/categories/qo900XGNlSHzmYl0ymQSVIiSafVLEIm0IKalG1Fo.jpg'),
(107, 80, 'Food Test Kits', 'lab-consumables-food-test-kits', '2025-06-22 12:11:07', '2025-06-22 12:11:07', 'https://maxmedme.com/storage/categories/bR4mRLXzCXcngsdKobip6Rp9NXFxBCstLmHnMlmI.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`),
  ADD KEY `categories_slug_index` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
