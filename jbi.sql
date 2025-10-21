-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 21, 2025 at 02:11 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jbi`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `name`, `year`, `start_date`, `end_date`, `is_current`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '2025-2026', 2025, '2025-01-01', '2025-12-31', 1, 1, '2025-05-28 17:13:26', '2025-05-28 17:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('general','academic','administrative','emergency','event') NOT NULL DEFAULT 'general',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_roles`)),
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `send_email` tinyint(1) NOT NULL DEFAULT 0,
  `send_sms` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `instructions` text DEFAULT NULL,
  `type` enum('homework','quiz','exam','project','essay','presentation') NOT NULL DEFAULT 'homework',
  `max_points` int(11) NOT NULL DEFAULT 100,
  `weight_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `due_date` datetime NOT NULL,
  `available_from` datetime DEFAULT NULL,
  `available_until` datetime DEFAULT NULL,
  `allow_late_submission` tinyint(1) NOT NULL DEFAULT 0,
  `late_penalty_per_day` int(11) NOT NULL DEFAULT 0,
  `allowed_file_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_file_types`)),
  `max_file_size` int(11) NOT NULL DEFAULT 10240,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `rubric` text DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assignment_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `submission_text` text DEFAULT NULL,
  `submitted_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`submitted_files`)),
  `submitted_at` datetime NOT NULL,
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `days_late` int(11) NOT NULL DEFAULT 0,
  `score` decimal(5,2) DEFAULT NULL,
  `adjusted_score` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `rubric_scores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rubric_scores`)),
  `status` enum('submitted','graded','returned','resubmitted') NOT NULL DEFAULT 'submitted',
  `graded_at` datetime DEFAULT NULL,
  `graded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `attempt_number` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `class_start_time` time NOT NULL,
  `class_end_time` time NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'absent',
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `minutes_late` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `check_in_method` varchar(255) DEFAULT NULL,
  `marked_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-28 16:44:49', '2025-05-28 16:44:49'),
(2, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-28 19:05:13', '2025-05-28 19:05:13'),
(3, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-28 19:07:08', '2025-05-28 19:07:08'),
(4, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-28 19:09:07', '2025-05-28 19:09:07'),
(5, 2, 'login', 'User', 2, NULL, NULL, NULL, NULL, NULL, '2025-05-28 19:09:52', '2025-05-28 19:09:52'),
(6, 2, 'logout', 'User', 2, NULL, NULL, NULL, NULL, NULL, '2025-05-28 19:16:27', '2025-05-28 19:16:27'),
(7, 3, 'login', 'User', 3, NULL, NULL, NULL, NULL, NULL, '2025-05-28 19:18:40', '2025-05-28 19:18:40'),
(8, 3, 'logout', 'User', 3, NULL, NULL, NULL, NULL, NULL, '2025-05-28 19:38:58', '2025-05-28 19:38:58'),
(9, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-28 19:39:56', '2025-05-28 19:39:56'),
(10, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-29 05:53:53', '2025-05-29 05:53:53'),
(11, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-29 07:07:52', '2025-05-29 07:07:52'),
(12, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-29 07:09:41', '2025-05-29 07:09:41'),
(13, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-29 08:11:35', '2025-05-29 08:11:35'),
(14, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-29 08:12:09', '2025-05-29 08:12:09'),
(15, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-05-29 08:15:37', '2025-05-29 08:15:37'),
(16, 3, 'login', 'User', 3, NULL, NULL, NULL, NULL, NULL, '2025-05-29 08:15:58', '2025-05-29 08:15:58'),
(17, 3, 'logout', 'User', 3, NULL, NULL, NULL, NULL, NULL, '2025-05-29 08:17:45', '2025-05-29 08:17:45'),
(18, 2, 'login', 'User', 2, NULL, NULL, NULL, NULL, NULL, '2025-05-29 08:18:46', '2025-05-29 08:18:46'),
(19, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-06-15 06:52:26', '2025-06-15 06:52:26'),
(20, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-06-15 08:06:38', '2025-06-15 08:06:38'),
(21, NULL, 'failed_login', 'User', 6, NULL, NULL, NULL, NULL, NULL, '2025-06-15 10:13:37', '2025-06-15 10:13:37'),
(22, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-06-15 10:14:08', '2025-06-15 10:14:08'),
(23, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-06-15 10:55:03', '2025-06-15 10:55:03'),
(24, NULL, 'login', 'User', 6, NULL, NULL, NULL, NULL, NULL, '2025-06-15 10:55:30', '2025-06-15 10:55:30'),
(25, NULL, 'logout', 'User', 6, NULL, NULL, NULL, NULL, NULL, '2025-06-15 10:56:30', '2025-06-15 10:56:30'),
(26, 1, 'failed_login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-06-15 11:02:53', '2025-06-15 11:02:53'),
(27, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-06-15 11:03:01', '2025-06-15 11:03:01'),
(28, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-06-15 11:11:11', '2025-06-15 11:11:11'),
(29, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-06-15 11:18:14', '2025-06-15 11:18:14'),
(30, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-06-15 14:50:05', '2025-06-15 14:50:05'),
(31, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-06-15 15:06:48', '2025-06-15 15:06:48'),
(32, 10, 'failed_login', 'User', 10, NULL, NULL, NULL, NULL, NULL, '2025-06-15 15:08:02', '2025-06-15 15:08:02'),
(33, 10, 'login', 'User', 10, NULL, NULL, NULL, NULL, NULL, '2025-06-15 15:08:45', '2025-06-15 15:08:45'),
(34, 2, 'login', 'User', 2, NULL, NULL, NULL, NULL, NULL, '2025-07-07 19:27:30', '2025-07-07 19:27:30'),
(35, 2, 'logout', 'User', 2, NULL, NULL, NULL, NULL, NULL, '2025-07-07 19:32:26', '2025-07-07 19:32:26'),
(36, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-07 19:34:35', '2025-07-07 19:34:35'),
(37, 1, 'failed_login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-07 23:10:28', '2025-07-07 23:10:28'),
(38, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-07 23:11:31', '2025-07-07 23:11:31'),
(39, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-08 05:38:52', '2025-07-08 05:38:52'),
(40, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-09 08:23:31', '2025-07-09 08:23:31'),
(41, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-09 12:17:11', '2025-07-09 12:17:11'),
(42, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-09 15:06:56', '2025-07-09 15:06:56'),
(43, 3, 'login', 'User', 3, NULL, NULL, NULL, NULL, NULL, '2025-07-09 15:09:29', '2025-07-09 15:09:29'),
(44, 3, 'logout', 'User', 3, NULL, NULL, NULL, NULL, NULL, '2025-07-09 15:57:54', '2025-07-09 15:57:54'),
(45, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-09 15:58:55', '2025-07-09 15:58:55'),
(46, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-09 16:46:54', '2025-07-09 16:46:54'),
(47, 3, 'login', 'User', 3, NULL, NULL, NULL, NULL, NULL, '2025-07-09 16:47:29', '2025-07-09 16:47:29'),
(48, 2, 'login', 'User', 2, NULL, NULL, NULL, NULL, NULL, '2025-07-17 14:55:41', '2025-07-17 14:55:41'),
(49, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-17 14:57:16', '2025-07-17 14:57:16'),
(50, 1, 'failed_login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-18 07:11:40', '2025-07-18 07:11:40'),
(51, 1, 'failed_login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-18 07:11:47', '2025-07-18 07:11:47'),
(52, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-18 07:11:54', '2025-07-18 07:11:54'),
(53, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-07-18 07:16:03', '2025-07-18 07:16:03'),
(54, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-08-14 08:42:59', '2025-08-14 08:42:59'),
(55, 1, 'logout', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-08-14 08:44:32', '2025-08-14 08:44:32'),
(56, 2, 'login', 'User', 2, NULL, NULL, NULL, NULL, NULL, '2025-08-14 08:45:13', '2025-08-14 08:45:13'),
(57, 2, 'logout', 'User', 2, NULL, NULL, NULL, NULL, NULL, '2025-08-14 08:51:04', '2025-08-14 08:51:04'),
(58, 3, 'login', 'User', 3, NULL, NULL, NULL, NULL, NULL, '2025-08-14 08:51:12', '2025-08-14 08:51:12'),
(59, 3, 'logout', 'User', 3, NULL, NULL, NULL, NULL, NULL, '2025-08-14 08:59:35', '2025-08-14 08:59:35'),
(60, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-08-14 08:59:40', '2025-08-14 08:59:40'),
(61, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-08-21 08:36:49', '2025-08-21 08:36:49'),
(62, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-09-30 06:29:06', '2025-09-30 06:29:06'),
(63, 1, 'failed_login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-08 18:01:43', '2025-10-08 18:01:43'),
(64, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-08 18:01:51', '2025-10-08 18:01:51'),
(65, 1, 'login', 'User', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-12 08:36:57', '2025-10-12 08:36:57');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_levengalvin@gmail.com|127.0.0.1', 'i:1;', 1759957126),
('laravel_cache_levengalvin@gmail.com|127.0.0.1:timer', 'i:1759957126;', 1759957126);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_code` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `credits` int(11) NOT NULL DEFAULT 3,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `instructor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `semester_id` bigint(20) UNSIGNED NOT NULL,
  `schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schedule`)),
  `room` varchar(255) DEFAULT NULL,
  `max_students` int(11) NOT NULL DEFAULT 30,
  `capacity` int(11) DEFAULT NULL,
  `status` enum('active','inactive','completed','cancelled') NOT NULL DEFAULT 'active',
  `syllabus_file` varchar(255) DEFAULT NULL,
  `prerequisites` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`prerequisites`)),
  `fee_amount` decimal(10,2) DEFAULT NULL,
  `learning_objectives` text DEFAULT NULL,
  `assessment_methods` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `code`, `name`, `description`, `credits`, `department_id`, `instructor_id`, `semester_id`, `schedule`, `room`, `max_students`, `capacity`, `status`, `syllabus_file`, `prerequisites`, `fee_amount`, `learning_objectives`, `assessment_methods`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '', 'BIBL879', 'Bio NHY', NULL, 4, 1, 2, 1, NULL, NULL, 30, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2025-07-07 23:50:04', '2025-07-08 05:50:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_date` date NOT NULL,
  `status` enum('enrolled','dropped','completed','failed') NOT NULL DEFAULT 'enrolled',
  `final_grade` decimal(5,2) DEFAULT NULL,
  `letter_grade` varchar(2) DEFAULT NULL,
  `grade_points` decimal(5,2) DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_enrollments`
--

INSERT INTO `course_enrollments` (`id`, `user_id`, `course_id`, `enrollment_date`, `status`, `final_grade`, `letter_grade`, `grade_points`, `completion_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '2025-07-08', 'enrolled', NULL, NULL, NULL, NULL, NULL, '2025-07-08 06:34:01', '2025-07-08 06:34:01');

-- --------------------------------------------------------

--
-- Table structure for table `course_materials`
--

CREATE TABLE `course_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('document','video','audio','link','image','presentation') NOT NULL DEFAULT 'document',
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `external_url` varchar(255) DEFAULT NULL,
  `is_downloadable` tinyint(1) NOT NULL DEFAULT 1,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `available_from` datetime DEFAULT NULL,
  `available_until` datetime DEFAULT NULL,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `access_permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`access_permissions`)),
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_materials`
--

INSERT INTO `course_materials` (`id`, `course_id`, `title`, `description`, `type`, `file_path`, `file_name`, `file_type`, `file_size`, `external_url`, `is_downloadable`, `is_published`, `available_from`, `available_until`, `download_count`, `view_count`, `access_permissions`, `uploaded_by`, `order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Quidem iste aut non', 'Dolore perferendis rerum aliqua Et accusamus cupidatat quia voluptas excepteur enim et eum', 'image', 'course_materials/1/1752063643_blue-red-white-square-badge-simple-medical-clinic-logo.png', 'Blue Red White Square Badge Simple Medical Clinic Logo.png', 'image/png', 15228, NULL, 1, 1, NULL, NULL, 0, 0, NULL, 1, 56, '2025-07-09 09:20:43', '2025-07-09 09:20:43', NULL),
(2, 1, 'Practice self-compassion', 'Speak to yourself the way you would speak to a friend going through a tough time. Being kind to yourself reduces stress, boosts resilience, and improves overall emotional well-being', 'document', 'course_materials/1/1752063802_blue-red-white-square-badge-simple-medical-clinic-logo.pdf', 'Blue Red White Square Badge Simple Medical Clinic Logo.pdf', 'application/pdf', 9120, NULL, 1, 1, NULL, NULL, 0, 0, NULL, 1, 2, '2025-07-09 09:23:22', '2025-07-09 09:23:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faculty_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `head_of_department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `faculty_id`, `name`, `code`, `description`, `head_of_department_id`, `location`, `phone`, `email`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'Biblical Studies', 'BIBL', 'Study of Biblical texts and interpretation', NULL, NULL, NULL, NULL, 1, '2025-05-28 16:42:50', '2025-07-18 07:14:30', NULL),
(2, NULL, 'Theology', 'THEO', 'Systematic study of Christian doctrine', NULL, NULL, NULL, NULL, 1, '2025-05-28 16:42:50', '2025-05-28 16:42:50', NULL),
(3, NULL, 'Church History', 'HIST', 'History of Christianity and the Church', NULL, NULL, NULL, NULL, 1, '2025-05-28 16:42:50', '2025-05-28 16:42:50', NULL),
(4, NULL, 'Christian Ministry', 'MINI', 'Practical ministry and pastoral care', NULL, NULL, NULL, NULL, 1, '2025-05-28 16:42:50', '2025-07-09 14:03:52', NULL),
(5, NULL, 'Biblical Languages', 'LANG', 'Hebrew, Greek, and Aramaic languages', 2, NULL, NULL, NULL, 1, '2025-05-28 16:42:50', '2025-07-18 07:14:17', NULL),
(6, NULL, 'Social Sciences', 'CSD564', 'The Faculty of Social Sciences is dedicated to the study of human behavior, relationships, and society. Through interdisciplinary approaches, it explores how individuals and communities interact, how societies are structured, and how policies and institutions influence everyday life.\r\n\r\nPrograms within Social Sciences often include disciplines such as Psychology, Sociology, Anthropology, Political Science, Economics, and Human Geography, offering students critical thinking, research, and analytical skills that are applicable across various sectors.\r\n\r\nThe faculty prepares students to address real-world challenges by fostering a deep understanding of cultural, political, and economic systems — equipping them to contribute meaningfully to both local and global communities.', 2, 'Law street', '+1 (403) 596-8216', 'zukotan@mailinator.com', 1, '2025-07-09 13:56:47', '2025-07-09 14:03:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `dean_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `contact_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`contact_info`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`id`, `name`, `code`, `description`, `dean_id`, `location`, `phone`, `email`, `website`, `contact_info`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Madeson Baldwin', 'ULLAM NULL', 'Ex obcaecati dolorum', 2, 'Reprehenderit enim e', '+1 (129) 429-9709', 'qynedy@mailinator.com', 'https://www.neqyrow.in', NULL, 1, '2025-07-09 13:55:57', '2025-07-09 16:35:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_profiles`
--

CREATE TABLE `faculty_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position` varchar(255) NOT NULL DEFAULT 'Faculty Member',
  `hire_date` date DEFAULT NULL,
  `designation` varchar(255) DEFAULT 'Faculty Member',
  `qualification` varchar(255) DEFAULT 'Bachelor''s Degree',
  `specialization` varchar(255) DEFAULT NULL,
  `qualifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`qualifications`)),
  `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`documents`)),
  `application_status` enum('submitted','under_review','approved','rejected') NOT NULL DEFAULT 'submitted',
  `experience` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`experience`)),
  `application_notes` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `employment_type` varchar(255) DEFAULT 'full_time',
  `employment_status` enum('pending','active','inactive','on_leave','terminated','retired') NOT NULL DEFAULT 'pending',
  `salary` decimal(10,2) DEFAULT NULL,
  `office_location` varchar(255) DEFAULT NULL,
  `office_hours` varchar(255) DEFAULT NULL,
  `research_interests` text DEFAULT NULL,
  `publications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`publications`)),
  `certifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certifications`)),
  `years_of_experience` int(11) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `linkedin_profile` varchar(255) DEFAULT NULL,
  `personal_website` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculty_profiles`
--

INSERT INTO `faculty_profiles` (`id`, `user_id`, `employee_id`, `department_id`, `position`, `hire_date`, `designation`, `qualification`, `specialization`, `qualifications`, `documents`, `application_status`, `experience`, `application_notes`, `notes`, `joining_date`, `employment_type`, `employment_status`, `salary`, `office_location`, `office_hours`, `research_interests`, `publications`, `certifications`, `years_of_experience`, `bio`, `linkedin_profile`, `personal_website`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'EMP20250001', 1, 'Faculty Member', '2025-07-08', 'Faculty Member', 'Masters', 'Law', '{\"highest_degree\":\"Masters\",\"institution\":\"Kamp\",\"graduation_year\":\"2006\",\"specialization\":\"Law\",\"certifications\":[]}', NULL, 'submitted', '{\"years_of_experience\":\"20\",\"previous_positions\":[],\"research_interests\":[]}', NULL, NULL, '2025-07-08', 'full_time', 'active', NULL, NULL, NULL, NULL, NULL, NULL, 20, NULL, NULL, NULL, 'active', '2025-07-07 23:34:03', '2025-07-09 12:58:53'),
(2, 11, 'FAC20250002', 4, 'At nostrum ut suscip', '2025-07-09', 'At nostrum ut suscip', 'Iste inventore magna', 'Id ut minim fuga V', '{\"highest_degree\":\"Iste inventore magna\",\"institution\":\"Dolorem elit vel ma\",\"graduation_year\":\"1989\",\"specialization\":\"Id ut minim fuga V\",\"certifications\":[\"Quasi cupidatat ipsu\"]}', NULL, 'approved', '{\"years_of_experience\":\"20\",\"research_interests\":[\"Ut autem exercitatio\"]}', NULL, NULL, '2025-07-09', 'visiting', 'active', NULL, NULL, NULL, NULL, NULL, NULL, 20, 'Eos eveniet error', 'https://www.falebihaw.me.uk', 'https://www.bic.ca', 'active', '2025-07-09 14:20:20', '2025-07-09 14:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_records`
--

CREATE TABLE `fee_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `fee_structure_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `late_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','partial','paid','overdue','cancelled') NOT NULL DEFAULT 'pending',
  `due_date` date NOT NULL,
  `paid_date` date DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_notes` text DEFAULT NULL,
  `payment_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_history`)),
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_records`
--

INSERT INTO `fee_records` (`id`, `user_id`, `fee_structure_id`, `invoice_number`, `amount`, `discount_amount`, `late_fee`, `total_amount`, `paid_amount`, `balance_amount`, `status`, `due_date`, `paid_date`, `payment_method`, `transaction_id`, `payment_notes`, `payment_history`, `processed_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 10, 7, 'INV-20250930-000001', 19.00, 0.00, 0.00, 19.00, 19.00, 0.00, 'paid', '2025-08-09', '2025-09-30', 'cash', NULL, NULL, '[{\"amount\":\"19.00\",\"date\":\"2025-09-30\",\"method\":\"cash\",\"transaction_id\":null,\"notes\":null,\"processed_by\":1,\"processed_at\":\"2025-09-30T09:45:32.890741Z\"}]', 1, '2025-09-30 06:29:51', '2025-09-30 06:45:32', NULL),
(2, 10, 10, 'INV-20251012-000002', 75.00, 0.00, 0.00, 75.00, 65.00, 10.00, 'partial', '2025-12-28', NULL, 'check', 'ertyuiouy', NULL, '[{\"amount\":\"45\",\"date\":\"2025-10-12\",\"method\":\"cash\",\"transaction_id\":\"36728GHYJ\",\"notes\":\"Next Payment will be recieved next month\",\"processed_by\":1,\"processed_at\":\"2025-10-12T11:42:53.571428Z\"},{\"amount\":\"20.00\",\"date\":\"2025-10-12\",\"method\":\"check\",\"transaction_id\":\"ertyuiouy\",\"notes\":null,\"processed_by\":1,\"processed_at\":\"2025-10-12T11:43:27.771032Z\"}]', 1, '2025-10-12 08:37:42', '2025-10-12 08:43:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fee_structures`
--

CREATE TABLE `fee_structures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('tuition','library','laboratory','technology','activity','other') NOT NULL DEFAULT 'other',
  `amount` decimal(10,2) NOT NULL,
  `frequency` enum('one_time','semester','monthly','annual') NOT NULL DEFAULT 'semester',
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `semester_id` bigint(20) UNSIGNED DEFAULT NULL,
  `applicable_to` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_to`)),
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `due_date` date DEFAULT NULL,
  `late_fee_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `late_fee_days` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_structures`
--

INSERT INTO `fee_structures` (`id`, `name`, `description`, `type`, `amount`, `frequency`, `academic_year_id`, `semester_id`, `applicable_to`, `is_mandatory`, `is_active`, `due_date`, `late_fee_amount`, `late_fee_days`, `created_at`, `updated_at`) VALUES
(1, 'Ishmael Kramer', 'Culpa qui vel excep', 'tuition', 970.00, 'semester', 1, 2, NULL, 1, 1, '2025-12-17', 75.00, 23, '2025-08-21 09:27:50', '2025-08-21 09:27:50'),
(2, 'Ishmael Kramer', 'Culpa qui vel excep', 'tuition', 970.00, 'semester', 1, 2, NULL, 1, 1, '2025-12-17', 75.00, 23, '2025-08-21 09:39:01', '2025-08-21 09:39:01'),
(3, 'Jane Bush', 'Quis et aspernatur d', 'laboratory', 19.00, 'annual', 1, 3, NULL, 1, 1, '2025-08-09', 72.00, 8, '2025-08-21 09:39:34', '2025-08-21 09:39:34'),
(4, 'Jane Bush', 'Quis et aspernatur d', 'laboratory', 19.00, 'semester', 1, 3, NULL, 1, 1, '2025-08-09', 72.00, 8, '2025-08-21 09:39:48', '2025-08-21 09:39:48'),
(5, 'Jane Bush', 'Quis et aspernatur d', 'laboratory', 19.00, 'semester', 1, 3, NULL, 1, 1, '2025-08-09', 72.00, 8, '2025-08-21 09:41:00', '2025-08-21 09:41:00'),
(6, 'Jane Bush', 'Quis et aspernatur d', 'laboratory', 19.00, 'semester', 1, 3, NULL, 1, 1, '2025-08-09', 72.00, 8, '2025-08-21 09:42:55', '2025-08-21 09:42:55'),
(7, 'Jane Bush', 'Quis et aspernatur d', 'laboratory', 19.00, 'semester', 1, 3, NULL, 1, 1, '2025-08-09', 72.00, 8, '2025-08-21 09:44:12', '2025-08-21 09:44:12'),
(8, 'Good And Services', 'Tenetur provident i', 'laboratory', 75.00, 'one_time', 1, 3, NULL, 1, 1, '2025-12-28', 0.00, 27, '2025-09-30 08:17:54', '2025-09-30 08:17:54'),
(9, 'Good And Services', 'Tenetur provident i', 'laboratory', 75.00, 'one_time', 1, 3, NULL, 1, 1, '2025-12-28', 0.00, 27, '2025-09-30 08:18:02', '2025-09-30 08:18:02'),
(10, 'Good And Services', 'Tenetur provident i', 'laboratory', 75.00, 'one_time', 1, 3, NULL, 1, 1, '2025-12-28', 0.00, 27, '2025-09-30 08:18:18', '2025-09-30 08:18:18');

-- --------------------------------------------------------

--
-- Table structure for table `forums`
--

CREATE TABLE `forums` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('course','department','general','announcement') NOT NULL DEFAULT 'general',
  `access_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`access_roles`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `allow_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `moderated` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE `forum_replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `topic_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content` text NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `likes_count` int(11) NOT NULL DEFAULT 0,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

CREATE TABLE `forum_topics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `forum_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `replies_count` int(11) NOT NULL DEFAULT 0,
  `last_reply_at` datetime DEFAULT NULL,
  `last_reply_by` bigint(20) UNSIGNED DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `assignment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade_type` varchar(255) NOT NULL,
  `points_earned` decimal(5,2) NOT NULL,
  `points_possible` decimal(5,2) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `letter_grade` varchar(2) DEFAULT NULL,
  `grade_points` decimal(5,2) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `graded_at` datetime NOT NULL,
  `graded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000002_create_cache_table', 1),
(2, '0001_01_01_000003_create_jobs_table', 1),
(3, '0001_01_01_00001_create_users_table', 1),
(4, '2024_01_15_000001_create_departments_table', 1),
(5, '2024_01_15_000002_create_academic_years_table', 1),
(6, '2024_01_15_000003_create_semesters_table', 1),
(7, '2024_01_15_000004_create_courses_table', 1),
(8, '2024_01_15_000005_create_course_enrollments_table', 1),
(9, '2024_01_15_000006_create_assignments_table', 1),
(10, '2024_01_15_000007_create_assignment_submissions_table', 1),
(11, '2024_01_15_000008_create_grades_table', 1),
(12, '2024_01_15_000009_create_attendance_table', 1),
(13, '2024_01_15_000010_create_course_materials_table', 1),
(14, '2024_01_15_000011_create_announcements_table', 1),
(15, '2024_01_15_000012_create_fee_structures_table', 1),
(16, '2024_01_15_000013_create_fee_records_table', 1),
(17, '2024_01_15_000014_create_forums_table', 1),
(18, '2024_01_15_000015_create_forum_topics_table', 1),
(19, '2024_01_15_000016_create_forum_replies_table', 1),
(20, '2024_01_15_000017_create_notifications_table', 1),
(21, '2024_01_15_000018_create_system_settings_table', 1),
(22, '2024_01_15_000019_create_audit_logs_table', 1),
(23, '2024_01_15_000020_create_student_profiles_table', 1),
(24, '2024_01_15_000021_create_faculty_profiles_table', 1),
(25, '2025_05_28_151615_create_sessions_table', 1),
(26, '2025_05_28_182646_add_force_password_reset_to_users_table', 1),
(27, '2024_01_16_000001_add_email_verification_fields_to_users_table', 2),
(28, '2024_01_16_000002_add_default_password_to_users_table', 3),
(29, '2024_01_16_000003_add_missing_fields_to_student_profiles_table', 4),
(30, '2024_01_16_000004_add_missing_fields_to_faculty_profiles_table', 5),
(31, '2024_01_16_000005_add_employment_status_to_faculty_profiles_table', 6),
(32, '2025_07_07_225321_add_employee_id_to_faculty_profiles_table', 7),
(33, '2025_07_08_020202_fix_faculty_profiles_table_structure', 8),
(35, '2025_07_08_021657_fix_faculty_profiles_qualification_field', 9),
(36, '2025_07_08_094958_add_capacity_field_to_courses_table', 10),
(37, '2025_07_08_101450_create_student_notes_table', 11),
(38, '2025_07_09_114257_add_order_column_to_course_materials_table', 12),
(39, '2025_07_09_160828_create_faculties_table', 13),
(40, '2025_07_09_161106_add_faculty_id_to_departments_table', 14),
(41, '2025_07_09_165938_fix_department_head_column_name', 15);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `action_url` varchar(255) DEFAULT NULL,
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sms_sent` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `scheduled_for` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `registration_start` date DEFAULT NULL,
  `registration_end` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `academic_year_id`, `name`, `start_date`, `end_date`, `registration_start`, `registration_end`, `is_current`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Spring', '2025-01-15', '2025-05-15', '2024-12-16', '2025-01-08', 1, 1, '2025-05-28 17:13:26', '2025-05-28 17:13:26'),
(2, 1, 'Fall', '2025-08-15', '2025-12-15', '2025-07-01', '2025-08-10', 1, 1, '2025-05-28 17:13:37', '2025-05-28 17:13:37'),
(3, 1, 'Summer', '2026-06-01', '2026-07-31', '2026-04-01', '2026-05-25', 0, 1, '2025-05-28 17:13:37', '2025-05-28 17:13:37');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('9rcRbGbawhTtykuRBCdGsFHvRUsAZmZwJKMzSRg6', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia05kUVRUb0h4TTRaYW1JMUtjeTdpM0oxckx2NzA1cko0NzYzNk9XdSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNToiaHR0cDovLzEyNy4wLjAuMTo4MDAxL2FkbWluL3JlcG9ydHMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAxL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1760087124),
('9XS3J36GyxhHOzNlizzraOxL6AVDwlScvsO4E5aE', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWGZLRnppZnE0QXZ1WHljaktBaEJ3YlBkRWhWWkdDTlBKbWhlT2pIZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9mZWVzL3N0cnVjdHVyZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc1NTc3NjIwOTt9fQ==', 1755780859),
('cDMvKXsavZSQuenhvivFCkMPkn57hUdiABChcDkc', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZGtFZ0c1eEptVVRvUjE5NUxEVTFkdXpyaG02Rm9CbnJXZVM1NENLMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMi9hZG1pbi91c2VycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzU5MjI0NTQ2O319', 1759231533),
('JFS0EnE00bKcynrW02DAXyjmm8tZvqjGe1y36nUb', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibDJTUFREMU41YjRLM3V1NmJWbVpUazhXbG9udm11UkFMTDhjV2x5VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1761048211),
('UNc04Wpeu8aA9HzLeAR8ixoDOJlkXSGgIvIDw4w3', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibXdDc3pEc3lvbWVrRFFWY090TWF0UFZjNzlndk92ZVpSVXdic2gzciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZXBvcnRzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NjAyNjkwMTc7fX0=', 1760269714),
('X6QizO1IIWxT0KdQxQyFWTRv3Jo8VU1xMBTcPGa3', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaUtxTHlmT3JzQ3BDc2FDV29OcVQ3SWZ1dHVRUTNQOXRDMTdIT3h1ZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMS9hZG1pbi9yZXBvcnRzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NTk5NTczMTE7fX0=', 1759959800);

-- --------------------------------------------------------

--
-- Table structure for table `student_notes`
--

CREATE TABLE `student_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `note` text NOT NULL,
  `type` enum('general','academic','disciplinary','counseling','medical') NOT NULL DEFAULT 'general',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `noted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `admission_number` varchar(255) NOT NULL,
  `admission_date` date NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `program` varchar(255) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `current_semester` int(11) NOT NULL DEFAULT 1,
  `status` enum('pending','active','inactive','graduated','dropped','suspended') NOT NULL DEFAULT 'pending',
  `application_status` enum('submitted','under_review','approved','rejected') NOT NULL DEFAULT 'submitted',
  `current_gpa` decimal(3,2) NOT NULL DEFAULT 0.00,
  `cumulative_gpa` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_credits_earned` int(11) NOT NULL DEFAULT 0,
  `total_credits_required` int(11) NOT NULL DEFAULT 120,
  `expected_graduation_date` date DEFAULT NULL,
  `actual_graduation_date` date DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_phone` varchar(255) DEFAULT NULL,
  `guardian_email` varchar(255) DEFAULT NULL,
  `guardian_address` text DEFAULT NULL,
  `previous_school` varchar(255) DEFAULT NULL,
  `previous_school_address` varchar(255) DEFAULT NULL,
  `graduation_year` year(4) DEFAULT NULL,
  `previous_gpa` decimal(3,2) DEFAULT NULL,
  `academic_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`academic_history`)),
  `qualifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`qualifications`)),
  `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`documents`)),
  `application_notes` text DEFAULT NULL,
  `achievements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`achievements`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `user_id`, `admission_number`, `admission_date`, `department_id`, `program`, `specialization`, `current_semester`, `status`, `application_status`, `current_gpa`, `cumulative_gpa`, `total_credits_earned`, `total_credits_required`, `expected_graduation_date`, `actual_graduation_date`, `guardian_name`, `guardian_phone`, `guardian_email`, `guardian_address`, `previous_school`, `previous_school_address`, `graduation_year`, `previous_gpa`, `academic_history`, `qualifications`, `documents`, `application_notes`, `achievements`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 'JBI20250001', '2025-06-15', 1, 'Bachelor of Arts in Biblical Studies', NULL, 1, 'active', 'submitted', 0.00, 0.00, 0, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-15 07:22:18', '2025-06-15 07:57:07'),
(3, 7, 'JBI20250002', '2025-06-15', 1, 'Master of Arts', NULL, 1, 'pending', 'submitted', 0.00, 0.00, 0, 120, '2027-06-15', NULL, 'Clayton Randall', '+1 (302) 214-4548', 'xikepi@mailinator.com', 'Itaque aliqua Minim', 'Qui eiusmod voluptas', 'Libero qui sit alia', '1997', 1.00, '{\"high_school\":{\"name\":\"Qui eiusmod voluptas\",\"address\":\"Libero qui sit alia\",\"graduation_year\":\"1997\",\"gpa\":\"1\",\"major_subjects\":[\"Quam pariatur Autem\"]}}', '{\"high_school_diploma\":false,\"sat_score\":\"819\",\"act_score\":\"18\",\"toefl_score\":\"30\",\"ielts_score\":\"1\",\"other_certifications\":[\"Aut minim autem cum\"]}', '[\"student-documents\\/eNtOVyxAIaPS7P9JR3zJw2vly2bQoE8Zmbru6l3V.pdf\",\"student-documents\\/MUQPH4cwgbrbxn1xcmkFJMw58rHKjpZ2zIS1xE87.png\",\"student-documents\\/SVSo44qehpDMM47Qu74bwtutEq0lDcqLn5W90SZb.jpg\",\"student-documents\\/KdZxdKjRmwH3mcgzZUrdTVvhPdxfwkioF117W4kk.jpg\"]', 'Qui id tempore cons', NULL, 'Application submitted on 2025-06-15 13:58:48', '2025-06-15 10:58:48', '2025-06-15 10:58:48'),
(4, 8, 'JBI20250003', '2025-06-15', 4, 'Master of Divinity', NULL, 1, 'pending', 'submitted', 0.00, 0.00, 0, 120, '2028-06-15', NULL, 'Gregory Espinoza', '+1 (952) 536-4685', 'togaham@mailinator.com', 'Vitae hic totam volu', 'Sed voluptate pariat', 'Vel eligendi et dict', '2017', 2.00, '{\"high_school\":{\"name\":\"Sed voluptate pariat\",\"address\":\"Vel eligendi et dict\",\"graduation_year\":\"2017\",\"gpa\":\"2\",\"major_subjects\":[\"Reiciendis soluta en\"]}}', '{\"high_school_diploma\":false,\"sat_score\":\"1587\",\"act_score\":\"23\",\"toefl_score\":\"92\",\"ielts_score\":\"1\",\"other_certifications\":[\"Repudiandae aperiam\"]}', '[]', 'Eiusmod quia nesciun', NULL, 'Application submitted on 2025-06-15 14:15:06', '2025-06-15 11:15:06', '2025-06-15 11:15:06'),
(6, 10, 'JBI20250005', '2025-06-15', 2, 'Bachelor of Theology', NULL, 1, 'active', 'approved', 0.00, 0.00, 0, 120, '2029-06-15', NULL, 'Bernard Klein', '0789000987', 'levengalvin@gmail.com', 'Plot 76 Bombo Road, Nalubega Com', 'Qui eiusmod voluptas', 'Plot 76 Bombo Road, Nalubega Com', '2017', 3.40, '{\"high_school\":{\"name\":\"Qui eiusmod voluptas\",\"address\":\"Plot 76 Bombo Road, Nalubega Com\",\"graduation_year\":\"2017\",\"gpa\":\"3.4\",\"major_subjects\":[\"Phy\",\"Maths\"]}}', '{\"high_school_diploma\":true,\"sat_score\":null,\"act_score\":null,\"toefl_score\":null,\"ielts_score\":null,\"other_certifications\":[]}', '[\"student-documents\\/jPMPBeoAbwfmwk4LC9SmoS4Q9kIeogD7p8dCzMJi.pdf\",\"student-documents\\/ukRPJf7jueRAwmIhDKLnVdnFzr8QVFP5bKhfTub5.png\",\"student-documents\\/5xW8narFDQLyujLjaTystdO26cyF66XloGcN2m29.jpg\"]', 'the nabsyye hehhaykskskk', NULL, 'Application submitted on 2025-06-15 17:49:12\nApproved on 2025-06-15 17:57:53 by System Administrator. This you the', '2025-06-15 14:49:12', '2025-06-15 14:57:53');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `first_name` varchar(256) DEFAULT NULL,
  `last_name` varchar(256) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `default_password` varchar(255) DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `force_password_reset` tinyint(1) NOT NULL DEFAULT 0,
  `role` enum('admin','faculty','student','parent') NOT NULL DEFAULT 'student',
  `student_id` varchar(255) DEFAULT NULL,
  `employee_id` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `emergency_phone` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferences`)),
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `emergency_contact_name` varchar(256) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `first_name`, `last_name`, `email`, `email_verified_at`, `password`, `default_password`, `must_change_password`, `password_changed_at`, `force_password_reset`, `role`, `student_id`, `employee_id`, `phone`, `address`, `date_of_birth`, `gender`, `emergency_contact`, `emergency_phone`, `profile_picture`, `is_active`, `preferences`, `last_login_at`, `remember_token`, `emergency_contact_name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'System Administrator', NULL, NULL, 'admin@admin.com', NULL, '$2y$12$dUHBpdJWx5dozJkOdUtOweZoE/fAbRxq0gj6jLvSY8w1F/5fLLzMm', NULL, 0, NULL, 0, 'admin', NULL, 'JBI001', '+1-555-0101', '123 Admin Street, JBI Campus', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-05-28 16:42:50', '2025-05-28 22:03:50', NULL),
(2, 'Lawcom Normal', 'Lawcom', 'Normal', 'faculty@jbiuniversity.com', NULL, '$2y$12$7leTZa/mSIEtTjbJ34Cqnuj7LdKbsG9jcS/WgVmXW9Xlg3u7GTRUW', NULL, 0, NULL, 0, 'faculty', NULL, 'JBI002', '+1-555-0102', '456 Faculty Lane, JBI Campus', '1975-05-15', 'male', '0789078967', '0789000987', NULL, 1, NULL, NULL, NULL, NULL, '2025-05-28 16:42:50', '2025-07-09 12:58:53', NULL),
(3, 'Jane Student', 'Jane', 'Student', 'student@jbiuniversity.com', NULL, '$2y$12$7leTZa/mSIEtTjbJ34Cqnuj7LdKbsG9jcS/WgVmXW9Xlg3u7GTRUW', NULL, 0, NULL, 0, 'student', 'JBI2024001', '', '+1-555-0102', '789 Student Drive, JBI Campus', '2000-08-20', 'female', NULL, 'Mary Student (Mother)', NULL, 0, NULL, NULL, NULL, NULL, '2025-05-28 16:42:50', '2025-07-09 14:58:24', NULL),
(7, 'Brett Sexton', 'Brett', 'Sexton', 'rije@mailinator.com', NULL, '$2y$12$PuHbTD3UwvAQjDsL8ufKh.Of5qZ//XM0/PZm.jIkHE.VppNUqxYXy', NULL, 0, '2025-06-15 10:58:48', 0, 'student', NULL, NULL, '+1 (831) 753-5605', 'Autem consectetur mo', '1994-02-05', 'female', 'Idola Faulkner', '+1 (488) 832-1694', NULL, 0, NULL, NULL, NULL, NULL, '2025-06-15 10:58:48', '2025-06-15 10:58:48', NULL),
(8, 'Marshall Stanton', 'Marshall', 'Stanton', 'regawoqig@mailinator.com', NULL, '$2y$12$WD11v6enpEEv7nrfiOdeS.Fa1SLDUin5oPBYNCZ35Wy6TU2OKGdYe', NULL, 0, '2025-06-15 11:15:06', 0, 'student', NULL, NULL, '+1 (105) 207-8576', 'Voluptas aperiam et', '2001-04-29', 'other', 'Farrah Singleton', '+1 (963) 942-5032', NULL, 0, NULL, NULL, NULL, NULL, '2025-06-15 11:15:06', '2025-06-15 11:15:06', NULL),
(10, 'Hy Kin', 'Hy', 'Kin', 'herbert@johnsonbibleinstitute.com', '2025-06-15 14:53:36', '$2y$12$JO6YQrU.ymCkrRnD5xiR0.8b1D7pmUOHU0VWP/BSV0jxxUN8lLdke', 'JBI20252Zt1vI!', 1, '2025-06-15 14:57:53', 0, 'student', NULL, NULL, '0789098769', 'Plot 76 Bombo Road, Nalubega Com\r\nVoluptas quibusdam i', '2004-07-15', 'male', 'Gyu', '0702987654', 'profile-pictures/gNckCeeA7rXyUiGE1ZHNVld66jHjuSrpIDI7jmD6.jpg', 1, NULL, NULL, NULL, NULL, '2025-06-15 14:49:12', '2025-06-15 14:57:53', NULL),
(11, 'Randall Santos', 'Randall', 'Santos', 'jyjuvoreni@mailinator.com', '2025-07-09 14:20:20', '$2y$12$LIq6/JsBOZALz1nmq4RkzOXrFrxUuRcSAq2q1rY5622U7J5MVYQSy', NULL, 1, '2025-07-09 14:20:20', 0, 'faculty', NULL, NULL, '+1 (589) 442-8706', 'Dignissimos rerum ve', '1972-10-15', 'other', 'Accusamus nemo atque', '+1 (199) 961-2761', NULL, 1, NULL, NULL, NULL, NULL, '2025-07-09 14:20:20', '2025-07-09 14:20:20', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD KEY `academic_years_year_is_current_index` (`year`,`is_current`),
  ADD KEY `academic_years_is_active_index` (`is_active`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_created_by_foreign` (`created_by`),
  ADD KEY `announcements_type_is_published_index` (`type`,`is_published`),
  ADD KEY `announcements_course_id_is_published_index` (`course_id`,`is_published`),
  ADD KEY `announcements_department_id_is_published_index` (`department_id`,`is_published`),
  ADD KEY `announcements_published_at_expires_at_index` (`published_at`,`expires_at`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignments_course_id_due_date_index` (`course_id`,`due_date`),
  ADD KEY `assignments_course_id_is_published_index` (`course_id`,`is_published`),
  ADD KEY `assignments_due_date_index` (`due_date`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_assn_subm_id_user_atnum` (`assignment_id`,`user_id`,`attempt_number`),
  ADD KEY `assignment_submissions_graded_by_foreign` (`graded_by`),
  ADD KEY `assignment_submissions_assignment_id_status_index` (`assignment_id`,`status`),
  ADD KEY `assignment_submissions_user_id_submitted_at_index` (`user_id`,`submitted_at`),
  ADD KEY `assignment_submissions_submitted_at_index` (`submitted_at`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_user_id_course_id_attendance_date_unique` (`user_id`,`course_id`,`attendance_date`),
  ADD KEY `attendance_marked_by_foreign` (`marked_by`),
  ADD KEY `attendance_course_id_attendance_date_index` (`course_id`,`attendance_date`),
  ADD KEY `attendance_user_id_attendance_date_index` (`user_id`,`attendance_date`),
  ADD KEY `attendance_attendance_date_status_index` (`attendance_date`,`status`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `audit_logs_action_created_at_index` (`action`,`created_at`);

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
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_code_unique` (`code`),
  ADD UNIQUE KEY `courses_course_code_unique` (`course_code`),
  ADD KEY `courses_semester_id_foreign` (`semester_id`),
  ADD KEY `courses_code_semester_id_index` (`code`,`semester_id`),
  ADD KEY `courses_instructor_id_status_index` (`instructor_id`,`status`),
  ADD KEY `courses_department_id_status_index` (`department_id`,`status`),
  ADD KEY `courses_status_index` (`status`);

--
-- Indexes for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_enrollments_user_id_course_id_unique` (`user_id`,`course_id`),
  ADD KEY `course_enrollments_user_id_status_index` (`user_id`,`status`),
  ADD KEY `course_enrollments_course_id_status_index` (`course_id`,`status`);

--
-- Indexes for table `course_materials`
--
ALTER TABLE `course_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_materials_course_id_type_index` (`course_id`,`type`),
  ADD KEY `course_materials_course_id_is_published_index` (`course_id`,`is_published`),
  ADD KEY `course_materials_uploaded_by_index` (`uploaded_by`),
  ADD KEY `course_materials_order_index` (`order`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_code_unique` (`code`),
  ADD KEY `departments_head_of_department_id_foreign` (`head_of_department_id`),
  ADD KEY `departments_code_index` (`code`),
  ADD KEY `departments_is_active_index` (`is_active`),
  ADD KEY `departments_faculty_id_index` (`faculty_id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faculties_code_unique` (`code`),
  ADD KEY `faculties_dean_id_foreign` (`dean_id`),
  ADD KEY `faculties_is_active_name_index` (`is_active`,`name`);

--
-- Indexes for table `faculty_profiles`
--
ALTER TABLE `faculty_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faculty_profiles_employee_id_unique` (`employee_id`),
  ADD KEY `faculty_profiles_user_id_status_index` (`user_id`,`status`),
  ADD KEY `faculty_profiles_department_id_status_index` (`department_id`,`status`),
  ADD KEY `faculty_profiles_designation_status_index` (`designation`,`status`),
  ADD KEY `faculty_profiles_application_status_index` (`application_status`),
  ADD KEY `faculty_profiles_employee_id_index` (`employee_id`),
  ADD KEY `faculty_profiles_employment_status_index` (`employment_status`),
  ADD KEY `faculty_profiles_employment_status_idx` (`employment_status`),
  ADD KEY `faculty_profiles_application_status_idx` (`application_status`),
  ADD KEY `faculty_profiles_dept_employment_idx` (`department_id`,`employment_status`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fee_records`
--
ALTER TABLE `fee_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fee_records_invoice_number_unique` (`invoice_number`),
  ADD KEY `fee_records_processed_by_foreign` (`processed_by`),
  ADD KEY `fee_records_user_id_status_index` (`user_id`,`status`),
  ADD KEY `fee_records_fee_structure_id_status_index` (`fee_structure_id`,`status`),
  ADD KEY `fee_records_due_date_status_index` (`due_date`,`status`),
  ADD KEY `fee_records_invoice_number_index` (`invoice_number`);

--
-- Indexes for table `fee_structures`
--
ALTER TABLE `fee_structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_structures_academic_year_id_is_active_index` (`academic_year_id`,`is_active`),
  ADD KEY `fee_structures_semester_id_is_active_index` (`semester_id`,`is_active`),
  ADD KEY `fee_structures_type_is_active_index` (`type`,`is_active`);

--
-- Indexes for table `forums`
--
ALTER TABLE `forums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forums_created_by_foreign` (`created_by`),
  ADD KEY `forums_course_id_is_active_index` (`course_id`,`is_active`),
  ADD KEY `forums_department_id_is_active_index` (`department_id`,`is_active`),
  ADD KEY `forums_type_is_active_index` (`type`,`is_active`);

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forum_replies_topic_id_is_approved_index` (`topic_id`,`is_approved`),
  ADD KEY `forum_replies_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `forum_replies_parent_id_index` (`parent_id`);

--
-- Indexes for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forum_topics_last_reply_by_foreign` (`last_reply_by`),
  ADD KEY `forum_topics_forum_id_is_approved_index` (`forum_id`,`is_approved`),
  ADD KEY `forum_topics_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `forum_topics_is_pinned_last_reply_at_index` (`is_pinned`,`last_reply_at`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grades_assignment_id_foreign` (`assignment_id`),
  ADD KEY `grades_graded_by_foreign` (`graded_by`),
  ADD KEY `grades_user_id_course_id_index` (`user_id`,`course_id`),
  ADD KEY `grades_course_id_grade_type_index` (`course_id`,`grade_type`),
  ADD KEY `grades_user_id_graded_at_index` (`user_id`,`graded_at`),
  ADD KEY `grades_is_published_index` (`is_published`);

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`),
  ADD KEY `notifications_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `notifications_type_scheduled_for_index` (`type`,`scheduled_for`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semesters_academic_year_id_is_current_index` (`academic_year_id`,`is_current`),
  ADD KEY `semesters_is_active_index` (`is_active`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `student_notes`
--
ALTER TABLE `student_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_notes_created_by_foreign` (`created_by`),
  ADD KEY `student_notes_student_id_created_at_index` (`student_id`,`created_at`),
  ADD KEY `student_notes_type_priority_index` (`type`,`priority`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_profiles_admission_number_unique` (`admission_number`),
  ADD KEY `student_profiles_user_id_status_index` (`user_id`,`status`),
  ADD KEY `student_profiles_department_id_status_index` (`department_id`,`status`),
  ADD KEY `student_profiles_admission_number_index` (`admission_number`),
  ADD KEY `student_profiles_current_semester_status_index` (`current_semester`,`status`),
  ADD KEY `student_profiles_application_status_index` (`application_status`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`),
  ADD KEY `system_settings_group_key_index` (`group`,`key`),
  ADD KEY `system_settings_is_public_index` (`is_public`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_student_id_unique` (`student_id`),
  ADD UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  ADD KEY `users_role_is_active_index` (`role`,`is_active`),
  ADD KEY `users_student_id_index` (`student_id`),
  ADD KEY `users_employee_id_index` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_materials`
--
ALTER TABLE `course_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faculty_profiles`
--
ALTER TABLE `faculty_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_records`
--
ALTER TABLE `fee_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fee_structures`
--
ALTER TABLE `fee_structures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `forums`
--
ALTER TABLE `forums`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_notes`
--
ALTER TABLE `student_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcements_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_graded_by_foreign` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `assignment_submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_marked_by_foreign` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `courses_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courses_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `course_enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_enrollments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_materials`
--
ALTER TABLE `course_materials`
  ADD CONSTRAINT `course_materials_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_materials_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `departments_head_of_department_id_foreign` FOREIGN KEY (`head_of_department_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faculties`
--
ALTER TABLE `faculties`
  ADD CONSTRAINT `faculties_dean_id_foreign` FOREIGN KEY (`dean_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faculty_profiles`
--
ALTER TABLE `faculty_profiles`
  ADD CONSTRAINT `faculty_profiles_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faculty_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_records`
--
ALTER TABLE `fee_records`
  ADD CONSTRAINT `fee_records_fee_structure_id_foreign` FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_records_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fee_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_structures`
--
ALTER TABLE `fee_structures`
  ADD CONSTRAINT `fee_structures_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_structures_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forums`
--
ALTER TABLE `forums`
  ADD CONSTRAINT `forums_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forums_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forums_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD CONSTRAINT `forum_replies_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `forum_replies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_replies_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD CONSTRAINT `forum_topics_forum_id_foreign` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_topics_last_reply_by_foreign` FOREIGN KEY (`last_reply_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `forum_topics_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_graded_by_foreign` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `semesters`
--
ALTER TABLE `semesters`
  ADD CONSTRAINT `semesters_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_notes`
--
ALTER TABLE `student_notes`
  ADD CONSTRAINT `student_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_notes_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
