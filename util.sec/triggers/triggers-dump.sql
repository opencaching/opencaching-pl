-- MySQL dump 10.11
--
-- Host: localhost    Database: opecaching
-- ------------------------------------------------------
-- Server version	5.0.33-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache_attrib`
--


/*!50003 SET @OLD_SQL_MODE=@@SQL_MODE*/;
DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cacheDescBeforeInsert` BEFORE INSERT ON `cache_desc` FOR EACH ROW BEGIN SET NEW.`date_created`=NOW(); END */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cacheDescAfterInsert` AFTER INSERT ON `cache_desc` FOR EACH ROW BEGIN UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=NEW.`cache_id`; END */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cacheDescAfterUpdate` AFTER UPDATE ON `cache_desc` FOR EACH ROW BEGIN IF OLD.`cache_id` != NEW.`cache_id` OR OLD.`language` != NEW.`language` THEN UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=NEW.`cache_id`; IF OLD.`cache_id` != NEW.`cache_id` THEN UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=OLD.`cache_id`; END IF; END IF; END */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cacheDescAfterDelete` AFTER DELETE ON `cache_desc` FOR EACH ROW BEGIN UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=OLD.`cache_id`; END */;;

DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE */;


/*!50003 SET @OLD_SQL_MODE=@@SQL_MODE*/;
DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cacheIgnoreAfterInsert` AFTER INSERT ON `cache_ignore` FOR EACH ROW BEGIN 
						UPDATE `caches` SET `ignorer_count`=(SELECT COUNT(*) FROM `cache_ignore` WHERE `cache_id`=NEW.cache_id) WHERE `cache_id`=NEW.cache_id;
					END */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cacheIgnoreAfterUpdate` AFTER UPDATE ON `cache_ignore` FOR EACH ROW BEGIN 
						IF OLD.`cache_id`!=NEW.`cache_id` THEN
							UPDATE `caches` SET `ignorer_count`=(SELECT COUNT(*) FROM `cache_ignore` WHERE `cache_id`=OLD.cache_id) WHERE `cache_id`=OLD.cache_id;
							UPDATE `caches` SET `ignorer_count`=(SELECT COUNT(*) FROM `cache_ignore` WHERE `cache_id`=NEW.cache_id) WHERE `cache_id`=NEW.cache_id;
						END IF;
					END */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cacheIgnoreAfterDelete` AFTER DELETE ON `cache_ignore` FOR EACH ROW BEGIN 
						UPDATE `caches` SET `ignorer_count`=(SELECT COUNT(*) FROM `cache_ignore` WHERE `cache_id`=OLD.cache_id) WHERE `cache_id`=OLD.cache_id;
					END */;;

DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE */;

/*!50003 SET @OLD_SQL_MODE=@@SQL_MODE*/;
DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cacheRatingAfterInsert` AFTER INSERT ON `cache_rating` FOR EACH ROW BEGIN 
UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=NEW.`cache_id`) WHERE `cache_id`=NEW.`cache_id`;
END */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cacheRatingAfterUpdate` AFTER UPDATE ON `cache_rating` FOR EACH ROW BEGIN 
IF OLD.`cache_id`!=NEW.`cache_id` THEN
UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=OLD.`cache_id`) WHERE `cache_id`=OLD.`cache_id`;
UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=NEW.`cache_id`) WHERE `cache_id`=NEW.`cache_id`;
END IF;
END */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cacheRatingAfterDelete` AFTER DELETE ON `cache_rating` FOR EACH ROW BEGIN 
UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=OLD.`cache_id`) WHERE `cache_id`=OLD.`cache_id`;
END */;;

DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE */;

/*!50003 SET @OLD_SQL_MODE=@@SQL_MODE*/;
DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cachesAfterInsert` AFTER INSERT ON `caches` FOR EACH ROW BEGIN INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_modified`, `longitude`, `latitude`) VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`); INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_modified`, `country`) VALUES (NEW.`cache_id`, NOW(), NEW.`country`); UPDATE `user`, (SELECT COUNT(*) AS `hidden_count` FROM `caches` WHERE `user_id`=NEW.`user_id` AND `status` IN (1, 2, 3)) AS `c` SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=NEW.`user_id`; END */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cachesAfterUpdate` AFTER UPDATE ON `caches` FOR EACH ROW BEGIN IF NEW.`longitude` != OLD.`longitude` OR NEW.`latitude` != OLD.`latitude` THEN INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_modified`, `longitude`, `latitude`) VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`); END IF; IF NEW.`country` != OLD.`country` THEN INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_modified`, `country`) VALUES (NEW.`cache_id`, NOW(), NEW.`country`); END IF; IF NEW.`status` != OLD.`status` OR NEW.`user_id` != OLD.`user_id` THEN UPDATE `user`, (SELECT COUNT(*) AS `hidden_count` FROM `caches` WHERE `user_id`=NEW.`user_id` AND `status` IN (1, 2, 3)) AS `c` SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=NEW.`user_id`; IF NEW.`user_id` != OLD.`user_id` THEN UPDATE `user`, (SELECT COUNT(*) AS `hidden_count` FROM `caches` WHERE `user_id`=OLD.`user_id` AND `status` IN (1, 2, 3)) AS `c` SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=OLD.`user_id`; END IF; END IF; END */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `cachesAfterDelete` AFTER DELETE ON `caches` FOR EACH ROW BEGIN DELETE FROM `cache_coordinates` WHERE `cache_id`=OLD.`cache_id`; DELETE FROM `cache_countries` WHERE `cache_id`=OLD.`cache_id`; UPDATE `user`, (SELECT COUNT(*) AS `hidden_count` FROM `caches` WHERE `user_id`=OLD.`user_id` AND `status` IN (1, 2, 3)) AS `c` SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=OLD.`user_id`; END */;;

DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-01-18 10:51:28
