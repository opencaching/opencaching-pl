-- andrixnet
-- New cache types according to https://github.com/opencaching/opencaching-pl/issues/860
-- cache_type ID changes according to https://github.com/opencaching/opencaching-pl/issues/2024

-- ########################################################################
-- Recreate table `cache_type` (simpler then multiple editing in this case)
-- Table structure for table `cache_type`
--

DROP TABLE IF EXISTS `cache_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT 'This also is the translation ID number; see I18n::getIdColumnName()',
  `short` varchar(10) NOT NULL,
  `pl` varchar(60) NOT NULL,
  `en` varchar(60) NOT NULL,
  `nl` varchar(60) NOT NULL,
  `ro` varchar(60) NOT NULL,
  `de` varchar(60) NOT NULL,
  `fr` varchar(60) NOT NULL,
  `icon_large` varchar(60) NOT NULL,
  `icon_small` varchar(60) NOT NULL,
  `color` varchar(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Repopulate table `cache_type`
LOCK TABLES `cache_type` WRITE;
/*!40000 ALTER TABLE `cache_type` DISABLE KEYS */;
--                               id,sort,short,pl,en,nl,ro,de,fr,icon_large,icon_small,color
INSERT INTO `cache_type` VALUES (1,5,'Other','Nietypowa','Other type','Onbekende Cache','Necunoscută','Sonstiger Cachetyp','Autre type','cache/unknown.png','cache/16x16-unknown.png','#FFFF00');
INSERT INTO `cache_type` VALUES (2,1,'Traditional','Tradycyjna','Traditional','Traditionele Cache','Tradiţională','Traditioneller Cache','Traditionnel','cache/traditional.png','cache/16x16-traditional.png','#0000FF');
INSERT INTO `cache_type` VALUES (3,2,'Multicache','Multicache','Multicache','Multi Cache','Multiplă','Multicache','Multicache','cache/multi.png','cache/16x16-multi.png','#00D400');
INSERT INTO `cache_type` VALUES (4,8,'Virtual','Wirtualna','Virtual','Virtuele Cache','Virtuală','Virtueller Cache','Virtuelle','cache/virtual.png','cache/16x16-virtual.png','#00D5FF');
INSERT INTO `cache_type` VALUES (5,7,'Webcam.','Webcam','Webcam','Webcam Cache','Cameră web','Webcam-Cache','Webcam','cache/webcam.png','cache/16x16-webcam.png','#00FFFF');
INSERT INTO `cache_type` VALUES (6,6,'Event','Wydarzenie','Event','Evenement','Eveniment','Event-Cache','Événement','cache/event.png','cache/16x16-event.png','#FF80FF');
INSERT INTO `cache_type` VALUES (7,3,'Quiz','Quiz','Puzzle','Puzzel Cache','Puzzle','Rätselcache','Énigme','cache/quiz.png','cache/16x16-quiz.png','#FF8000');
INSERT INTO `cache_type` VALUES (9,4,'Moving','Mobilna','Moving','Reizend','Mobilă','Beweglicher Cache','Mobile','cache/moving.png','cache/16x16-moving.png','#FF99FF');
INSERT INTO `cache_type` VALUES (21,9,'GeoPath FINAL','FINAŁ GeoŚcieżki','GeoPath FINAL','GeoPath FINAL','FINAL GeoTraseu','GeoPath FINALE','GeoPath FINALE','cache/geopath.png','cache/16x16-geopath.png','#00CC00');
INSERT INTO `cache_type` VALUES (22,99,'Owncache','Own cache','Own cache','Eigen Cache','Personală','Persönlicher Cache','Votre cache','cache/owncache.png','cache/16x16-owncache.png','#009900');
INSERT INTO `cache_type` VALUES (23,23,'Guestbook','Księga gości','Guestbook','Gastenboek','Carte de oaspeţi','Gästebuch','Livre d\'or','cache/guestbook.png','cache/16x16-guestbook.png','#CC9900');
INSERT INTO `cache_type` VALUES (24,24,'BIT cache','BIT cache','BIT cache','BIT cache','BIT cache','BIT cache','BIT cache','cache/bitcache.png','cache/16x16-bitcache.png','#000000');
INSERT INTO `cache_type` VALUES (25,25,'Benchmark','Punkt geodezyjny','Benchmark','Meetpunt','Marcaj topografic','Vermessungspunkt','Point géodésique','cache/benchmark.png','cache/16x16-benchmark.png','#666666');
INSERT INTO `cache_type` VALUES (26,26,'Challenge','Wyzwanie','Challenge','Uitdaging','Provocare','Herausforderung','Défi','cache/challenge.png','cache/16x16-challenge.png','#FF0000');
/*!40000 ALTER TABLE `cache_type` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

-- ########################################################################
-- Implement cache_type_id changes in caches table

-- Podcast ID=9 => 1 (other)
-- * safeguard to handle old data from when "podcache" was still in use, if any
-- * add MP3 attribute (new ID according to https://wiki.opencaching.eu/index.php?title=Cache_attributes ) 
INSERT INTO `caches_attributes` SELECT `cache_id`,205 FROM `caches` where `type`=9;
-- * change type to "other"
UPDATE `caches`
    SET `type`=1 WHERE `type`=9;

-- Moving cache ID=8 => 9
UPDATE `caches`
    SET `type`=9 WHERE `type`=8;
    
-- Owncache ID=10 => 22
UPDATE `caches`
    SET `type`=22 WHERE `type`=10;

