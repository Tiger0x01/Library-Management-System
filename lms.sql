-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2026 at 02:41 AM
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
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `BookId` int(10) NOT NULL,
  `Title` varchar(50) DEFAULT NULL,
  `Availability` int(5) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `Author` varchar(50) NOT NULL,
  `Year` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT 0,
  `file_path` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`BookId`, `Title`, `Availability`, `status`, `Author`, `Year`, `Description`, `is_online`, `file_path`, `price`) VALUES
(29, 'Clean Code', 6, 'New', 'Robert C. Martin', 2008, 'A handbook of agile software craftsmanship that teaches developers how to write clean, maintainable, and efficient code.', 0, NULL, 0.00),
(30, 'Design Patterns', 2, 'New', 'Erich Gamma', 1994, 'A classic guide that introduces reusable object-oriented design patterns to solve common software design problems.', 0, NULL, 0.00),
(31, 'Introduction to Algorithms', 4, 'New', 'Thomas H. Cormen', 2009, 'A comprehensive book covering fundamental algorithms, data structures, and problem-solving techniques used in computer science.', 0, NULL, 0.00),
(32, 'Python Crash Course', 4, 'New', 'Eric Matthes', 2019, 'A fast-paced, beginner-friendly guide to learning Python through hands-on projects and practical examples.', 0, NULL, 0.00),
(33, 'JavaScript: The Good Parts', 2, 'New', 'Douglas Crockford', 2008, 'A deep dive into the core features of JavaScript, focusing on the best practices and most powerful aspects of the language.', 0, NULL, 0.00),
(34, 'test', 5, 'Lost', 'test.io', NULL, NULL, 0, NULL, 0.00),
(35, 'Library Mangement', 1, 'Digital', 'Mohamed Elnemr', NULL, NULL, 1, 'uploads/books/1776642042_library_presentation__1_.pdf', 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchased_books`
--

CREATE TABLE `purchased_books` (
  `id` int(10) NOT NULL,
  `RollNo` varchar(50) NOT NULL,
  `BookId` int(10) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `transaction_id` varchar(50) DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `purchased_books`
--

INSERT INTO `purchased_books` (`id`, `RollNo`, `BookId`, `purchase_date`, `transaction_id`) VALUES
(1, 'Mohamed', 35, '2026-04-19 23:42:25', 'N/A'),
(2, 'Ali', 35, '2026-04-20 00:21:15', '01066453045');

-- --------------------------------------------------------

--
-- Table structure for table `record`
--

CREATE TABLE `record` (
  `RollNo` varchar(50) NOT NULL,
  `BookId` int(10) NOT NULL,
  `Date_of_Issue` date DEFAULT NULL,
  `Due_Date` date DEFAULT NULL,
  `Date_of_Return` date DEFAULT NULL,
  `Renewals_left` int(10) DEFAULT NULL,
  `Time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `record`
--

INSERT INTO `record` (`RollNo`, `BookId`, `Date_of_Issue`, `Due_Date`, `Date_of_Return`, `Renewals_left`, `Time`) VALUES
('Ali', 32, '2026-04-17', '2026-06-16', NULL, 1, '02:26:53'),
('Mohamed', 29, '2026-04-17', '2026-06-16', NULL, 1, '02:35:26'),
('Mohamed', 30, '2026-04-17', '2026-06-16', NULL, 1, '02:35:29'),
('Mohamed', 32, '2026-04-17', '2026-06-16', NULL, 1, '02:34:59');

-- --------------------------------------------------------

--
-- Table structure for table `renew`
--

CREATE TABLE `renew` (
  `RollNo` varchar(50) NOT NULL,
  `BookId` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return`
--

CREATE TABLE `return` (
  `RollNo` varchar(50) NOT NULL,
  `BookId` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `RollNo` varchar(50) NOT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `EmailId` varchar(50) DEFAULT NULL,
  `MobNo` bigint(11) DEFAULT NULL,
  `Password` varchar(50) DEFAULT NULL,
  `wallet_balance` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`RollNo`, `Name`, `Type`, `EmailId`, `MobNo`, `Password`, `wallet_balance`) VALUES
('ADMIN', 'admin', 'Admin', 'ADMIN', 123456789, 'admin', 0.00),
('Ali', 'Ali Ahmed', 'Student', 'Ali@gmail.com', 1234567891, 'student', 0.00),
('Mohamed', 'Mohamed Elnemr', 'Student', 'MohamedElnemr@gmail.com', 1066453045, 'student', 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`BookId`);

--
-- Indexes for table `purchased_books`
--
ALTER TABLE `purchased_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `RollNo` (`RollNo`),
  ADD KEY `BookId` (`BookId`);

--
-- Indexes for table `record`
--
ALTER TABLE `record`
  ADD PRIMARY KEY (`RollNo`,`BookId`),
  ADD KEY `BookId` (`BookId`);

--
-- Indexes for table `renew`
--
ALTER TABLE `renew`
  ADD PRIMARY KEY (`RollNo`,`BookId`),
  ADD KEY `BookId` (`BookId`);

--
-- Indexes for table `return`
--
ALTER TABLE `return`
  ADD PRIMARY KEY (`RollNo`,`BookId`),
  ADD KEY `BookId` (`BookId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`RollNo`),
  ADD UNIQUE KEY `EmailId` (`EmailId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `BookId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `purchased_books`
--
ALTER TABLE `purchased_books`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `purchased_books`
--
ALTER TABLE `purchased_books`
  ADD CONSTRAINT `purchased_books_ibfk_1` FOREIGN KEY (`RollNo`) REFERENCES `user` (`RollNo`),
  ADD CONSTRAINT `purchased_books_ibfk_2` FOREIGN KEY (`BookId`) REFERENCES `book` (`BookId`);

--
-- Constraints for table `record`
--
ALTER TABLE `record`
  ADD CONSTRAINT `record_ibfk_1` FOREIGN KEY (`RollNo`) REFERENCES `user` (`RollNo`),
  ADD CONSTRAINT `record_ibfk_2` FOREIGN KEY (`BookId`) REFERENCES `book` (`BookId`);

--
-- Constraints for table `renew`
--
ALTER TABLE `renew`
  ADD CONSTRAINT `renew_ibfk_1` FOREIGN KEY (`RollNo`) REFERENCES `user` (`RollNo`),
  ADD CONSTRAINT `renew_ibfk_2` FOREIGN KEY (`BookId`) REFERENCES `book` (`BookId`);

--
-- Constraints for table `return`
--
ALTER TABLE `return`
  ADD CONSTRAINT `return_ibfk_1` FOREIGN KEY (`RollNo`) REFERENCES `user` (`RollNo`),
  ADD CONSTRAINT `return_ibfk_2` FOREIGN KEY (`BookId`) REFERENCES `book` (`BookId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
