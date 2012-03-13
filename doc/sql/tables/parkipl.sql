SET NAMES 'utf8';
DROP TABLE IF EXISTS `parkipl`;
CREATE TABLE IF NOT EXISTS `parkipl` (
  `OGR_FID` int(11) NOT NULL AUTO_INCREMENT,
  `SHAPE` geometry NOT NULL,
  `id` double DEFAULT NULL,
  `name` varchar(80) CHARACTER SET utf8 COLLATE utf8_polish_ci DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `dispclass` double DEFAULT NULL,
  `xcoords` varchar(11) DEFAULT NULL,
  `ycoords` varchar(11) DEFAULT NULL,
  `link` varchar(240) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT 'www.parkinarodowe.edu.pl/pn/ 	',
  `logo` varchar(64) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT 'npa.png',
  UNIQUE KEY `OGR_FID` (`OGR_FID`),
  SPATIAL KEY `SHAPE` (`SHAPE`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

