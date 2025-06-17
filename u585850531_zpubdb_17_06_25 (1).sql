-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 17, 2025 at 10:42 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u585850531_zpubdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adminsreg`
--

DROP TABLE IF EXISTS `adminsreg`;
CREATE TABLE IF NOT EXISTS `adminsreg` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `adminsreg`
--

INSERT INTO `adminsreg` (`id`, `username`, `password`, `created_at`) VALUES
(13, 'haripriya', '$2y$10$isoheW4RMcMwJJe/y7FWg.UOzczH1CVko./Q7IXvMy46/PXSFDnry', '2025-05-25 23:47:17'),
(12, 'ashika', '$2y$10$Km/fjyuRGPEfcGSuYw4gfuDBUFCCA04okqirs2wlvCAz5VzpyKJW2', '2025-04-09 07:22:02'),
(11, 'Haripriya K T', '$2y$10$aLCNEdMtXYxzEhWitFHhee9EIxpSQTTp.NSWU.pnau6J8xl1.W1Wa', '2025-04-09 07:14:45'),
(10, 'hi', '$2y$10$ciQRtGN8TLqmdlvh/Acjxeo4xNEw7GKoBt087mjmJu33nhUrjGZYi', '2025-04-09 06:09:39'),
(9, 'aarya', '$2y$10$D0EaBC7wmvNmY6iMU6yl7OzttEk4h0W2n5s1FOcCc9Ka8GXCHagLm', '2025-04-09 04:18:12');

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

DROP TABLE IF EXISTS `author`;
CREATE TABLE IF NOT EXISTS `author` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `degree` varchar(100) DEFAULT NULL,
  `address` text,
  `gender` varchar(10) DEFAULT NULL,
  `researcher_type` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `street_address` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `author_journal`
--

DROP TABLE IF EXISTS `author_journal`;
CREATE TABLE IF NOT EXISTS `author_journal` (
  `author_id` int NOT NULL,
  `journal_id` int NOT NULL,
  PRIMARY KEY (`author_id`,`journal_id`),
  KEY `journal_id` (`journal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `course_id` int NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `offer_price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `subtotal` decimal(10,2) GENERATED ALWAYS AS ((`offer_price` * `quantity`)) STORED,
  `gst` decimal(10,2) GENERATED ALWAYS AS (((`offer_price` * `quantity`) * 0.18)) STORED,
  `total` decimal(10,2) GENERATED ALWAYS AS (((`offer_price` * `quantity`) * 1.18)) STORED,
  `session_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `order_id`, `course_id`, `course_name`, `price`, `offer_price`, `image`, `quantity`, `session_id`) VALUES
(92, 1, 56, 'Data Science with Pythons', 0.00, 0.00, '', 1, ''),
(91, 78, 37, 'AI Mastery: From Fundamentals to Breakthrough', 0.00, 0.00, '', 1, ''),
(93, 1, 56, 'Data Science with Pythons', 0.00, 0.00, '', 1, ''),
(94, 0, 56, 'Data Science with Pythons', 0.00, 0.00, '', 1, ''),
(95, 0, 44, 'Machine Learning', 0.00, 0.00, '', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

DROP TABLE IF EXISTS `checkout`;
CREATE TABLE IF NOT EXISTS `checkout` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `college_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `current_semester` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `hear_about_us` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `checkout`
--

INSERT INTO `checkout` (`id`, `first_name`, `last_name`, `city`, `phone`, `email`, `college_name`, `current_semester`, `hear_about_us`, `order_date`) VALUES
(1, 'HARIPRIYA', ' KT', 'KANNUR', '8590862475', 'haripriyakt33@gmail.com', 'St Aloysius College', '4', 'yy', '2025-06-16 05:38:31'),
(2, 'HARIPRIYA', ' KT', 'KANNUR', '8590862475', 'haripriyakt33@gmail.com', 'St Aloysius College', '4', 'yy', '2025-06-16 05:41:41');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject_id` int DEFAULT NULL,
  `course_name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `about` text,
  `author_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `offer_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `subject_id`, `course_name`, `description`, `about`, `author_name`, `price`, `offer_price`, `image`) VALUES
(29, 0, '', '', NULL, '', 0.00, 0.00, NULL),
(35, 32, 'Mastering Python and Machine Learning for Innovation', 'Beginner-friendly', NULL, 'Mosh Hamedani', 21000.00, 20000.00, '1742277312_m1.jpg'),
(56, 8, 'Data Science with Pythons', 'Smart Data Decisions', NULL, 'Krishna Kumar', 1200.00, 599.00, '1742474919_d1.jpg'),
(37, 10, 'AI Mastery: From Fundamentals to Breakthrough', 'Intelligent Machine Thinking', NULL, 'Lovleen Bhatian', 4200.00, 780.00, '1742378891_ain.jpg'),
(63, 11, 'Database Management Excellence', 'Efficient Data Management', NULL, 'Saurabh Shukla', 1200.00, 450.00, '1742915757_dbmsl.jpg'),
(39, 15, 'JavaScript Mastery', 'Interactive Web Experiences', NULL, 'Mosh Hamedani', 1300.00, 750.00, '1742378084_js.jpg'),
(40, 14, 'Java Mastery', 'Robust Application Development', NULL, 'Krishna Kumar', 1200.00, 430.00, '1742379074_JS3.jpg'),
(41, 16, 'PHP Development', 'Dynamic Web Solution', NULL, 'freeCodeCamp.org Team', 1500.00, 450.00, '1742379358_lds4.jpg'),
(42, 18, 'Bigdata Analytics Course', 'Massive Data Insights', NULL, 'Emory Continuing Education', 1800.00, 750.00, '1742378147_a1.jpg'),
(65, 77, 'Full Stack Development', 'Building Online Presence', NULL, 'freeCodeCamp.org Team', 1500.00, 240.00, '1742989977_websit.jpg'),
(43, 19, 'Deep Learning Specialization', 'In-depth neural networks', NULL, 'Andrew Ng', 12000.00, 11500.00, '1742280130_deep.jpg'),
(44, 13, 'Machine Learning', 'Powerful Easy Coding', NULL, 'Jovian', 1700.00, 600.00, '1742378182_pyth.jpg'),
(50, 33, 'Machine Learning Fundamentals', 'Core ML Concepts', NULL, 'Alice White', 17000.00, 13000.00, '1742378030_m1.jpg'),
(64, 76, 'Data Structures and Algorithms', 'Organized Data Handling', NULL, 'William Fiset', 1300.00, 450.00, '1742989508_ds8.jpg'),
(66, 78, 'Cloud Computing full Course', 'delivery of computing services over the internet', NULL, 'Simplilearn', 14000.00, 12300.00, '1742992520_cld2.jpg'),
(67, 79, 'Learn CyberSecurity', 'Protecting Digital World', NULL, 'Simplilearn', 5000.00, 1500.00, '1742993015_cyber.jpg'),
(71, 81, 'Machine Learning for Everybody', 'Learning From Data', NULL, 'Jovian', 1800.00, 1400.00, '1743490698_m1.jpg'),
(72, 82, 'Speed Maths Tricks', 'A series of topics are being discussed which will help the users understand Quant easily and make them more confirmable during interviews.', NULL, 'Dhanajay Kumar', 1000.00, 500.00, '1745585016_speedmath.png');

-- --------------------------------------------------------

--
-- Table structure for table `course_payments`
--

DROP TABLE IF EXISTS `course_payments`;
CREATE TABLE IF NOT EXISTS `course_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edited_documents`
--

DROP TABLE IF EXISTS `edited_documents`;
CREATE TABLE IF NOT EXISTS `edited_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_text` text NOT NULL,
  `edited_text` text NOT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `editorial_teams`
--

DROP TABLE IF EXISTS `editorial_teams`;
CREATE TABLE IF NOT EXISTS `editorial_teams` (
  `team_id` int NOT NULL AUTO_INCREMENT,
  `team_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `editorial_teams`
--

INSERT INTO `editorial_teams` (`team_id`, `team_name`) VALUES
(23, 'Team1');

-- --------------------------------------------------------

--
-- Table structure for table `editorial_team_members`
--

DROP TABLE IF EXISTS `editorial_team_members`;
CREATE TABLE IF NOT EXISTS `editorial_team_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `team_id` int DEFAULT NULL,
  `editor_id` int DEFAULT NULL,
  `role` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `is_new` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `team_id` (`team_id`),
  KEY `editor_id` (`editor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `editors`
--

DROP TABLE IF EXISTS `editors`;
CREATE TABLE IF NOT EXISTS `editors` (
  `editor_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `degree` varchar(100) DEFAULT NULL,
  `cv_path` varchar(255) DEFAULT NULL,
  `address` text,
  `gender` varchar(10) DEFAULT NULL,
  `editor_type` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `street_address` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `editor_board` text,
  `editor_experience` text,
  `editor_payment_type` varchar(100) DEFAULT NULL,
  `editor_account_holder` varchar(255) DEFAULT NULL,
  `editor_bank_name` varchar(255) DEFAULT NULL,
  `editor_account_number` varchar(100) DEFAULT NULL,
  `editor_ifsc` varchar(100) DEFAULT NULL,
  `editor_branch_name` varchar(255) DEFAULT NULL,
  `editor_bank_country` varchar(100) DEFAULT NULL,
  `journal_id` int DEFAULT NULL,
  `reviewer_id` int DEFAULT NULL,
  `author_id` int DEFAULT NULL,
  `contract_status` enum('not_sent','sent','pending_verification','signed','reupload') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'not_sent',
  `registration_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `upload_date` datetime DEFAULT NULL,
  `contract_file` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `paper_name` varchar(255) DEFAULT NULL,
  `co_author` text,
  PRIMARY KEY (`editor_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `editor_reviewer_messages`
--

DROP TABLE IF EXISTS `editor_reviewer_messages`;
CREATE TABLE IF NOT EXISTS `editor_reviewer_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `recipient_id` int NOT NULL,
  `recipient_role` enum('editor','reviewer') NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reply_to` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reply_to` (`reply_to`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `editor_tasks`
--

DROP TABLE IF EXISTS `editor_tasks`;
CREATE TABLE IF NOT EXISTS `editor_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paper_id` int DEFAULT NULL,
  `editor_id` int DEFAULT NULL,
  `task_type` varchar(100) DEFAULT NULL,
  `assigned_by` int DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('Pending','Accepted','Rejected','Completed') DEFAULT 'Pending',
  `response_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reminder_sent` tinyint DEFAULT '0',
  `result` enum('Not Processed','Processed for Next Level','Revision Request','Revised Submitted') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `feedback` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `editor_tasks`
--

INSERT INTO `editor_tasks` (`id`, `paper_id`, `editor_id`, `task_type`, `assigned_by`, `deadline`, `status`, `response_date`, `created_at`, `reminder_sent`, `result`, `feedback`) VALUES
(63, 49, 28, '4', 27, '2025-05-31', 'Completed', '2025-05-26', '2025-05-25 23:59:26', 0, 'Processed for Next Level', 'asdfgertxcvwertdfg'),
(62, 49, 28, '3', 27, '2025-05-31', 'Completed', '2025-05-26', '2025-05-25 19:03:22', 0, 'Processed for Next Level', ''),
(61, 49, 28, '2', 27, '2025-05-31', 'Completed', '2025-05-26', '2025-05-25 19:00:16', 0, 'Processed for Next Level', ''),
(60, 49, 28, '1', 27, '2025-05-30', 'Completed', '2025-05-26', '2025-05-25 18:59:06', 0, 'Processed for Next Level', 'asdfgerty');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paper_id` int NOT NULL,
  `reviewer_id` int NOT NULL,
  `author_id` int NOT NULL,
  `journal_name` varchar(255) NOT NULL,
  `feedback` text NOT NULL,
  `review_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paper_id` (`paper_id`),
  KEY `reviewer_id` (`reviewer_id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `paper_id`, `reviewer_id`, `author_id`, `journal_name`, `feedback`, `review_date`) VALUES
(9, 49, 20, 8, 'Calphad', 'wertyuiopsdfghjklxcvbnmedxcvbnmfghj', '2025-05-25 23:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `journals`
--

DROP TABLE IF EXISTS `journals`;
CREATE TABLE IF NOT EXISTS `journals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `journal_name` varchar(255) NOT NULL,
  `primary_subject` varchar(255) NOT NULL,
  `secondary_subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `publisher` varchar(255) NOT NULL,
  `issn` varchar(255) DEFAULT NULL,
  `access_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `author_payment_required` tinyint(1) DEFAULT '0',
  `reader_payment_required` tinyint(1) DEFAULT '0',
  `author_apc_amount` decimal(10,2) DEFAULT '0.00',
  `reader_fee_amount` decimal(10,2) DEFAULT '0.00',
  `payment_currency` varchar(10) DEFAULT NULL,
  `payment_link` varchar(255) DEFAULT NULL,
  `payment_notes` text,
  `submission_status` varchar(50) NOT NULL,
  `journal_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `journal_abbreviation` varchar(255) NOT NULL,
  `editorial_board` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `publication_frequency` varchar(255) NOT NULL,
  `indexing_info` text,
  `scope` text NOT NULL,
  `author_guidelines` varchar(255) DEFAULT NULL,
  `review_process` varchar(255) NOT NULL,
  `impact_factor` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `citescore` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `acceptance_rate` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `editorial_team_id` int DEFAULT NULL,
  `keywords` text,
  PRIMARY KEY (`id`),
  KEY `fk_editorial_team` (`editorial_team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `journals`
--

INSERT INTO `journals` (`id`, `journal_name`, `primary_subject`, `secondary_subject`, `description`, `publisher`, `issn`, `access_type`, `author_payment_required`, `reader_payment_required`, `author_apc_amount`, `reader_fee_amount`, `payment_currency`, `payment_link`, `payment_notes`, `submission_status`, `journal_image`, `created_at`, `journal_abbreviation`, `editorial_board`, `country`, `publication_frequency`, `indexing_info`, `scope`, `author_guidelines`, `review_process`, `impact_factor`, `citescore`, `acceptance_rate`, `editorial_team_id`, `keywords`) VALUES
(26, 'AEÜ - International Journal of Electronics and Communications', 'Social Sciences', 'Psychology', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'zieers', '1234-5688', 'Open Access', 1, 0, 250.00, 0.00, 'INR', 'https://www.example.com/payment', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'Accepting Submissions', 'uploads/6832baa0be37c_Screenshot 2025-05-25 115138.png', '2025-05-25 06:37:20', ' AJO-DO Clin Companion', '', 'India', 'Quarterly', NULL, 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share in', '0', NULL, NULL, NULL, 23, 'ai,cyber'),
(27, 'Calphad', 'Computer Science', 'Data Science', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'zieers', '1234-5670', 'Subscription Based', 0, 1, 0.00, 350.00, 'INR', 'https://www.example.com/payment', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'Accepting Submissions', 'uploads/6832bb21d3ad3_Screenshot 2025-05-25 115332.png', '2025-05-25 06:39:29', 'Calphad', '', 'India', 'Quarterly', NULL, 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share in', '0', NULL, NULL, NULL, 23, 'math,data'),
(28, 'Biocybernetics and Biomedical Engineering', 'Medicine', 'Neurology', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'zieers', NULL, 'Subscription Based', 0, 1, 0.00, 300.00, 'INR', 'https://www.example.com/payment', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'Accepting Submissions', 'uploads/6832bb7c298d1_Screenshot 2025-05-25 115159.png', '2025-05-25 06:41:00', 'Biocybernetics and Biomedical Engineering', '', 'India', 'Quarterly', NULL, 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share in', '0', NULL, NULL, NULL, NULL, 'social,medicine'),
(29, 'Computational Condensed Matter', 'Engineering', 'Mechanical', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'zieers', NULL, 'Open Access', 1, 0, 400.00, 0.00, 'INR', 'https://www.example.com/payment', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'Accepting Submissions', 'uploads/6832bbd976b75_Screenshot 2025-05-25 115215.png', '2025-05-25 06:42:33', 'Computational Condensed Matter', '', 'India', 'Quarterly', NULL, 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share innovative ideas, methodologies, and findings.\r\nOur journal accepts original research articles, review papers, case studies, and short communications.\r\nWe support interdisciplinary studies in science, technology, medicine, social sciences, arts, and humanities.\r\nEach submission undergoes a rigorous double-blind peer review process to ensure academic excellence.\r\nAuthors benefit from fast and transparent editorial handling with expert guidance.\r\nOur journal promotes ethical publishing and follows international publication standards.\r\nAccepted papers are published online with a DOI for global accessibility and citation.\r\nWe encourage young researchers and first-time authors to submit their work confidently.\r\nJoin us in advancing knowledge and shaping the future through open, accessible research.\r\n\r\n', 'The International Journal of Emerging Research is a peer-reviewed, open access journal dedicated to publishing high-quality research across diverse disciplines.\r\nWe aim to provide a global platform for academics, researchers, and professionals to share in', '0', NULL, NULL, NULL, NULL, 'mech,ai');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `sender_role` enum('reviewer','editor') NOT NULL,
  `recipient_id` int NOT NULL,
  `paper_id` int DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `name`, `email`, `address`, `city`, `state`, `zip`, `total_amount`, `created_at`) VALUES
(1, 'HARIPRIYA K T', 'haripriyarajeev03@gmail.com', 'kakkoprath thekke veetil', 'KANNUR', 'Kerala', '670521', NULL, '2025-04-14 05:22:56'),
(2, 'HARIPRIYA K T', 'haripriyarajeev03@gmail.com', 'kakkoprath thekke veetil', 'KANNUR', 'Kerala', '670521', NULL, '2025-04-14 05:23:15'),
(3, 'HARIPRIYA K T', 'haripriyarajeev03@gmail.com', 'kakkoprath thekke veetil', 'KANNUR', 'Kerala', '670521', NULL, '2025-04-15 05:47:17'),
(4, '', '', '', '', '', '', NULL, '2025-04-15 06:11:22'),
(5, '', 'haripriyarajeev03@gmail.com', '', '', '', '', NULL, '2025-04-15 07:06:57'),
(6, '', '', '', '', '', '', NULL, '2025-04-15 07:29:32');

-- --------------------------------------------------------

--
-- Table structure for table `papers`
--

DROP TABLE IF EXISTS `papers`;
CREATE TABLE IF NOT EXISTS `papers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `journal_id` int NOT NULL,
  `author_id` int NOT NULL,
  `title` varchar(500) NOT NULL,
  `abstract` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submission_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `keywords` text,
  `file_hash` varchar(250) DEFAULT NULL,
  `cover_letter_path` varchar(255) NOT NULL,
  `copyright_agreement_path` varchar(255) NOT NULL,
  `supplementary_files_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Under Review','Rejected (Pre-Review)','Rejected (Post-Review)','Revision Requested','Revised Submitted','Reinstated for Review','Accepted with Revisions','Accepted (Final Decision)','Published') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Pending',
  `comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `doi` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `volume` varchar(20) DEFAULT NULL,
  `issue` varchar(20) DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL,
  `citation_count` int DEFAULT '0',
  `download_count` int DEFAULT '0',
  `year` int DEFAULT NULL,
  `editor_id` int NOT NULL,
  `feedback` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_hash` (`file_hash`),
  KEY `journal_id` (`journal_id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `papers`
--

INSERT INTO `papers` (`id`, `journal_id`, `author_id`, `title`, `abstract`, `file_path`, `submission_date`, `keywords`, `file_hash`, `cover_letter_path`, `copyright_agreement_path`, `supplementary_files_path`, `status`, `comments`, `doi`, `updated_at`, `volume`, `issue`, `completed_date`, `citation_count`, `download_count`, `year`, `editor_id`, `feedback`) VALUES
(49, 27, 8, 'scarifce', 'wertyuiopasdfghjklxcvbnmwertyuiopsdfghjklxcvbnm', 'C:\\wamp64\\www\\my_publication_site\\researcher\\author/../../uploads/6832e41922e13_Resume Arya Thulicheri[1].pdf', '2025-05-25 09:34:17', 'ai,cyber', 'd3889bd07c5c881dfba78add2edf11d7088ae3f07c0055ce6c8189ac868e0622', 'C:\\wamp64\\www\\my_publication_site\\researcher\\author/../../uploads/6832e419251f4_Software Design Document.pdf', 'C:\\wamp64\\www\\my_publication_site\\researcher\\author/../../uploads/6832e4192554a_plagiarism_report_39.pdf', 'C:\\wamp64\\www\\my_publication_site\\researcher\\author/../../uploads/6832e419257ef_SRS[1].pdf', 'Accepted (Final Decision)', NULL, NULL, '2025-05-26 05:30:28', NULL, NULL, NULL, 0, 0, NULL, 27, 'ertyuiodfghjklxcvbnmertyuiodfghjkxcvbn');

-- --------------------------------------------------------

--
-- Table structure for table `paper_assignments`
--

DROP TABLE IF EXISTS `paper_assignments`;
CREATE TABLE IF NOT EXISTS `paper_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paper_id` int NOT NULL,
  `reviewer_id` int NOT NULL,
  `assigned_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','In-Review','Rejected','Revision Requested','Revised Submitted','Completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Pending',
  `deadline` date NOT NULL,
  `completed_date` datetime DEFAULT NULL,
  `revision_date` datetime DEFAULT NULL,
  `editor_id` int NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paper_id` (`paper_id`),
  KEY `reviewer_id` (`reviewer_id`),
  KEY `fk_editor` (`editor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `paper_assignments`
--

INSERT INTO `paper_assignments` (`id`, `paper_id`, `reviewer_id`, `assigned_date`, `status`, `deadline`, `completed_date`, `revision_date`, `editor_id`, `updated_at`) VALUES
(47, 49, 20, '2025-05-25 23:55:08', 'Completed', '2025-05-31', '2025-05-26 05:27:09', NULL, 0, '2025-05-25 23:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `paper_authors`
--

DROP TABLE IF EXISTS `paper_authors`;
CREATE TABLE IF NOT EXISTS `paper_authors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paper_id` int DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `affiliation_type` enum('individual','affiliated') DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `institute` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paper_id` (`paper_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paper_id` int NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `payment_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(10,2) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) NOT NULL,
  `author_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plagiarism_reports`
--

DROP TABLE IF EXISTS `plagiarism_reports`;
CREATE TABLE IF NOT EXISTS `plagiarism_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paper_id` int DEFAULT NULL,
  `plagiarism_percentage` decimal(5,2) DEFAULT NULL,
  `report_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `feedback` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `paper_id` (`paper_id`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plagiarism_reports`
--

INSERT INTO `plagiarism_reports` (`id`, `paper_id`, `plagiarism_percentage`, `report_path`, `created_at`, `feedback`) VALUES
(61, 49, 18.00, 'reports/plagiarism_report_49.pdf', '2025-05-25 19:02:31', ''),
(62, 49, 18.00, 'reports/plagiarism_report_49.pdf', '2025-05-25 19:02:36', ''),
(63, 49, 8.00, 'reports/plagiarism_report_49.pdf', '2025-05-25 19:02:40', ''),
(60, 49, 27.00, 'reports/plagiarism_report_49.pdf', '2025-05-25 19:02:26', ''),
(59, 49, 38.00, 'reports/plagiarism_report_49.pdf', '2025-05-25 19:02:24', ''),
(58, 49, 37.00, 'reports/plagiarism_report_49.pdf', '2025-05-25 19:02:21', ''),
(57, 49, 33.00, 'reports/plagiarism_report_49.pdf', '2025-05-25 19:02:17', ''),
(56, 49, 20.00, 'reports/plagiarism_report_49.pdf', '2025-05-25 19:02:02', '');

-- --------------------------------------------------------

--
-- Table structure for table `reviewers`
--

DROP TABLE IF EXISTS `reviewers`;
CREATE TABLE IF NOT EXISTS `reviewers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `degree` varchar(100) DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `gender` varchar(10) DEFAULT NULL,
  `reviewer_type` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `street_address` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `experience` text,
  `review_frequency` varchar(100) DEFAULT NULL,
  `payment_type` varchar(100) DEFAULT NULL,
  `account_holder_name` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(100) DEFAULT NULL,
  `branch_name` varchar(255) DEFAULT NULL,
  `bank_country` varchar(100) DEFAULT NULL,
  `registration_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `contract_status` enum('not_sent','sent','pending_verification','signed') NOT NULL DEFAULT 'not_sent',
  `contract_file` varchar(255) DEFAULT NULL,
  `upload_date` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL,
  `cv_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviewer_journals`
--

DROP TABLE IF EXISTS `reviewer_journals`;
CREATE TABLE IF NOT EXISTS `reviewer_journals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reviewer_id` int NOT NULL,
  `journal_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reviewer_id` (`reviewer_id`),
  KEY `journal_id` (`journal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviewer_requests`
--

DROP TABLE IF EXISTS `reviewer_requests`;
CREATE TABLE IF NOT EXISTS `reviewer_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reviewer_id` int NOT NULL,
  `journal_id` int NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `contract_status` enum('not-sent','sent','pending_verification','not_signed','signed','reupload') DEFAULT 'not-sent',
  `contract_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reviewer_id` (`reviewer_id`),
  KEY `journal_id` (`journal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(50) NOT NULL,
  `description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `description`) VALUES
(8, 'Data science', 'Data Science is a field that uses statistical methods, machine learning, and data visualization to analyze and interpret complex data. It involves data cleaning, exploratory analysis, and the use of algorithms for prediction. Key topics include machine learning techniques (supervised, unsupervised), data visualization tools like Matplotlib, and programming languages such as Python and R. Big Data tools like Hadoop and Spark are also vital for handling large datasets.'),
(10, 'Artificial intelligence', 'Artificial Intelligence aims to create machines capable of performing tasks that typically require human intelligence, such as decision-making and problem-solving. It includes machine learning, deep learning, and natural language processing (NLP). Key areas to focus on are neural networks, computer vision, reinforcement learning, and ethical implications. Python, R, and Java are commonly used in AI development.'),
(11, 'Database Management System', 'DBMS is software that helps manage, store, and manipulate data efficiently. It covers relational databases (SQL), NoSQL databases, and database design techniques. Topics include writing SQL queries, database normalization, transaction management, and data integrity. Tools like MySQL, MongoDB, and PostgreSQL are widely used for managing data storage.'),
(13, 'Python', 'Python is a versatile programming language used across various domains, from web development to data science. It’s known for its simplicity and readability. Key topics include Python syntax, object-oriented programming, libraries like Pandas and NumPy for data analysis, and frameworks such as Flask and Django for web development. Python is also heavily used in automation and machine learning.'),
(14, 'Java', 'Java is a high-level programming language, known for its portability and scalability. It is commonly used in large enterprise systems. Important topics include object-oriented programming (OOP) concepts like inheritance, polymorphism, Java’s collections framework, and multithreading. Java is also used in building web applications with frameworks like Spring and Hibernate.'),
(15, 'Javascript', 'JavaScript is essential for web development, enabling interactive and dynamic content on websites. It is used both in front-end development (with frameworks like React, Angular) and back-end development (with Node.js). Key areas to study include JavaScript syntax, asynchronous programming (Promises, async/await), DOM manipulation, and modern features like ES6+.'),
(16, 'PHP', 'PHP is a server-side scripting language used for web development. It allows dynamic content generation on websites and integrates with databases like MySQL. Core topics include PHP syntax, working with forms, object-oriented PHP, and security practices like preventing SQL injection and cross-site scripting (XSS). Frameworks like Laravel and Symfony are also critical in PHP development.'),
(18, 'Big data', 'Big Data involves handling large and complex datasets that require advanced tools for storage, processing, and analysis. Key topics include Hadoop’s ecosystem (HDFS, MapReduce), real-time data processing with Apache Spark, and NoSQL databases like Cassandra. Data streaming with Kafka and cloud platforms such as AWS and Google Cloud are also essential for Big Data management.'),
(79, 'Cybersecurity', 'Cybersecurity involves protecting systems and networks from cyber threats. It covers network security protocols like SSL/TLS, cryptography (encryption, hashing), and ethical hacking. Key topics include intrusion detection systems (IDS), firewalls, malware analysis, and ensuring data integrity. Understanding security compliance frameworks and responding to cyber incidents is also crucial.'),
(77, 'Web development', 'Web development encompasses creating websites and web applications. It includes both front-end and back-end technologies. Key areas to study are HTML, CSS, and JavaScript for the front-end, with the frameworks like React and Angular. On the back-end, Node.js, PHP, and frameworks like Django are popular. Additional topics include RESTful APIs, database integration, responsive design, and web security best practices.'),
(76, 'Data structure', 'Data Structures are methods for organizing and storing data to allow efficient access and modification. Key topics include arrays, linked lists, stacks, queues, trees (binary, AVL), and graphs. Sorting algorithms (like Quick Sort, Merge Sort) and searching techniques are critical. Understanding time complexity (Big O notation) and recursion is also important for solving algorithmic problems.'),
(81, 'Machine learning', 'Machine Learning (ML) is a branch of AI that allows computers to learn from data and make predictions without explicit programming. It uses algorithms to identify patterns and improve over time.  ML is applied in areas like image recognition, natural language processing, and recommendations, with techniques like supervised, unsupervised, and reinforcement learning.'),
(82, 'Quantitative Aptitude Made Easy', '\"Quantitative Aptitude Made Easy\" by Quant Guru is an initiative to help students to crack Quantitative Aptitude Round during any Interview of Competitive Examination. \n A series of topics are being discussed which will help the users understand Quant easily and make them more confirmable during interviews.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `admin`) VALUES
(28, 'Anju', '', 'Scaria', 'anjuscaria7@gmail.com', '$2y$10$5kbnmHDuz5cY4ov6WAGB4efsYF.Srd0O1FDbfAq/3x0XRx.SvrAOy', 0),
(29, 'Sayan ', '', 'Dey', 'sayan.deybrs1997@gmail.com', '$2y$10$V3oZ0MvHHitYdyPLQc9Jb.mVuC6XT.Qfq3J.6jf8DZ65izza16PNW', 0);

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject_id` int NOT NULL,
  `course_id` int NOT NULL,
  `video_link` varchar(255) NOT NULL,
  `description` text,
  `pdf_path` varchar(255) DEFAULT NULL,
  `video_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  KEY `course_id` (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `subject_id`, `course_id`, `video_link`, `description`, `pdf_path`, `video_name`) VALUES
(88, 8, 74, 'https://youtu.be/JL_grPUnXzY?si=F1u1nrkh7xc0W9Gc', 'Learn how to extract insights from data using statistics, visualization, and tools like Python, R, and SQL. Ideal for analysts, scientists, and decision-makers.', '', 'WHAT IS DATASCIENCE ?'),
(89, 8, 56, 'https://youtu.be/-ETQ97mXXF0?si=1q2scGYt1NSI56Pv', 'video will help you understand and learn Data Science Algorithms in detail. This Data Science Tutorial is ideal for both beginners as well as professionals who want to master Data Science Algorithms.', '1748270609_1744113145_data-science-course-syllabus.pdf', 'Learn Data Science Tutorial'),
(63, 10, 37, 'https://www.youtube.com/watch?v=JMUxmLyrhSk', 'Artificial Intelligence Full Course will provide you with a comprehensive and detailed knowledge of Artificial Intelligence concepts with hands-on examples', '1746606396_artifical intelligence (oe).pdf', 'Artificial Intelligence Tutorial'),
(64, 11, 63, 'https://www.youtube.com/watch?v=wR0jg0eQsZA', 'This course covers the fundamentals of Database Management Systems (DBMS), including data models, SQL, and database design. Students gain hands-on experience with tools like MySQL, focusing on querying, normalization, and security. It prepares learners for careers in software development, data management, and related fields.', '1746612921_1744113127_1742198304_dbms1.pdf', 'DATABASE EDUCATION SYSTEM'),
(65, 13, 44, 'https://www.youtube.com/watch?v=rfscVS0vtbw', 'Learn the Python programming language in this full course for beginners! You will learn the fundamentals of Python and code two Python programs line-by-line. No previous programming experience is necessary before watching this course.', '1746612997_1744113087_1742280386_python-course-syllabus.pdf', 'Python for Beginners'),
(66, 14, 40, 'https://www.youtube.com/watch?v=grEKMHGYyns', 'Master Java – a must-have language for software development, Android apps, and more! This beginner-friendly course takes you from basics to real coding skills', '1746613074_1744113069_1742279180_java.pdf', 'Java Full Course for Beginners	'),
(79, 15, 39, 'https://youtu.be/EerdGm-ehJQ?si=_8l6u7JwE0cGkdI2', 'This JavaScript tutorial and JavaScript full course is a project based series of JavaScript tutorials for software engineers. Each JavaScript tutorial builds on a project and provides some JavaScript exercises to practice what we learned. By the end, we\'ll learn how to create complex, interactive websites with JavaScript, HTML, and CSS, which will help you become a web developer and software engineer.', '1746687679_1744113205_javascript-syllabus.pdf', 'JavaScript Tutorial Full Course'),
(68, 16, 41, ' https://www.youtube.com/watch?v=OK_JCtrrv-c', 'Learn the PHP programming language in this full course / tutorial. The course is designed for new programmers, and will introduce common programming topics using the PHP language.	', '1746613202_1742280488_Syllabus_of_PHP_&_MY_Sql.pdf', 'PHP Programming Language Tutorial'),
(69, 18, 42, ' https://www.youtube.com/watch?v=BZQ9AeXvxXQ', 'This Edureka Big Data & Hadoop Full Course video will help you understand and learn Hadoop concepts in detail. This Big Data & Hadoop Tutorial is ideal for both beginners as well as professionals who want to master the Hadoop Ecosystem. Below are the topics covered in this Big Data & Hadoop Tutorial.', '1746613283_1742280657_Essentials_of_Big_Data_Griet (1).pdf', 'Hadoop Tutorial For Beginners'),
(70, 79, 67, 'https://www.youtube.com/watch?v=ciNHn38EyRc', 'This Post Graduate Program in Cyber Security will help you learn comprehensive approaches to protecting your infrastructure and securing data, including risk analysis, mitigation, and compliance. You will get foundational to advanced skills through industry-leading cyber security certification courses that are part of the program.', '1746613356_1744113258_Cyber SYLLABUS 2023.pdf', 'What Is Cyber Security | How It Works?'),
(71, 77, 65, ' https://www.youtube.com/watch?v=zJJjx8Q_ixQ', 'Learn full-stack web development in this full course for beginners. First, you will learn the basics of HTML, CSS, and JavaScript. Then, you will learn how to put everything together to create a frontend movie search app. Finally, you will learn how to create a backend API to create movie reviews and connect the frontend to the backend. The backend uses Node.js, Express, and MongoDB.', '1746613451_1744113269_22619 -  Web Based Application development with PHP.pdf', 'Full Stack Web Development for Beginners'),
(72, 76, 64, 'https://www.youtube.com/watch?v=8hly31xKli0', 'Learn and master the most common data structures in this full course from Google engineer William Fiset. This course teaches data structures to beginners using high quality animations to represent the data structures visually. You will learn how to code various data structures together with simple to follow step-by-step instructions. Every data structure presented will be accompanied by some working source code (in Java) to solidify your understanding.	\r\n', '1746613561_DATA STRUCTURES.pdf', 'Data Structures Easy to Advanced Course'),
(86, 82, 72, 'https://youtu.be/7ZRNGrkU_Zs?si=cqubR2h5v6C8ltO7', 'Finding the LCM of Fractions seems to be a tedious and time taking process, but it is no more a problem. Thanks to the tricks of aptitude which is brought to you by \"Quant Guru\"', '', 'Finding LCM Fractions'),
(87, 82, 72, 'https://youtu.be/oixonnGEOxg?si=YbxAWOBWM9HX8q2B', 'Speed Maths is an art of using numbers, formulae or strategies to get the answer in speedy manner.  People who excel at mathematics use better strategies than the rest, they don\'t necessarily have better brains.', '', 'Speed MATHS'),
(84, 82, 72, 'https://youtu.be/rpUdIImgH0s?si=R6aQUJwlgsoXWu_u', 'Ratio and Proportion is one of the very important topics in the Quantitative Aptitude. Here we have explained the easiest method to find the Ratio of any 3 ', '', 'Ratio of a, b & c'),
(85, 82, 72, 'https://youtu.be/77jORSVyHp4?si=Ve69ST2JBRI4RRmT', 'Ratio and Proportion is one of the very important topics in the Quantitative Aptitude. Here we have explained the easiest method to find the Ratio of any numbers.\r\nRatio A,B,C And D\r\n', '', 'Ratio of a, b, c, d'),
(91, 8, 77, 'https://www.youtube.com/live/i4yFBXOUPxg?si=oMkRwaflWcqcZQsg', 'Unlock the power of data with cutting-edge analytics and machine learning to drive your business decisions.', '', 'Data Science'),
(93, 8, 56, 'https://www.youtube.com/watch?v=Gv9_4yMHFhI', 'hhhhhhhhhhhhhhhhhhhhhhhhh', '1749652417_1746606396_artifical intelligence (oe).pdf', 'Learn Data Science Tutorialhhhh');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `journals`
--
ALTER TABLE `journals`
  ADD CONSTRAINT `fk_editorial_team` FOREIGN KEY (`editorial_team_id`) REFERENCES `editorial_teams` (`team_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
