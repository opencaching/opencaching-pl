-- 2017-04-26 Redefine entire `waypoint_type` table for consistency
-- @author: andrixnet

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
-- Table structure for table `waypoint_type`
--

DROP TABLE IF EXISTS `waypoint_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `waypoint_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pl` varchar(60) NOT NULL,
  `en` varchar(60) NOT NULL,
  `nl` varchar(60) NOT NULL,
  `de` varchar(60) NOT NULL,
  `ro` varchar(60) NOT NULL,
  `icon` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `waypoint_type`
--

LOCK TABLES `waypoint_type` WRITE;
/*!40000 ALTER TABLE `waypoint_type` DISABLE KEYS */;
INSERT INTO `waypoint_type` VALUES (1,'Punkt fizyczny','Physical point','Physische Punkt','Physische Punkt','Etapă fizică','images/waypoints/wp_physical.png');
INSERT INTO `waypoint_type` VALUES (2,'Punkt wirtualny','Virtual point','Virtuales punkt','Virtuales punkt','Etapă virtuală','images/waypoints/wp_virtual.png');
INSERT INTO `waypoint_type` VALUES (3,'Punkt końcowy','Final location','Endposition','Endposition','Locaţia finală','images/waypoints/wp_final.png');
INSERT INTO `waypoint_type` VALUES (4,'Interesujące miejsce','Interesting place','Interessanter Ort','Interessanter Ort','Loc interesant','images/waypoints/wp_reference.png');
INSERT INTO `waypoint_type` VALUES (5,'Parking','Parking area','Parkplatz','Parkplatz','Parcare','images/waypoints/wp_parking.png');
INSERT INTO `waypoint_type` VALUES (6,'Początek ścieżki','Trailhead','Begin wandelpad','Anfang wanderpfad','Început de traseu','images/waypoints/wp_trailhead.png');
/*!40000 ALTER TABLE `waypoint_type` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

