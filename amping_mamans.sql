-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2025 at 10:45 PM
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
  `tp_id` varchar(11) NOT NULL,
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
  `data_id` varchar(23) NOT NULL,
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
  `reason` enum('Yearly Budget Provision','Supplementary Budget','GL Release','Sponsor Donation','Sponsored GL Release','Budget Manipulation') DEFAULT NULL
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
('DATA-2025-AUG-000000033', 'Unarchived', '2025-10-09 19:59:15', '2025-11-04 19:36:19', NULL),
('DATA-2025-AUG-000000038', 'Unarchived', '2025-08-01 04:00:00', '2025-08-01 04:00:00', NULL),
('DATA-2025-AUG-000000041', 'Unarchived', '2025-10-28 11:40:00', '2025-10-28 11:40:00', NULL),
('DATA-2025-NOV-000000001', 'Archived', '2025-11-01 07:06:20', '2025-11-03 06:56:58', '2025-11-03 06:56:58'),
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

-- --------------------------------------------------------

--
-- Table structure for table `gl_templates`
--

CREATE TABLE `gl_templates` (
  `gl_tmp_id` varchar(14) NOT NULL,
  `data_id` varchar(23) NOT NULL,
  `gl_tmp_title` varchar(30) NOT NULL,
  `gl_content` text NOT NULL,
  `signers` text NOT NULL,
  `signatures` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guarantee_letters`
--

CREATE TABLE `guarantee_letters` (
  `gl_id` varchar(16) NOT NULL,
  `gl_tmp_id` varchar(14) NOT NULL,
  `application_id` varchar(25) NOT NULL,
  `budget_update_id` varchar(22) NOT NULL,
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
('MSG-TMP-2025-AUG-1', 'DATA-2025-AUG-000000033', 'Text Message Template 1', 'Greetings, [$application->applicant->client->member->first_name] [$application->applicant->client->member->middle_name] [$application->applicant->client->member->last_name] [$application->applicant->client->member->suffix]! Here are the important details for your AMPING application today.;;For Patient: [$application->patient->client->member->first_name] [$application->patient->client->member->middle_name] [$application->patient->client->member->last_name] [$application->patient->client->member->suffix];Service Type: [$application->service];Billed Amount: ₱ [$application->billed_amount];Assistance Amount: ₱ [$application->assistance_amount];Affiliate Partner: [$application->affiliate_partner->affiliate_partner_name];Applied At: [$application->application_date];Reapply At: [$application->reapplication_date];;Thank you for your visit with us! Come again!');

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
  `occupation_id` varchar(21) NOT NULL,
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
('STAFF-2025-02', 'MEMBER-2025-NOV-0001', 'ROLE-2025-2', 'profile_pictures/Cn7qWrMU4ZVN2RfUpOAsUGhuFweUvSSbVNqD2sbJ.jpg', '.jpg', '$2y$10$db9OIjWsv0xXy9xeDS8kXON/pU2Obre8ev4SQmeCwb8H2ylcGMrzC');

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
  `tp_id` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `account_id` varchar(21) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tp_type` enum('Affiliate Partner','Sponsor') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `idx_accounts_data_id` (`data_id`);

--
-- Indexes for table `affiliate_partners`
--
ALTER TABLE `affiliate_partners`
  ADD PRIMARY KEY (`ap_id`),
  ADD KEY `idx_affiliate_partners_tp_id` (`tp_id`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`applicant_id`),
  ADD KEY `idx_applicants_client_id` (`client_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `idx_applications_data_id` (`data_id`),
  ADD KEY `idx_applications_patient_id` (`patient_id`),
  ADD KEY `idx_applications_ap_id` (`ap_id`),
  ADD KEY `idx_applications_exp_range_id` (`exp_range_id`),
  ADD KEY `idx_applications_message_id` (`message_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`al_id`),
  ADD KEY `idx_audit_logs_staff_id` (`staff_id`);

--
-- Indexes for table `budget_updates`
--
ALTER TABLE `budget_updates`
  ADD PRIMARY KEY (`budget_update_id`),
  ADD KEY `idx_budget_updates_sponsor_id` (`sponsor_id`);

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
  ADD KEY `idx_clients_member_id` (`member_id`),
  ADD KEY `idx_clients_occupation_id` (`occupation_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `idx_contacts_client_id` (`client_id`);

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
  ADD KEY `idx_expense_ranges_tariff_list_id` (`tariff_list_id`),
  ADD KEY `idx_expense_ranges_service_id` (`service_id`);

--
-- Indexes for table `gl_templates`
--
ALTER TABLE `gl_templates`
  ADD PRIMARY KEY (`gl_tmp_id`),
  ADD KEY `idx_gl_templates_data_id` (`data_id`);

--
-- Indexes for table `guarantee_letters`
--
ALTER TABLE `guarantee_letters`
  ADD PRIMARY KEY (`gl_id`),
  ADD KEY `idx_guarantee_letters_gl_tmp_id` (`gl_tmp_id`),
  ADD KEY `idx_guarantee_letters_budget_update_id` (`budget_update_id`),
  ADD KEY `idx_guarantee_letters_application_id` (`application_id`);

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
  ADD KEY `idx_household_members_household_id` (`household_id`),
  ADD KEY `idx_household_members_client_id` (`client_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `idx_members_account_id` (`account_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_messages_staff_id` (`staff_id`),
  ADD KEY `idx_messages_contact_id` (`contact_id`);

--
-- Indexes for table `message_templates`
--
ALTER TABLE `message_templates`
  ADD PRIMARY KEY (`msg_tmp_id`),
  ADD KEY `idx_message_templates_data_id` (`data_id`);

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
  ADD KEY `idx_occupations_data_id` (`data_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `idx_patients_client_id` (`client_id`),
  ADD KEY `idx_patients_applicant_id` (`applicant_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_reports_staff_id` (`staff_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD KEY `idx_roles_data_id` (`data_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `idx_services_data_id` (`data_id`);

--
-- Indexes for table `signers`
--
ALTER TABLE `signers`
  ADD PRIMARY KEY (`signer_id`),
  ADD KEY `idx_signers_member_id` (`member_id`);

--
-- Indexes for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD PRIMARY KEY (`sponsor_id`),
  ADD KEY `idx_sponsors_tp_id` (`tp_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD KEY `idx_staff_member_id` (`member_id`),
  ADD KEY `idx_staff_role_id` (`role_id`);

--
-- Indexes for table `tariff_lists`
--
ALTER TABLE `tariff_lists`
  ADD PRIMARY KEY (`tariff_list_id`),
  ADD KEY `idx_tariff_lists_data_id` (`data_id`);

--
-- Indexes for table `third_parties`
--
ALTER TABLE `third_parties`
  ADD PRIMARY KEY (`tp_id`),
  ADD KEY `idx_third_parties_account_id` (`account_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `fk_accounts_data_data_id` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `affiliate_partners`
--
ALTER TABLE `affiliate_partners`
  ADD CONSTRAINT `fk_affiliate_partners_tp_third_parties` FOREIGN KEY (`tp_id`) REFERENCES `third_parties` (`tp_id`);

--
-- Constraints for table `applicants`
--
ALTER TABLE `applicants`
  ADD CONSTRAINT `fk_applicants_client_clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `fk_applications_ap_affiliate_partners` FOREIGN KEY (`ap_id`) REFERENCES `affiliate_partners` (`ap_id`),
  ADD CONSTRAINT `fk_applications_exp_range_expense_ranges` FOREIGN KEY (`exp_range_id`) REFERENCES `expense_ranges` (`exp_range_id`),
  ADD CONSTRAINT `fk_applications_message_messages` FOREIGN KEY (`message_id`) REFERENCES `messages` (`message_id`),
  ADD CONSTRAINT `fk_applications_patient_patients` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_logs_staff_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `budget_updates`
--
ALTER TABLE `budget_updates`
  ADD CONSTRAINT `fk_budget_updates_sponsor_sponsors` FOREIGN KEY (`sponsor_id`) REFERENCES `sponsors` (`sponsor_id`);

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `fk_clients_member_members` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`),
  ADD CONSTRAINT `fk_clients_occupation_occupations` FOREIGN KEY (`occupation_id`) REFERENCES `occupations` (`occupation_id`);

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `fk_contacts_client_clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `expense_ranges`
--
ALTER TABLE `expense_ranges`
  ADD CONSTRAINT `fk_expense_ranges_service_services` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`),
  ADD CONSTRAINT `fk_expense_ranges_tariff_list_tariff_lists` FOREIGN KEY (`tariff_list_id`) REFERENCES `tariff_lists` (`tariff_list_id`);

--
-- Constraints for table `gl_templates`
--
ALTER TABLE `gl_templates`
  ADD CONSTRAINT `fk_gl_templates_data_data` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `guarantee_letters`
--
ALTER TABLE `guarantee_letters`
  ADD CONSTRAINT `fk_guarantee_letters_application_applications` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`),
  ADD CONSTRAINT `fk_guarantee_letters_budget_update_budget_updates` FOREIGN KEY (`budget_update_id`) REFERENCES `budget_updates` (`budget_update_id`),
  ADD CONSTRAINT `fk_guarantee_letters_gltmp_gl_templates` FOREIGN KEY (`gl_tmp_id`) REFERENCES `gl_templates` (`gl_tmp_id`);

--
-- Constraints for table `household_members`
--
ALTER TABLE `household_members`
  ADD CONSTRAINT `fk_household_members_client_clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `fk_household_members_household_households` FOREIGN KEY (`household_id`) REFERENCES `households` (`household_id`);

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `fk_members_account_accounts` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_contact_contacts` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`contact_id`),
  ADD CONSTRAINT `fk_messages_staff_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `message_templates`
--
ALTER TABLE `message_templates`
  ADD CONSTRAINT `fk_message_templates_data_data` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `occupations`
--
ALTER TABLE `occupations`
  ADD CONSTRAINT `fk_occupations_data_data` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_applicant_applicants` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`),
  ADD CONSTRAINT `fk_patients_client_clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_staff_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `fk_roles_data_data` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `fk_services_data_data` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `signers`
--
ALTER TABLE `signers`
  ADD CONSTRAINT `fk_signers_member_members` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`);

--
-- Constraints for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD CONSTRAINT `fk_sponsors_tp_third_parties` FOREIGN KEY (`tp_id`) REFERENCES `third_parties` (`tp_id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_member_members` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`),
  ADD CONSTRAINT `fk_staff_role_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `tariff_lists`
--
ALTER TABLE `tariff_lists`
  ADD CONSTRAINT `fk_tariff_lists_data_data` FOREIGN KEY (`data_id`) REFERENCES `data` (`data_id`);

--
-- Constraints for table `third_parties`
--
ALTER TABLE `third_parties`
  ADD CONSTRAINT `fk_third_parties_account_accounts` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
