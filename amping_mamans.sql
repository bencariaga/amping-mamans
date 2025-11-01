-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2025 at 05:35 PM
-- Server version: 8.0.43
-- PHP Version: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amping_mamans`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` varchar(21) NOT NULL,
  `data_id` varchar(23) NOT NULL,
  `account_status` enum('Active','Deactivated') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `data_id`, `account_status`) VALUES
('ACCOUNT-2025-AUG-0001', 'DATA-2025-AUG-000000001', 'Active'),
('ACCOUNT-2025-NOV-0001', 'DATA-2025-NOV-000000001', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_partners`
--

CREATE TABLE `affiliate_partners` (
  `ap_id` varchar(10) NOT NULL,
  `tp_id` varchar(21) NOT NULL,
  `ap_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ap_type` enum('Hospital / Clinic','Pharmacy / Drugstore','Funeral Company') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `applicant_id` varchar(23) NOT NULL,
  `client_id` varchar(20) NOT NULL,
  `province` enum('South Cotabato') NOT NULL,
  `city` enum('General Santos') NOT NULL,
  `municipality` enum('N / A') DEFAULT NULL,
  `barangay` enum('Apopong','Baluan','Batomelong','Buayan','Bula','Calumpang','City Heights','Conel','Dadiangas East','Dadiangas North','Dadiangas South','Dadiangas West','Fatima','Katangawan','Labangal','Lagao','Ligaya','Mabuhay','Olympog','San Isidro','San Jose','Siguel','Sinawal','Tambler','Tinagacan','Upper Labay') NOT NULL,
  `subdivision` varchar(20) DEFAULT NULL,
  `purok` varchar(20) DEFAULT NULL,
  `sitio` varchar(20) DEFAULT NULL,
  `street` varchar(20) DEFAULT NULL,
  `phase` varchar(10) DEFAULT NULL,
  `block_number` varchar(10) DEFAULT NULL,
  `house_number` varchar(10) DEFAULT NULL,
  `job_status` enum('Permanent','Contractual','Casual','Retired') NOT NULL,
  `house_occupation_status` enum('Owner','Renter','House Sharer') NOT NULL,
  `lot_occupation_status` enum('Owner','Renter','Lot Sharer','Informal Settler') NOT NULL,
  `phic_affiliation` enum('Affiliated','Unaffiliated') NOT NULL,
  `phic_category` enum('Self-Employed','Sponsored / Indigent','Employed') DEFAULT NULL,
  `is_also_patient` enum('yes','no') NOT NULL,
  `patient_quantity` int(10) UNSIGNED ZEROFILL NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` varchar(25) NOT NULL,
  `patient_id` varchar(22) NOT NULL,
  `ap_id` varchar(10) NOT NULL,
  `exp_range_id` varchar(24) NOT NULL,
  `message_id` varchar(21) NOT NULL,
  `billed_amount` int(7) UNSIGNED ZEROFILL DEFAULT NULL,
  `assistance_amount` int(7) UNSIGNED ZEROFILL NOT NULL,
  `application_date` datetime DEFAULT NULL,
  `reapplication_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `al_id` varchar(19) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `staff_id` varchar(13) NOT NULL,
  `al_type` enum('Login','Logout','Page Access','Data Creation','Data Update','Data Deletion') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `al_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_updates`
--

CREATE TABLE `budget_updates` (
  `budget_update_id` varchar(22) NOT NULL,
  `sponsor_id` varchar(15) NOT NULL,
  `possessor` enum('AMPING','Sponsor') DEFAULT NULL,
  `amount_accum` int(16) UNSIGNED ZEROFILL DEFAULT NULL,
  `amount_spent` int(16) UNSIGNED ZEROFILL DEFAULT NULL,
  `amount_recent` int(16) UNSIGNED ZEROFILL DEFAULT NULL,
  `amount_before` int(16) UNSIGNED ZEROFILL DEFAULT NULL,
  `amount_change` int(16) UNSIGNED ZEROFILL DEFAULT NULL,
  `direction` enum('Positive','Negative') DEFAULT NULL,
  `reason` enum('Yearly Budget Provision','Supplementary Budget','GL Release','Sponsor Donation','Budget Manipulation') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` varchar(20) NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `occupation_id` varchar(21) DEFAULT NULL,
  `client_type` enum('Applicant','Household Member') NOT NULL,
  `birthdate` date DEFAULT NULL,
  `age` int(3) UNSIGNED ZEROFILL NOT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `civil_status` enum('Single','Married','Separated','Widowed') DEFAULT NULL,
  `monthly_income` int(7) UNSIGNED ZEROFILL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` varchar(21) NOT NULL,
  `client_id` varchar(20) NOT NULL,
  `contact_type` enum('Application','Emergency') NOT NULL,
  `contact_number` varchar(17) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data`
--

CREATE TABLE `data` (
  `data_id` varchar(23) NOT NULL,
  `archive_status` enum('Archived','Unarchived') NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `archived_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `data`
--

INSERT INTO `data` (`data_id`, `archive_status`, `created_at`, `updated_at`, `archived_at`) VALUES
('DATA-2025-AUG-000000001', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000003', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000004', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000005', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000006', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000007', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000008', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000009', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000010', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000011', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000012', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000013', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000014', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000015', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000016', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000017', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000018', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000019', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000020', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000021', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000022', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000023', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000024', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000025', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000033', 'Unarchived', '2025-10-09 19:59:15', '2025-11-01 22:50:51', NULL),
('DATA-2025-AUG-000000038', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000041', 'Unarchived', '2025-10-28 11:40:00', '2025-10-28 11:40:00', NULL),
('DATA-2025-NOV-000000001', 'Unarchived', '2025-11-01 07:06:20', '2025-11-01 07:06:20', NULL),
('DATA-2025-NOV-000000002', 'Archived', '2025-11-01 20:37:45', '2025-11-01 20:37:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expense_ranges`
--

CREATE TABLE `expense_ranges` (
  `exp_range_id` varchar(24) NOT NULL,
  `tariff_list_id` varchar(13) NOT NULL,
  `service_id` varchar(15) NOT NULL,
  `exp_range_min` int(7) UNSIGNED ZEROFILL NOT NULL,
  `exp_range_max` int(7) UNSIGNED ZEROFILL NOT NULL,
  `coverage_percent` int(3) UNSIGNED ZEROFILL NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expense_ranges`
--

INSERT INTO `expense_ranges` (`exp_range_id`, `tariff_list_id`, `service_id`, `exp_range_min`, `exp_range_max`, `coverage_percent`) VALUES
('EXP-RANGE-2025-AUG-00001', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0000001, 0000100, 010),
('EXP-RANGE-2025-AUG-00002', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0000101, 0000200, 010),
('EXP-RANGE-2025-AUG-00003', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0000201, 0000300, 010),
('EXP-RANGE-2025-AUG-00004', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0000301, 0000400, 020),
('EXP-RANGE-2025-AUG-00005', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0000401, 0000500, 020),
('EXP-RANGE-2025-AUG-00006', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0000501, 0000600, 020),
('EXP-RANGE-2025-AUG-00007', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0000601, 0000700, 030),
('EXP-RANGE-2025-AUG-00008', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0000701, 0000800, 030),
('EXP-RANGE-2025-AUG-00009', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0000801, 0000900, 030),
('EXP-RANGE-2025-AUG-00010', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0000901, 0001000, 040),
('EXP-RANGE-2025-AUG-00011', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0001001, 0002000, 040),
('EXP-RANGE-2025-AUG-00012', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0002001, 0003000, 040),
('EXP-RANGE-2025-AUG-00013', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0003001, 0004000, 040),
('EXP-RANGE-2025-AUG-00014', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0004001, 0005000, 050),
('EXP-RANGE-2025-AUG-00015', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0005001, 0006000, 050),
('EXP-RANGE-2025-AUG-00016', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0006001, 0007000, 050),
('EXP-RANGE-2025-AUG-00017', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0007001, 0008000, 050),
('EXP-RANGE-2025-AUG-00018', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0008001, 0009000, 060),
('EXP-RANGE-2025-AUG-00019', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0009001, 0010000, 060),
('EXP-RANGE-2025-AUG-00020', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0010001, 0020000, 060),
('EXP-RANGE-2025-AUG-00021', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0020001, 0030000, 060),
('EXP-RANGE-2025-AUG-00022', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0030001, 0040000, 070),
('EXP-RANGE-2025-AUG-00023', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0040001, 0050000, 070),
('EXP-RANGE-2025-AUG-00024', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0050001, 0060000, 070),
('EXP-RANGE-2025-AUG-00025', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0060001, 0070000, 070),
('EXP-RANGE-2025-AUG-00026', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0070001, 0080000, 080),
('EXP-RANGE-2025-AUG-00027', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0080001, 0090000, 080),
('EXP-RANGE-2025-AUG-00028', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0090001, 0100000, 080),
('EXP-RANGE-2025-AUG-00029', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0100001, 0200000, 080),
('EXP-RANGE-2025-AUG-00030', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0200001, 0300000, 090),
('EXP-RANGE-2025-AUG-00031', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0300001, 0400000, 090),
('EXP-RANGE-2025-AUG-00032', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0400001, 0500000, 090),
('EXP-RANGE-2025-AUG-00033', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0500001, 0600000, 090),
('EXP-RANGE-2025-AUG-00034', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0600001, 0700000, 100),
('EXP-RANGE-2025-AUG-00035', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0700001, 0800000, 100),
('EXP-RANGE-2025-AUG-00036', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0800001, 0900000, 100),
('EXP-RANGE-2025-AUG-00037', 'TL-2025-AUG-1', 'SERVICE-2025-01', 0900001, 1000000, 100),
('EXP-RANGE-2025-AUG-00038', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0000001, 0000100, 010),
('EXP-RANGE-2025-AUG-00039', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0000101, 0000200, 010),
('EXP-RANGE-2025-AUG-00040', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0000201, 0000300, 010),
('EXP-RANGE-2025-AUG-00041', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0000301, 0000400, 020),
('EXP-RANGE-2025-AUG-00042', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0000401, 0000500, 020),
('EXP-RANGE-2025-AUG-00043', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0000501, 0000600, 020),
('EXP-RANGE-2025-AUG-00044', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0000601, 0000700, 030),
('EXP-RANGE-2025-AUG-00045', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0000701, 0000800, 030),
('EXP-RANGE-2025-AUG-00046', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0000801, 0000900, 030),
('EXP-RANGE-2025-AUG-00047', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0000901, 0001000, 040),
('EXP-RANGE-2025-AUG-00048', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0001001, 0002000, 040),
('EXP-RANGE-2025-AUG-00049', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0002001, 0003000, 040),
('EXP-RANGE-2025-AUG-00050', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0003001, 0004000, 040),
('EXP-RANGE-2025-AUG-00051', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0004001, 0005000, 050),
('EXP-RANGE-2025-AUG-00052', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0005001, 0006000, 050),
('EXP-RANGE-2025-AUG-00053', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0006001, 0007000, 050),
('EXP-RANGE-2025-AUG-00054', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0007001, 0008000, 050),
('EXP-RANGE-2025-AUG-00055', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0008001, 0009000, 060),
('EXP-RANGE-2025-AUG-00056', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0009001, 0010000, 060),
('EXP-RANGE-2025-AUG-00057', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0010001, 0020000, 060),
('EXP-RANGE-2025-AUG-00058', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0020001, 0030000, 060),
('EXP-RANGE-2025-AUG-00059', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0030001, 0040000, 070),
('EXP-RANGE-2025-AUG-00060', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0040001, 0050000, 070),
('EXP-RANGE-2025-AUG-00061', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0050001, 0060000, 070),
('EXP-RANGE-2025-AUG-00062', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0060001, 0070000, 070),
('EXP-RANGE-2025-AUG-00063', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0070001, 0080000, 080),
('EXP-RANGE-2025-AUG-00064', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0080001, 0090000, 080),
('EXP-RANGE-2025-AUG-00065', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0090001, 0100000, 080),
('EXP-RANGE-2025-AUG-00066', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0100001, 0200000, 080),
('EXP-RANGE-2025-AUG-00067', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0200001, 0300000, 090),
('EXP-RANGE-2025-AUG-00068', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0300001, 0400000, 090),
('EXP-RANGE-2025-AUG-00069', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0400001, 0500000, 090),
('EXP-RANGE-2025-AUG-00070', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0500001, 0600000, 090),
('EXP-RANGE-2025-AUG-00071', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0600001, 0700000, 100),
('EXP-RANGE-2025-AUG-00072', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0700001, 0800000, 100),
('EXP-RANGE-2025-AUG-00073', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0800001, 0900000, 100),
('EXP-RANGE-2025-AUG-00074', 'TL-2025-AUG-1', 'SERVICE-2025-02', 0900001, 1000000, 100),
('EXP-RANGE-2025-AUG-00075', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0000001, 0000100, 010),
('EXP-RANGE-2025-AUG-00076', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0000101, 0000200, 010),
('EXP-RANGE-2025-AUG-00077', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0000201, 0000300, 010),
('EXP-RANGE-2025-AUG-00078', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0000301, 0000400, 020),
('EXP-RANGE-2025-AUG-00079', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0000401, 0000500, 020),
('EXP-RANGE-2025-AUG-00080', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0000501, 0000600, 020),
('EXP-RANGE-2025-AUG-00081', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0000601, 0000700, 030),
('EXP-RANGE-2025-AUG-00082', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0000701, 0000800, 030),
('EXP-RANGE-2025-AUG-00083', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0000801, 0000900, 030),
('EXP-RANGE-2025-AUG-00084', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0000901, 0001000, 040),
('EXP-RANGE-2025-AUG-00085', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0001001, 0002000, 040),
('EXP-RANGE-2025-AUG-00086', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0002001, 0003000, 040),
('EXP-RANGE-2025-AUG-00087', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0003001, 0004000, 040),
('EXP-RANGE-2025-AUG-00088', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0004001, 0005000, 050),
('EXP-RANGE-2025-AUG-00089', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0005001, 0006000, 050),
('EXP-RANGE-2025-AUG-00090', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0006001, 0007000, 050),
('EXP-RANGE-2025-AUG-00091', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0007001, 0008000, 050),
('EXP-RANGE-2025-AUG-00092', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0008001, 0009000, 060),
('EXP-RANGE-2025-AUG-00093', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0009001, 0010000, 060),
('EXP-RANGE-2025-AUG-00094', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0010001, 0020000, 060),
('EXP-RANGE-2025-AUG-00095', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0020001, 0030000, 060),
('EXP-RANGE-2025-AUG-00096', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0030001, 0040000, 070),
('EXP-RANGE-2025-AUG-00097', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0040001, 0050000, 070),
('EXP-RANGE-2025-AUG-00098', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0050001, 0060000, 070),
('EXP-RANGE-2025-AUG-00099', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0060001, 0070000, 070),
('EXP-RANGE-2025-AUG-00100', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0070001, 0080000, 080),
('EXP-RANGE-2025-AUG-00101', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0080001, 0090000, 080),
('EXP-RANGE-2025-AUG-00102', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0090001, 0100000, 080),
('EXP-RANGE-2025-AUG-00103', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0100001, 0200000, 080),
('EXP-RANGE-2025-AUG-00104', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0200001, 0300000, 090),
('EXP-RANGE-2025-AUG-00105', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0300001, 0400000, 090),
('EXP-RANGE-2025-AUG-00106', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0400001, 0500000, 090),
('EXP-RANGE-2025-AUG-00107', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0500001, 0600000, 090),
('EXP-RANGE-2025-AUG-00108', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0600001, 0700000, 100),
('EXP-RANGE-2025-AUG-00109', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0700001, 0800000, 100),
('EXP-RANGE-2025-AUG-00110', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0800001, 0900000, 100),
('EXP-RANGE-2025-AUG-00111', 'TL-2025-AUG-1', 'SERVICE-2025-03', 0900001, 1000000, 100),
('EXP-RANGE-2025-AUG-00112', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0000001, 0000100, 010),
('EXP-RANGE-2025-AUG-00113', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0000101, 0000200, 010),
('EXP-RANGE-2025-AUG-00114', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0000201, 0000300, 010),
('EXP-RANGE-2025-AUG-00115', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0000301, 0000400, 020),
('EXP-RANGE-2025-AUG-00116', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0000401, 0000500, 020),
('EXP-RANGE-2025-AUG-00117', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0000501, 0000600, 020),
('EXP-RANGE-2025-AUG-00118', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0000601, 0000700, 030),
('EXP-RANGE-2025-AUG-00119', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0000701, 0000800, 030),
('EXP-RANGE-2025-AUG-00120', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0000801, 0000900, 030),
('EXP-RANGE-2025-AUG-00121', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0000901, 0001000, 040),
('EXP-RANGE-2025-AUG-00122', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0001001, 0002000, 040),
('EXP-RANGE-2025-AUG-00123', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0002001, 0003000, 040),
('EXP-RANGE-2025-AUG-00124', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0003001, 0004000, 040),
('EXP-RANGE-2025-AUG-00125', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0004001, 0005000, 050),
('EXP-RANGE-2025-AUG-00126', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0005001, 0006000, 050),
('EXP-RANGE-2025-AUG-00127', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0006001, 0007000, 050),
('EXP-RANGE-2025-AUG-00128', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0007001, 0008000, 050),
('EXP-RANGE-2025-AUG-00129', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0008001, 0009000, 060),
('EXP-RANGE-2025-AUG-00130', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0009001, 0010000, 060),
('EXP-RANGE-2025-AUG-00131', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0010001, 0020000, 060),
('EXP-RANGE-2025-AUG-00132', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0020001, 0030000, 060),
('EXP-RANGE-2025-AUG-00133', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0030001, 0040000, 070),
('EXP-RANGE-2025-AUG-00134', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0040001, 0050000, 070),
('EXP-RANGE-2025-AUG-00135', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0050001, 0060000, 070),
('EXP-RANGE-2025-AUG-00136', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0060001, 0070000, 070),
('EXP-RANGE-2025-AUG-00137', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0070001, 0080000, 080),
('EXP-RANGE-2025-AUG-00138', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0080001, 0090000, 080),
('EXP-RANGE-2025-AUG-00139', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0090001, 0100000, 080),
('EXP-RANGE-2025-AUG-00140', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0100001, 0200000, 080),
('EXP-RANGE-2025-AUG-00141', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0200001, 0300000, 090),
('EXP-RANGE-2025-AUG-00142', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0300001, 0400000, 090),
('EXP-RANGE-2025-AUG-00143', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0400001, 0500000, 090),
('EXP-RANGE-2025-AUG-00144', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0500001, 0600000, 090),
('EXP-RANGE-2025-AUG-00145', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0600001, 0700000, 100),
('EXP-RANGE-2025-AUG-00146', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0700001, 0800000, 100),
('EXP-RANGE-2025-AUG-00147', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0800001, 0900000, 100),
('EXP-RANGE-2025-AUG-00148', 'TL-2025-AUG-1', 'SERVICE-2025-04', 0900001, 1000000, 100),
('EXP-RANGE-2025-AUG-00149', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0000001, 0000100, 010),
('EXP-RANGE-2025-AUG-00150', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0000101, 0000200, 010),
('EXP-RANGE-2025-AUG-00151', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0000201, 0000300, 010),
('EXP-RANGE-2025-AUG-00152', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0000301, 0000400, 020),
('EXP-RANGE-2025-AUG-00153', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0000401, 0000500, 020),
('EXP-RANGE-2025-AUG-00154', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0000501, 0000600, 020),
('EXP-RANGE-2025-AUG-00155', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0000601, 0000700, 030),
('EXP-RANGE-2025-AUG-00156', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0000701, 0000800, 030),
('EXP-RANGE-2025-AUG-00157', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0000801, 0000900, 030),
('EXP-RANGE-2025-AUG-00158', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0000901, 0001000, 040),
('EXP-RANGE-2025-AUG-00159', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0001001, 0002000, 040),
('EXP-RANGE-2025-AUG-00160', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0002001, 0003000, 040),
('EXP-RANGE-2025-AUG-00161', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0003001, 0004000, 040),
('EXP-RANGE-2025-AUG-00162', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0004001, 0005000, 050),
('EXP-RANGE-2025-AUG-00163', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0005001, 0006000, 050),
('EXP-RANGE-2025-AUG-00164', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0006001, 0007000, 050),
('EXP-RANGE-2025-AUG-00165', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0007001, 0008000, 050),
('EXP-RANGE-2025-AUG-00166', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0008001, 0009000, 060),
('EXP-RANGE-2025-AUG-00167', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0009001, 0010000, 060),
('EXP-RANGE-2025-AUG-00168', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0010001, 0020000, 060),
('EXP-RANGE-2025-AUG-00169', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0020001, 0030000, 060),
('EXP-RANGE-2025-AUG-00170', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0030001, 0040000, 070),
('EXP-RANGE-2025-AUG-00171', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0040001, 0050000, 070),
('EXP-RANGE-2025-AUG-00172', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0050001, 0060000, 070),
('EXP-RANGE-2025-AUG-00173', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0060001, 0070000, 070),
('EXP-RANGE-2025-AUG-00174', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0070001, 0080000, 080),
('EXP-RANGE-2025-AUG-00175', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0080001, 0090000, 080),
('EXP-RANGE-2025-AUG-00176', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0090001, 0100000, 080),
('EXP-RANGE-2025-AUG-00177', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0100001, 0200000, 080),
('EXP-RANGE-2025-AUG-00178', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0200001, 0300000, 090),
('EXP-RANGE-2025-AUG-00179', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0300001, 0400000, 090),
('EXP-RANGE-2025-AUG-00180', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0400001, 0500000, 090),
('EXP-RANGE-2025-AUG-00181', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0500001, 0600000, 090),
('EXP-RANGE-2025-AUG-00182', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0600001, 0700000, 100),
('EXP-RANGE-2025-AUG-00183', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0700001, 0800000, 100),
('EXP-RANGE-2025-AUG-00184', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0800001, 0900000, 100),
('EXP-RANGE-2025-AUG-00185', 'TL-2025-AUG-1', 'SERVICE-2025-05', 0900001, 1000000, 100),
('EXP-RANGE-2025-AUG-00186', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0000001, 0000100, 010),
('EXP-RANGE-2025-AUG-00187', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0000101, 0000200, 010),
('EXP-RANGE-2025-AUG-00188', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0000201, 0000300, 010),
('EXP-RANGE-2025-AUG-00189', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0000301, 0000400, 020),
('EXP-RANGE-2025-AUG-00190', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0000401, 0000500, 020),
('EXP-RANGE-2025-AUG-00191', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0000501, 0000600, 020),
('EXP-RANGE-2025-AUG-00192', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0000601, 0000700, 030),
('EXP-RANGE-2025-AUG-00193', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0000701, 0000800, 030),
('EXP-RANGE-2025-AUG-00194', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0000801, 0000900, 030),
('EXP-RANGE-2025-AUG-00195', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0000901, 0001000, 040),
('EXP-RANGE-2025-AUG-00196', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0001001, 0002000, 040),
('EXP-RANGE-2025-AUG-00197', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0002001, 0003000, 040),
('EXP-RANGE-2025-AUG-00198', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0003001, 0004000, 040),
('EXP-RANGE-2025-AUG-00199', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0004001, 0005000, 050),
('EXP-RANGE-2025-AUG-00200', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0005001, 0006000, 050),
('EXP-RANGE-2025-AUG-00201', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0006001, 0007000, 050),
('EXP-RANGE-2025-AUG-00202', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0007001, 0008000, 050),
('EXP-RANGE-2025-AUG-00203', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0008001, 0009000, 060),
('EXP-RANGE-2025-AUG-00204', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0009001, 0010000, 060),
('EXP-RANGE-2025-AUG-00205', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0010001, 0020000, 060),
('EXP-RANGE-2025-AUG-00206', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0020001, 0030000, 060),
('EXP-RANGE-2025-AUG-00207', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0030001, 0040000, 070),
('EXP-RANGE-2025-AUG-00208', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0040001, 0050000, 070),
('EXP-RANGE-2025-AUG-00209', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0050001, 0060000, 070),
('EXP-RANGE-2025-AUG-00210', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0060001, 0070000, 070),
('EXP-RANGE-2025-AUG-00211', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0070001, 0080000, 080),
('EXP-RANGE-2025-AUG-00212', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0080001, 0090000, 080),
('EXP-RANGE-2025-AUG-00213', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0090001, 0100000, 080),
('EXP-RANGE-2025-AUG-00214', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0100001, 0200000, 080),
('EXP-RANGE-2025-AUG-00215', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0200001, 0300000, 090),
('EXP-RANGE-2025-AUG-00216', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0300001, 0400000, 090),
('EXP-RANGE-2025-AUG-00217', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0400001, 0500000, 090),
('EXP-RANGE-2025-AUG-00218', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0500001, 0600000, 090),
('EXP-RANGE-2025-AUG-00219', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0600001, 0700000, 100),
('EXP-RANGE-2025-AUG-00220', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0700001, 0800000, 100),
('EXP-RANGE-2025-AUG-00221', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0800001, 0900000, 100),
('EXP-RANGE-2025-AUG-00222', 'TL-2025-AUG-1', 'SERVICE-2025-06', 0900001, 1000000, 100);

-- --------------------------------------------------------

--
-- Table structure for table `guarantee_letters`
--

CREATE TABLE `guarantee_letters` (
  `gl_id` varchar(16) NOT NULL,
  `data_id` varchar(23) NOT NULL,
  `application_id` varchar(25) NOT NULL,
  `sponsor_id` varchar(15) NOT NULL,
  `is_cancelled` enum('Yes','No') NOT NULL,
  `is_sponsored` enum('Yes','No') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `households`
--

CREATE TABLE `households` (
  `household_id` varchar(22) NOT NULL,
  `household_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `households`
--

INSERT INTO `households` (`household_id`, `household_name`) VALUES
('HOUSEHOLD-2025-NOV-001', 'Cariaga');

-- --------------------------------------------------------

--
-- Table structure for table `household_members`
--

CREATE TABLE `household_members` (
  `household_member_id` varchar(17) NOT NULL,
  `household_id` varchar(22) NOT NULL,
  `client_id` varchar(20) NOT NULL,
  `educational_attainment` enum('Elementary','High School','College') DEFAULT NULL,
  `relationship_to_applicant` enum('Self','Friend','Wife','Husband','Daughter','Son','Sister','Brother','Mother','Father','Granddaughter','Grandson','Grandmother','Grandfather','Aunt','Uncle','Nephew','Niece','Cousin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` varchar(20) NOT NULL,
  `account_id` varchar(21) NOT NULL,
  `member_type` enum('Client','Staff','Signer','Third-Party') NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `middle_name` varchar(20) DEFAULT NULL,
  `first_name` varchar(20) NOT NULL,
  `suffix` enum('Jr.','Sr.','II','III','IV','V') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `account_id`, `member_type`, `last_name`, `middle_name`, `first_name`, `suffix`) VALUES
('MEMBER-2025-AUG-0001', 'ACCOUNT-2025-AUG-0001', 'Staff', 'Cariaga', 'Leproso', 'Benhur', NULL),
('MEMBER-2025-NOV-0001', 'ACCOUNT-2025-NOV-0001', 'Staff', 'Dela Cruz', 'Santos', 'Maria', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` varchar(21) NOT NULL,
  `staff_id` varchar(13) NOT NULL,
  `contact_id` varchar(21) NOT NULL,
  `message_text` text NOT NULL,
  `sent_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_templates`
--

CREATE TABLE `message_templates` (
  `msg_tmp_id` varchar(18) NOT NULL,
  `data_id` varchar(23) NOT NULL,
  `msg_tmp_title` varchar(30) NOT NULL,
  `msg_tmp_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `message_templates`
--

INSERT INTO `message_templates` (`msg_tmp_id`, `data_id`, `msg_tmp_title`, `msg_tmp_text`) VALUES
('MSG-TMP-2025-AUG-1', 'DATA-2025-AUG-000000033', 'Text Message Template 1', 'Greetings, [$application->applicant->client->member->first_name] [$application->applicant->client->member->middle_name] [$application->applicant->client->member->last_name] [$application->applicant->client->member->suffix]! Here are the important details for your AMPING application today.;;For Patient: [$application->patient->client->member->first_name] [$application->patient->client->member->middle_name] [$application->patient->client->member->last_name] [$application->patient->client->member->suffix];Service Type: [$application->service_type];Billed Amount: ₱ [$application->billed_amount];Assistance Amount: ₱ [$application->assistance_amount];Affiliate Partner: [$application->affiliate_partner->affiliate_partner_name];Applied At: [$application->applied_at];Reapply At: [$application->reapply_at];;Thank you for your visit with us! Come again!');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_07_20_000000_create_cache_table', 1),
(2, '2025_10_31_000000_remove_foreign_key_constraints', 2),
(3, '2025_10_31_000000_remove_all_accounts_except_program_head', 3),
(4, '2025_10_31_000000_delete_all_accounts_except_program_head', 4),
(5, '2025_10_31_000000_enable_foreign_key_constraints', 5);

-- --------------------------------------------------------

--
-- Table structure for table `occupations`
--

CREATE TABLE `occupations` (
  `occupation_id` varchar(18) NOT NULL,
  `data_id` varchar(23) NOT NULL,
  `occupation` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `occupations`
--

INSERT INTO `occupations` (`occupation_id`, `data_id`, `occupation`) VALUES
('OCCUP-2025-01', 'DATA-2025-AUG-000000013', 'Jeepney Driver'),
('OCCUP-2025-02', 'DATA-2025-AUG-000000014', 'Tricycle Driver'),
('OCCUP-2025-03', 'DATA-2025-AUG-000000015', 'Pedicab Driver'),
('OCCUP-2025-04', 'DATA-2025-AUG-000000016', 'Construction Worker'),
('OCCUP-2025-05', 'DATA-2025-AUG-000000017', 'Factory Worker'),
('OCCUP-2025-06', 'DATA-2025-AUG-000000018', 'Warehouse Worker'),
('OCCUP-2025-07', 'DATA-2025-AUG-000000019', 'Farmer'),
('OCCUP-2025-08', 'DATA-2025-AUG-000000020', 'Fisherperson'),
('OCCUP-2025-09', 'DATA-2025-AUG-000000021', 'Janitor'),
('OCCUP-2025-10', 'DATA-2025-AUG-000000022', 'Teacher'),
('OCCUP-2025-11', 'DATA-2025-AUG-000000023', 'Student'),
('OCCUP-2025-12', 'DATA-2025-AUG-000000024', 'Security Guard'),
('OCCUP-2025-13', 'DATA-2025-AUG-000000038', 'Waiter'),
('OCCUP-2025-14', 'DATA-2025-AUG-000000041', 'Artist');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` varchar(22) NOT NULL,
  `client_id` varchar(20) NOT NULL,
  `applicant_id` varchar(23) NOT NULL,
  `patient_category` enum('PWD','Senior') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` varchar(15) NOT NULL,
  `staff_id` varchar(13) NOT NULL,
  `report_type` enum('Daily','Weekly','Monthly','Ad Hoc') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` varchar(11) NOT NULL,
  `data_id` varchar(23) NOT NULL,
  `role` varchar(20) NOT NULL,
  `allowed_actions` text,
  `access_scope` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `data_id`, `role`, `allowed_actions`, `access_scope`) VALUES
('ROLE-2025-1', 'DATA-2025-AUG-000000010', 'Program Head', 'Create, view, edit, and deactivate accounts of staff members, applicants, sponsors, affiliate partners, and services. Create, view, edit, archive, download, and print reports, including the AMPING\'s financial status and user activity data. Create, view, edit, archive, download, and print templates for assistance request forms, guarantee letters, and text messages. Create, view, edit, and delete tariff lists and change the version of tariff lists to use for assistance amount calculation. Create, view, edit, and delete staff role names and client occupation names. Assign and reassigned roles to staff members. Approve or reject assistance requests and authorize guarantee letters. Send text messages to applicants with approved guarantee letters. Update, add to, and monitor the program budget from government funds, sponsors, and other sources. Delete system cache and log data when necessary', 'Full access to every web page, every feature, and every module, without restrictions. Full access to profiles and system activities of staff members, applicants, patients, sponsors, and affiliate partners. Full access to templates for assistance request forms, guarantee letters, and text messages. Full access to financial records, such as budgets, expenses, and funding sources. Full access to staff role and client occupation names, and tariff lists. Full access to staff role and tariff list adjustments. Full access to data and account archiving, deletion, and deactivation. Full access to logs and reports'),
('ROLE-2025-2', 'DATA-2025-AUG-000000011', 'Encoder', 'Create, view, edit, and deactivate accounts of staff members, applicants, sponsors, affiliate partners, and services. View and use assistance request templates to create assistance request forms. View the AMPING\'s financial status, including the program budget sources from government funds, sponsors, and other sources. View the staff role names and client occupation names. View the roles of staff members. View accounts of staff members, applicants, sponsors, affiliate partners, and services', 'Access limited to viewing and editing account profiles. Access limited to viewing templates for assistance request forms. Access limited to viewing financial records, such as budgets, expenses, and funding sources. Access limited to viewing staff roles, client occupations, and tariff list versions'),
('ROLE-2025-3', 'DATA-2025-AUG-000000012', 'GL Operator', 'Approve or reject assistance requests and authorize guarantee letters. View the AMPING\'s financial status, including the program budget sources from government funds, sponsors, and other sources. View the roles of staff members. View the version of tariff lists to use for assistance amount calculation. View and use guarantee letter templates to create guarantee letters. View accounts of staff members, applicants, sponsors, affiliate partners, and services', 'Access limited to viewing account profiles. Access limited to viewing templates for guarantee letters. Access limited to approving and rejecting assistance requests and authorizing guarantee letters'),
('ROLE-2025-4', 'DATA-2025-AUG-000000003', 'SMS Operator', 'Send text messages to applicants with approved guarantee letters. View and use assistance request templates to create assistance request forms. View the AMPING\'s financial status, including the program budget sources from government funds, sponsors, and other sources. View the roles of staff members. View the version of tariff lists to use for assistance amount calculation. View accounts of staff members, applicants, sponsors, affiliate partners, and services', 'Access limited to viewing account profiles. Access limited to viewing templates for text messages. Access limited to sending text messages to applicants with approved guarantee letters');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` varchar(15) NOT NULL,
  `data_id` varchar(23) NOT NULL,
  `service` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `data_id`, `service`) VALUES
('SERVICE-2025-01', 'DATA-2025-AUG-000000004', 'Hospital Bill'),
('SERVICE-2025-02', 'DATA-2025-AUG-000000005', 'Medical Prescription'),
('SERVICE-2025-03', 'DATA-2025-AUG-000000006', 'Laboratory Test'),
('SERVICE-2025-04', 'DATA-2025-AUG-000000007', 'Diagnostic Test'),
('SERVICE-2025-05', 'DATA-2025-AUG-000000008', 'Hemodialysis'),
('SERVICE-2025-06', 'DATA-2025-AUG-000000009', 'Blood Request');

-- --------------------------------------------------------

--
-- Table structure for table `signers`
--

CREATE TABLE `signers` (
  `signer_id` varchar(14) NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `post_nominal_letters` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

CREATE TABLE `sponsors` (
  `sponsor_id` varchar(15) NOT NULL,
  `tp_id` varchar(11) NOT NULL,
  `sponsor_type` enum('Politician','Business Owner','Other') DEFAULT NULL,
  `designation` varchar(30) DEFAULT NULL,
  `organization_name` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` varchar(13) NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `role_id` varchar(11) NOT NULL,
  `file_name` text,
  `file_extension` enum('.jpg','.jpeg','jfif','.png') DEFAULT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `member_id`, `role_id`, `file_name`, `file_extension`, `password`) VALUES
('STAFF-2025-01', 'MEMBER-2025-AUG-0001', 'ROLE-2025-1', 'profile_pictures/dRGNkHAAEqiMTUWPdeQlzbgWagXuFnaoQc4zjzqL.png', '.png', '$2y$10$LfnfOs8unDeUIS5BGIxQ9uJWb/O2XW3Hy/5iqBytMyCI6A0qkL12y'),
('STAFF-2025-02', 'MEMBER-2025-NOV-0001', 'ROLE-2025-2', 'profile_pictures/EUsGF1xWUh6ZiKgYVtpE8N5z8QErtRqxHaTB1uvB.png', '.png', '$2y$10$11k3067e1gdlosL5oEk0gesttEhy5.MechWXX6mOxWWH8SVAWulku');

-- --------------------------------------------------------

--
-- Table structure for table `tariff_lists`
--

CREATE TABLE `tariff_lists` (
  `tariff_list_id` varchar(13) NOT NULL,
  `data_id` varchar(23) NOT NULL,
  `tl_status` enum('Draft','Scheduled','Active','Inactive') NOT NULL,
  `effectivity_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tariff_lists`
--

INSERT INTO `tariff_lists` (`tariff_list_id`, `data_id`, `tl_status`, `effectivity_date`) VALUES
('TL-2025-AUG-1', 'DATA-2025-AUG-000000025', 'Active', '2025-08-02');

-- --------------------------------------------------------

--
-- Table structure for table `third_parties`
--

CREATE TABLE `third_parties` (
  `tp_id` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `account_id` varchar(21) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tp_type` enum('Affiliate Partner','Sponsor') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `data_id` (`data_id`);

--
-- Indexes for table `affiliate_partners`
--
ALTER TABLE `affiliate_partners`
  ADD PRIMARY KEY (`ap_id`),
  ADD KEY `tp_id` (`tp_id`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`applicant_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `ap_id` (`ap_id`),
  ADD KEY `exp_range_id` (`exp_range_id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`al_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `budget_updates`
--
ALTER TABLE `budget_updates`
  ADD PRIMARY KEY (`budget_update_id`),
  ADD KEY `sponsor_id` (`sponsor_id`);

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
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `occupation_id` (`occupation_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`data_id`);

--
-- Indexes for table `expense_ranges`
--
ALTER TABLE `expense_ranges`
  ADD PRIMARY KEY (`exp_range_id`),
  ADD KEY `tariff_list_id` (`tariff_list_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `guarantee_letters`
--
ALTER TABLE `guarantee_letters`
  ADD PRIMARY KEY (`gl_id`),
  ADD KEY `data_id` (`data_id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `sponsor_id` (`sponsor_id`);

--
-- Indexes for table `households`
--
ALTER TABLE `households`
  ADD PRIMARY KEY (`household_id`);

--
-- Indexes for table `household_members`
--
ALTER TABLE `household_members`
  ADD PRIMARY KEY (`household_member_id`),
  ADD KEY `household_id` (`household_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `contact_id` (`contact_id`);

--
-- Indexes for table `message_templates`
--
ALTER TABLE `message_templates`
  ADD PRIMARY KEY (`msg_tmp_id`),
  ADD KEY `data_id` (`data_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `occupations`
--
ALTER TABLE `occupations`
  ADD PRIMARY KEY (`occupation_id`),
  ADD KEY `data_id` (`data_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD KEY `data_id` (`data_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `data_id` (`data_id`);

--
-- Indexes for table `signers`
--
ALTER TABLE `signers`
  ADD PRIMARY KEY (`signer_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD PRIMARY KEY (`sponsor_id`),
  ADD KEY `tp_id` (`tp_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `tariff_lists`
--
ALTER TABLE `tariff_lists`
  ADD PRIMARY KEY (`tariff_list_id`),
  ADD KEY `data_id` (`data_id`);

--
-- Indexes for table `third_parties`
--
ALTER TABLE `third_parties`
  ADD PRIMARY KEY (`tp_id`),
  ADD KEY `account_id` (`account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
