-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2025 at 06:28 PM
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
('ACCOUNT-2025-AUG-0006', 'DATA-2025-AUG-000000006', 'Active'),
('ACCOUNT-2025-AUG-0007', 'DATA-2025-AUG-000000007', 'Active'),
('ACCOUNT-2025-AUG-0008', 'DATA-2025-AUG-000000008', 'Active'),
('ACCOUNT-2025-AUG-0009', 'DATA-2025-AUG-000000009', 'Active'),
('ACCOUNT-2025-AUG-0010', 'DATA-2025-AUG-000000010', 'Active'),
('ACCOUNT-2025-AUG-0011', 'DATA-2025-AUG-000000011', 'Active'),
('ACCOUNT-2025-AUG-0012', 'DATA-2025-AUG-000000012', 'Active'),
('ACCOUNT-2025-AUG-0013', 'DATA-2025-AUG-000000013', 'Active'),
('ACCOUNT-2025-AUG-0014', 'DATA-2025-AUG-000000014', 'Active'),
('ACCOUNT-2025-AUG-0015', 'DATA-2025-AUG-000000015', 'Active'),
('ACCOUNT-2025-AUG-0016', 'DATA-2025-AUG-000000016', 'Active'),
('ACCOUNT-2025-AUG-0017', 'DATA-2025-AUG-000000017', 'Active'),
('ACCOUNT-2025-AUG-0018', 'DATA-2025-AUG-000000046', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_partners`
--

CREATE TABLE `affiliate_partners` (
  `ap_id` varchar(10) NOT NULL,
  `account_id` varchar(21) NOT NULL,
  `ap_name` varchar(255) NOT NULL,
  `ap_type` enum('Hospital / Clinic','Pharmacy / Drugstore','Funeral Company') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `affiliate_partners`
--

INSERT INTO `affiliate_partners` (`ap_id`, `account_id`, `ap_name`, `ap_type`) VALUES
('AP-2025-01', 'ACCOUNT-2025-AUG-0006', 'St. Elizabeth Hospital, Inc.', 'Hospital / Clinic'),
('AP-2025-02', 'ACCOUNT-2025-AUG-0007', 'Rojon Pharmacy', 'Pharmacy / Drugstore'),
('AP-2025-03', 'ACCOUNT-2025-AUG-0008', 'Auguis Clinic and Hospital', 'Hospital / Clinic'),
('AP-2025-04', 'ACCOUNT-2025-AUG-0009', 'Mercury Drug Corporation', 'Pharmacy / Drugstore');

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
  `patient_quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`applicant_id`, `client_id`, `province`, `city`, `municipality`, `barangay`, `subdivision`, `purok`, `sitio`, `street`, `phase`, `block_number`, `house_number`, `job_status`, `house_occupation_status`, `lot_occupation_status`, `phic_affiliation`, `phic_category`, `is_also_patient`, `patient_quantity`) VALUES
('APPLICANT-2025-AUG-0001', 'CLIENT-2025-AUG-0001', 'South Cotabato', 'General Santos', 'N / A', 'Labangal', 'Doña Soledad', NULL, NULL, NULL, NULL, NULL, NULL, 'Casual', 'Renter', 'Renter', 'Affiliated', 'Employed', 'yes', 2),
('APPLICANT-2025-AUG-0002', 'CLIENT-2025-AUG-0003', 'South Cotabato', 'General Santos', 'N / A', 'Labangal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Casual', 'Owner', 'Owner', 'Affiliated', 'Sponsored / Indigent', 'yes', 3),
('APPLICANT-2025-AUG-0003', 'CLIENT-2025-AUG-0007', 'South Cotabato', 'General Santos', 'N / A', 'City Heights', NULL, NULL, NULL, 'Daproza', NULL, NULL, NULL, 'Casual', 'Owner', 'Owner', 'Affiliated', 'Employed', 'yes', 2);

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
  `billed_amount` int DEFAULT NULL,
  `apply_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `patient_id`, `ap_id`, `exp_range_id`, `message_id`, `billed_amount`, `apply_at`) VALUES
('APPLICATION-2025-AUG-0001', 'PATIENT-2025-AUG-00008', 'AP-2025-01', 'EXP-RANGE-2025-AUG-00002', 'MESSAGE-2025-AUG-0003', 150, '2025-10-28 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_log_id` varchar(19) NOT NULL,
  `staff_id` varchar(13) NOT NULL,
  `audit_log_type` enum('Login','Logout','Page Access','Data Creation','Data Update','Data Deletion') DEFAULT NULL,
  `audit_log_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_updates`
--

CREATE TABLE `budget_updates` (
  `budget_update_id` varchar(22) NOT NULL,
  `sponsor_id` varchar(15) NOT NULL,
  `possessor` enum('AMPING','Sponsor') DEFAULT NULL,
  `amount_accum` int DEFAULT NULL,
  `amount_spent` int DEFAULT NULL,
  `amount_recent` int DEFAULT NULL,
  `amount_before` int DEFAULT NULL,
  `amount_change` int DEFAULT NULL,
  `direction` enum('Positive','Negative') DEFAULT NULL,
  `reason` enum('Yearly Budget Provision','Supplementary Budget','GL Release','Sponsor Donation','Budget Manipulation') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `budget_updates`
--

INSERT INTO `budget_updates` (`budget_update_id`, `sponsor_id`, `possessor`, `amount_accum`, `amount_spent`, `amount_recent`, `amount_before`, `amount_change`, `direction`, `reason`) VALUES
('BDG-UPD-2025-AUG-00001', 'SPONSOR-2025-01', 'Sponsor', 600000000, 0, 600000000, 500000000, 100000000, 'Positive', 'Sponsor Donation'),
('BDG-UPD-2025-AUG-00002', 'SPONSOR-2025-02', 'Sponsor', 700000000, 0, 700000000, 600000000, 100000000, 'Positive', 'Sponsor Donation'),
('BDG-UPD-2025-AUG-00003', 'SPONSOR-2025-03', 'Sponsor', 800000000, 0, 800000000, 700000000, 100000000, 'Positive', 'Sponsor Donation'),
('BDG-UPD-2025-AUG-00004', 'SPONSOR-2025-03', 'Sponsor', 900000000, 0, 900000000, 800000000, 100000000, 'Positive', 'Sponsor Donation'),
('BDG-UPD-2025-AUG-00005', 'SPONSOR-2025-04', 'Sponsor', 1000000000, 0, 1000000000, 900000000, 100000000, 'Positive', 'Sponsor Donation');

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
  `birthdate` date DEFAULT NULL,
  `age` int NOT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `civil_status` enum('Single','Married','Separated','Widowed') DEFAULT NULL,
  `monthly_income` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `member_id`, `occupation_id`, `birthdate`, `age`, `sex`, `civil_status`, `monthly_income`) VALUES
('CLIENT-2025-AUG-0001', 'MEMBER-2025-AUG-0008', 'OCCUP-2025-11', '2003-04-02', 22, 'Male', 'Single', 10000),
('CLIENT-2025-AUG-0003', 'MEMBER-2025-AUG-0010', 'OCCUP-2025-13', '2003-04-02', 22, 'Male', 'Single', 10000),
('CLIENT-2025-AUG-0004', 'MEMBER-2025-AUG-0011', NULL, NULL, 100, 'Male', NULL, NULL),
('CLIENT-2025-AUG-0005', 'MEMBER-2025-AUG-0012', NULL, NULL, 40, 'Male', NULL, NULL),
('CLIENT-2025-AUG-0006', 'MEMBER-2025-AUG-0013', NULL, NULL, 22, 'Female', NULL, NULL),
('CLIENT-2025-AUG-0007', 'MEMBER-2025-AUG-0014', 'OCCUP-2025-10', '2000-01-01', 25, 'Female', 'Single', 15000),
('CLIENT-2025-AUG-0008', 'MEMBER-2025-AUG-0015', NULL, NULL, 22, 'Male', NULL, NULL);

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

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`contact_id`, `client_id`, `contact_type`, `contact_number`) VALUES
('CONTACT-2025-AUG-0001', 'CLIENT-2025-AUG-0001', 'Application', '0912-345-6789'),
('CONTACT-2025-AUG-0002', 'CLIENT-2025-AUG-0003', 'Application', '0907-632-3656'),
('CONTACT-2025-AUG-0003', 'CLIENT-2025-AUG-0007', 'Application', '0993-959-7683');

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
('DATA-2025-AUG-000000026', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000033', 'Unarchived', '2025-10-09 19:59:15', '2025-10-28 08:10:52', NULL),
('DATA-2025-AUG-000000037', 'Unarchived', '2025-10-18 15:55:24', '2025-10-18 15:55:24', NULL),
('DATA-2025-AUG-000000038', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000041', 'Unarchived', '2025-10-28 11:40:00', '2025-10-28 11:40:00', NULL),
('DATA-2025-AUG-000000046', 'Unarchived', '2025-10-28 20:18:13', '2025-10-28 20:18:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expense_ranges`
--

CREATE TABLE `expense_ranges` (
  `exp_range_id` varchar(24) NOT NULL,
  `tariff_list_id` varchar(13) NOT NULL,
  `service_id` varchar(15) NOT NULL,
  `exp_range_min` int NOT NULL,
  `exp_range_max` int NOT NULL,
  `coverage_percent` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expense_ranges`
--

INSERT INTO `expense_ranges` (`exp_range_id`, `tariff_list_id`, `service_id`, `exp_range_min`, `exp_range_max`, `coverage_percent`) VALUES
('EXP-RANGE-2025-AUG-00001', 'TL-2025-AUG-1', 'SERVICE-2025-01', 1, 100, 10),
('EXP-RANGE-2025-AUG-00002', 'TL-2025-AUG-1', 'SERVICE-2025-01', 101, 200, 10),
('EXP-RANGE-2025-AUG-00003', 'TL-2025-AUG-1', 'SERVICE-2025-01', 201, 300, 10),
('EXP-RANGE-2025-AUG-00004', 'TL-2025-AUG-1', 'SERVICE-2025-01', 301, 400, 20),
('EXP-RANGE-2025-AUG-00005', 'TL-2025-AUG-1', 'SERVICE-2025-01', 401, 500, 20),
('EXP-RANGE-2025-AUG-00006', 'TL-2025-AUG-1', 'SERVICE-2025-01', 501, 600, 20),
('EXP-RANGE-2025-AUG-00007', 'TL-2025-AUG-1', 'SERVICE-2025-01', 601, 700, 30),
('EXP-RANGE-2025-AUG-00008', 'TL-2025-AUG-1', 'SERVICE-2025-01', 701, 800, 30),
('EXP-RANGE-2025-AUG-00009', 'TL-2025-AUG-1', 'SERVICE-2025-01', 801, 900, 30),
('EXP-RANGE-2025-AUG-00010', 'TL-2025-AUG-1', 'SERVICE-2025-01', 901, 1000, 40),
('EXP-RANGE-2025-AUG-00011', 'TL-2025-AUG-1', 'SERVICE-2025-01', 1001, 2000, 40),
('EXP-RANGE-2025-AUG-00012', 'TL-2025-AUG-1', 'SERVICE-2025-01', 2001, 3000, 40),
('EXP-RANGE-2025-AUG-00013', 'TL-2025-AUG-1', 'SERVICE-2025-01', 3001, 4000, 40),
('EXP-RANGE-2025-AUG-00014', 'TL-2025-AUG-1', 'SERVICE-2025-01', 4001, 5000, 50),
('EXP-RANGE-2025-AUG-00015', 'TL-2025-AUG-1', 'SERVICE-2025-01', 5001, 6000, 50),
('EXP-RANGE-2025-AUG-00016', 'TL-2025-AUG-1', 'SERVICE-2025-01', 6001, 7000, 50),
('EXP-RANGE-2025-AUG-00017', 'TL-2025-AUG-1', 'SERVICE-2025-01', 7001, 8000, 50),
('EXP-RANGE-2025-AUG-00018', 'TL-2025-AUG-1', 'SERVICE-2025-01', 8001, 9000, 60),
('EXP-RANGE-2025-AUG-00019', 'TL-2025-AUG-1', 'SERVICE-2025-01', 9001, 10000, 60),
('EXP-RANGE-2025-AUG-00020', 'TL-2025-AUG-1', 'SERVICE-2025-01', 10001, 20000, 60),
('EXP-RANGE-2025-AUG-00021', 'TL-2025-AUG-1', 'SERVICE-2025-01', 20001, 30000, 60),
('EXP-RANGE-2025-AUG-00022', 'TL-2025-AUG-1', 'SERVICE-2025-01', 30001, 40000, 70),
('EXP-RANGE-2025-AUG-00023', 'TL-2025-AUG-1', 'SERVICE-2025-01', 40001, 50000, 70),
('EXP-RANGE-2025-AUG-00024', 'TL-2025-AUG-1', 'SERVICE-2025-01', 50001, 60000, 70),
('EXP-RANGE-2025-AUG-00025', 'TL-2025-AUG-1', 'SERVICE-2025-01', 60001, 70000, 70),
('EXP-RANGE-2025-AUG-00026', 'TL-2025-AUG-1', 'SERVICE-2025-01', 70001, 80000, 80),
('EXP-RANGE-2025-AUG-00027', 'TL-2025-AUG-1', 'SERVICE-2025-01', 80001, 90000, 80),
('EXP-RANGE-2025-AUG-00028', 'TL-2025-AUG-1', 'SERVICE-2025-01', 90001, 100000, 80),
('EXP-RANGE-2025-AUG-00029', 'TL-2025-AUG-1', 'SERVICE-2025-01', 100001, 200000, 80),
('EXP-RANGE-2025-AUG-00030', 'TL-2025-AUG-1', 'SERVICE-2025-01', 200001, 300000, 90),
('EXP-RANGE-2025-AUG-00031', 'TL-2025-AUG-1', 'SERVICE-2025-01', 300001, 400000, 90),
('EXP-RANGE-2025-AUG-00032', 'TL-2025-AUG-1', 'SERVICE-2025-01', 400001, 500000, 90),
('EXP-RANGE-2025-AUG-00033', 'TL-2025-AUG-1', 'SERVICE-2025-01', 500001, 600000, 90),
('EXP-RANGE-2025-AUG-00034', 'TL-2025-AUG-1', 'SERVICE-2025-01', 600001, 700000, 100),
('EXP-RANGE-2025-AUG-00035', 'TL-2025-AUG-1', 'SERVICE-2025-01', 700001, 800000, 100),
('EXP-RANGE-2025-AUG-00036', 'TL-2025-AUG-1', 'SERVICE-2025-01', 800001, 900000, 100),
('EXP-RANGE-2025-AUG-00037', 'TL-2025-AUG-1', 'SERVICE-2025-01', 900001, 1000000, 100),
('EXP-RANGE-2025-AUG-00038', 'TL-2025-AUG-1', 'SERVICE-2025-02', 1, 100, 10),
('EXP-RANGE-2025-AUG-00039', 'TL-2025-AUG-1', 'SERVICE-2025-02', 101, 200, 10),
('EXP-RANGE-2025-AUG-00040', 'TL-2025-AUG-1', 'SERVICE-2025-02', 201, 300, 10),
('EXP-RANGE-2025-AUG-00041', 'TL-2025-AUG-1', 'SERVICE-2025-02', 301, 400, 20),
('EXP-RANGE-2025-AUG-00042', 'TL-2025-AUG-1', 'SERVICE-2025-02', 401, 500, 20),
('EXP-RANGE-2025-AUG-00043', 'TL-2025-AUG-1', 'SERVICE-2025-02', 501, 600, 20),
('EXP-RANGE-2025-AUG-00044', 'TL-2025-AUG-1', 'SERVICE-2025-02', 601, 700, 30),
('EXP-RANGE-2025-AUG-00045', 'TL-2025-AUG-1', 'SERVICE-2025-02', 701, 800, 30),
('EXP-RANGE-2025-AUG-00046', 'TL-2025-AUG-1', 'SERVICE-2025-02', 801, 900, 30),
('EXP-RANGE-2025-AUG-00047', 'TL-2025-AUG-1', 'SERVICE-2025-02', 901, 1000, 40),
('EXP-RANGE-2025-AUG-00048', 'TL-2025-AUG-1', 'SERVICE-2025-02', 1001, 2000, 40),
('EXP-RANGE-2025-AUG-00049', 'TL-2025-AUG-1', 'SERVICE-2025-02', 2001, 3000, 40),
('EXP-RANGE-2025-AUG-00050', 'TL-2025-AUG-1', 'SERVICE-2025-02', 3001, 4000, 40),
('EXP-RANGE-2025-AUG-00051', 'TL-2025-AUG-1', 'SERVICE-2025-02', 4001, 5000, 50),
('EXP-RANGE-2025-AUG-00052', 'TL-2025-AUG-1', 'SERVICE-2025-02', 5001, 6000, 50),
('EXP-RANGE-2025-AUG-00053', 'TL-2025-AUG-1', 'SERVICE-2025-02', 6001, 7000, 50),
('EXP-RANGE-2025-AUG-00054', 'TL-2025-AUG-1', 'SERVICE-2025-02', 7001, 8000, 50),
('EXP-RANGE-2025-AUG-00055', 'TL-2025-AUG-1', 'SERVICE-2025-02', 8001, 9000, 60),
('EXP-RANGE-2025-AUG-00056', 'TL-2025-AUG-1', 'SERVICE-2025-02', 9001, 10000, 60),
('EXP-RANGE-2025-AUG-00057', 'TL-2025-AUG-1', 'SERVICE-2025-02', 10001, 20000, 60),
('EXP-RANGE-2025-AUG-00058', 'TL-2025-AUG-1', 'SERVICE-2025-02', 20001, 30000, 60),
('EXP-RANGE-2025-AUG-00059', 'TL-2025-AUG-1', 'SERVICE-2025-02', 30001, 40000, 70),
('EXP-RANGE-2025-AUG-00060', 'TL-2025-AUG-1', 'SERVICE-2025-02', 40001, 50000, 70),
('EXP-RANGE-2025-AUG-00061', 'TL-2025-AUG-1', 'SERVICE-2025-02', 50001, 60000, 70),
('EXP-RANGE-2025-AUG-00062', 'TL-2025-AUG-1', 'SERVICE-2025-02', 60001, 70000, 70),
('EXP-RANGE-2025-AUG-00063', 'TL-2025-AUG-1', 'SERVICE-2025-02', 70001, 80000, 80),
('EXP-RANGE-2025-AUG-00064', 'TL-2025-AUG-1', 'SERVICE-2025-02', 80001, 90000, 80),
('EXP-RANGE-2025-AUG-00065', 'TL-2025-AUG-1', 'SERVICE-2025-02', 90001, 100000, 80),
('EXP-RANGE-2025-AUG-00066', 'TL-2025-AUG-1', 'SERVICE-2025-02', 100001, 200000, 80),
('EXP-RANGE-2025-AUG-00067', 'TL-2025-AUG-1', 'SERVICE-2025-02', 200001, 300000, 90),
('EXP-RANGE-2025-AUG-00068', 'TL-2025-AUG-1', 'SERVICE-2025-02', 300001, 400000, 90),
('EXP-RANGE-2025-AUG-00069', 'TL-2025-AUG-1', 'SERVICE-2025-02', 400001, 500000, 90),
('EXP-RANGE-2025-AUG-00070', 'TL-2025-AUG-1', 'SERVICE-2025-02', 500001, 600000, 90),
('EXP-RANGE-2025-AUG-00071', 'TL-2025-AUG-1', 'SERVICE-2025-02', 600001, 700000, 100),
('EXP-RANGE-2025-AUG-00072', 'TL-2025-AUG-1', 'SERVICE-2025-02', 700001, 800000, 100),
('EXP-RANGE-2025-AUG-00073', 'TL-2025-AUG-1', 'SERVICE-2025-02', 800001, 900000, 100),
('EXP-RANGE-2025-AUG-00074', 'TL-2025-AUG-1', 'SERVICE-2025-02', 900001, 1000000, 100),
('EXP-RANGE-2025-AUG-00075', 'TL-2025-AUG-1', 'SERVICE-2025-03', 1, 100, 10),
('EXP-RANGE-2025-AUG-00076', 'TL-2025-AUG-1', 'SERVICE-2025-03', 101, 200, 10),
('EXP-RANGE-2025-AUG-00077', 'TL-2025-AUG-1', 'SERVICE-2025-03', 201, 300, 10),
('EXP-RANGE-2025-AUG-00078', 'TL-2025-AUG-1', 'SERVICE-2025-03', 301, 400, 20),
('EXP-RANGE-2025-AUG-00079', 'TL-2025-AUG-1', 'SERVICE-2025-03', 401, 500, 20),
('EXP-RANGE-2025-AUG-00080', 'TL-2025-AUG-1', 'SERVICE-2025-03', 501, 600, 20),
('EXP-RANGE-2025-AUG-00081', 'TL-2025-AUG-1', 'SERVICE-2025-03', 601, 700, 30),
('EXP-RANGE-2025-AUG-00082', 'TL-2025-AUG-1', 'SERVICE-2025-03', 701, 800, 30),
('EXP-RANGE-2025-AUG-00083', 'TL-2025-AUG-1', 'SERVICE-2025-03', 801, 900, 30),
('EXP-RANGE-2025-AUG-00084', 'TL-2025-AUG-1', 'SERVICE-2025-03', 901, 1000, 40),
('EXP-RANGE-2025-AUG-00085', 'TL-2025-AUG-1', 'SERVICE-2025-03', 1001, 2000, 40),
('EXP-RANGE-2025-AUG-00086', 'TL-2025-AUG-1', 'SERVICE-2025-03', 2001, 3000, 40),
('EXP-RANGE-2025-AUG-00087', 'TL-2025-AUG-1', 'SERVICE-2025-03', 3001, 4000, 40),
('EXP-RANGE-2025-AUG-00088', 'TL-2025-AUG-1', 'SERVICE-2025-03', 4001, 5000, 50),
('EXP-RANGE-2025-AUG-00089', 'TL-2025-AUG-1', 'SERVICE-2025-03', 5001, 6000, 50),
('EXP-RANGE-2025-AUG-00090', 'TL-2025-AUG-1', 'SERVICE-2025-03', 6001, 7000, 50),
('EXP-RANGE-2025-AUG-00091', 'TL-2025-AUG-1', 'SERVICE-2025-03', 7001, 8000, 50),
('EXP-RANGE-2025-AUG-00092', 'TL-2025-AUG-1', 'SERVICE-2025-03', 8001, 9000, 60),
('EXP-RANGE-2025-AUG-00093', 'TL-2025-AUG-1', 'SERVICE-2025-03', 9001, 10000, 60),
('EXP-RANGE-2025-AUG-00094', 'TL-2025-AUG-1', 'SERVICE-2025-03', 10001, 20000, 60),
('EXP-RANGE-2025-AUG-00095', 'TL-2025-AUG-1', 'SERVICE-2025-03', 20001, 30000, 60),
('EXP-RANGE-2025-AUG-00096', 'TL-2025-AUG-1', 'SERVICE-2025-03', 30001, 40000, 70),
('EXP-RANGE-2025-AUG-00097', 'TL-2025-AUG-1', 'SERVICE-2025-03', 40001, 50000, 70),
('EXP-RANGE-2025-AUG-00098', 'TL-2025-AUG-1', 'SERVICE-2025-03', 50001, 60000, 70),
('EXP-RANGE-2025-AUG-00099', 'TL-2025-AUG-1', 'SERVICE-2025-03', 60001, 70000, 70),
('EXP-RANGE-2025-AUG-00100', 'TL-2025-AUG-1', 'SERVICE-2025-03', 70001, 80000, 80),
('EXP-RANGE-2025-AUG-00101', 'TL-2025-AUG-1', 'SERVICE-2025-03', 80001, 90000, 80),
('EXP-RANGE-2025-AUG-00102', 'TL-2025-AUG-1', 'SERVICE-2025-03', 90001, 100000, 80),
('EXP-RANGE-2025-AUG-00103', 'TL-2025-AUG-1', 'SERVICE-2025-03', 100001, 200000, 80),
('EXP-RANGE-2025-AUG-00104', 'TL-2025-AUG-1', 'SERVICE-2025-03', 200001, 300000, 90),
('EXP-RANGE-2025-AUG-00105', 'TL-2025-AUG-1', 'SERVICE-2025-03', 300001, 400000, 90),
('EXP-RANGE-2025-AUG-00106', 'TL-2025-AUG-1', 'SERVICE-2025-03', 400001, 500000, 90),
('EXP-RANGE-2025-AUG-00107', 'TL-2025-AUG-1', 'SERVICE-2025-03', 500001, 600000, 90),
('EXP-RANGE-2025-AUG-00108', 'TL-2025-AUG-1', 'SERVICE-2025-03', 600001, 700000, 100),
('EXP-RANGE-2025-AUG-00109', 'TL-2025-AUG-1', 'SERVICE-2025-03', 700001, 800000, 100),
('EXP-RANGE-2025-AUG-00110', 'TL-2025-AUG-1', 'SERVICE-2025-03', 800001, 900000, 100),
('EXP-RANGE-2025-AUG-00111', 'TL-2025-AUG-1', 'SERVICE-2025-03', 900001, 1000000, 100),
('EXP-RANGE-2025-AUG-00112', 'TL-2025-AUG-1', 'SERVICE-2025-04', 1, 100, 10),
('EXP-RANGE-2025-AUG-00113', 'TL-2025-AUG-1', 'SERVICE-2025-04', 101, 200, 10),
('EXP-RANGE-2025-AUG-00114', 'TL-2025-AUG-1', 'SERVICE-2025-04', 201, 300, 10),
('EXP-RANGE-2025-AUG-00115', 'TL-2025-AUG-1', 'SERVICE-2025-04', 301, 400, 20),
('EXP-RANGE-2025-AUG-00116', 'TL-2025-AUG-1', 'SERVICE-2025-04', 401, 500, 20),
('EXP-RANGE-2025-AUG-00117', 'TL-2025-AUG-1', 'SERVICE-2025-04', 501, 600, 20),
('EXP-RANGE-2025-AUG-00118', 'TL-2025-AUG-1', 'SERVICE-2025-04', 601, 700, 30),
('EXP-RANGE-2025-AUG-00119', 'TL-2025-AUG-1', 'SERVICE-2025-04', 701, 800, 30),
('EXP-RANGE-2025-AUG-00120', 'TL-2025-AUG-1', 'SERVICE-2025-04', 801, 900, 30),
('EXP-RANGE-2025-AUG-00121', 'TL-2025-AUG-1', 'SERVICE-2025-04', 901, 1000, 40),
('EXP-RANGE-2025-AUG-00122', 'TL-2025-AUG-1', 'SERVICE-2025-04', 1001, 2000, 40),
('EXP-RANGE-2025-AUG-00123', 'TL-2025-AUG-1', 'SERVICE-2025-04', 2001, 3000, 40),
('EXP-RANGE-2025-AUG-00124', 'TL-2025-AUG-1', 'SERVICE-2025-04', 3001, 4000, 40),
('EXP-RANGE-2025-AUG-00125', 'TL-2025-AUG-1', 'SERVICE-2025-04', 4001, 5000, 50),
('EXP-RANGE-2025-AUG-00126', 'TL-2025-AUG-1', 'SERVICE-2025-04', 5001, 6000, 50),
('EXP-RANGE-2025-AUG-00127', 'TL-2025-AUG-1', 'SERVICE-2025-04', 6001, 7000, 50),
('EXP-RANGE-2025-AUG-00128', 'TL-2025-AUG-1', 'SERVICE-2025-04', 7001, 8000, 50),
('EXP-RANGE-2025-AUG-00129', 'TL-2025-AUG-1', 'SERVICE-2025-04', 8001, 9000, 60),
('EXP-RANGE-2025-AUG-00130', 'TL-2025-AUG-1', 'SERVICE-2025-04', 9001, 10000, 60),
('EXP-RANGE-2025-AUG-00131', 'TL-2025-AUG-1', 'SERVICE-2025-04', 10001, 20000, 60),
('EXP-RANGE-2025-AUG-00132', 'TL-2025-AUG-1', 'SERVICE-2025-04', 20001, 30000, 60),
('EXP-RANGE-2025-AUG-00133', 'TL-2025-AUG-1', 'SERVICE-2025-04', 30001, 40000, 70),
('EXP-RANGE-2025-AUG-00134', 'TL-2025-AUG-1', 'SERVICE-2025-04', 40001, 50000, 70),
('EXP-RANGE-2025-AUG-00135', 'TL-2025-AUG-1', 'SERVICE-2025-04', 50001, 60000, 70),
('EXP-RANGE-2025-AUG-00136', 'TL-2025-AUG-1', 'SERVICE-2025-04', 60001, 70000, 70),
('EXP-RANGE-2025-AUG-00137', 'TL-2025-AUG-1', 'SERVICE-2025-04', 70001, 80000, 80),
('EXP-RANGE-2025-AUG-00138', 'TL-2025-AUG-1', 'SERVICE-2025-04', 80001, 90000, 80),
('EXP-RANGE-2025-AUG-00139', 'TL-2025-AUG-1', 'SERVICE-2025-04', 90001, 100000, 80),
('EXP-RANGE-2025-AUG-00140', 'TL-2025-AUG-1', 'SERVICE-2025-04', 100001, 200000, 80),
('EXP-RANGE-2025-AUG-00141', 'TL-2025-AUG-1', 'SERVICE-2025-04', 200001, 300000, 90),
('EXP-RANGE-2025-AUG-00142', 'TL-2025-AUG-1', 'SERVICE-2025-04', 300001, 400000, 90),
('EXP-RANGE-2025-AUG-00143', 'TL-2025-AUG-1', 'SERVICE-2025-04', 400001, 500000, 90),
('EXP-RANGE-2025-AUG-00144', 'TL-2025-AUG-1', 'SERVICE-2025-04', 500001, 600000, 90),
('EXP-RANGE-2025-AUG-00145', 'TL-2025-AUG-1', 'SERVICE-2025-04', 600001, 700000, 100),
('EXP-RANGE-2025-AUG-00146', 'TL-2025-AUG-1', 'SERVICE-2025-04', 700001, 800000, 100),
('EXP-RANGE-2025-AUG-00147', 'TL-2025-AUG-1', 'SERVICE-2025-04', 800001, 900000, 100),
('EXP-RANGE-2025-AUG-00148', 'TL-2025-AUG-1', 'SERVICE-2025-04', 900001, 1000000, 100),
('EXP-RANGE-2025-AUG-00149', 'TL-2025-AUG-1', 'SERVICE-2025-05', 1, 100, 10),
('EXP-RANGE-2025-AUG-00150', 'TL-2025-AUG-1', 'SERVICE-2025-05', 101, 200, 10),
('EXP-RANGE-2025-AUG-00151', 'TL-2025-AUG-1', 'SERVICE-2025-05', 201, 300, 10),
('EXP-RANGE-2025-AUG-00152', 'TL-2025-AUG-1', 'SERVICE-2025-05', 301, 400, 20),
('EXP-RANGE-2025-AUG-00153', 'TL-2025-AUG-1', 'SERVICE-2025-05', 401, 500, 20),
('EXP-RANGE-2025-AUG-00154', 'TL-2025-AUG-1', 'SERVICE-2025-05', 501, 600, 20),
('EXP-RANGE-2025-AUG-00155', 'TL-2025-AUG-1', 'SERVICE-2025-05', 601, 700, 30),
('EXP-RANGE-2025-AUG-00156', 'TL-2025-AUG-1', 'SERVICE-2025-05', 701, 800, 30),
('EXP-RANGE-2025-AUG-00157', 'TL-2025-AUG-1', 'SERVICE-2025-05', 801, 900, 30),
('EXP-RANGE-2025-AUG-00158', 'TL-2025-AUG-1', 'SERVICE-2025-05', 901, 1000, 40),
('EXP-RANGE-2025-AUG-00159', 'TL-2025-AUG-1', 'SERVICE-2025-05', 1001, 2000, 40),
('EXP-RANGE-2025-AUG-00160', 'TL-2025-AUG-1', 'SERVICE-2025-05', 2001, 3000, 40),
('EXP-RANGE-2025-AUG-00161', 'TL-2025-AUG-1', 'SERVICE-2025-05', 3001, 4000, 40),
('EXP-RANGE-2025-AUG-00162', 'TL-2025-AUG-1', 'SERVICE-2025-05', 4001, 5000, 50),
('EXP-RANGE-2025-AUG-00163', 'TL-2025-AUG-1', 'SERVICE-2025-05', 5001, 6000, 50),
('EXP-RANGE-2025-AUG-00164', 'TL-2025-AUG-1', 'SERVICE-2025-05', 6001, 7000, 50),
('EXP-RANGE-2025-AUG-00165', 'TL-2025-AUG-1', 'SERVICE-2025-05', 7001, 8000, 50),
('EXP-RANGE-2025-AUG-00166', 'TL-2025-AUG-1', 'SERVICE-2025-05', 8001, 9000, 60),
('EXP-RANGE-2025-AUG-00167', 'TL-2025-AUG-1', 'SERVICE-2025-05', 9001, 10000, 60),
('EXP-RANGE-2025-AUG-00168', 'TL-2025-AUG-1', 'SERVICE-2025-05', 10001, 20000, 60),
('EXP-RANGE-2025-AUG-00169', 'TL-2025-AUG-1', 'SERVICE-2025-05', 20001, 30000, 60),
('EXP-RANGE-2025-AUG-00170', 'TL-2025-AUG-1', 'SERVICE-2025-05', 30001, 40000, 70),
('EXP-RANGE-2025-AUG-00171', 'TL-2025-AUG-1', 'SERVICE-2025-05', 40001, 50000, 70),
('EXP-RANGE-2025-AUG-00172', 'TL-2025-AUG-1', 'SERVICE-2025-05', 50001, 60000, 70),
('EXP-RANGE-2025-AUG-00173', 'TL-2025-AUG-1', 'SERVICE-2025-05', 60001, 70000, 70),
('EXP-RANGE-2025-AUG-00174', 'TL-2025-AUG-1', 'SERVICE-2025-05', 70001, 80000, 80),
('EXP-RANGE-2025-AUG-00175', 'TL-2025-AUG-1', 'SERVICE-2025-05', 80001, 90000, 80),
('EXP-RANGE-2025-AUG-00176', 'TL-2025-AUG-1', 'SERVICE-2025-05', 90001, 100000, 80),
('EXP-RANGE-2025-AUG-00177', 'TL-2025-AUG-1', 'SERVICE-2025-05', 100001, 200000, 80),
('EXP-RANGE-2025-AUG-00178', 'TL-2025-AUG-1', 'SERVICE-2025-05', 200001, 300000, 90),
('EXP-RANGE-2025-AUG-00179', 'TL-2025-AUG-1', 'SERVICE-2025-05', 300001, 400000, 90),
('EXP-RANGE-2025-AUG-00180', 'TL-2025-AUG-1', 'SERVICE-2025-05', 400001, 500000, 90),
('EXP-RANGE-2025-AUG-00181', 'TL-2025-AUG-1', 'SERVICE-2025-05', 500001, 600000, 90),
('EXP-RANGE-2025-AUG-00182', 'TL-2025-AUG-1', 'SERVICE-2025-05', 600001, 700000, 100),
('EXP-RANGE-2025-AUG-00183', 'TL-2025-AUG-1', 'SERVICE-2025-05', 700001, 800000, 100),
('EXP-RANGE-2025-AUG-00184', 'TL-2025-AUG-1', 'SERVICE-2025-05', 800001, 900000, 100),
('EXP-RANGE-2025-AUG-00185', 'TL-2025-AUG-1', 'SERVICE-2025-05', 900001, 1000000, 100),
('EXP-RANGE-2025-AUG-00186', 'TL-2025-AUG-1', 'SERVICE-2025-06', 1, 100, 10),
('EXP-RANGE-2025-AUG-00187', 'TL-2025-AUG-1', 'SERVICE-2025-06', 101, 200, 10),
('EXP-RANGE-2025-AUG-00188', 'TL-2025-AUG-1', 'SERVICE-2025-06', 201, 300, 10),
('EXP-RANGE-2025-AUG-00189', 'TL-2025-AUG-1', 'SERVICE-2025-06', 301, 400, 20),
('EXP-RANGE-2025-AUG-00190', 'TL-2025-AUG-1', 'SERVICE-2025-06', 401, 500, 20),
('EXP-RANGE-2025-AUG-00191', 'TL-2025-AUG-1', 'SERVICE-2025-06', 501, 600, 20),
('EXP-RANGE-2025-AUG-00192', 'TL-2025-AUG-1', 'SERVICE-2025-06', 601, 700, 30),
('EXP-RANGE-2025-AUG-00193', 'TL-2025-AUG-1', 'SERVICE-2025-06', 701, 800, 30),
('EXP-RANGE-2025-AUG-00194', 'TL-2025-AUG-1', 'SERVICE-2025-06', 801, 900, 30),
('EXP-RANGE-2025-AUG-00195', 'TL-2025-AUG-1', 'SERVICE-2025-06', 901, 1000, 40),
('EXP-RANGE-2025-AUG-00196', 'TL-2025-AUG-1', 'SERVICE-2025-06', 1001, 2000, 40),
('EXP-RANGE-2025-AUG-00197', 'TL-2025-AUG-1', 'SERVICE-2025-06', 2001, 3000, 40),
('EXP-RANGE-2025-AUG-00198', 'TL-2025-AUG-1', 'SERVICE-2025-06', 3001, 4000, 40),
('EXP-RANGE-2025-AUG-00199', 'TL-2025-AUG-1', 'SERVICE-2025-06', 4001, 5000, 50),
('EXP-RANGE-2025-AUG-00200', 'TL-2025-AUG-1', 'SERVICE-2025-06', 5001, 6000, 50),
('EXP-RANGE-2025-AUG-00201', 'TL-2025-AUG-1', 'SERVICE-2025-06', 6001, 7000, 50),
('EXP-RANGE-2025-AUG-00202', 'TL-2025-AUG-1', 'SERVICE-2025-06', 7001, 8000, 50),
('EXP-RANGE-2025-AUG-00203', 'TL-2025-AUG-1', 'SERVICE-2025-06', 8001, 9000, 60),
('EXP-RANGE-2025-AUG-00204', 'TL-2025-AUG-1', 'SERVICE-2025-06', 9001, 10000, 60),
('EXP-RANGE-2025-AUG-00205', 'TL-2025-AUG-1', 'SERVICE-2025-06', 10001, 20000, 60),
('EXP-RANGE-2025-AUG-00206', 'TL-2025-AUG-1', 'SERVICE-2025-06', 20001, 30000, 60),
('EXP-RANGE-2025-AUG-00207', 'TL-2025-AUG-1', 'SERVICE-2025-06', 30001, 40000, 70),
('EXP-RANGE-2025-AUG-00208', 'TL-2025-AUG-1', 'SERVICE-2025-06', 40001, 50000, 70),
('EXP-RANGE-2025-AUG-00209', 'TL-2025-AUG-1', 'SERVICE-2025-06', 50001, 60000, 70),
('EXP-RANGE-2025-AUG-00210', 'TL-2025-AUG-1', 'SERVICE-2025-06', 60001, 70000, 70),
('EXP-RANGE-2025-AUG-00211', 'TL-2025-AUG-1', 'SERVICE-2025-06', 70001, 80000, 80),
('EXP-RANGE-2025-AUG-00212', 'TL-2025-AUG-1', 'SERVICE-2025-06', 80001, 90000, 80),
('EXP-RANGE-2025-AUG-00213', 'TL-2025-AUG-1', 'SERVICE-2025-06', 90001, 100000, 80),
('EXP-RANGE-2025-AUG-00214', 'TL-2025-AUG-1', 'SERVICE-2025-06', 100001, 200000, 80),
('EXP-RANGE-2025-AUG-00215', 'TL-2025-AUG-1', 'SERVICE-2025-06', 200001, 300000, 90),
('EXP-RANGE-2025-AUG-00216', 'TL-2025-AUG-1', 'SERVICE-2025-06', 300001, 400000, 90),
('EXP-RANGE-2025-AUG-00217', 'TL-2025-AUG-1', 'SERVICE-2025-06', 400001, 500000, 90),
('EXP-RANGE-2025-AUG-00218', 'TL-2025-AUG-1', 'SERVICE-2025-06', 500001, 600000, 90),
('EXP-RANGE-2025-AUG-00219', 'TL-2025-AUG-1', 'SERVICE-2025-06', 600001, 700000, 100),
('EXP-RANGE-2025-AUG-00220', 'TL-2025-AUG-1', 'SERVICE-2025-06', 700001, 800000, 100),
('EXP-RANGE-2025-AUG-00221', 'TL-2025-AUG-1', 'SERVICE-2025-06', 800001, 900000, 100),
('EXP-RANGE-2025-AUG-00222', 'TL-2025-AUG-1', 'SERVICE-2025-06', 900001, 1000000, 100);

-- --------------------------------------------------------

--
-- Table structure for table `guarantee_letters`
--

CREATE TABLE `guarantee_letters` (
  `gl_id` varchar(16) NOT NULL,
  `data_id` varchar(23) NOT NULL,
  `application_id` varchar(25) NOT NULL,
  `sponsor_id` varchar(15) NOT NULL,
  `is_sponsored` enum('Yes','No') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guarantee_letters`
--

INSERT INTO `guarantee_letters` (`gl_id`, `data_id`, `application_id`, `sponsor_id`, `is_sponsored`) VALUES
('GL-2025-AUG-0001', 'DATA-2025-AUG-000000026', 'APPLICATION-2025-AUG-0001', 'SPONSOR-2025-01', 'Yes');

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
('HOUSEHOLD-2025-AUG-001', 'Cariaga'),
('HOUSEHOLD-2025-AUG-002', 'Carreon');

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

--
-- Dumping data for table `household_members`
--

INSERT INTO `household_members` (`household_member_id`, `household_id`, `client_id`, `educational_attainment`, `relationship_to_applicant`) VALUES
('HM-2025-AUG-00001', 'HOUSEHOLD-2025-AUG-001', 'CLIENT-2025-AUG-0001', 'College', 'Self'),
('HM-2025-AUG-00002', 'HOUSEHOLD-2025-AUG-002', 'CLIENT-2025-AUG-0003', 'College', 'Self');

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
('MEMBER-2025-AUG-0002', 'ACCOUNT-2025-AUG-0010', 'Third-Party', 'Al-Alawi', 'Marbella', 'Mariam', NULL),
('MEMBER-2025-AUG-0003', 'ACCOUNT-2025-AUG-0011', 'Third-Party', 'Harake', 'Ocampo', 'Zeinab', NULL),
('MEMBER-2025-AUG-0004', 'ACCOUNT-2025-AUG-0012', 'Third-Party', 'Otwell', NULL, 'Taylor', NULL),
('MEMBER-2025-AUG-0005', 'ACCOUNT-2025-AUG-0013', 'Third-Party', 'Pacquiao', 'Dapidran', 'Emmanuel', 'Sr.'),
('MEMBER-2025-AUG-0006', 'ACCOUNT-2025-AUG-0014', 'Signer', 'Pacquiao', 'Geronimo', 'Lorelie', NULL),
('MEMBER-2025-AUG-0007', 'ACCOUNT-2025-AUG-0015', 'Signer', 'Ambuang', 'Dapidran', 'Maritess', NULL),
('MEMBER-2025-AUG-0008', 'ACCOUNT-2025-AUG-0016', 'Client', 'Cariaga', 'Leproso', 'Benhur', NULL),
('MEMBER-2025-AUG-0010', 'ACCOUNT-2025-AUG-0017', 'Client', 'Carreon', 'Ledesma', 'Benjamin', 'Jr.'),
('MEMBER-2025-AUG-0011', 'ACCOUNT-2025-AUG-0017', 'Client', 'Rivera', 'Ibarra', 'Simoun', NULL),
('MEMBER-2025-AUG-0012', 'ACCOUNT-2025-AUG-0017', 'Client', 'Saavedra', NULL, 'Julito', NULL),
('MEMBER-2025-AUG-0013', 'ACCOUNT-2025-AUG-0016', 'Client', 'Hinoctan', 'Provendido', 'Angel', NULL),
('MEMBER-2025-AUG-0014', 'ACCOUNT-2025-AUG-0018', 'Client', 'Castañeda', NULL, 'Key', NULL),
('MEMBER-2025-AUG-0015', 'ACCOUNT-2025-AUG-0018', 'Client', 'Cariaga', NULL, 'Benhur', NULL);

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

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `staff_id`, `contact_id`, `message_text`, `sent_at`) VALUES
('MESSAGE-2025-AUG-0003', 'STAFF-2025-01', 'CONTACT-2025-AUG-0003', 'Greetings, Key  Castañeda ! Here are the important details for your AMPING application today.<br><br>For Patient: Key  Castañeda <br>Service Type: Hospital Bill<br>Billed Amount: ₱ 150<br>Assistance Amount: ₱ 15<br>Affiliate Partner: St. Elizabeth Hospital, Inc.<br>Applied At: October 28, 2025<br>Reapply At: January 26, 2026<br><br>Thank you for your visit with us! Come again!', '2025-10-28 20:24:19');

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
(2, '2025_10_31_000000_remove_foreign_key_constraints', 2);

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

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `client_id`, `applicant_id`, `patient_category`) VALUES
('PATIENT-2025-AUG-00002', 'CLIENT-2025-AUG-0003', 'APPLICANT-2025-AUG-0002', NULL),
('PATIENT-2025-AUG-00003', 'CLIENT-2025-AUG-0004', 'APPLICANT-2025-AUG-0002', 'Senior'),
('PATIENT-2025-AUG-00004', 'CLIENT-2025-AUG-0005', 'APPLICANT-2025-AUG-0002', 'PWD'),
('PATIENT-2025-AUG-00006', 'CLIENT-2025-AUG-0006', 'APPLICANT-2025-AUG-0001', NULL),
('PATIENT-2025-AUG-00007', 'CLIENT-2025-AUG-0001', 'APPLICANT-2025-AUG-0001', NULL),
('PATIENT-2025-AUG-00008', 'CLIENT-2025-AUG-0007', 'APPLICANT-2025-AUG-0003', 'PWD'),
('PATIENT-2025-AUG-00009', 'CLIENT-2025-AUG-0008', 'APPLICANT-2025-AUG-0003', NULL);

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

--
-- Dumping data for table `signers`
--

INSERT INTO `signers` (`signer_id`, `member_id`, `post_nominal_letters`) VALUES
('SIGNER-2025-1', 'MEMBER-2025-AUG-0006', NULL),
('SIGNER-2025-2', 'MEMBER-2025-AUG-0007', 'MMPA');

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

CREATE TABLE `sponsors` (
  `sponsor_id` varchar(15) NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `sponsor_type` enum('Politician','Business Owner','Other') DEFAULT NULL,
  `designation` varchar(30) DEFAULT NULL,
  `organization_name` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sponsors`
--

INSERT INTO `sponsors` (`sponsor_id`, `member_id`, `sponsor_type`, `designation`, `organization_name`) VALUES
('SPONSOR-2025-01', 'MEMBER-2025-AUG-0002', 'Business Owner', 'Chief Executive Officer', 'Ivana Skin'),
('SPONSOR-2025-02', 'MEMBER-2025-AUG-0003', 'Other', 'Content Creator', 'YouTube'),
('SPONSOR-2025-03', 'MEMBER-2025-AUG-0004', 'Business Owner', 'Founder', 'Laravel Company'),
('SPONSOR-2025-04', 'MEMBER-2025-AUG-0005', 'Politician', 'Senator', NULL);

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
('STAFF-2025-01', 'MEMBER-2025-AUG-0001', 'ROLE-2025-1', NULL, NULL, '$2y$10$hdrsmpA1LSXsHsTxJhP2JuiAKUY6px2sPXJ1s4qNo0zqXxGlU4Zvq');

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
  ADD KEY `account_id` (`account_id`);

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
  ADD PRIMARY KEY (`audit_log_id`),
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
  ADD KEY `member_id` (`member_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `affiliate_partners`
--
ALTER TABLE `affiliate_partners`
  ADD CONSTRAINT `affiliate_partners_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `applicants`
--
ALTER TABLE `applicants`
  ADD CONSTRAINT `applicants_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`ap_id`) REFERENCES `affiliate_partners` (`ap_id`),
  ADD CONSTRAINT `applications_ibfk_3` FOREIGN KEY (`exp_range_id`) REFERENCES `expense_ranges` (`exp_range_id`),
  ADD CONSTRAINT `applications_ibfk_4` FOREIGN KEY (`message_id`) REFERENCES `messages` (`message_id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `budget_updates`
--
ALTER TABLE `budget_updates`
  ADD CONSTRAINT `budget_updates_ibfk_1` FOREIGN KEY (`sponsor_id`) REFERENCES `sponsors` (`sponsor_id`);

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`),
  ADD CONSTRAINT `clients_ibfk_2` FOREIGN KEY (`occupation_id`) REFERENCES `occupations` (`occupation_id`);

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `expense_ranges`
--
ALTER TABLE `expense_ranges`
  ADD CONSTRAINT `expense_ranges_ibfk_1` FOREIGN KEY (`tariff_list_id`) REFERENCES `tariff_lists` (`tariff_list_id`),
  ADD CONSTRAINT `expense_ranges_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Constraints for table `guarantee_letters`
--
ALTER TABLE `guarantee_letters`
  ADD CONSTRAINT `guarantee_letters_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`),
  ADD CONSTRAINT `guarantee_letters_ibfk_2` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`),
  ADD CONSTRAINT `guarantee_letters_ibfk_3` FOREIGN KEY (`sponsor_id`) REFERENCES `sponsors` (`sponsor_id`);

--
-- Constraints for table `household_members`
--
ALTER TABLE `household_members`
  ADD CONSTRAINT `household_members_ibfk_1` FOREIGN KEY (`household_id`) REFERENCES `households` (`household_id`),
  ADD CONSTRAINT `household_members_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`contact_id`);

--
-- Constraints for table `message_templates`
--
ALTER TABLE `message_templates`
  ADD CONSTRAINT `message_templates_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `occupations`
--
ALTER TABLE `occupations`
  ADD CONSTRAINT `occupations_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `patients_ibfk_2` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `signers`
--
ALTER TABLE `signers`
  ADD CONSTRAINT `signers_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`);

--
-- Constraints for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD CONSTRAINT `sponsors_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`),
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `tariff_lists`
--
ALTER TABLE `tariff_lists`
  ADD CONSTRAINT `tariff_lists_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
