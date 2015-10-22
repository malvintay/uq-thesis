-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 15, 2015 at 02:46 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `quiz_summary`
--

CREATE TABLE IF NOT EXISTS `quiz_summary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(32) NOT NULL,
  `week1` int(11) NOT NULL,
  `week1_max` int(11) NOT NULL,
  `week2` int(11) NOT NULL,
  `week2_max` int(11) NOT NULL,
  `week3` int(11) NOT NULL,
  `week3_max` int(11) NOT NULL,
  `week4` int(11) NOT NULL,
  `week4_max` int(11) NOT NULL,
  `week5` int(11) NOT NULL,
  `week5_max` int(11) NOT NULL,
  `week6` int(11) NOT NULL,
  `week6_max` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=778 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
