SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


--
-- Table structure for table `gk_item_type`
--

CREATE TABLE IF NOT EXISTS `gk_item_type` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gk_item_type`
--

INSERT INTO `gk_item_type` (`id`, `name`) VALUES
(0, ''),
(1, 'A book/CD/DVD...'),
(2, 'A human'),
(3, 'A coin'),
(4, 'KretyPost');

--
-- Table structure for table `gk_move_type`
--

CREATE TABLE IF NOT EXISTS `gk_move_type` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gk_move_type`
--

INSERT INTO `gk_move_type` (`id`, `name`) VALUES
(0, 'Dropped to'),
(1, 'Grabbed from'),
(2, 'A comment'),
(3, 'Seen in'),
(4, 'Archived'),
(5, 'Dipped in');

--
-- Initialize sysconfig variables
--

UPDATE `sysconfig`
    SET `value`=NOW()
    WHERE `name`='geokrety_lastupdate';

