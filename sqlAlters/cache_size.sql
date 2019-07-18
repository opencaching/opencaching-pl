-- 2019-07-17
-- @author: harrieklomp
--
-- Table structure for table `cache_size`
--

RENAME TABLE `cache_size` TO `cache_size_backup`; 

CREATE TABLE `cache_size` (
  `id` int(11) NOT NULL,
  `pl` varchar(60) NOT NULL,
  `en` varchar(60) NOT NULL,
  `nl` varchar(60) NOT NULL,
  `ro` varchar(60) NOT NULL,
  `de` varchar(60) NOT NULL,
  `fr` varchar(60) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cache_size`
--

INSERT INTO `cache_size` (`id`, `pl`, `en`, `nl`, `ro`, `de`, `fr`) VALUES
(8, 'Nano', 'Nano', 'Nano', 'Nano', 'Nano', 'Nano'),
(2, 'Mikro', 'Micro', 'Micro', 'Micro', 'Mikro', 'Micro'),
(3, 'Mała', 'Small', 'Klein', 'Mică', 'Klein', 'Petite'),
(4, 'Normalna', 'Regular', 'Normaal', 'Normală', 'Normal', 'Normale'),
(5, 'Duża', 'Large', 'Groot', 'Mare', 'Groß', 'Grande'),
(6, 'Bardzo duża', 'Very large', 'Extra groot', 'Foarte mare', 'Extra groß', 'Très grande'),
(7, 'Bez pojemnika', 'No container', 'Geen behuizing', 'Fără cutie', 'Kein Behälter', 'Aucune boîte'),
(1, 'Nieokreślony', 'Not specified', 'Niet opgegeven', 'Nespecificat', 'Nicht angegeben', 'Non spécifié');

--
-- Indexes for table `cache_size`
--

ALTER TABLE `cache_size`
  ADD PRIMARY KEY (`id`);


