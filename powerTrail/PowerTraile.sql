-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 09 Sie 2013, 22:33
-- Wersja serwera: 5.5.31
-- Wersja PHP: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `ocpl`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `PowerTrail`
--

CREATE TABLE IF NOT EXISTS `PowerTrail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `centerLatitude` float NOT NULL,
  `centerLongitude` float NOT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `cacheCount` int(11) NOT NULL,
  `description` text NOT NULL,
  `image` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `PowerTrail_actionsLog`
--

CREATE TABLE IF NOT EXISTS `PowerTrail_actionsLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `PowerTrailId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `actionDateTime` datetime NOT NULL,
  `actionType` int(11) NOT NULL,
  `description` text NOT NULL,
  `cacheId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `powerTrail_caches`
--

CREATE TABLE IF NOT EXISTS `powerTrail_caches` (
  `cacheId` int(11) NOT NULL,
  `PowerTrailId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='combain caches witch PowerTrails';

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `PowerTrail_comments`
--

CREATE TABLE IF NOT EXISTS `PowerTrail_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `PowerTrailId` int(11) NOT NULL,
  `commentType` int(11) NOT NULL,
  `commentText` text NOT NULL,
  `logDateTime` datetime NOT NULL,
  `dbInsertDateTime` datetime NOT NULL,
  `deleted` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `PowerTrail_finalCaches`
--

CREATE TABLE IF NOT EXISTS `PowerTrail_finalCaches` (
  `id` int(11) NOT NULL,
  `cacheId` int(11) NOT NULL,
  `PowerTrailId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `PowerTrail_owners`
--

CREATE TABLE IF NOT EXISTS `PowerTrail_owners` (
  `PowerTrailId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `privileages` int(11) NOT NULL,
  UNIQUE KEY `PowerTrailId` (`PowerTrailId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
