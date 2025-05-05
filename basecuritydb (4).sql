-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 08:04 PM
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
-- Database: `basecuritydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `adminID` int(11) NOT NULL,
  `adminFirstName` varchar(250) NOT NULL,
  `adminLastName` varchar(250) NOT NULL,
  `adminEmail` varchar(250) DEFAULT NULL,
  `adminPhone` varchar(50) DEFAULT NULL,
  `adminUsername` varchar(100) NOT NULL,
  `adminPassword` varchar(100) NOT NULL,
  `adminStatus` varchar(50) DEFAULT NULL,
  `adminNotes` varchar(300) DEFAULT NULL,
  `adminIsActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`adminID`, `adminFirstName`, `adminLastName`, `adminEmail`, `adminPhone`, `adminUsername`, `adminPassword`, `adminStatus`, `adminNotes`, `adminIsActive`) VALUES
(1, 'ATest1', 'ATest1', 'ATest1ATest1ATest1ATest1', NULL, 'ATest1', 'ATest1', NULL, 'ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1ATest1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `adminlog`
--

CREATE TABLE `adminlog` (
  `logID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `actionType` varchar(100) DEFAULT NULL,
  `actionDetails` varchar(500) DEFAULT NULL,
  `logDateTime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `annualreminders`
--

CREATE TABLE `annualreminders` (
  `reminderID` int(11) NOT NULL,
  `serviceName` varchar(255) NOT NULL,
  `clientID` int(11) DEFAULT NULL,
  `dueDate` datetime NOT NULL,
  `isAnnual` tinyint(1) NOT NULL DEFAULT 0,
  `reminderNotes` text DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `isActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `annualreminders`
--

INSERT INTO `annualreminders` (`reminderID`, `serviceName`, `clientID`, `dueDate`, `isAnnual`, `reminderNotes`, `createdAt`, `isActive`) VALUES
(3, 'test', NULL, '2025-04-30 12:07:00', 0, NULL, '2025-04-30 12:06:53', 1),
(4, 'test1', NULL, '2025-04-30 12:09:00', 0, NULL, '2025-04-30 12:07:16', 1),
(5, 'test11', NULL, '2025-04-30 12:12:00', 0, NULL, '2025-04-30 12:10:16', 1),
(6, 'test11', NULL, '2025-04-30 12:12:00', 0, NULL, '2025-04-30 12:13:51', 1),
(7, 'test', NULL, '2025-04-30 12:16:00', 0, NULL, '2025-04-30 12:14:12', 1),
(8, 'test', NULL, '2025-04-30 12:16:00', 0, NULL, '2025-04-30 12:19:31', 1),
(9, 'test', NULL, '2025-04-30 12:16:00', 0, NULL, '2025-04-30 12:20:05', 1),
(10, 'test', NULL, '2025-04-30 12:22:00', 0, NULL, '2025-04-30 12:20:18', 1),
(11, 'test', NULL, '2025-04-30 12:22:00', 0, NULL, '2025-04-30 12:24:08', 1),
(12, 'test', NULL, '2025-04-30 12:25:00', 0, NULL, '2025-04-30 12:24:15', 1),
(13, 'test', NULL, '2025-04-30 12:28:00', 0, NULL, '2025-04-30 12:27:55', 1),
(14, 'testttt', NULL, '2025-04-30 12:29:00', 0, NULL, '2025-04-30 12:28:17', 1),
(15, 'test', NULL, '2025-04-30 12:36:00', 0, NULL, '2025-04-30 12:35:16', 1),
(16, 'testingg', NULL, '2025-04-30 12:42:00', 0, NULL, '2025-04-30 12:41:33', 1),
(17, 'hi', NULL, '2025-05-05 00:45:00', 0, '0', '2025-05-05 00:43:34', 1),
(18, 'bye', NULL, '2025-05-05 00:45:00', 1, '0', '2025-05-05 00:43:52', 1),
(19, 'bye', NULL, '2026-05-04 00:45:00', 1, '', '2025-05-05 00:43:52', 1),
(20, 'hi there', NULL, '2025-05-05 00:52:00', 0, '0', '2025-05-05 00:51:07', 1),
(21, 'hi there', NULL, '2025-05-05 00:52:00', 0, '0', '2025-05-05 00:54:22', 1),
(22, 'hello there', NULL, '2025-05-05 00:55:00', 0, '0', '2025-05-05 00:54:46', 1),
(23, 'hello there', NULL, '2025-05-05 00:55:00', 0, '0', '2025-05-05 00:57:03', 1),
(24, 'yoyo', NULL, '2025-05-05 00:58:00', 0, '0', '2025-05-05 00:57:20', 1),
(25, 'yoyo', NULL, '2025-05-05 00:58:00', 0, '0', '2025-05-05 01:04:11', 1),
(26, 'qwer', NULL, '2025-05-05 01:05:00', 0, '0', '2025-05-05 01:04:32', 1),
(27, 'asd', NULL, '2025-05-05 01:10:00', 0, '0', '2025-05-05 01:09:06', 1),
(28, '100', NULL, '2025-05-05 01:27:00', 0, '1233', '2025-05-05 01:27:03', 1),
(29, '098', NULL, '2025-05-05 01:28:00', 0, '9098', '2025-05-05 01:27:14', 1),
(30, '000', NULL, '2025-05-05 01:31:00', 0, '0', '2025-05-05 01:30:07', 1),
(31, '000', NULL, '2025-05-05 01:34:00', 0, '0', '2025-05-05 01:33:42', 1),
(32, '000', NULL, '2025-05-05 01:34:00', 0, '0', '2025-05-05 01:37:37', 0),
(33, '000', NULL, '2025-05-05 01:38:00', 0, '0', '2025-05-05 01:37:49', 0),
(34, '000000', NULL, '2025-05-05 01:42:00', 0, '1234556789', '2025-05-05 01:40:20', 0),
(35, 'poo', NULL, '2025-05-05 01:42:00', 0, '0', '2025-05-05 01:41:24', 0),
(36, 'Sustainability Alert: CRITICAL - CRITICAL', NULL, '2025-05-05 12:18:50', 0, '🚨 CRITICAL: Power usage is extremely high (1536.3W)! Immediate action required to reduce system load.', '2025-05-05 11:18:50', 1),
(37, 'Sustainability Alert: CRITICAL - CRITICAL', NULL, '2025-05-05 13:04:02', 0, '🚨 CRITICAL: Power usage is extremely high (7375.8W)! Immediate action required to reduce system load.', '2025-05-05 12:04:02', 1),
(38, 'Sustainability Alert: CRITICAL - CRITICAL', NULL, '2025-05-05 13:04:07', 0, '🚨 CRITICAL: Power usage is extremely high (1216.2W)! Immediate action required to reduce system load.', '2025-05-05 12:04:07', 1),
(39, 'Sustainability Alert: CRITICAL - CRITICAL', NULL, '2025-05-05 13:05:39', 0, '🚨 CRITICAL: Power usage is extremely high (7375.8W)! Immediate action required to reduce system load.', '2025-05-05 12:04:21', 0),
(40, '345', 10003, '2025-05-05 12:09:00', 0, '345', '2025-05-05 12:08:38', 0),
(41, 'Sustainability Alert: CRITICAL', NULL, '2025-05-05 13:12:50', 0, '🚨 CRITICAL: Power usage is extremely high (4917.3W)! Immediate action required to reduce system load.', '2025-05-05 12:12:50', 0),
(42, 'Sustainability Alert: CRITICAL', NULL, '2025-05-05 13:48:11', 0, '🚨 CRITICAL: Power usage is extremely high (3841.3W)! Immediate action required to reduce system load.', '2025-05-05 12:48:11', 1),
(43, 'Sustainability Alert: CRITICAL - CRITICAL', NULL, '2025-05-05 13:51:33', 0, '🚨 CRITICAL: Power usage is extremely high (1844.1W)! Immediate action required to reduce system load.', '2025-05-05 12:51:33', 0),
(44, 'Sustainability Alert: CRITICAL - CRITICAL', NULL, '2025-05-05 13:51:44', 0, '🚨 CRITICAL: Power usage is extremely high (1844.1W)! Immediate action required to reduce system load.', '2025-05-05 12:51:44', 0),
(45, 'Sustainability Alert: CRITICAL - CRITICAL', NULL, '2025-05-05 14:16:40', 0, '🚨 CRITICAL: Power usage is extremely high (2682.2W)! Immediate action required to reduce system load.', '2025-05-05 13:16:40', 1),
(46, 'Sustainability Alert: CRITICAL - CRITICAL', NULL, '2025-05-05 14:19:08', 0, '🚨 CRITICAL: Power usage is extremely high (1018.7W)! Immediate action required to reduce system load.', '2025-05-05 13:19:08', 0),
(47, 'hello there', 10003, '2025-05-05 16:38:12', 0, '0', '2025-05-05 15:22:48', 1),
(48, 'Sustainability Alert: CRITICAL - CRITICAL', NULL, '2025-05-05 16:23:30', 0, '🚨 CRITICAL: Power usage is extremely high (1475.3W)! Immediate action required to reduce system load.', '2025-05-05 15:23:30', 1),
(49, 'hello there', NULL, '2025-05-05 15:27:00', 0, '0', '2025-05-05 15:26:26', 1);

-- --------------------------------------------------------

--
-- Table structure for table `clientfiles`
--

CREATE TABLE `clientfiles` (
  `fileID` int(11) NOT NULL,
  `clientID` int(11) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `fileType` varchar(100) NOT NULL,
  `filePath` varchar(500) NOT NULL,
  `fileSize` int(11) NOT NULL,
  `fileDateTime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clientfiles`
--

INSERT INTO `clientfiles` (`fileID`, `clientID`, `fileName`, `fileType`, `filePath`, `fileSize`, `fileDateTime`) VALUES
(2, 10003, 'CV _ Hasan Khan - Copy.pdf', 'application/pdf', 'uploads/1746394939_CV _ Hasan Khan - Copy.pdf', 126906, '2025-05-04 22:42:19'),
(3, 10003, 'CV _ Hasan Khan - Copy.pdf', 'application/pdf', 'uploads/1746394947_CV _ Hasan Khan - Copy.pdf', 126906, '2025-05-04 22:42:27');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `clientID` int(11) NOT NULL,
  `clientFirstName` varchar(250) NOT NULL,
  `clientLastName` varchar(250) NOT NULL,
  `clientAddress1` varchar(300) DEFAULT NULL,
  `clientAddress2` varchar(300) DEFAULT NULL,
  `clientPhone` varchar(50) DEFAULT NULL,
  `clientEmail` varchar(300) DEFAULT NULL,
  `clientIsDue` tinyint(1) DEFAULT 0,
  `clientStatus` varchar(50) DEFAULT NULL,
  `clientNotes` varchar(300) DEFAULT NULL,
  `clientDateTimeCreated` datetime DEFAULT current_timestamp(),
  `clientDateTimeAdded` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`clientID`, `clientFirstName`, `clientLastName`, `clientAddress1`, `clientAddress2`, `clientPhone`, `clientEmail`, `clientIsDue`, `clientStatus`, `clientNotes`, `clientDateTimeCreated`, `clientDateTimeAdded`) VALUES
(10001, 'CTest1', 'CTest1', 'CTest1CTest1CTest1', '', '', '', 0, NULL, 'CTest1CTest1CTest1CTest1CTest1CTest1CTest1CTest1CTest1CTest1', '2025-03-11 15:25:32', '2025-03-11 15:25:32'),
(10002, 'John', 'Steve', '17 Austin Road Luton LU31TY', NULL, NULL, 'hak0816@my.londonmet.ac.uk', 0, NULL, NULL, '2025-04-25 12:10:55', '2025-04-25 12:10:55'),
(10003, 'cat ', 'dog', '166-220 Holloway Road, London, N7 8DB', '', '', 'na@gmail.com', 0, NULL, 'uni ', '2025-04-25 14:34:42', '2025-04-25 14:34:42');

-- --------------------------------------------------------

--
-- Table structure for table `clientserviceshistory`
--

CREATE TABLE `clientserviceshistory` (
  `serviceHistoryID` int(11) NOT NULL,
  `clientID` int(11) DEFAULT NULL,
  `engineerID` int(11) DEFAULT NULL,
  `scheduleID` int(11) DEFAULT NULL,
  `serviceHistoryJobType` varchar(50) DEFAULT NULL,
  `serviceHistoryDate` date DEFAULT NULL,
  `serviceHistoryStartTime` time DEFAULT NULL,
  `serviceHistoryEndTime` time DEFAULT NULL,
  `serviceHistoryDetails` varchar(250) DEFAULT NULL,
  `serviceHistoryDocuments` varchar(250) DEFAULT NULL,
  `feedbackID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engineer`
--

CREATE TABLE `engineer` (
  `engineerID` int(11) NOT NULL,
  `engineerFirstName` varchar(250) NOT NULL,
  `engineerLastName` varchar(250) NOT NULL,
  `engineerEmail` varchar(250) DEFAULT NULL,
  `engineerPhone` varchar(50) DEFAULT NULL,
  `engineerSpeciality` varchar(100) DEFAULT NULL,
  `engineerUsername` varchar(100) NOT NULL,
  `engineerPassword` varchar(100) NOT NULL,
  `engineerStatus` varchar(50) DEFAULT NULL,
  `engineerNotes` varchar(300) DEFAULT NULL,
  `engineerIsActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `engineer`
--

INSERT INTO `engineer` (`engineerID`, `engineerFirstName`, `engineerLastName`, `engineerEmail`, `engineerPhone`, `engineerSpeciality`, `engineerUsername`, `engineerPassword`, `engineerStatus`, `engineerNotes`, `engineerIsActive`) VALUES
(1, 'ETest1', 'ETest1', NULL, NULL, 'fire ', 'ETest1', 'ETest1', NULL, 'ETest1ETest1ETest1ETest1ETest1ETest1', 1),
(2, 'ETest1', 'ETest1', NULL, NULL, 'fire ', 'ETest1', 'ETest1', NULL, 'ETest1ETest1ETest1ETest1ETest1ETest1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedbackID` int(11) NOT NULL,
  `clientID` int(11) DEFAULT NULL,
  `feedbackRating` varchar(10) DEFAULT NULL,
  `feedbackComments` varchar(250) DEFAULT NULL,
  `feedbackSentDateTime` datetime DEFAULT current_timestamp(),
  `feedbackRecievedDateTime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedbackID`, `clientID`, `feedbackRating`, `feedbackComments`, `feedbackSentDateTime`, `feedbackRecievedDateTime`) VALUES
(5, 10001, '4', '', '2025-05-04 22:21:11', '2025-05-04 22:21:11'),
(6, NULL, '', '', '2025-05-05 00:16:20', '2025-05-05 00:16:20'),
(10, 10003, '1', 'really bad', '2025-05-05 14:21:09', '2025-05-05 14:21:09'),
(13, 10003, '5', 'really good, thanks ', '2025-05-05 14:50:53', '2025-05-05 14:50:53');

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `managerID` int(11) NOT NULL,
  `managerFirstName` varchar(250) NOT NULL,
  `managerLastName` varchar(250) NOT NULL,
  `managerEmail` varchar(250) DEFAULT NULL,
  `managerPhone` varchar(50) DEFAULT NULL,
  `managerUsername` varchar(100) NOT NULL,
  `managerPassword` varchar(100) NOT NULL,
  `managerStatus` varchar(50) DEFAULT NULL,
  `managerNotes` varchar(300) DEFAULT NULL,
  `managerIsActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`managerID`, `managerFirstName`, `managerLastName`, `managerEmail`, `managerPhone`, `managerUsername`, `managerPassword`, `managerStatus`, `managerNotes`, `managerIsActive`) VALUES
(1, 'MTest1', 'MTest1', NULL, NULL, 'MTest1', 'MTest1', NULL, 'MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1MTest1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notificationID` int(11) NOT NULL,
  `clientID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `notificationType` varchar(50) DEFAULT NULL,
  `notificationDetails` varchar(250) DEFAULT NULL,
  `notificationDateTime` datetime DEFAULT current_timestamp(),
  `notificationIsRead` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `paymentID` int(11) NOT NULL,
  `clientID` int(11) DEFAULT NULL,
  `invoiceReference` int(11) DEFAULT NULL,
  `paymentAmount` decimal(10,2) DEFAULT NULL,
  `paymentDateTime` datetime DEFAULT current_timestamp(),
  `paymentIsPaid` tinyint(1) DEFAULT 0,
  `paymentNotes` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`paymentID`, `clientID`, `invoiceReference`, `paymentAmount`, `paymentDateTime`, `paymentIsPaid`, `paymentNotes`) VALUES
(1, 10001, 5, 3000.00, '2025-05-05 01:16:20', 1, '0'),
(2, 10001, 6, NULL, '2025-05-05 01:16:20', 1, ''),
(3, 10002, 15, 0.10, '2025-05-05 15:15:15', 1, '0');

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `reminderID` int(11) NOT NULL,
  `clientID` int(11) NOT NULL,
  `reminderDate` date NOT NULL,
  `reminderType` varchar(100) NOT NULL,
  `reminderDetails` text DEFAULT NULL,
  `reminderStatus` varchar(50) DEFAULT 'Active',
  `createdDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`reminderID`, `clientID`, `reminderDate`, `reminderType`, `reminderDetails`, `reminderStatus`, `createdDate`) VALUES
(1, 10002, '2026-05-09', 'Annual Service', 'Annual service reminder for client #10002. Previous service on 2025-05-09.', 'Active', '2025-05-04 23:29:14');

-- --------------------------------------------------------

--
-- Table structure for table `salesteam`
--

CREATE TABLE `salesteam` (
  `SalesID` int(11) NOT NULL,
  `salesFirstName` varchar(250) NOT NULL,
  `salesLastName` varchar(250) NOT NULL,
  `salesEmail` varchar(250) DEFAULT NULL,
  `salesPhone` varchar(50) DEFAULT NULL,
  `salesUsername` varchar(100) NOT NULL,
  `salesPassword` varchar(100) NOT NULL,
  `salesStatus` varchar(50) DEFAULT NULL,
  `salesNotes` varchar(300) DEFAULT NULL,
  `salesIsActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salesteam`
--

INSERT INTO `salesteam` (`SalesID`, `salesFirstName`, `salesLastName`, `salesEmail`, `salesPhone`, `salesUsername`, `salesPassword`, `salesStatus`, `salesNotes`, `salesIsActive`) VALUES
(1, 'STest1', 'STest1', NULL, NULL, 'STest1', 'STest1', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `schedulediary`
--

CREATE TABLE `schedulediary` (
  `scheduleID` int(11) NOT NULL,
  `engineerID` int(11) DEFAULT NULL,
  `clientID` int(11) DEFAULT NULL,
  `scheduleDate` date DEFAULT NULL,
  `ScheduleStartTime` time DEFAULT NULL,
  `scheduleEndTime` time DEFAULT NULL,
  `scheduleJobType` varchar(50) DEFAULT NULL,
  `scheduleIsAnnualService` tinyint(1) DEFAULT 0,
  `scheduleStatus` varchar(50) DEFAULT NULL,
  `scheduleDetails` varchar(250) DEFAULT NULL,
  `scheduleNotes` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedulediary`
--

INSERT INTO `schedulediary` (`scheduleID`, `engineerID`, `clientID`, `scheduleDate`, `ScheduleStartTime`, `scheduleEndTime`, `scheduleJobType`, `scheduleIsAnnualService`, `scheduleStatus`, `scheduleDetails`, `scheduleNotes`) VALUES
(5, 1, 10001, '2025-03-10', '08:00:00', '09:00:00', 'Annual Service ', 1, 'Complete', 'TESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTI', 'TESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTINGTESTI'),
(6, 2, 10001, '2025-03-11', '08:00:00', '09:00:00', 'Installation', 0, 'not completed ', 'n/a', 'n/a'),
(7, 1, 10002, '2025-04-25', '08:00:00', '09:00:00', 'Installation', 0, 'Complete', 'done ', 'great '),
(8, 1, 10003, '2025-04-25', '11:00:00', '13:00:00', 'Annual Service ', 1, 'in progress', 'na', 'na'),
(9, 2, 10003, '2025-04-26', '09:00:00', '10:00:00', 'Annual Service', 1, 'in progress', 'na', 'na'),
(10, 1, 10003, '2025-04-26', '12:00:00', '13:00:00', 'Installation', 0, 'in progress', 'na', 'na'),
(11, 1, 10002, '2025-04-28', '08:00:00', '13:00:00', 'Installation', 0, 'in progress', 'test1', 'test2'),
(12, 1, 10002, '2025-05-05', '09:00:00', '12:00:00', 'Installation', 0, 'rebook', '12', '12'),
(13, 1, 10003, '2025-05-07', '09:00:00', '10:00:00', 'Installation', 0, 'in progress', 'we1', 're1'),
(15, 2, 10002, '2025-05-09', '09:00:00', '14:00:00', 'Annual Service', 1, 'in progress', '12', '423'),
(16, 1, 10003, '2025-05-05', '13:00:00', '14:00:00', 'Annual Service', 1, 'in progress', 'annual', 'annual');

-- --------------------------------------------------------

--
-- Table structure for table `schedulefiles`
--

CREATE TABLE `schedulefiles` (
  `fileID` int(11) NOT NULL,
  `scheduleID` int(11) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `fileType` varchar(100) NOT NULL,
  `filePath` varchar(500) NOT NULL,
  `fileSize` int(11) NOT NULL,
  `fileDateTime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedulefiles`
--

INSERT INTO `schedulefiles` (`fileID`, `scheduleID`, `fileName`, `fileType`, `filePath`, `fileSize`, `fileDateTime`) VALUES
(1, 13, 'CV _ Hasan Khan.pdf', 'application/pdf', 'uploads/1746397213_CV _ Hasan Khan.pdf', 110037, '2025-05-04 23:20:13'),
(2, 15, 'MOOSE MAN SCREENPLAY_HasanKhan.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'uploads/1746397754_MOOSE MAN SCREENPLAY_HasanKhan.docx', 28998, '2025-05-04 23:29:14');

-- --------------------------------------------------------

--
-- Table structure for table `surveydiary`
--

CREATE TABLE `surveydiary` (
  `surveyID` int(11) NOT NULL,
  `surveyDate` date DEFAULT NULL,
  `surveyTime` time DEFAULT NULL,
  `surveyCreatedByID` varchar(250) DEFAULT NULL,
  `surveyNotes` varchar(250) DEFAULT NULL,
  `surveyStatus` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surveydiary`
--

INSERT INTO `surveydiary` (`surveyID`, `surveyDate`, `surveyTime`, `surveyCreatedByID`, `surveyNotes`, `surveyStatus`) VALUES
(1, '2025-03-10', '08:00:00', 'ATest - ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ', 'ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 VATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 A', 'Accepted'),
(2, '2025-03-11', '08:00:00', 'Test1 - Admin', 'ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 AT', 'accepted'),
(3, '2025-03-12', '08:00:00', 'Test1 - Admin', 'ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 AT', 'accepted'),
(4, '2025-03-13', '08:00:00', 'Test1 - Admin', 'ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 AT', 'accepted'),
(5, '2025-03-14', '08:00:00', 'Test1 - Admin', 'ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 AT', 'accepted'),
(6, '2025-03-15', '08:00:00', 'Test1 - Admin', 'ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 AT', 'accepted'),
(7, '2025-03-16', '08:00:00', 'ATest - ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ', 'ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 ATest 1 AT', 'accepted'),
(8, '2025-05-07', '08:00:00', 'Test1 - Admin', '123213213e', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `sustainabilitymetrics`
--

CREATE TABLE `sustainabilitymetrics` (
  `metricID` int(11) NOT NULL,
  `energyUsageWh` double DEFAULT NULL COMMENT 'Energy in Wh',
  `carbonOffsetKg` double DEFAULT NULL COMMENT 'Carbon offset in kg CO₂',
  `waterUsageMl` double DEFAULT NULL COMMENT 'Water usage in mL',
  `performanceScore` int(3) DEFAULT NULL COMMENT 'Performance score 0–100',
  `metricMeasurementDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `metricNotes` varchar(250) DEFAULT NULL,
  `sustainabilityScore` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sustainabilitymetrics`
--

INSERT INTO `sustainabilitymetrics` (`metricID`, `energyUsageWh`, `carbonOffsetKg`, `waterUsageMl`, `performanceScore`, `metricMeasurementDateTime`, `metricNotes`, `sustainabilityScore`) VALUES
(1, 0.00201234375, 0.0000010061718952476369, 0.40246875, 100, '2025-04-30 21:02:59', 'auto-log', 0),
(2, 0.03751305355118962, 0.000018756533811740743, 7.502610710237924, 88, '2025-04-30 21:03:00', 'auto-log', 0),
(3, 0.04325946059958802, 0.000021629739656698665, 8.651892119917603, 88, '2025-04-30 21:03:01', 'auto-log', 0),
(4, 0.05280037195959302, 0.00002640019991919291, 10.560074391918604, 88, '2025-04-30 21:03:02', 'auto-log', 0),
(5, 0.033320787528881055, 0.000016660399315814935, 6.664157505776211, 88, '2025-04-30 21:03:03', 'auto-log', 0),
(6, 0.005427886947571839, 0.0000027139436210957034, 1.0855773895143679, 88, '2025-04-30 21:03:04', 'auto-log', 0),
(7, 0.0013415625, 0.0000006707812589989498, 0.2683125, 100, '2025-04-30 21:03:10', 'auto-log', 0),
(8, 0.012722933359157149, 0.00000636146748894374, 2.54458667183143, 86, '2025-04-30 21:03:11', 'auto-log', 0),
(9, 0.07605982976401474, 0.000038029943807495886, 15.211965952802947, 86, '2025-04-30 21:03:13', 'auto-log', 0),
(10, 0.11125513919418141, 0.00005562763148562069, 22.251027838836283, 86, '2025-04-30 21:03:13', 'auto-log', 0),
(11, 0.08975182141966245, 0.000044875950986778474, 17.95036428393249, 86, '2025-04-30 21:03:14', 'auto-log', 0),
(12, 0.10264019162771998, 0.00005132014848890468, 20.528038325543996, 86, '2025-04-30 21:03:15', 'auto-log', 0),
(13, 0.10279638962483063, 0.00005139824764790391, 20.55927792496613, 86, '2025-04-30 21:03:17', 'auto-log', 0),
(14, 0.07318359990640827, 0.00003659182673240062, 14.636719981281654, 86, '2025-04-30 21:03:17', 'auto-log', 0),
(15, 0.004840923089306918, 0.0000024204616618261406, 0.9681846178613837, 86, '2025-04-30 21:03:19', 'auto-log', 0),
(16, 0.017293085611631398, 0.00000864654430106975, 3.4586171223262796, 86, '2025-04-30 21:03:20', 'auto-log', 0),
(17, 0.002111898926451191, 0.0000010559494855261809, 0.42237978529023823, 86, '2025-04-30 21:03:21', 'auto-log', 0),
(18, 0.0008837063909866848, 0.00000044185319939802736, 0.17674127819733695, 86, '2025-04-30 21:03:22', 'auto-log', 0),
(19, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 86, '2025-04-30 21:03:23', 'auto-log', 0),
(20, 0.0005968773192731605, 0.00000029843866141789295, 0.1193754638546321, 86, '2025-04-30 21:03:23', 'auto-log', 0),
(21, 0.00026385937500000004, 0.00000013192968784810888, 0.05277187500000001, 86, '2025-04-30 21:03:25', 'auto-log', 0),
(22, 0.016336939371272487, 0.000008168471020114184, 3.2673878742544975, 86, '2025-04-30 21:03:25', 'auto-log', 0),
(23, 0.016596480500076928, 0.000008298241627254288, 3.3192961000153858, 86, '2025-04-30 21:03:26', 'auto-log', 0),
(24, 0.005966105939526286, 0.0000029830531477352435, 1.1932211879052572, 86, '2025-04-30 21:03:27', 'auto-log', 0),
(25, 0.0023008917039054506, 0.0000011504458784232384, 0.4601783407810901, 86, '2025-04-30 21:03:28', 'auto-log', 0),
(26, 0.0021823034049309794, 0.0000010911517262777305, 0.4364606809861959, 86, '2025-04-30 21:03:30', 'auto-log', 0),
(27, 0.001685981643916166, 0.0000008429908361707536, 0.3371963287832332, 86, '2025-04-30 21:03:30', 'auto-log', 0),
(28, 0.002542117980084237, 0.0000012710590223539376, 0.5084235960168474, 86, '2025-04-30 21:03:32', 'auto-log', 0),
(29, 0.0032104146170465683, 0.0000016052073600570944, 0.6420829234093137, 86, '2025-04-30 21:03:33', 'auto-log', 0),
(30, 0.002127819078392058, 0.0000010639095618340993, 0.4255638156784116, 86, '2025-04-30 21:03:34', 'auto-log', 0),
(31, 0.0024823993149807795, 0.0000012411996883019215, 0.4964798629961559, 86, '2025-04-30 21:03:35', 'auto-log', 0),
(32, 0.001133793639468262, 0.0000005668968261615712, 0.22675872789365242, 86, '2025-04-30 21:03:35', 'auto-log', 0),
(33, 0.0021117770423130133, 0.000001055888543454518, 0.42235540846260267, 86, '2025-04-30 21:03:37', 'auto-log', 0),
(34, 0.0009426647938579555, 0.0000004713324013720623, 0.1885329587715911, 86, '2025-04-30 21:03:37', 'auto-log', 0),
(35, 0.0016975952314312165, 0.0000008487976301247562, 0.3395190462862433, 86, '2025-04-30 21:03:39', 'auto-log', 0),
(36, 0.00226829012075183, 0.0000011341450861016153, 0.453658024150366, 86, '2025-04-30 21:03:40', 'auto-log', 0),
(37, 0.0020651754016151242, 0.0000010325877221323095, 0.41303508032302483, 86, '2025-04-30 21:03:41', 'auto-log', 0),
(38, 0.0017133754229014158, 0.0000008566877261289846, 0.3426750845802832, 86, '2025-04-30 21:03:42', 'auto-log', 0),
(39, 0.0008319424071997055, 0.0000004159712070604936, 0.1663884814399411, 86, '2025-04-30 21:03:43', 'auto-log', 0),
(40, 0.01023659529356682, 0.0000051182981707228254, 2.047319058713364, 86, '2025-04-30 21:03:44', 'auto-log', 0),
(41, 0.06657827834028969, 0.00003328916133348058, 13.315655668057937, 86, '2025-04-30 21:03:45', 'auto-log', 0),
(42, 0.071376607272533, 0.00003568832910936683, 14.275321454506601, 86, '2025-04-30 21:03:46', 'auto-log', 0),
(43, 0.07610969058540469, 0.00003805487425612736, 15.221938117080938, 86, '2025-04-30 21:03:46', 'auto-log', 0),
(44, 0.027712404087612, 0.000013856205883692703, 5.5424808175224, 86, '2025-04-30 21:03:47', 'auto-log', 0),
(45, 0.0032840336745534444, 0.000001642016891201108, 0.6568067349106889, 86, '2025-04-30 21:03:49', 'auto-log', 0),
(46, 0.0015527847174628987, 0.0000007763923707871514, 0.31055694349257973, 86, '2025-04-30 21:03:50', 'auto-log', 0),
(47, 0.003265188054092636, 0.000001632594080353583, 0.6530376108185272, 86, '2025-04-30 21:03:50', 'auto-log', 0),
(48, 0.008315208366882987, 0.000004157604529154944, 1.6630416733765974, 86, '2025-04-30 21:03:52', 'auto-log', 0),
(49, 0.0006640006578777102, 0.00000033200033114333946, 0.13280013157554205, 86, '2025-04-30 21:03:53', 'auto-log', 0),
(50, 0.054552112401489695, 0.000027276071080409687, 10.910422480297939, 86, '2025-04-30 21:03:53', 'auto-log', 0),
(51, 0.09439614342995581, 0.00004719811626813738, 18.879228685991162, 86, '2025-04-30 21:03:54', 'auto-log', 0),
(52, 0.03204593678680138, 0.000016022973528111014, 6.409187357360276, 86, '2025-04-30 21:03:56', 'auto-log', 0),
(53, 0.015329752680213485, 0.00000766487751511333, 3.065950536042697, 86, '2025-04-30 21:03:56', 'auto-log', 0),
(54, 0.011687202214229735, 0.000005843601790068346, 2.337440442845947, 86, '2025-04-30 21:03:58', 'auto-log', 0),
(55, 0.006818246853105084, 0.0000034091236589949932, 1.3636493706210169, 86, '2025-04-30 21:03:58', 'auto-log', 0),
(56, 0.016367112461822167, 0.000008183557570322936, 3.2734224923644333, 86, '2025-04-30 21:04:00', 'auto-log', 0),
(57, 0.012624010590123422, 0.0000063120060918899284, 2.5248021180246845, 86, '2025-04-30 21:04:01', 'auto-log', 0),
(58, 0.015054003308830843, 0.0000075270027875305, 3.0108006617661687, 86, '2025-04-30 21:04:01', 'auto-log', 0),
(59, 0.043399723138497215, 0.000021699870986928455, 8.679944627699443, 86, '2025-04-30 21:04:02', 'auto-log', 0),
(60, 0.0779606338995461, 0.00003898034733907525, 15.592126779909222, 86, '2025-04-30 21:04:03', 'auto-log', 0),
(61, 0.05924840731556534, 0.000029624221209651518, 11.849681463113068, 86, '2025-04-30 21:04:05', 'auto-log', 0),
(62, 0.021529573098569874, 0.000010764788866897528, 4.305914619713975, 86, '2025-04-30 21:04:05', 'auto-log', 0),
(63, 0.011591697629420436, 0.000005795849486547488, 2.3183395258840873, 86, '2025-04-30 21:04:06', 'auto-log', 0),
(64, 0.002438967231517774, 0.0000012194836455016927, 0.4877934463035548, 86, '2025-04-30 21:04:07', 'auto-log', 0),
(65, 0.0026691227334215656, 0.0000013345614023318635, 0.5338245466843131, 86, '2025-04-30 21:04:09', 'auto-log', 0),
(66, 0.004292914644313627, 0.000002146457414302394, 0.8585829288627254, 86, '2025-04-30 21:04:09', 'auto-log', 0),
(67, 0.002765394149387197, 0.0000013826971129306226, 0.5530788298774394, 86, '2025-04-30 21:04:11', 'auto-log', 0),
(68, 0.0026885137717998003, 0.0000013442569220404316, 0.5377027543599601, 86, '2025-04-30 21:04:11', 'auto-log', 0),
(69, 0.04216607819263952, 0.00002108304798621051, 8.433215638527903, 86, '2025-04-30 21:04:13', 'auto-log', 0),
(70, 0.034694448297268886, 0.000017347230167158155, 6.9388896594537774, 86, '2025-04-30 21:04:13', 'auto-log', 0),
(71, 0.015136853663086657, 0.000007568427977165023, 3.0273707326173316, 86, '2025-04-30 21:04:14', 'auto-log', 0),
(72, 0.010753303284978087, 0.000005376652220656702, 2.1506606569956173, 86, '2025-04-30 21:04:15', 'auto-log', 0),
(73, 0.016245435171102465, 0.000008122718905122052, 3.249087034220493, 86, '2025-04-30 21:04:16', 'auto-log', 0),
(74, 0.007948479709548411, 0.000003974240170665855, 1.589695941909682, 86, '2025-04-30 21:04:17', 'auto-log', 0),
(75, 0.0012198252559654649, 0.0000006099126354226008, 0.24396505119309297, 86, '2025-04-30 21:04:18', 'auto-log', 0),
(76, 0.002323461753228272, 0.0000011617309036065086, 0.4646923506456544, 86, '2025-04-30 21:04:20', 'auto-log', 0),
(77, 0.0029596913033865862, 0.0000014798456954921565, 0.5919382606773173, 86, '2025-04-30 21:04:20', 'auto-log', 0),
(78, 0.009439700546862793, 0.00000471985071897113, 1.8879401093725587, 86, '2025-04-30 21:04:22', 'auto-log', 0),
(79, 0.003681710589974886, 0.0000018408553627624073, 0.7363421179949772, 86, '2025-04-30 21:04:22', 'auto-log', 0),
(80, 0.005704084944366478, 0.000002852042634866164, 1.1408169888732955, 86, '2025-04-30 21:04:24', 'auto-log', 0),
(81, 0.0033859666851010412, 0.0000016929833998743726, 0.6771933370202082, 86, '2025-04-30 21:04:25', 'auto-log', 0),
(82, 0.0065329442593124705, 0.000003266472343053039, 1.3065888518624942, 86, '2025-04-30 21:04:26', 'auto-log', 0),
(83, 0.017215602753092304, 0.000008607802858431042, 3.4431205506184606, 86, '2025-04-30 21:04:27', 'auto-log', 0),
(84, 0.015258777725016465, 0.000007629390026659721, 3.051755545003293, 86, '2025-04-30 21:04:28', 'auto-log', 0),
(85, 0.007918819688329706, 0.00000395941015770338, 1.5837639376659411, 86, '2025-04-30 21:04:29', 'auto-log', 0),
(86, 0.003998750806090888, 0.000001999375482995484, 0.7997501612181777, 86, '2025-04-30 21:04:30', 'auto-log', 0),
(87, 0.009716353776712903, 0.0000048581773603941045, 1.9432707553425805, 86, '2025-04-30 21:04:30', 'auto-log', 0),
(88, 0.009435558114438553, 0.000004717779502368061, 1.8871116228877105, 86, '2025-04-30 21:04:32', 'auto-log', 0),
(89, 0.004012265017590486, 0.0000020061325892865954, 0.8024530035180971, 86, '2025-04-30 21:04:33', 'auto-log', 0),
(90, 0.0073137627200312, 0.000003656881627471226, 1.46275254400624, 86, '2025-04-30 21:04:34', 'auto-log', 0),
(91, 0.008612191686164356, 0.000004306096213931407, 1.7224383372328713, 86, '2025-04-30 21:04:35', 'auto-log', 0),
(92, 0.0019141312697562392, 0.0000009570656531976123, 0.3828262539512478, 86, '2025-04-30 21:04:35', 'auto-log', 0),
(93, 0.00201234375, 0.0000010061718952476369, 0.40246875, 100, '2025-05-04 12:33:55', 'auto-log', 0),
(94, 0.014208888944114146, 0.000007104445481519699, 2.8417777888228293, 19, '2025-05-04 12:33:58', 'auto-log', 0),
(95, 0.006171292589225391, 0.0000030856464850369566, 1.2342585178450782, 19, '2025-05-04 12:33:59', 'auto-log', 0),
(96, 0.012514309873287176, 0.000006257155719683346, 2.5028619746574354, 19, '2025-05-04 12:34:00', 'auto-log', 0),
(97, 0.0210968909298643, 0.000010548447690326184, 4.21937818597286, 19, '2025-05-04 12:34:01', 'auto-log', 0),
(98, 0.004744590402052324, 0.0000023722953135818523, 0.9489180804104648, 19, '2025-05-04 12:34:02', 'auto-log', 0),
(99, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:03', 'auto-log', 0),
(100, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:04', 'auto-log', 0),
(101, 0.017233370515313446, 0.00000861668674260202, 3.4466741030626893, 19, '2025-05-04 12:34:05', 'auto-log', 0),
(102, 0.010692700604876256, 0.00000534635087410736, 2.138540120975251, 19, '2025-05-04 12:34:06', 'auto-log', 0),
(103, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:07', 'auto-log', 0),
(104, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:08', 'auto-log', 0),
(105, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:09', 'auto-log', 0),
(106, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:10', 'auto-log', 0),
(107, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:11', 'auto-log', 0),
(108, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:12', 'auto-log', 0),
(109, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:13', 'auto-log', 0),
(110, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:14', 'auto-log', 0),
(111, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:15', 'auto-log', 0),
(112, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:16', 'auto-log', 0),
(113, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:17', 'auto-log', 0),
(114, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:18', 'auto-log', 0),
(115, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:19', 'auto-log', 0),
(116, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:20', 'auto-log', 0),
(117, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:21', 'auto-log', 0),
(118, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:22', 'auto-log', 0),
(119, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:23', 'auto-log', 0),
(120, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:24', 'auto-log', 0),
(121, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:25', 'auto-log', 0),
(122, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:26', 'auto-log', 0),
(123, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:27', 'auto-log', 0),
(124, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:28', 'auto-log', 0),
(125, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:29', 'auto-log', 0),
(126, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:30', 'auto-log', 0),
(127, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:31', 'auto-log', 0),
(128, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:32', 'auto-log', 0),
(129, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:33', 'auto-log', 0),
(130, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:34', 'auto-log', 0),
(131, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:35', 'auto-log', 0),
(132, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:36', 'auto-log', 0),
(133, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:37', 'auto-log', 0),
(134, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:38', 'auto-log', 0),
(135, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:39', 'auto-log', 0),
(136, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:40', 'auto-log', 0),
(137, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:41', 'auto-log', 0),
(138, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:42', 'auto-log', 0),
(139, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:43', 'auto-log', 0),
(140, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:44', 'auto-log', 0),
(141, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:45', 'auto-log', 0),
(142, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:46', 'auto-log', 0),
(143, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:47', 'auto-log', 0),
(144, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:48', 'auto-log', 0),
(145, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:49', 'auto-log', 0),
(146, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:50', 'auto-log', 0),
(147, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:51', 'auto-log', 0),
(148, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:52', 'auto-log', 0),
(149, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:53', 'auto-log', 0),
(150, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:54', 'auto-log', 0),
(151, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:55', 'auto-log', 0),
(152, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:56', 'auto-log', 0),
(153, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:57', 'auto-log', 0),
(154, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:58', 'auto-log', 0),
(155, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:34:59', 'auto-log', 0),
(156, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:35:00', 'auto-log', 0),
(157, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:35:01', 'auto-log', 0),
(158, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:35:02', 'auto-log', 0),
(159, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:35:03', 'auto-log', 0),
(160, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:35:04', 'auto-log', 0),
(161, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:35:05', 'auto-log', 0),
(162, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:35:39', 'auto-log', 0),
(163, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:36:39', 'auto-log', 0),
(164, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:37:39', 'auto-log', 0),
(165, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:38:39', 'auto-log', 0),
(166, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:39:39', 'auto-log', 0),
(167, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:40:39', 'auto-log', 0),
(168, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:41:39', 'auto-log', 0),
(169, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:42:39', 'auto-log', 0),
(170, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:43:39', 'auto-log', 0),
(171, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:44:39', 'auto-log', 0),
(172, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:08', 'auto-log', 0),
(173, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:08', 'auto-log', 0),
(174, 0.02819929662271369, 0.000014099652287358496, 5.639859324542738, 19, '2025-05-04 12:45:09', 'auto-log', 0),
(175, 0.005386261414948022, 0.000002693130852533071, 1.0772522829896043, 19, '2025-05-04 12:45:10', 'auto-log', 0),
(176, 0.010241780318360006, 0.000005120890683650323, 2.048356063672001, 19, '2025-05-04 12:45:11', 'auto-log', 0),
(177, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:12', 'auto-log', 0),
(178, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:13', 'auto-log', 0),
(179, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:14', 'auto-log', 0),
(180, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:15', 'auto-log', 0),
(181, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:16', 'auto-log', 0),
(182, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:17', 'auto-log', 0),
(183, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:18', 'auto-log', 0),
(184, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:19', 'auto-log', 0),
(185, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:20', 'auto-log', 0),
(186, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:21', 'auto-log', 0),
(187, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:22', 'auto-log', 0),
(188, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:23', 'auto-log', 0),
(189, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:24', 'auto-log', 0),
(190, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:25', 'auto-log', 0),
(191, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:26', 'auto-log', 0),
(192, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:27', 'auto-log', 0),
(193, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:28', 'auto-log', 0),
(194, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:29', 'auto-log', 0),
(195, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:30', 'auto-log', 0),
(196, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:31', 'auto-log', 0),
(197, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:32', 'auto-log', 0),
(198, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:33', 'auto-log', 0),
(199, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:34', 'auto-log', 0),
(200, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:35', 'auto-log', 0),
(201, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:36', 'auto-log', 0),
(202, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:37', 'auto-log', 0),
(203, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:38', 'auto-log', 0),
(204, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:39', 'auto-log', 0),
(205, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:40', 'auto-log', 0),
(206, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:41', 'auto-log', 0),
(207, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:42', 'auto-log', 0),
(208, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:43', 'auto-log', 0),
(209, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:44', 'auto-log', 0),
(210, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 19, '2025-05-04 12:45:45', 'auto-log', 0),
(211, 0.013586990640157701, 0.000006793496243110424, 2.7173981280315402, 19, '2025-05-04 12:45:46', 'auto-log', 0),
(212, 0.00201234375, 0.0000010061718952476369, 0.40246875, 100, '2025-05-04 12:49:23', 'auto-log', 0),
(213, 0.003995455081292714, 0.000001997727620464664, 0.7990910162585428, 80, '2025-05-04 12:49:24', 'auto-log', 0),
(214, 0.012425409131509234, 0.000006212705337708578, 2.485081826301847, 80, '2025-05-04 12:49:25', 'auto-log', 0),
(215, 0.0047943181954223715, 0.0000023971592126386204, 0.9588636390844743, 80, '2025-05-04 12:49:26', 'auto-log', 0),
(216, 0.006489473638099245, 0.0000032447370296159636, 1.2978947276198491, 80, '2025-05-04 12:49:27', 'auto-log', 0),
(217, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 12:49:28', 'auto-log', 0),
(218, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 12:49:29', 'auto-log', 0),
(219, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 12:49:30', 'auto-log', 0),
(220, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 12:49:31', 'auto-log', 0),
(221, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 12:49:32', 'auto-log', 0),
(222, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 12:49:33', 'auto-log', 0),
(223, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 12:49:34', 'auto-log', 0),
(224, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 12:49:35', 'auto-log', 0),
(225, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 12:49:36', 'auto-log', 0),
(226, 0.013329293643503716, 0.000006664647710102204, 2.6658587287007434, 80, '2025-05-04 12:49:37', 'auto-log', 0),
(227, 0.00201234375, 0.0000010061718952476369, 0.40246875, 100, '2025-05-04 13:10:57', 'auto-log', 0),
(228, 0.0026501477179601707, 0.0000013250738940965001, 0.5300295435920341, 80, '2025-05-04 13:10:58', 'auto-log', 0),
(229, 0.001856229400500618, 0.0000009281147174782468, 0.3712458801001236, 80, '2025-05-04 13:10:59', 'auto-log', 0),
(230, 0.001432436047197953, 0.0000007162180338583416, 0.2864872094395906, 80, '2025-05-04 13:11:00', 'auto-log', 0),
(231, 0.008683195009853699, 0.000004341597881916227, 1.7366390019707398, 80, '2025-05-04 13:11:01', 'auto-log', 0),
(232, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:02', 'auto-log', 0),
(233, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:03', 'auto-log', 0),
(234, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:04', 'auto-log', 0),
(235, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:05', 'auto-log', 0),
(236, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:06', 'auto-log', 0),
(237, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:07', 'auto-log', 0),
(238, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:08', 'auto-log', 0),
(239, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:09', 'auto-log', 0),
(240, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:10', 'auto-log', 0),
(241, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:11', 'auto-log', 0),
(242, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:12', 'auto-log', 0),
(243, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:13', 'auto-log', 0),
(244, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:14', 'auto-log', 0),
(245, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:15', 'auto-log', 0),
(246, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:16', 'auto-log', 0),
(247, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:17', 'auto-log', 0),
(248, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:18', 'auto-log', 0),
(249, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:19', 'auto-log', 0),
(250, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:20', 'auto-log', 0),
(251, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:21', 'auto-log', 0),
(252, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:22', 'auto-log', 0),
(253, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:23', 'auto-log', 0),
(254, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:24', 'auto-log', 0),
(255, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:25', 'auto-log', 0),
(256, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:26', 'auto-log', 0),
(257, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:27', 'auto-log', 0),
(258, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:28', 'auto-log', 0),
(259, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:29', 'auto-log', 0),
(260, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:30', 'auto-log', 0),
(261, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:31', 'auto-log', 0),
(262, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:32', 'auto-log', 0),
(263, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:33', 'auto-log', 0),
(264, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:34', 'auto-log', 0),
(265, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:35', 'auto-log', 0),
(266, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:36', 'auto-log', 0),
(267, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:37', 'auto-log', 0),
(268, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:38', 'auto-log', 0),
(269, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:39', 'auto-log', 0),
(270, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:40', 'auto-log', 0),
(271, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:41', 'auto-log', 0),
(272, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:42', 'auto-log', 0),
(273, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:43', 'auto-log', 0),
(274, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:44', 'auto-log', 0),
(275, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:45', 'auto-log', 0),
(276, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:46', 'auto-log', 0),
(277, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:47', 'auto-log', 0),
(278, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:48', 'auto-log', 0),
(279, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:49', 'auto-log', 0),
(280, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:50', 'auto-log', 0),
(281, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:51', 'auto-log', 0),
(282, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:52', 'auto-log', 0),
(283, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:53', 'auto-log', 0),
(284, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:54', 'auto-log', 0),
(285, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:55', 'auto-log', 0),
(286, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:56', 'auto-log', 0),
(287, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:57', 'auto-log', 0),
(288, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:58', 'auto-log', 0),
(289, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:11:59', 'auto-log', 0),
(290, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:12:00', 'auto-log', 0),
(291, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:12:01', 'auto-log', 0),
(292, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:12:39', 'auto-log', 0),
(293, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:13:39', 'auto-log', 0),
(294, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:14:39', 'auto-log', 0),
(295, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:09', 'auto-log', 0),
(296, 0.021818453226628287, 0.00001090922899353865, 4.363690645325658, 80, '2025-05-04 13:15:09', 'auto-log', 0),
(297, 0.010059448308301607, 0.0000050297246601133055, 2.0118896616603212, 80, '2025-05-04 13:15:10', 'auto-log', 0),
(298, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:11', 'auto-log', 0),
(299, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:13', 'auto-log', 0),
(300, 0, 0, 0, 80, '2025-05-04 13:15:14', 'auto-log', 0),
(301, 0.00031171875, 0.00000015585937548584288, 0.062343749999999996, 80, '2025-05-04 13:15:14', 'auto-log', 0),
(302, 0, 0, 0, 80, '2025-05-04 13:15:17', 'auto-log', 0),
(303, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:17', 'auto-log', 0),
(304, 0.00031171875, 0.00000015585937548584288, 0.062343749999999996, 80, '2025-05-04 13:15:17', 'auto-log', 0),
(305, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:18', 'auto-log', 0),
(306, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:19', 'auto-log', 0),
(307, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:20', 'auto-log', 0),
(308, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:21', 'auto-log', 0),
(309, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:22', 'auto-log', 0),
(310, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:23', 'auto-log', 0),
(311, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:24', 'auto-log', 0),
(312, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:25', 'auto-log', 0),
(313, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:26', 'auto-log', 0),
(314, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:27', 'auto-log', 0),
(315, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:28', 'auto-log', 0),
(316, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:29', 'auto-log', 0),
(317, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:30', 'auto-log', 0),
(318, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:31', 'auto-log', 0),
(319, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:32', 'auto-log', 0),
(320, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:33', 'auto-log', 0),
(321, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:34', 'auto-log', 0),
(322, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:35', 'auto-log', 0),
(323, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:36', 'auto-log', 0),
(324, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:37', 'auto-log', 0),
(325, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:38', 'auto-log', 0),
(326, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:39', 'auto-log', 0),
(327, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:40', 'auto-log', 0),
(328, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:41', 'auto-log', 0),
(329, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:42', 'auto-log', 0),
(330, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:43', 'auto-log', 0),
(331, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:44', 'auto-log', 0),
(332, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:45', 'auto-log', 0),
(333, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:46', 'auto-log', 0),
(334, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:47', 'auto-log', 0),
(335, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:48', 'auto-log', 0),
(336, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:49', 'auto-log', 0),
(337, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:50', 'auto-log', 0),
(338, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:51', 'auto-log', 0),
(339, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:52', 'auto-log', 0),
(340, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:53', 'auto-log', 0),
(341, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:54', 'auto-log', 0),
(342, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:55', 'auto-log', 0),
(343, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:56', 'auto-log', 0),
(344, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:57', 'auto-log', 0),
(345, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:58', 'auto-log', 0),
(346, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:15:59', 'auto-log', 0),
(347, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:00', 'auto-log', 0),
(348, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:01', 'auto-log', 0),
(349, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:02', 'auto-log', 0),
(350, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:03', 'auto-log', 0),
(351, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:04', 'auto-log', 0),
(352, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:05', 'auto-log', 0),
(353, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:06', 'auto-log', 0),
(354, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:07', 'auto-log', 0),
(355, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:08', 'auto-log', 0),
(356, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:09', 'auto-log', 0),
(357, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:16:39', 'auto-log', 0),
(358, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:17:39', 'auto-log', 0),
(359, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:18:39', 'auto-log', 0),
(360, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:19:39', 'auto-log', 0),
(361, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:20:39', 'auto-log', 0),
(362, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:21:39', 'auto-log', 0),
(363, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:22:39', 'auto-log', 0),
(364, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:23:39', 'auto-log', 0),
(365, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:24:39', 'auto-log', 0),
(366, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:25:39', 'auto-log', 0),
(367, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:26:39', 'auto-log', 0),
(368, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:27:39', 'auto-log', 0),
(369, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:28:39', 'auto-log', 0),
(370, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:29:39', 'auto-log', 0),
(371, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:30:39', 'auto-log', 0),
(372, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:31:39', 'auto-log', 0),
(373, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:31:45', 'auto-log', 0),
(374, 0.02110265783523908, 0.00001055133114423038, 4.220531567047816, 80, '2025-05-04 13:31:45', 'auto-log', 0),
(375, 0.005048449199809585, 0.000002524224727338989, 1.009689839961917, 80, '2025-05-04 13:31:46', 'auto-log', 0),
(376, 0.015871824716859138, 0.000007935913618003668, 3.1743649433718275, 80, '2025-05-04 13:31:47', 'auto-log', 0),
(377, 0.011439843586910125, 0.000005719922447805168, 2.287968717382025, 80, '2025-05-04 13:31:48', 'auto-log', 0),
(378, 0.014128273057627708, 0.000007064137526854352, 2.825654611525542, 80, '2025-05-04 13:31:49', 'auto-log', 0),
(379, 0.008890701472183718, 0.0000044453511313147216, 1.7781402944367437, 80, '2025-05-04 13:31:50', 'auto-log', 0),
(380, 0.006958553300413914, 0.000003479276892314278, 1.3917106600827829, 80, '2025-05-04 13:31:51', 'auto-log', 0),
(381, 0.007705126061386429, 0.000003852563327538053, 1.5410252122772856, 80, '2025-05-04 13:31:52', 'auto-log', 0),
(382, 0.011852207927181117, 0.000005926104665964722, 2.3704415854362235, 80, '2025-05-04 13:31:53', 'auto-log', 0),
(383, 0.011932905985928943, 0.000005966453704935699, 2.3865811971857886, 80, '2025-05-04 13:31:54', 'auto-log', 0),
(384, 0.012159847782835015, 0.000006079924630726998, 2.431969556567003, 80, '2025-05-04 13:31:55', 'auto-log', 0),
(385, 0.009596706864637651, 0.00000479835389280274, 1.9193413729275304, 80, '2025-05-04 13:31:56', 'auto-log', 0),
(386, 0.019844153704674124, 0.000009922078821289244, 3.9688307409348247, 80, '2025-05-04 13:31:57', 'auto-log', 0),
(387, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:31:58', 'auto-log', 0),
(388, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:31:59', 'auto-log', 0),
(389, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:00', 'auto-log', 0),
(390, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:01', 'auto-log', 0),
(391, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:02', 'auto-log', 0),
(392, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:03', 'auto-log', 0),
(393, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:04', 'auto-log', 0),
(394, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:05', 'auto-log', 0),
(395, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:06', 'auto-log', 0),
(396, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:07', 'auto-log', 0),
(397, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:08', 'auto-log', 0),
(398, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:09', 'auto-log', 0),
(399, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:10', 'auto-log', 0),
(400, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:11', 'auto-log', 0),
(401, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:12', 'auto-log', 0),
(402, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:13', 'auto-log', 0),
(403, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:14', 'auto-log', 0),
(404, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:15', 'auto-log', 0),
(405, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:16', 'auto-log', 0),
(406, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:17', 'auto-log', 0),
(407, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:18', 'auto-log', 0),
(408, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:19', 'auto-log', 0),
(409, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:20', 'auto-log', 0),
(410, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:21', 'auto-log', 0),
(411, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:22', 'auto-log', 0),
(412, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:23', 'auto-log', 0),
(413, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:24', 'auto-log', 0),
(414, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:25', 'auto-log', 0),
(415, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:26', 'auto-log', 0),
(416, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:27', 'auto-log', 0),
(417, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:28', 'auto-log', 0),
(418, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:29', 'auto-log', 0),
(419, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:30', 'auto-log', 0),
(420, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:31', 'auto-log', 0),
(421, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:32', 'auto-log', 0),
(422, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:33', 'auto-log', 0),
(423, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:34', 'auto-log', 0),
(424, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:35', 'auto-log', 0),
(425, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:36', 'auto-log', 0),
(426, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:37', 'auto-log', 0),
(427, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:38', 'auto-log', 0),
(428, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:39', 'auto-log', 0),
(429, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:40', 'auto-log', 0),
(430, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:41', 'auto-log', 0),
(431, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:42', 'auto-log', 0),
(432, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:43', 'auto-log', 0),
(433, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:44', 'auto-log', 0),
(434, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:45', 'auto-log', 0),
(435, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:46', 'auto-log', 0),
(436, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:47', 'auto-log', 0),
(437, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:48', 'auto-log', 0),
(438, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:49', 'auto-log', 0),
(439, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:50', 'auto-log', 0),
(440, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:51', 'auto-log', 0),
(441, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:52', 'auto-log', 0),
(442, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:53', 'auto-log', 0),
(443, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:54', 'auto-log', 0),
(444, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:55', 'auto-log', 0),
(445, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:56', 'auto-log', 0);
INSERT INTO `sustainabilitymetrics` (`metricID`, `energyUsageWh`, `carbonOffsetKg`, `waterUsageMl`, `performanceScore`, `metricMeasurementDateTime`, `metricNotes`, `sustainabilityScore`) VALUES
(446, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:32:57', 'auto-log', 0),
(447, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:33:39', 'auto-log', 0),
(448, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:34:39', 'auto-log', 0),
(449, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:35:39', 'auto-log', 0),
(450, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:36:39', 'auto-log', 0),
(451, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:37:39', 'auto-log', 0),
(452, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:38:39', 'auto-log', 0),
(453, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:39:39', 'auto-log', 0),
(454, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:40:39', 'auto-log', 0),
(455, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:41:39', 'auto-log', 0),
(456, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:42:39', 'auto-log', 0),
(457, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:43:39', 'auto-log', 0),
(458, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:44:39', 'auto-log', 0),
(459, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:45:39', 'auto-log', 0),
(460, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:46:39', 'auto-log', 0),
(461, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:47:39', 'auto-log', 0),
(462, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:48:39', 'auto-log', 0),
(463, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:49:39', 'auto-log', 0),
(464, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:50:39', 'auto-log', 0),
(465, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:51:39', 'auto-log', 0),
(466, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:52:39', 'auto-log', 0),
(467, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:53:39', 'auto-log', 0),
(468, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:54:39', 'auto-log', 0),
(469, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:55:39', 'auto-log', 0),
(470, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 80, '2025-05-04 13:56:39', 'auto-log', 0),
(471, 0, 0, 0, 80, '2025-05-04 13:57:39', 'auto-log', 0),
(472, 0, 0, 0, 80, '2025-05-04 13:58:39', 'auto-log', 0),
(473, 0, 0, 0, 80, '2025-05-04 13:59:39', 'auto-log', 0),
(474, 0, 0, 0, 80, '2025-05-04 14:00:39', 'auto-log', 0),
(475, 0, 0, 0, 80, '2025-05-04 14:01:39', 'auto-log', 0),
(476, 0, 0, 0, 80, '2025-05-04 14:02:39', 'auto-log', 0),
(477, 0, 0, 0, 80, '2025-05-04 14:03:39', 'auto-log', 0),
(478, 0, 0, 0, 80, '2025-05-04 14:04:39', 'auto-log', 0),
(479, 0, 0, 0, 80, '2025-05-04 14:05:39', 'auto-log', 0),
(480, 0, 0, 0, 80, '2025-05-04 14:06:39', 'auto-log', 0),
(481, 0, 0, 0, 80, '2025-05-04 14:07:39', 'auto-log', 0),
(482, 0, 0, 0, 80, '2025-05-04 14:08:39', 'auto-log', 0),
(483, 0, 0, 0, 80, '2025-05-04 14:09:39', 'auto-log', 0),
(484, 0, 0, 0, 80, '2025-05-04 14:10:39', 'auto-log', 0),
(485, 0, 0, 0, 80, '2025-05-04 14:11:39', 'auto-log', 0),
(486, 0, 0, 0, 80, '2025-05-04 14:12:39', 'auto-log', 0),
(487, 0, 0, 0, 80, '2025-05-04 14:13:39', 'auto-log', 0),
(488, 0, 0, 0, 80, '2025-05-04 14:14:39', 'auto-log', 0),
(489, 0, 0, 0, 80, '2025-05-04 14:15:39', 'auto-log', 0),
(490, 0, 0, 0, 80, '2025-05-04 14:16:39', 'auto-log', 0),
(491, 0, 0, 0, 80, '2025-05-04 14:17:39', 'auto-log', 0),
(492, 0, 0, 0, 80, '2025-05-04 14:18:39', 'auto-log', 0),
(493, 0, 0, 0, 80, '2025-05-04 14:19:39', 'auto-log', 0),
(494, 0, 0, 0, 80, '2025-05-04 14:20:39', 'auto-log', 0),
(495, 0, 0, 0, 80, '2025-05-04 14:21:39', 'auto-log', 0),
(496, 0, 0, 0, 80, '2025-05-04 14:22:39', 'auto-log', 0),
(497, 0, 0, 0, 80, '2025-05-04 14:23:39', 'auto-log', 0),
(498, 0, 0, 0, 80, '2025-05-04 14:24:39', 'auto-log', 0),
(499, 0, 0, 0, 80, '2025-05-04 14:25:39', 'auto-log', 0),
(500, 0, 0, 0, 80, '2025-05-04 14:26:39', 'auto-log', 0),
(501, 0, 0, 0, 80, '2025-05-04 14:27:39', 'auto-log', 0),
(502, 0, 0, 0, 80, '2025-05-04 14:28:39', 'auto-log', 0),
(503, 0, 0, 0, 80, '2025-05-04 14:29:39', 'auto-log', 0),
(504, 0, 0, 0, 80, '2025-05-04 14:30:39', 'auto-log', 0),
(505, 0, 0, 0, 80, '2025-05-04 14:31:39', 'auto-log', 0),
(506, 0, 0, 0, 80, '2025-05-04 14:32:39', 'auto-log', 0),
(507, 0, 0, 0, 80, '2025-05-04 14:33:39', 'auto-log', 0),
(508, 0, 0, 0, 80, '2025-05-04 14:34:39', 'auto-log', 0),
(509, 0, 0, 0, 80, '2025-05-04 14:35:39', 'auto-log', 0),
(510, 0, 0, 0, 80, '2025-05-04 14:36:39', 'auto-log', 0),
(511, 0, 0, 0, 80, '2025-05-04 14:37:39', 'auto-log', 0),
(512, 0, 0, 0, 80, '2025-05-04 14:38:39', 'auto-log', 0),
(513, 0, 0, 0, 80, '2025-05-04 14:39:39', 'auto-log', 0),
(514, 0, 0, 0, 80, '2025-05-04 14:40:39', 'auto-log', 0),
(515, 0, 0, 0, 80, '2025-05-04 14:41:39', 'auto-log', 0),
(516, 0, 0, 0, 80, '2025-05-04 14:42:39', 'auto-log', 0),
(517, 0, 0, 0, 80, '2025-05-04 14:43:39', 'auto-log', 0),
(518, 0, 0, 0, 80, '2025-05-04 14:44:39', 'auto-log', 0),
(519, 0, 0, 0, 80, '2025-05-04 14:45:39', 'auto-log', 0),
(520, 0, 0, 0, 80, '2025-05-04 14:46:39', 'auto-log', 0),
(521, 0, 0, 0, 80, '2025-05-04 14:47:39', 'auto-log', 0),
(522, 0, 0, 0, 80, '2025-05-04 14:48:39', 'auto-log', 0),
(523, 0, 0, 0, 80, '2025-05-04 14:49:39', 'auto-log', 0),
(524, 0, 0, 0, 80, '2025-05-04 14:50:39', 'auto-log', 0),
(525, 0, 0, 0, 80, '2025-05-04 14:51:39', 'auto-log', 0),
(526, 0, 0, 0, 80, '2025-05-04 14:52:39', 'auto-log', 0),
(527, 0, 0, 0, 80, '2025-05-04 14:53:39', 'auto-log', 0),
(528, 0, 0, 0, 80, '2025-05-04 14:54:39', 'auto-log', 0),
(529, 0, 0, 0, 80, '2025-05-04 14:55:39', 'auto-log', 0),
(530, 0, 0, 0, 80, '2025-05-04 14:56:39', 'auto-log', 0),
(531, 0, 0, 0, 80, '2025-05-04 14:57:37', 'auto-log', 0),
(532, 0, 0, 0, 80, '2025-05-04 14:57:37', 'auto-log', 0),
(533, 0.027272368923988047, 0.000013636188180904557, 5.454473784797609, 80, '2025-05-04 14:57:38', 'auto-log', 0),
(534, 0, 0, 0, 80, '2025-05-04 14:57:39', 'auto-log', 0),
(535, 0, 0, 0, 80, '2025-05-04 14:57:40', 'auto-log', 0),
(536, 0, 0, 0, 80, '2025-05-04 14:57:41', 'auto-log', 0),
(537, 0, 0, 0, 80, '2025-05-04 14:57:42', 'auto-log', 0),
(538, 0, 0, 0, 80, '2025-05-04 14:57:43', 'auto-log', 0),
(539, 0, 0, 0, 80, '2025-05-04 14:57:44', 'auto-log', 0),
(540, 0, 0, 0, 80, '2025-05-04 14:57:45', 'auto-log', 0),
(541, 0, 0, 0, 80, '2025-05-04 14:57:46', 'auto-log', 0),
(542, 0, 0, 0, 80, '2025-05-04 14:57:47', 'auto-log', 0),
(543, 0, 0, 0, 80, '2025-05-04 14:57:48', 'auto-log', 0),
(544, 0, 0, 0, 80, '2025-05-04 14:57:49', 'auto-log', 0),
(545, 0, 0, 0, 80, '2025-05-04 14:57:50', 'auto-log', 0),
(546, 0, 0, 0, 80, '2025-05-04 14:57:51', 'auto-log', 0),
(547, 0, 0, 0, 80, '2025-05-04 14:57:52', 'auto-log', 0),
(548, 0, 0, 0, 80, '2025-05-04 14:57:53', 'auto-log', 0),
(549, 0, 0, 0, 80, '2025-05-04 14:57:54', 'auto-log', 0),
(550, 0, 0, 0, 80, '2025-05-04 14:57:55', 'auto-log', 0),
(551, 0, 0, 0, 80, '2025-05-04 14:57:56', 'auto-log', 0),
(552, 0, 0, 0, 80, '2025-05-04 14:57:57', 'auto-log', 0),
(553, 0, 0, 0, 80, '2025-05-04 14:57:58', 'auto-log', 0),
(554, 0, 0, 0, 80, '2025-05-04 14:57:59', 'auto-log', 0),
(555, 0, 0, 0, 80, '2025-05-04 14:58:00', 'auto-log', 0),
(556, 0, 0, 0, 80, '2025-05-04 14:58:01', 'auto-log', 0),
(557, 0, 0, 0, 80, '2025-05-04 14:58:02', 'auto-log', 0),
(558, 0, 0, 0, 80, '2025-05-04 14:58:03', 'auto-log', 0),
(559, 0, 0, 0, 80, '2025-05-04 14:58:04', 'auto-log', 0),
(560, 0, 0, 0, 80, '2025-05-04 14:58:05', 'auto-log', 0),
(561, 0, 0, 0, 80, '2025-05-04 14:58:06', 'auto-log', 0),
(562, 0, 0, 0, 80, '2025-05-04 14:58:07', 'auto-log', 0),
(563, 0, 0, 0, 80, '2025-05-04 14:58:08', 'auto-log', 0),
(564, 0, 0, 0, 80, '2025-05-04 14:58:09', 'auto-log', 0),
(565, 0, 0, 0, 80, '2025-05-04 14:58:10', 'auto-log', 0),
(566, 0, 0, 0, 80, '2025-05-04 14:58:11', 'auto-log', 0),
(567, 0, 0, 0, 80, '2025-05-04 14:58:12', 'auto-log', 0),
(568, 0, 0, 0, 80, '2025-05-04 14:58:13', 'auto-log', 0),
(569, 0, 0, 0, 80, '2025-05-04 14:58:14', 'auto-log', 0),
(570, 0, 0, 0, 80, '2025-05-04 14:58:15', 'auto-log', 0),
(571, 0, 0, 0, 80, '2025-05-04 14:58:16', 'auto-log', 0),
(572, 0, 0, 0, 80, '2025-05-04 14:58:17', 'auto-log', 0),
(573, 0, 0, 0, 80, '2025-05-04 14:58:18', 'auto-log', 0),
(574, 0, 0, 0, 80, '2025-05-04 14:58:19', 'auto-log', 0),
(575, 0, 0, 0, 80, '2025-05-04 14:58:20', 'auto-log', 0),
(576, 0, 0, 0, 80, '2025-05-04 14:58:21', 'auto-log', 0),
(577, 0, 0, 0, 80, '2025-05-04 14:58:22', 'auto-log', 0),
(578, 0, 0, 0, 80, '2025-05-04 14:58:23', 'auto-log', 0),
(579, 0, 0, 0, 80, '2025-05-04 14:58:24', 'auto-log', 0),
(580, 0, 0, 0, 80, '2025-05-04 14:58:25', 'auto-log', 0),
(581, 0, 0, 0, 80, '2025-05-04 14:58:26', 'auto-log', 0),
(582, 0, 0, 0, 80, '2025-05-04 14:58:27', 'auto-log', 0),
(583, 0, 0, 0, 80, '2025-05-04 14:58:28', 'auto-log', 0),
(584, 0, 0, 0, 80, '2025-05-04 14:58:29', 'auto-log', 0),
(585, 0, 0, 0, 80, '2025-05-04 14:58:30', 'auto-log', 0),
(586, 0, 0, 0, 80, '2025-05-04 14:58:31', 'auto-log', 0),
(587, 0, 0, 0, 80, '2025-05-04 14:58:32', 'auto-log', 0),
(588, 0, 0, 0, 80, '2025-05-04 14:58:33', 'auto-log', 0),
(589, 0, 0, 0, 80, '2025-05-04 14:58:34', 'auto-log', 0),
(590, 0, 0, 0, 80, '2025-05-04 14:58:35', 'auto-log', 0),
(591, 0, 0, 0, 80, '2025-05-04 14:58:36', 'auto-log', 0),
(592, 0, 0, 0, 80, '2025-05-04 14:58:37', 'auto-log', 0),
(593, 0, 0, 0, 80, '2025-05-04 14:58:39', 'auto-log', 0),
(594, 0, 0, 0, 80, '2025-05-04 14:59:39', 'auto-log', 0),
(595, 0, 0, 0, 80, '2025-05-04 15:00:39', 'auto-log', 0),
(596, 0, 0, 0, 80, '2025-05-04 15:01:39', 'auto-log', 0),
(597, 0, 0, 0, 80, '2025-05-04 15:02:39', 'auto-log', 0),
(598, 0, 0, 0, 80, '2025-05-04 15:03:39', 'auto-log', 0),
(599, 0, 0, 0, 80, '2025-05-04 15:04:39', 'auto-log', 0),
(600, 0, 0, 0, 80, '2025-05-04 15:05:39', 'auto-log', 0),
(601, 0, 0, 0, 80, '2025-05-04 15:06:39', 'auto-log', 0),
(602, 0, 0, 0, 80, '2025-05-04 15:07:39', 'auto-log', 0),
(603, 0, 0, 0, 80, '2025-05-04 15:08:39', 'auto-log', 0),
(604, 0, 0, 0, 80, '2025-05-04 15:09:39', 'auto-log', 0),
(605, 0, 0, 0, 80, '2025-05-04 15:10:39', 'auto-log', 0),
(606, 0, 0, 0, 80, '2025-05-04 15:11:39', 'auto-log', 0),
(607, 0, 0, 0, 80, '2025-05-04 15:12:39', 'auto-log', 0),
(608, 0, 0, 0, 80, '2025-05-04 15:13:39', 'auto-log', 0),
(609, 0, 0, 0, 80, '2025-05-04 15:14:39', 'auto-log', 0),
(610, 0, 0, 0, 80, '2025-05-04 15:15:39', 'auto-log', 0),
(611, 0, 0, 0, 80, '2025-05-04 15:16:39', 'auto-log', 0),
(612, 0, 0, 0, 80, '2025-05-04 15:17:39', 'auto-log', 0),
(613, 0, 0, 0, 80, '2025-05-04 15:18:39', 'auto-log', 0),
(614, 0, 0, 0, 80, '2025-05-04 15:19:39', 'auto-log', 0),
(615, 0, 0, 0, 80, '2025-05-04 15:20:39', 'auto-log', 0),
(616, 0, 0, 0, 80, '2025-05-04 15:21:39', 'auto-log', 0),
(617, 0, 0, 0, 80, '2025-05-04 15:22:39', 'auto-log', 0),
(618, 0, 0, 0, 80, '2025-05-04 15:23:39', 'auto-log', 0),
(619, 0, 0, 0, 80, '2025-05-04 15:24:39', 'auto-log', 0),
(620, 0, 0, 0, 80, '2025-05-04 15:25:39', 'auto-log', 0),
(621, 0, 0, 0, 80, '2025-05-04 15:26:39', 'auto-log', 0),
(622, 0, 0, 0, 80, '2025-05-04 15:27:39', 'auto-log', 0),
(623, 0, 0, 0, 80, '2025-05-04 15:28:39', 'auto-log', 0),
(624, 0, 0, 0, 80, '2025-05-04 15:29:39', 'auto-log', 0),
(625, 0, 0, 0, 80, '2025-05-04 15:30:39', 'auto-log', 0),
(626, 0, 0, 0, 80, '2025-05-04 15:31:39', 'auto-log', 0),
(627, 0, 0, 0, 80, '2025-05-04 15:32:39', 'auto-log', 0),
(628, 0, 0, 0, 80, '2025-05-04 15:33:39', 'auto-log', 0),
(629, 0, 0, 0, 80, '2025-05-04 15:34:39', 'auto-log', 0),
(630, 0, 0, 0, 80, '2025-05-04 15:35:39', 'auto-log', 0),
(631, 0, 0, 0, 80, '2025-05-04 15:36:39', 'auto-log', 0),
(632, 0, 0, 0, 80, '2025-05-04 15:37:39', 'auto-log', 0),
(633, 0, 0, 0, 80, '2025-05-04 15:38:39', 'auto-log', 0),
(634, 0, 0, 0, 80, '2025-05-04 15:39:39', 'auto-log', 0),
(635, 0, 0, 0, 80, '2025-05-04 15:40:39', 'auto-log', 0),
(636, 0, 0, 0, 80, '2025-05-04 15:41:39', 'auto-log', 0),
(637, 0, 0, 0, 80, '2025-05-04 15:42:39', 'auto-log', 0),
(638, 0, 0, 0, 80, '2025-05-04 15:43:39', 'auto-log', 0),
(639, 0, 0, 0, 80, '2025-05-04 15:44:39', 'auto-log', 0),
(640, 0, 0, 0, 80, '2025-05-04 15:45:39', 'auto-log', 0),
(641, 0, 0, 0, 80, '2025-05-04 15:46:39', 'auto-log', 0),
(642, 0, 0, 0, 80, '2025-05-04 15:47:39', 'auto-log', 0),
(643, 0, 0, 0, 80, '2025-05-04 15:48:39', 'auto-log', 0),
(644, 0, 0, 0, 80, '2025-05-04 15:49:39', 'auto-log', 0),
(645, 0, 0, 0, 80, '2025-05-04 15:50:39', 'auto-log', 0),
(646, 0, 0, 0, 80, '2025-05-04 15:51:39', 'auto-log', 0),
(647, 0, 0, 0, 80, '2025-05-04 15:52:39', 'auto-log', 0),
(648, 0, 0, 0, 80, '2025-05-04 15:53:39', 'auto-log', 0),
(649, 0, 0, 0, 80, '2025-05-04 15:54:39', 'auto-log', 0),
(650, 0, 0, 0, 80, '2025-05-04 15:55:39', 'auto-log', 0),
(651, 0, 0, 0, 80, '2025-05-04 15:56:39', 'auto-log', 0),
(652, 0, 0, 0, 80, '2025-05-04 15:57:39', 'auto-log', 0),
(653, 0, 0, 0, 80, '2025-05-04 15:58:39', 'auto-log', 0),
(654, 0, 0, 0, 80, '2025-05-04 15:59:39', 'auto-log', 0),
(655, 0, 0, 0, 80, '2025-05-04 16:00:39', 'auto-log', 0),
(656, 0, 0, 0, 80, '2025-05-04 16:01:39', 'auto-log', 0),
(657, 0, 0, 0, 80, '2025-05-04 16:02:39', 'auto-log', 0),
(658, 0, 0, 0, 80, '2025-05-04 16:03:39', 'auto-log', 0),
(659, 0, 0, 0, 80, '2025-05-04 16:04:39', 'auto-log', 0),
(660, 0, 0, 0, 80, '2025-05-04 16:05:13', 'auto-log', 0),
(661, 0.034799651464005384, 0.000017399831787081402, 6.959930292801077, 80, '2025-05-04 16:05:14', 'auto-log', 0),
(662, 0.021502020010329595, 0.00001075101231684912, 4.300404002065919, 80, '2025-05-04 16:05:15', 'auto-log', 0),
(663, 0.0003147212731489999, 0.00000015736063706974735, 0.06294425462979998, 80, '2025-05-04 16:05:16', 'auto-log', 0),
(664, 0.00201234375, 0.0000010061718952476369, 0.40246875, 100, '2025-05-04 16:05:20', 'auto-log', 0),
(665, 0.002394223615231938, 0.0000011971118362775027, 0.4788447230463876, 90, '2025-05-04 16:05:20', 'auto-log', 0),
(666, 0.006320407290913172, 0.0000031602038451943277, 1.2640814581826345, 90, '2025-05-04 16:05:21', 'auto-log', 0),
(667, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 90, '2025-05-04 16:05:22', 'auto-log', 0),
(668, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 16:05:23', 'auto-log', 0),
(669, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 16:05:24', 'auto-log', 0),
(670, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 90, '2025-05-04 16:05:25', 'auto-log', 0),
(671, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 16:05:26', 'auto-log', 0),
(672, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 90, '2025-05-04 16:05:27', 'auto-log', 0),
(673, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 16:05:28', 'auto-log', 0),
(674, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 90, '2025-05-04 16:05:29', 'auto-log', 0),
(675, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 16:05:30', 'auto-log', 0),
(676, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 16:05:31', 'auto-log', 0),
(677, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 16:05:32', 'auto-log', 0),
(678, 0.00383094663297828, 0.0000019154733898699008, 0.766189326595656, 90, '2025-05-04 16:05:33', 'auto-log', 0),
(679, 0.005806333715438111, 0.000002903167026286612, 1.1612667430876222, 90, '2025-05-04 16:05:34', 'auto-log', 0),
(680, 0.008719625426727575, 0.000004359813093523126, 1.743925085345515, 90, '2025-05-04 16:05:35', 'auto-log', 0),
(681, 0.014811083526595343, 0.000007405542860138649, 2.9622167053190687, 90, '2025-05-04 16:05:36', 'auto-log', 0),
(682, 0.024626266545556507, 0.000012313136305043274, 4.9252533091113015, 90, '2025-05-04 16:05:37', 'auto-log', 0),
(683, 0.004252514282810469, 0.0000021262572318246233, 0.8505028565620939, 90, '2025-05-04 16:05:38', 'auto-log', 0),
(684, 0.00201234375, 0.0000010061718952476369, 0.40246875, 100, '2025-05-04 16:06:14', 'auto-log', 0),
(685, 0.011295892833409685, 0.000005647947054690818, 2.259178566681937, 94, '2025-05-04 16:06:15', 'auto-log', 0),
(686, 0.0006619323423950413, 0.0000003309661733882928, 0.13238646847900826, 94, '2025-05-04 16:06:16', 'auto-log', 0),
(687, 0.0007356060503902714, 0.00000036780302790071696, 0.14712121007805426, 94, '2025-05-04 16:06:17', 'auto-log', 0),
(688, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:18', 'auto-log', 0),
(689, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:19', 'auto-log', 0),
(690, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:20', 'auto-log', 0),
(691, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:21', 'auto-log', 0),
(692, 0.028245525610385475, 0.000014122766794241322, 5.649105122077095, 94, '2025-05-04 16:06:22', 'auto-log', 0),
(693, 0.032036330723018015, 0.00001601817049314144, 6.407266144603603, 94, '2025-05-04 16:06:23', 'auto-log', 0),
(694, 0.12157095443327483, 0.00006078555111412223, 24.314190886654966, 94, '2025-05-04 16:06:24', 'auto-log', 0),
(695, 0.14219915713682657, 0.00007109967967141475, 28.439831427365313, 94, '2025-05-04 16:06:25', 'auto-log', 0),
(696, 0.0019862098922303994, 0.0000009931049658403484, 0.3972419784460799, 94, '2025-05-04 16:06:26', 'auto-log', 0),
(697, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:27', 'auto-log', 0),
(698, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:28', 'auto-log', 0),
(699, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:29', 'auto-log', 0),
(700, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:30', 'auto-log', 0),
(701, 0.019934152345390466, 0.000009967078159547381, 3.986830469078093, 94, '2025-05-04 16:06:31', 'auto-log', 0),
(702, 0.00647415903472867, 0.000003237079726938011, 1.2948318069457339, 94, '2025-05-04 16:06:32', 'auto-log', 0),
(703, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:33', 'auto-log', 0),
(704, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:34', 'auto-log', 0),
(705, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:35', 'auto-log', 0),
(706, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:36', 'auto-log', 0),
(707, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:37', 'auto-log', 0),
(708, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:38', 'auto-log', 0),
(709, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:39', 'auto-log', 0),
(710, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:40', 'auto-log', 0),
(711, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:41', 'auto-log', 0),
(712, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:42', 'auto-log', 0),
(713, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:43', 'auto-log', 0),
(714, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:44', 'auto-log', 0),
(715, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:45', 'auto-log', 0),
(716, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:46', 'auto-log', 0),
(717, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:47', 'auto-log', 0),
(718, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:48', 'auto-log', 0),
(719, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:49', 'auto-log', 0),
(720, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:50', 'auto-log', 0),
(721, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:51', 'auto-log', 0),
(722, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:52', 'auto-log', 0),
(723, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:53', 'auto-log', 0),
(724, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:54', 'auto-log', 0),
(725, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:55', 'auto-log', 0),
(726, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:56', 'auto-log', 0),
(727, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 94, '2025-05-04 16:06:57', 'auto-log', 0),
(728, 0.012068458891525543, 0.000006034230174001272, 2.413691778305109, 94, '2025-05-04 16:06:58', 'auto-log', 0),
(729, 0.008127180076133606, 0.000004063590368322084, 1.625436015226721, 94, '2025-05-04 16:06:59', 'auto-log', 0),
(730, 0.00040498109562371983, 0.00000020249054863190835, 0.08099621912474396, 94, '2025-05-04 16:07:00', 'auto-log', 0),
(731, 0.0008116165922366782, 0.0000004058082994119466, 0.16232331844733563, 94, '2025-05-04 16:07:01', 'auto-log', 0),
(732, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 94, '2025-05-04 16:07:02', 'auto-log', 0),
(733, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 94, '2025-05-04 16:07:03', 'auto-log', 0),
(734, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 94, '2025-05-04 16:07:04', 'auto-log', 0),
(735, 0.015379034869164638, 0.000007689518617155885, 3.0758069738329277, 94, '2025-05-04 16:07:05', 'auto-log', 0),
(736, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 94, '2025-05-04 16:07:06', 'auto-log', 0),
(737, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 94, '2025-05-04 16:07:07', 'auto-log', 0),
(738, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 94, '2025-05-04 16:07:08', 'auto-log', 0),
(739, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 94, '2025-05-04 16:07:09', 'auto-log', 0),
(740, 0.00010390625000000001, 0.00000005195312505398255, 0.02078125, 94, '2025-05-04 16:07:10', 'auto-log', 0),
(741, 0.009043811569197802, 0.000004521906193551539, 1.8087623138395605, 94, '2025-05-04 16:07:11', 'auto-log', 0),
(742, 0.015258422360509753, 0.000007629212344352142, 3.0516844721019507, 94, '2025-05-04 16:07:12', 'auto-log', 0),
(743, 0.00201234375, 0.0000010061718952476369, 0.40246875, 100, '2025-05-04 22:07:33', 'auto-log', 0),
(744, 0.007275961527006316, 0.000003637981028201239, 1.4551923054012632, 25, '2025-05-04 22:07:36', 'auto-log', 0),
(745, 0.0004561924515278397, 0.00000022809622680447765, 0.09123849030556794, 25, '2025-05-04 22:07:37', 'auto-log', 0),
(746, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:07:37', 'auto-log', 0),
(747, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:07:39', 'auto-log', 0),
(748, 0.002469931173130135, 0.0000012349656170678676, 0.49398623462602703, 25, '2025-05-04 22:07:39', 'auto-log', 0),
(749, 0.0025470787021078653, 0.0000012735393834919821, 0.509415740421573, 25, '2025-05-04 22:07:40', 'auto-log', 0),
(750, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:07:41', 'auto-log', 0),
(751, 0.0003861486043268515, 0.00000019307430290897947, 0.07722972086537029, 25, '2025-05-04 22:07:42', 'auto-log', 0),
(752, 0.07395965611062867, 0.000036979855405468, 14.791931222125735, 25, '2025-05-04 22:07:43', 'auto-log', 0),
(753, 0.10036524911924462, 0.00005018267492553846, 20.073049823848923, 25, '2025-05-04 22:07:44', 'auto-log', 0),
(754, 0.10812749485549862, 0.00005406380588552503, 21.625498971099724, 25, '2025-05-04 22:07:45', 'auto-log', 0),
(755, 0.0973597806489497, 0.000048679937719109304, 19.47195612978994, 25, '2025-05-04 22:07:46', 'auto-log', 0),
(756, 0.08029655559753542, 0.00004014831003645192, 16.059311119507083, 25, '2025-05-04 22:07:47', 'auto-log', 0),
(757, 0.08471855488613188, 0.000042359313329233645, 16.943710977226374, 25, '2025-05-04 22:07:48', 'auto-log', 0),
(758, 0.0014526620369892337, 0.0000007263310290457518, 0.29053240739784675, 25, '2025-05-04 22:07:49', 'auto-log', 0),
(759, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:07:50', 'auto-log', 0),
(760, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:07:51', 'auto-log', 0),
(761, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:07:52', 'auto-log', 0),
(762, 0.0008638316102620739, 0.0000004319158088620622, 0.17276632205241477, 25, '2025-05-04 22:07:53', 'auto-log', 0),
(763, 0.0003579588060355454, 0.0000001789794036584452, 0.07159176120710908, 25, '2025-05-04 22:07:54', 'auto-log', 0),
(764, 0.01696136212328385, 0.00000848068250008095, 3.39227242465677, 25, '2025-05-04 22:07:55', 'auto-log', 0),
(765, 0.07532560592016377, 0.00003766283132981643, 15.065121184032753, 25, '2025-05-04 22:07:56', 'auto-log', 0),
(766, 0.10009293368686663, 0.00005004651693641019, 20.018586737373326, 25, '2025-05-04 22:07:57', 'auto-log', 0),
(767, 0.09961988015715696, 0.0000498099896991811, 19.923976031431394, 25, '2025-05-04 22:07:58', 'auto-log', 0),
(768, 0.04850338984176581, 0.000024251706683777033, 9.700677968353162, 25, '2025-05-04 22:07:59', 'auto-log', 0),
(769, 0.04063826288841869, 0.000020319139701551396, 8.127652577683737, 25, '2025-05-04 22:08:00', 'auto-log', 0),
(770, 0.029892782665165665, 0.000014946395800475112, 5.978556533033133, 25, '2025-05-04 22:08:01', 'auto-log', 0),
(771, 0.03397014021575055, 0.000016985075877727408, 6.79402804315011, 25, '2025-05-04 22:08:02', 'auto-log', 0),
(772, 0.10293139526030706, 0.00005146575060451418, 20.586279052061414, 25, '2025-05-04 22:08:03', 'auto-log', 0),
(773, 0.11096068394531967, 0.00005548040353402675, 22.192136789063934, 25, '2025-05-04 22:08:04', 'auto-log', 0),
(774, 0.1081620191263954, 0.00005408106805830961, 21.63240382527908, 25, '2025-05-04 22:08:05', 'auto-log', 0),
(775, 0.1000871428648795, 0.00005004362151962059, 20.017428572975902, 25, '2025-05-04 22:08:06', 'auto-log', 0),
(776, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:08:07', 'auto-log', 0),
(777, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:08:08', 'auto-log', 0),
(778, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:08:09', 'auto-log', 0),
(779, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:08:10', 'auto-log', 0),
(780, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:08:11', 'auto-log', 0),
(781, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:08:12', 'auto-log', 0),
(782, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 25, '2025-05-04 22:08:13', 'auto-log', 0),
(783, 0.0029319657539697945, 0.0000014659829199670133, 0.5863931507939589, 25, '2025-05-04 22:08:14', 'auto-log', 0),
(784, 0.0019957930076490737, 0.0000009978965237404855, 0.39915860152981475, 25, '2025-05-04 22:08:15', 'auto-log', 0),
(785, 0.00230226155060262, 0.0000011511308018033514, 0.46045231012052396, 25, '2025-05-04 22:08:16', 'auto-log', 0),
(786, 0.01888781167824904, 0.00000944390762287167, 3.777562335649808, 25, '2025-05-04 22:08:17', 'auto-log', 0),
(787, 0.0031770137613200936, 0.0000015885069311271292, 0.6354027522640188, 25, '2025-05-04 22:08:18', 'auto-log', 0),
(788, 0.0009096364097032348, 0.0000004548182089888094, 0.18192728194064695, 25, '2025-05-04 22:08:19', 'auto-log', 0),
(789, 0.014393624583822096, 0.0000071968133277931905, 2.878724916764419, 25, '2025-05-04 22:08:20', 'auto-log', 0),
(790, 0.011712151129506469, 0.0000058560762506256556, 2.342430225901294, 25, '2025-05-04 22:08:21', 'auto-log', 0),
(791, 0.005200834855524532, 0.000002600417563005682, 1.0401669711049064, 25, '2025-05-04 22:08:22', 'auto-log', 0),
(792, 0.0035299426346563855, 0.0000017649713796306676, 0.7059885269312771, 25, '2025-05-04 22:08:23', 'auto-log', 0),
(793, 0.006794112404292276, 0.000003397056432945955, 1.358822480858455, 25, '2025-05-04 22:08:24', 'auto-log', 0),
(794, 0.006497145697440603, 0.0000032485730597848124, 1.2994291394881206, 25, '2025-05-04 22:08:25', 'auto-log', 0),
(795, 0.002609182329810981, 0.0000013045911989446527, 0.5218364659621961, 25, '2025-05-04 22:08:26', 'auto-log', 0),
(796, 0.004582016231686052, 0.00000229100822081739, 0.9164032463372104, 25, '2025-05-04 22:08:27', 'auto-log', 0),
(797, 0.005457253281823941, 0.0000027286267898200373, 1.0914506563647883, 25, '2025-05-04 22:08:28', 'auto-log', 0),
(798, 0.009203744311050978, 0.000004601872579070035, 1.8407488622101957, 25, '2025-05-04 22:08:29', 'auto-log', 0),
(799, 0.008917290945970395, 0.000004458645870575587, 1.783458189194079, 25, '2025-05-04 22:08:30', 'auto-log', 0),
(800, 0.009980988005008315, 0.000004990494500604765, 1.9961976010016629, 25, '2025-05-04 22:08:31', 'auto-log', 0),
(801, 0.010935540749985434, 0.000005467770972922973, 2.1871081499970866, 25, '2025-05-04 22:08:32', 'auto-log', 0),
(802, 0.011813864268335167, 0.000005906932832004528, 2.3627728536670336, 25, '2025-05-04 22:08:33', 'auto-log', 0),
(803, 0.01247440651796968, 0.000006237204037038929, 2.494881303593936, 25, '2025-05-04 22:08:34', 'auto-log', 0),
(804, 0.018710947916919483, 0.000009355475708957602, 3.7421895833838965, 25, '2025-05-04 22:08:35', 'auto-log', 0),
(805, 0.0052796079499080086, 0.0000026398041143253047, 1.0559215899816017, 25, '2025-05-04 22:08:36', 'auto-log', 0),
(806, 0.00201234375, 0.0000010061718952476369, 0.40246875, 100, '2025-05-04 22:24:47', 'auto-log', 0),
(807, 0.000395859375, 0.00000019792968828352322, 0.079171875, 67, '2025-05-04 22:24:47', 'auto-log', 0),
(808, 0.006737276769493267, 0.000003368638611701125, 1.3474553538986533, 67, '2025-05-04 22:24:48', 'auto-log', 0),
(809, 0.00201234375, 0.0000010061718952476369, 0.40246875, 100, '2025-05-04 22:43:53', 'auto-log', 0),
(810, 0.007881921012891256, 0.000003940960817069022, 1.576384202578251, 82, '2025-05-04 22:43:54', 'auto-log', 0),
(811, 0.004251737761431808, 0.0000021258689711022738, 0.8503475522863615, 82, '2025-05-04 22:43:55', 'auto-log', 0),
(812, 0.0020661328125, 0.0000010330664275945241, 0.4132265625, 100, '2025-05-04 23:09:19', 'auto-log', 0),
(813, 0.011496076085139398, 0.000005748038703368526, 2.2992152170278795, 83, '2025-05-04 23:09:20', 'auto-log', 0),
(814, 0.007207237095234827, 0.000003603618807338746, 1.4414474190469655, 83, '2025-05-04 23:09:21', 'auto-log', 0),
(815, 0.00206296875, 0.0000010314843962792004, 0.41259375000000004, 100, '2025-05-04 23:15:04', 'auto-log', 0),
(816, 0.016928311470572912, 0.000008464157168125102, 3.3856622941145824, 82, '2025-05-04 23:15:05', 'auto-log', 0),
(817, 0.006723278077356745, 0.000003361639264690713, 1.344655615471349, 82, '2025-05-04 23:15:07', 'auto-log', 0),
(818, 0.00206296875, 0.0000010314843962792004, 0.41259375000000004, 100, '2025-05-04 23:29:41', 'auto-log', 0),
(819, 0.016416989978450553, 0.000008208496336813077, 3.2833979956901107, 95, '2025-05-04 23:29:42', 'auto-log', 0),
(820, 0.00206296875, 0.0000010314843962792004, 0.41259375000000004, 100, '2025-05-04 23:30:17', 'auto-log', 0),
(821, 0.01246030592810861, 0.000006230153740350424, 2.492061185621722, 90, '2025-05-04 23:30:18', 'auto-log', 0),
(822, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:20', 'auto-log', 0),
(823, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:21', 'auto-log', 0),
(824, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:22', 'auto-log', 0),
(825, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:23', 'auto-log', 0),
(826, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:24', 'auto-log', 0),
(827, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:25', 'auto-log', 0),
(828, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:26', 'auto-log', 0),
(829, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:27', 'auto-log', 0),
(830, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:28', 'auto-log', 0),
(831, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:29', 'auto-log', 0),
(832, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:30', 'auto-log', 0),
(833, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:31', 'auto-log', 0),
(834, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:32', 'auto-log', 0),
(835, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:33', 'auto-log', 0),
(836, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:34', 'auto-log', 0),
(837, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:35', 'auto-log', 0),
(838, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:36', 'auto-log', 0),
(839, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:37', 'auto-log', 0),
(840, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:38', 'auto-log', 0),
(841, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:39', 'auto-log', 0),
(842, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:40', 'auto-log', 0),
(843, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:41', 'auto-log', 0),
(844, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:42', 'auto-log', 0),
(845, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:43', 'auto-log', 0),
(846, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:44', 'auto-log', 0),
(847, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:45', 'auto-log', 0),
(848, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:46', 'auto-log', 0),
(849, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:47', 'auto-log', 0),
(850, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:48', 'auto-log', 0),
(851, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:49', 'auto-log', 0),
(852, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:50', 'auto-log', 0),
(853, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:51', 'auto-log', 0),
(854, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:52', 'auto-log', 0),
(855, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:53', 'auto-log', 0),
(856, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:54', 'auto-log', 0),
(857, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:55', 'auto-log', 0),
(858, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:56', 'auto-log', 0),
(859, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:57', 'auto-log', 0),
(860, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:58', 'auto-log', 0),
(861, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:30:59', 'auto-log', 0),
(862, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:00', 'auto-log', 0),
(863, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:01', 'auto-log', 0),
(864, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:02', 'auto-log', 0),
(865, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:03', 'auto-log', 0),
(866, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:04', 'auto-log', 0),
(867, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:05', 'auto-log', 0),
(868, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:06', 'auto-log', 0),
(869, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:07', 'auto-log', 0),
(870, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:08', 'auto-log', 0),
(871, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:09', 'auto-log', 0),
(872, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:10', 'auto-log', 0),
(873, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:11', 'auto-log', 0),
(874, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:12', 'auto-log', 0),
(875, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:13', 'auto-log', 0),
(876, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:14', 'auto-log', 0),
(877, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:15', 'auto-log', 0),
(878, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:16', 'auto-log', 0),
(879, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:17', 'auto-log', 0),
(880, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:18', 'auto-log', 0),
(881, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:39', 'auto-log', 0),
(882, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:58', 'auto-log', 0),
(883, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:58', 'auto-log', 0),
(884, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:31:59', 'auto-log', 0),
(885, 0.007640249097361178, 0.000003820124840547621, 1.5280498194722356, 90, '2025-05-04 23:32:00', 'auto-log', 0),
(886, 0.005572779149628706, 0.0000027863897300936902, 1.1145558299257412, 90, '2025-05-04 23:32:01', 'auto-log', 0),
(887, 0.00025469219072999657, 0.00000012734609568933885, 0.05093843814599931, 90, '2025-05-04 23:32:02', 'auto-log', 0),
(888, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:03', 'auto-log', 0),
(889, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:04', 'auto-log', 0),
(890, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:05', 'auto-log', 0),
(891, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:06', 'auto-log', 0),
(892, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:07', 'auto-log', 0),
(893, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:08', 'auto-log', 0),
(894, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:09', 'auto-log', 0),
(895, 0.0003311259903055652, 0.00000016556299570100473, 0.06622519806111303, 90, '2025-05-04 23:32:10', 'auto-log', 0),
(896, 0.007602892365134203, 0.000003801446471586963, 1.5205784730268406, 90, '2025-05-04 23:32:11', 'auto-log', 0),
(897, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:13', 'auto-log', 0),
(898, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:14', 'auto-log', 0),
(899, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:15', 'auto-log', 0),
(900, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:16', 'auto-log', 0),
(901, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:17', 'auto-log', 0),
(902, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:18', 'auto-log', 0),
(903, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:19', 'auto-log', 0),
(904, 0.000155859375, 0.00000007792968762146073, 0.031171874999999998, 90, '2025-05-04 23:32:19', 'auto-log', 0),
(905, 0.014858927156308592, 0.000007429464682092877, 2.9717854312617185, 90, '2025-05-04 23:32:20', 'auto-log', 0),
(906, 0.0030391406249999997, 0.0000015195703586818785, 0.6078281249999999, 100, '2025-05-05 01:42:05', 'auto-log', 0),
(907, 0.00475441519533569, 0.0000023772077106901642, 0.950883039067138, 92, '2025-05-05 01:42:06', 'auto-log', 0),
(908, 0.015523097798618204, 0.000007761550104141929, 3.104619559723641, 92, '2025-05-05 01:42:07', 'auto-log', 0),
(909, 0.0504313924732285, 0.000025215708953240984, 10.086278494645699, 92, '2025-05-05 01:42:08', 'auto-log', 0),
(910, 0.007099949171355627, 0.0000035499748377242043, 1.4199898342711255, 92, '2025-05-05 01:42:09', 'auto-log', 0),
(911, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:10', 'auto-log', 0),
(912, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:12', 'auto-log', 0),
(913, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:13', 'auto-log', 0),
(914, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:14', 'auto-log', 0),
(915, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:15', 'auto-log', 0),
(916, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:16', 'auto-log', 0),
(917, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:17', 'auto-log', 0),
(918, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:18', 'auto-log', 0),
(919, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:19', 'auto-log', 0),
(920, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:20', 'auto-log', 0),
(921, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:21', 'auto-log', 0),
(922, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:22', 'auto-log', 0),
(923, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:23', 'auto-log', 0),
(924, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:24', 'auto-log', 0),
(925, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:25', 'auto-log', 0),
(926, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:26', 'auto-log', 0),
(927, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:27', 'auto-log', 0),
(928, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:28', 'auto-log', 0),
(929, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:29', 'auto-log', 0),
(930, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:30', 'auto-log', 0),
(931, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:31', 'auto-log', 0),
(932, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:32', 'auto-log', 0),
(933, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:33', 'auto-log', 0),
(934, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:34', 'auto-log', 0),
(935, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:35', 'auto-log', 0),
(936, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:36', 'auto-log', 0),
(937, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:37', 'auto-log', 0),
(938, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:38', 'auto-log', 0),
(939, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:39', 'auto-log', 0),
(940, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:40', 'auto-log', 0),
(941, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:40', 'auto-log', 0),
(942, 0.003151290332243789, 0.0000015756452157750485, 0.6302580664487578, 92, '2025-05-05 01:42:41', 'auto-log', 0),
(943, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:43', 'auto-log', 0),
(944, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:44', 'auto-log', 0),
(945, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:45', 'auto-log', 0),
(946, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:46', 'auto-log', 0),
(947, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:47', 'auto-log', 0),
(948, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:48', 'auto-log', 0),
(949, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:49', 'auto-log', 0),
(950, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:50', 'auto-log', 0),
(951, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:51', 'auto-log', 0),
(952, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:52', 'auto-log', 0),
(953, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:53', 'auto-log', 0),
(954, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:54', 'auto-log', 0),
(955, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:55', 'auto-log', 0),
(956, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:56', 'auto-log', 0),
(957, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:58', 'auto-log', 0),
(958, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:58', 'auto-log', 0),
(959, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:42:59', 'auto-log', 0),
(960, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:00', 'auto-log', 0),
(961, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:01', 'auto-log', 0),
(962, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:02', 'auto-log', 0),
(963, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:04', 'auto-log', 0),
(964, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:05', 'auto-log', 0),
(965, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:06', 'auto-log', 0),
(966, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:07', 'auto-log', 0),
(967, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:08', 'auto-log', 0),
(968, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:09', 'auto-log', 0),
(969, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:10', 'auto-log', 0),
(970, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:11', 'auto-log', 0),
(971, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:12', 'auto-log', 0),
(972, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:13', 'auto-log', 0),
(973, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:14', 'auto-log', 0),
(974, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:14', 'auto-log', 0),
(975, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:15', 'auto-log', 0),
(976, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:17', 'auto-log', 0),
(977, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:18', 'auto-log', 0),
(978, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:19', 'auto-log', 0),
(979, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:20', 'auto-log', 0),
(980, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:21', 'auto-log', 0),
(981, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:22', 'auto-log', 0),
(982, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:23', 'auto-log', 0),
(983, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:24', 'auto-log', 0),
(984, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:25', 'auto-log', 0),
(985, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:26', 'auto-log', 0),
(986, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:27', 'auto-log', 0),
(987, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:28', 'auto-log', 0),
(988, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:29', 'auto-log', 0);
INSERT INTO `sustainabilitymetrics` (`metricID`, `energyUsageWh`, `carbonOffsetKg`, `waterUsageMl`, `performanceScore`, `metricMeasurementDateTime`, `metricNotes`, `sustainabilityScore`) VALUES
(989, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:30', 'auto-log', 0),
(990, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:31', 'auto-log', 0),
(991, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:32', 'auto-log', 0),
(992, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:34', 'auto-log', 0),
(993, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:34', 'auto-log', 0),
(994, 0.03318022381305047, 0.000016590117411161494, 6.636044762610094, 92, '2025-05-05 01:43:35', 'auto-log', 0),
(995, 0.09376771729611555, 0.00004688390260998182, 18.75354345922311, 92, '2025-05-05 01:43:36', 'auto-log', 0),
(996, 0.06544516166026026, 0.00003272260224547606, 13.089032332052053, 92, '2025-05-05 01:43:37', 'auto-log', 0),
(997, 0.10871732157981792, 0.00005435871988718902, 21.743464315963585, 92, '2025-05-05 01:43:38', 'auto-log', 0),
(998, 0.12151848714756522, 0.0000607593174074962, 24.303697429513043, 92, '2025-05-05 01:43:39', 'auto-log', 0),
(999, 0.12304282619544693, 0.00006152148879540885, 24.608565239089387, 92, '2025-05-05 01:43:40', 'auto-log', 0),
(1000, 0.12590045539199612, 0.00006295030695062141, 25.180091078399226, 92, '2025-05-05 01:43:41', 'auto-log', 0),
(1001, 0.14732334708341566, 0.0000736617820625508, 29.46466941668313, 92, '2025-05-05 01:43:42', 'auto-log', 0),
(1002, 0.12457512984533818, 0.00006228764251748398, 24.915025969067635, 92, '2025-05-05 01:43:43', 'auto-log', 0),
(1003, 0.13418166787347832, 0.00006709092396033912, 26.836333574695665, 92, '2025-05-05 01:43:44', 'auto-log', 0),
(1004, 0.13201697883277502, 0.00006600857655880101, 26.403395766555004, 92, '2025-05-05 01:43:45', 'auto-log', 0),
(1005, 0.0214936766373397, 0.000010746840628560527, 4.2987353274679405, 92, '2025-05-05 01:43:46', 'auto-log', 0),
(1006, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 92, '2025-05-05 01:43:47', 'auto-log', 0),
(1007, 0.0007465422527734189, 0.0000003732711291733361, 0.14930845055468378, 92, '2025-05-05 01:43:48', 'auto-log', 0),
(1008, 0.04703213269543316, 0.00002351607740782411, 9.406426539086631, 92, '2025-05-05 01:43:49', 'auto-log', 0),
(1009, 0.08141793348108892, 0.00004070899988494392, 16.28358669621778, 92, '2025-05-05 01:43:50', 'auto-log', 0),
(1010, 0.06836083373323548, 0.000034180440232635686, 13.672166746647097, 92, '2025-05-05 01:43:51', 'auto-log', 0),
(1011, 0.0029854960054919943, 0.0000014927480473119292, 0.5970992010983989, 92, '2025-05-05 01:43:52', 'auto-log', 0),
(1012, 0.0005368828125, 0.0000002684414076912158, 0.10737656250000001, 92, '2025-05-05 01:43:53', 'auto-log', 0),
(1013, 0.04845962001939554, 0.000024229821751371635, 9.691924003879109, 92, '2025-05-05 01:43:54', 'auto-log', 0),
(1014, 0.024880100820772116, 0.000012440053505483143, 4.9760201641544235, 92, '2025-05-05 01:43:55', 'auto-log', 0),
(1015, 0.0006356040856491308, 0.00000031780204484452817, 0.12712081712982617, 92, '2025-05-05 01:43:56', 'auto-log', 0),
(1016, 0.0019922617565395775, 0.0000009961308981153233, 0.3984523513079155, 92, '2025-05-05 01:43:57', 'auto-log', 0),
(1017, 0.007417928817848791, 0.0000037089646840527356, 1.4835857635697582, 92, '2025-05-05 01:43:58', 'auto-log', 0),
(1018, 0.0030391406249999997, 0.0000015195703586818785, 0.6078281249999999, 100, '2025-05-05 01:44:03', 'auto-log', 0),
(1019, 0.016323541147364205, 0.00000816177190597208, 3.264708229472841, 95, '2025-05-05 01:44:04', 'auto-log', 0),
(1020, 0.001355799954997382, 0.0000006778999866896586, 0.2711599909994764, 95, '2025-05-05 01:44:05', 'auto-log', 0),
(1021, 0.012166984758668613, 0.000006083493119511897, 2.4333969517337226, 95, '2025-05-05 01:44:06', 'auto-log', 0),
(1022, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:07', 'auto-log', 0),
(1023, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:08', 'auto-log', 0),
(1024, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:09', 'auto-log', 0),
(1025, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:10', 'auto-log', 0),
(1026, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:11', 'auto-log', 0),
(1027, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:12', 'auto-log', 0),
(1028, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:13', 'auto-log', 0),
(1029, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:14', 'auto-log', 0),
(1030, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:15', 'auto-log', 0),
(1031, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:16', 'auto-log', 0),
(1032, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:17', 'auto-log', 0),
(1033, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:18', 'auto-log', 0),
(1034, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:19', 'auto-log', 0),
(1035, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:20', 'auto-log', 0),
(1036, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:21', 'auto-log', 0),
(1037, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:22', 'auto-log', 0),
(1038, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:23', 'auto-log', 0),
(1039, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:24', 'auto-log', 0),
(1040, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:25', 'auto-log', 0),
(1041, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:26', 'auto-log', 0),
(1042, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:27', 'auto-log', 0),
(1043, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:28', 'auto-log', 0),
(1044, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:29', 'auto-log', 0),
(1045, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:30', 'auto-log', 0),
(1046, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:31', 'auto-log', 0),
(1047, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:32', 'auto-log', 0),
(1048, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:33', 'auto-log', 0),
(1049, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:34', 'auto-log', 0),
(1050, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:35', 'auto-log', 0),
(1051, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:36', 'auto-log', 0),
(1052, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:37', 'auto-log', 0),
(1053, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:38', 'auto-log', 0),
(1054, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:39', 'auto-log', 0),
(1055, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:40', 'auto-log', 0),
(1056, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:41', 'auto-log', 0),
(1057, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:42', 'auto-log', 0),
(1058, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:43', 'auto-log', 0),
(1059, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:44', 'auto-log', 0),
(1060, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:45', 'auto-log', 0),
(1061, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:46', 'auto-log', 0),
(1062, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:47', 'auto-log', 0),
(1063, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:48', 'auto-log', 0),
(1064, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:49', 'auto-log', 0),
(1065, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:50', 'auto-log', 0),
(1066, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:51', 'auto-log', 0),
(1067, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:52', 'auto-log', 0),
(1068, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:53', 'auto-log', 0),
(1069, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:54', 'auto-log', 0),
(1070, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:55', 'auto-log', 0),
(1071, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:56', 'auto-log', 0),
(1072, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:57', 'auto-log', 0),
(1073, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:58', 'auto-log', 0),
(1074, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:44:59', 'auto-log', 0),
(1075, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:45:00', 'auto-log', 0),
(1076, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:45:01', 'auto-log', 0),
(1077, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:45:02', 'auto-log', 0),
(1078, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:45:03', 'auto-log', 0),
(1079, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:45:04', 'auto-log', 0),
(1080, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:45:05', 'auto-log', 0),
(1081, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 95, '2025-05-05 01:45:20', 'auto-log', 0),
(1082, 0.010215152097596004, 0.000005107576570544664, 2.0430304195192006, 95, '2025-05-05 01:45:21', 'auto-log', 0),
(1083, 0.01318519389352592, 0.00000659259781600965, 2.637038778705184, 95, '2025-05-05 01:45:22', 'auto-log', 0),
(1084, 0.0031133203125000004, 0.000001556660204713817, 0.6226640625000001, 100, '2025-05-05 01:45:23', 'auto-log', 0),
(1085, 0.014695444967212675, 0.000007347723563386852, 2.939088993442535, 90, '2025-05-05 01:45:24', 'auto-log', 0),
(1086, 0.004424515215140417, 0.0000022122577054518834, 0.8849030430280834, 90, '2025-05-05 01:45:25', 'auto-log', 0),
(1087, 0.0007566478749955577, 0.0000003783239403603589, 0.15132957499911154, 90, '2025-05-05 01:45:26', 'auto-log', 0),
(1088, 0.00036781041881999777, 0.00000018390521008642142, 0.07356208376399956, 90, '2025-05-05 01:45:27', 'auto-log', 0),
(1089, 0.0029652629281828177, 0.0000014826315080553298, 0.5930525856365635, 90, '2025-05-05 01:45:28', 'auto-log', 0),
(1090, 0.008665840486897735, 0.000004332920618932824, 1.733168097379547, 90, '2025-05-05 01:45:29', 'auto-log', 0),
(1091, 0.006338411388622837, 0.0000031692058951887134, 1.2676822777245675, 90, '2025-05-05 01:45:30', 'auto-log', 0),
(1092, 0.006369576144904103, 0.0000031847882753095528, 1.2739152289808204, 90, '2025-05-05 01:45:31', 'auto-log', 0),
(1093, 0.0006708905149267522, 0.00000033544525971384656, 0.13417810298535043, 90, '2025-05-05 01:45:32', 'auto-log', 0),
(1094, 0.000349921875, 0.00000017496093811222659, 0.069984375, 90, '2025-05-05 01:45:33', 'auto-log', 0),
(1095, 0.000349921875, 0.00000017496093811222659, 0.069984375, 90, '2025-05-05 01:45:34', 'auto-log', 0),
(1096, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 90, '2025-05-05 01:45:35', 'auto-log', 0),
(1097, 0.0029201679192179383, 0.0000014600840022458727, 0.5840335838435876, 90, '2025-05-05 01:45:36', 'auto-log', 0),
(1098, 0.0015527649454355052, 0.0000007763824847731474, 0.31055298908710105, 90, '2025-05-05 01:45:37', 'auto-log', 0),
(1099, 0.0009377366409392962, 0.00000046886832486639813, 0.18754732818785924, 90, '2025-05-05 01:45:38', 'auto-log', 0),
(1100, 0.020142910173053793, 0.000010071457115211047, 4.028582034610759, 90, '2025-05-05 01:45:39', 'auto-log', 0),
(1101, 0.022791811472289383, 0.000011395908333478044, 4.5583622944578766, 90, '2025-05-05 01:45:40', 'auto-log', 0),
(1102, 0.04021382336046313, 0.00002010691976598951, 8.042764672092625, 90, '2025-05-05 01:45:41', 'auto-log', 0),
(1103, 0.03921647162218713, 0.000019608243500751798, 7.8432943244374265, 90, '2025-05-05 01:45:42', 'auto-log', 0),
(1104, 0.052461232264923, 0.000026230629893365953, 10.492246452984599, 90, '2025-05-05 01:45:43', 'auto-log', 0),
(1105, 0.08245815802401131, 0.000041229113008744785, 16.49163160480226, 90, '2025-05-05 01:45:44', 'auto-log', 0),
(1106, 0.05982236164495355, 0.00002991119871605154, 11.964472328990711, 90, '2025-05-05 01:45:45', 'auto-log', 0),
(1107, 0.07030893528938655, 0.000035154492361425186, 14.061787057877309, 90, '2025-05-05 01:45:46', 'auto-log', 0),
(1108, 0.07184804374319095, 0.000035924047682302426, 14.369608748638191, 90, '2025-05-05 01:45:47', 'auto-log', 0),
(1109, 0.02035062456616678, 0.000010175314353822993, 4.070124913233356, 90, '2025-05-05 01:45:48', 'auto-log', 0),
(1110, 0.0031133203125000004, 0.000001556660204713817, 0.6226640625000001, 100, '2025-05-05 01:45:56', 'auto-log', 0),
(1111, 0.013522498551488155, 0.0000067612501900339126, 2.7044997102976307, 95, '2025-05-05 01:45:57', 'auto-log', 0),
(1112, 0.000637158778731111, 0.0000003185793913954121, 0.1274317557462222, 95, '2025-05-05 01:45:58', 'auto-log', 0),
(1113, 0.0031133203125000004, 0.000001556660204713817, 0.6226640625000001, 100, '2025-05-05 10:48:58', 'auto-log', 0),
(1114, 0.0004158915650098827, 0.0000002079457833697703, 0.08317831300197653, 23, '2025-05-05 10:49:01', 'auto-log', 0),
(1115, 0.0007657812500000001, 0.0000003828906279321047, 0.15315625000000002, 23, '2025-05-05 10:49:02', 'auto-log', 0),
(1116, 0.0006336567537269517, 0.00000031682837887108027, 0.12673135074539035, 23, '2025-05-05 10:49:03', 'auto-log', 0),
(1117, 0.000349921875, 0.00000017496093811222659, 0.069984375, 23, '2025-05-05 10:49:04', 'auto-log', 0),
(1118, 0.00490432404228958, 0.000002452162141406761, 0.9808648084579159, 23, '2025-05-05 10:49:05', 'auto-log', 0),
(1119, 0.10063678854291234, 0.00005031844491027222, 20.12735770858247, 23, '2025-05-05 10:49:06', 'auto-log', 0),
(1120, 0.1543925554951306, 0.00007719639693287127, 30.878511099026117, 23, '2025-05-05 10:49:07', 'auto-log', 0),
(1121, 0.07542076056523044, 0.00003771040872407084, 15.084152113046088, 23, '2025-05-05 10:49:08', 'auto-log', 0),
(1122, 0.0013857091652839883, 0.0000006928545922429437, 0.2771418330567976, 23, '2025-05-05 10:49:09', 'auto-log', 0),
(1123, 0.003106422079768876, 0.0000015532110881337287, 0.6212844159537753, 23, '2025-05-05 10:49:10', 'auto-log', 0),
(1124, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:11', 'auto-log', 0),
(1125, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:12', 'auto-log', 0),
(1126, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:13', 'auto-log', 0),
(1127, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:14', 'auto-log', 0),
(1128, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:15', 'auto-log', 0),
(1129, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:16', 'auto-log', 0),
(1130, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:17', 'auto-log', 0),
(1131, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:18', 'auto-log', 0),
(1132, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:19', 'auto-log', 0),
(1133, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:20', 'auto-log', 0),
(1134, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:21', 'auto-log', 0),
(1135, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:22', 'auto-log', 0),
(1136, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:23', 'auto-log', 0),
(1137, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:24', 'auto-log', 0),
(1138, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:25', 'auto-log', 0),
(1139, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:26', 'auto-log', 0),
(1140, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:27', 'auto-log', 0),
(1141, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:28', 'auto-log', 0),
(1142, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:29', 'auto-log', 0),
(1143, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:30', 'auto-log', 0),
(1144, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:31', 'auto-log', 0),
(1145, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:32', 'auto-log', 0),
(1146, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:33', 'auto-log', 0),
(1147, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:34', 'auto-log', 0),
(1148, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:35', 'auto-log', 0),
(1149, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:36', 'auto-log', 0),
(1150, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:37', 'auto-log', 0),
(1151, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:38', 'auto-log', 0),
(1152, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:39', 'auto-log', 0),
(1153, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:40', 'auto-log', 0),
(1154, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:41', 'auto-log', 0),
(1155, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:42', 'auto-log', 0),
(1156, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:43', 'auto-log', 0),
(1157, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:44', 'auto-log', 0),
(1158, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:45', 'auto-log', 0),
(1159, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:46', 'auto-log', 0),
(1160, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:47', 'auto-log', 0),
(1161, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:48', 'auto-log', 0),
(1162, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:49', 'auto-log', 0),
(1163, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:50', 'auto-log', 0),
(1164, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:51', 'auto-log', 0),
(1165, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:52', 'auto-log', 0),
(1166, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:53', 'auto-log', 0),
(1167, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:54', 'auto-log', 0),
(1168, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:55', 'auto-log', 0),
(1169, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:56', 'auto-log', 0),
(1170, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:57', 'auto-log', 0),
(1171, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:58', 'auto-log', 0),
(1172, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:49:59', 'auto-log', 0),
(1173, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:00', 'auto-log', 0),
(1174, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:01', 'auto-log', 0),
(1175, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:02', 'auto-log', 0),
(1176, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:03', 'auto-log', 0),
(1177, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:04', 'auto-log', 0),
(1178, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:05', 'auto-log', 0),
(1179, 0.01248580674577196, 0.000006242904152362831, 2.497161349154392, 23, '2025-05-05 10:50:06', 'auto-log', 0),
(1180, 0.005140014099400258, 0.0000025700071817988538, 1.0280028198800517, 23, '2025-05-05 10:50:07', 'auto-log', 0),
(1181, 0, 0, 0, 23, '2025-05-05 10:50:08', 'auto-log', 0),
(1182, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:09', 'auto-log', 0),
(1183, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:10', 'auto-log', 0),
(1184, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:11', 'auto-log', 0),
(1185, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:12', 'auto-log', 0),
(1186, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:13', 'auto-log', 0),
(1187, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:14', 'auto-log', 0),
(1188, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:15', 'auto-log', 0),
(1189, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:16', 'auto-log', 0),
(1190, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:17', 'auto-log', 0),
(1191, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:18', 'auto-log', 0),
(1192, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:19', 'auto-log', 0),
(1193, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:20', 'auto-log', 0),
(1194, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:21', 'auto-log', 0),
(1195, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:22', 'auto-log', 0),
(1196, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:23', 'auto-log', 0),
(1197, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:24', 'auto-log', 0),
(1198, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:25', 'auto-log', 0),
(1199, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:26', 'auto-log', 0),
(1200, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:27', 'auto-log', 0),
(1201, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:28', 'auto-log', 0),
(1202, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:29', 'auto-log', 0),
(1203, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:30', 'auto-log', 0),
(1204, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:31', 'auto-log', 0),
(1205, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:32', 'auto-log', 0),
(1206, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:33', 'auto-log', 0),
(1207, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:34', 'auto-log', 0),
(1208, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:35', 'auto-log', 0),
(1209, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:36', 'auto-log', 0),
(1210, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:37', 'auto-log', 0),
(1211, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:38', 'auto-log', 0),
(1212, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:39', 'auto-log', 0),
(1213, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:40', 'auto-log', 0),
(1214, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:41', 'auto-log', 0),
(1215, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:42', 'auto-log', 0),
(1216, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:43', 'auto-log', 0),
(1217, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:44', 'auto-log', 0),
(1218, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:45', 'auto-log', 0),
(1219, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:46', 'auto-log', 0),
(1220, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:47', 'auto-log', 0),
(1221, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:48', 'auto-log', 0),
(1222, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:49', 'auto-log', 0),
(1223, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:50', 'auto-log', 0),
(1224, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:51', 'auto-log', 0),
(1225, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:52', 'auto-log', 0),
(1226, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:53', 'auto-log', 0),
(1227, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:54', 'auto-log', 0),
(1228, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:55', 'auto-log', 0),
(1229, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:56', 'auto-log', 0),
(1230, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:57', 'auto-log', 0),
(1231, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:58', 'auto-log', 0),
(1232, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:50:59', 'auto-log', 0),
(1233, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:51:00', 'auto-log', 0),
(1234, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:51:01', 'auto-log', 0),
(1235, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:51:02', 'auto-log', 0),
(1236, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:51:03', 'auto-log', 0),
(1237, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:51:04', 'auto-log', 0),
(1238, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:51:05', 'auto-log', 0),
(1239, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:51:06', 'auto-log', 0),
(1240, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:51:07', 'auto-log', 0),
(1241, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:51:37', 'auto-log', 0),
(1242, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:52:37', 'auto-log', 0),
(1243, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:53:37', 'auto-log', 0),
(1244, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:54:37', 'auto-log', 0),
(1245, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:55:37', 'auto-log', 0),
(1246, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:56:37', 'auto-log', 0),
(1247, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:57:37', 'auto-log', 0),
(1248, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:58:37', 'auto-log', 0),
(1249, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 10:59:37', 'auto-log', 0),
(1250, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 11:00:37', 'auto-log', 0),
(1251, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 11:01:37', 'auto-log', 0),
(1252, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 11:02:37', 'auto-log', 0),
(1253, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 11:03:37', 'auto-log', 0),
(1254, 0.0005248828125, 0.00000026244140762750984, 0.1049765625, 23, '2025-05-05 11:03:48', 'auto-log', 0),
(1255, 0.023188781561191214, 0.00001159439346919356, 4.637756312238243, 23, '2025-05-05 11:03:49', 'auto-log', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('client','manager','engineer','administrator','sales') NOT NULL,
  `isLoggedIn` tinyint(1) NOT NULL DEFAULT 0,
  `lastLogin` datetime DEFAULT NULL,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `adminlog`
--
ALTER TABLE `adminlog`
  ADD PRIMARY KEY (`logID`);

--
-- Indexes for table `annualreminders`
--
ALTER TABLE `annualreminders`
  ADD PRIMARY KEY (`reminderID`),
  ADD KEY `idx_clientID` (`clientID`);

--
-- Indexes for table `clientfiles`
--
ALTER TABLE `clientfiles`
  ADD PRIMARY KEY (`fileID`),
  ADD KEY `clientID` (`clientID`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`clientID`);

--
-- Indexes for table `clientserviceshistory`
--
ALTER TABLE `clientserviceshistory`
  ADD PRIMARY KEY (`serviceHistoryID`),
  ADD KEY `clientID` (`clientID`),
  ADD KEY `engineerID` (`engineerID`),
  ADD KEY `scheduleID` (`scheduleID`),
  ADD KEY `feedbackID` (`feedbackID`);

--
-- Indexes for table `engineer`
--
ALTER TABLE `engineer`
  ADD PRIMARY KEY (`engineerID`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedbackID`),
  ADD KEY `clientID` (`clientID`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`managerID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notificationID`),
  ADD KEY `clientID` (`clientID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`paymentID`),
  ADD KEY `clientID` (`clientID`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`reminderID`),
  ADD KEY `clientID` (`clientID`);

--
-- Indexes for table `salesteam`
--
ALTER TABLE `salesteam`
  ADD PRIMARY KEY (`SalesID`);

--
-- Indexes for table `schedulediary`
--
ALTER TABLE `schedulediary`
  ADD PRIMARY KEY (`scheduleID`),
  ADD KEY `engineerID` (`engineerID`),
  ADD KEY `clientID` (`clientID`);

--
-- Indexes for table `schedulefiles`
--
ALTER TABLE `schedulefiles`
  ADD PRIMARY KEY (`fileID`),
  ADD KEY `scheduleID` (`scheduleID`);

--
-- Indexes for table `surveydiary`
--
ALTER TABLE `surveydiary`
  ADD PRIMARY KEY (`surveyID`);

--
-- Indexes for table `sustainabilitymetrics`
--
ALTER TABLE `sustainabilitymetrics`
  ADD PRIMARY KEY (`metricID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `adminlog`
--
ALTER TABLE `adminlog`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `annualreminders`
--
ALTER TABLE `annualreminders`
  MODIFY `reminderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `clientfiles`
--
ALTER TABLE `clientfiles`
  MODIFY `fileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `clientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10004;

--
-- AUTO_INCREMENT for table `clientserviceshistory`
--
ALTER TABLE `clientserviceshistory`
  MODIFY `serviceHistoryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `engineer`
--
ALTER TABLE `engineer`
  MODIFY `engineerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `managerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notificationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `paymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `reminderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `salesteam`
--
ALTER TABLE `salesteam`
  MODIFY `SalesID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `schedulediary`
--
ALTER TABLE `schedulediary`
  MODIFY `scheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `schedulefiles`
--
ALTER TABLE `schedulefiles`
  MODIFY `fileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `surveydiary`
--
ALTER TABLE `surveydiary`
  MODIFY `surveyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sustainabilitymetrics`
--
ALTER TABLE `sustainabilitymetrics`
  MODIFY `metricID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1256;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `annualreminders`
--
ALTER TABLE `annualreminders`
  ADD CONSTRAINT `fk_client_reminder` FOREIGN KEY (`clientID`) REFERENCES `clients` (`clientID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `clientfiles`
--
ALTER TABLE `clientfiles`
  ADD CONSTRAINT `clientfiles_ibfk_1` FOREIGN KEY (`clientID`) REFERENCES `clients` (`clientID`);

--
-- Constraints for table `clientserviceshistory`
--
ALTER TABLE `clientserviceshistory`
  ADD CONSTRAINT `clientserviceshistory_ibfk_1` FOREIGN KEY (`clientID`) REFERENCES `clients` (`clientID`),
  ADD CONSTRAINT `clientserviceshistory_ibfk_2` FOREIGN KEY (`engineerID`) REFERENCES `engineer` (`engineerID`),
  ADD CONSTRAINT `clientserviceshistory_ibfk_3` FOREIGN KEY (`scheduleID`) REFERENCES `schedulediary` (`scheduleID`),
  ADD CONSTRAINT `clientserviceshistory_ibfk_4` FOREIGN KEY (`feedbackID`) REFERENCES `feedback` (`feedbackID`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`clientID`) REFERENCES `clients` (`clientID`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`clientID`) REFERENCES `clients` (`clientID`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`clientID`) REFERENCES `clients` (`clientID`);

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`clientID`) REFERENCES `clients` (`clientID`);

--
-- Constraints for table `schedulediary`
--
ALTER TABLE `schedulediary`
  ADD CONSTRAINT `schedulediary_ibfk_1` FOREIGN KEY (`engineerID`) REFERENCES `engineer` (`engineerID`),
  ADD CONSTRAINT `schedulediary_ibfk_2` FOREIGN KEY (`clientID`) REFERENCES `clients` (`clientID`);

--
-- Constraints for table `schedulefiles`
--
ALTER TABLE `schedulefiles`
  ADD CONSTRAINT `schedulefiles_ibfk_1` FOREIGN KEY (`scheduleID`) REFERENCES `schedulediary` (`scheduleID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
