SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Baza danych: `ocpl`
--

--
-- Zrzut danych tabeli `cache_attrib`
--

INSERT INTO `cache_attrib` (`id`, `language`, `text_short`, `text_long`, `icon_large`, `icon_no`, `icon_undef`, `category`, `default`) VALUES
(60, 'PL', 'Natura', 'Umiejscowiona na łonie natury, lasy, góry itp', 'images/attributes/nature.png', 'images/attributes/nature-no.png', 'images/attributes/nature-undef.png', 1, 0),
(40, 'PL', 'Szybka skrzynka', 'Szybka skrzynka', 'images/attributes/quick.png', 'images/attributes/quick-no.png', 'images/attributes/quick-undef.png', 1, 0),
(40, 'EN', 'One-minute cache', 'One-minute cache', 'images/attributes/quick.png', 'images/attributes/quick-no.png', 'images/attributes/quick-undef.png', 1, 0),
(41, 'EN', 'Children', 'Go geocaching with children', 'images/attributes/children.png', 'images/attributes/children-no.png', 'images/attributes/children-undef.png', 1, 0),
(51, 'PL', 'Offset cache', 'Offset cache', 'images/attributes/offset.png', 'images/attributes/offset-no.png', 'images/attributes/offset-undef.png', 1, 0),
(51, 'EN', 'Offset cache', 'Offset cache', 'images/attributes/offset.png', 'images/attributes/offset-no.png', 'images/attributes/offset-undef.png', 1, 0),
(43, 'PL', 'GeoHotel', 'GeoHotel', 'images/attributes/geohotel.png', 'images/attributes/geohotel-no.png', 'images/attributes/geohotel-undef.png', 1, 0),
(43, 'EN', 'GeoHotel', 'GeoHotel', 'images/attributes/geohotel.png', 'images/attributes/geohotel-no.png', 'images/attributes/geohotel-undef.png', 1, 0),
(90, 'PL', 'Niebezpieczeństwo', 'Skrzynka niebezpieczna', 'images/attributes/danger.png', 'images/attributes/danger-no.png', 'images/attributes/danger-undef.png', 1, 0),
(90, 'EN', 'Danger', 'Dangerous Cache', 'images/attributes/danger.png', 'images/attributes/danger-no.png', 'images/attributes/danger-undef.png', 1, 0),
(60, 'EN', 'Nature', 'Nature', 'images/attributes/nature.png', 'images/attributes/nature-no.png', 'images/attributes/nature-undef.png', 1, 0),
(41, 'PL', 'Dzieci', 'Można zabrać dzieci', 'images/attributes/children.png', 'images/attributes/children-no.png', 'images/attributes/children-undef.png', 1, 0),
(82, 'PL', 'Latarka', 'Potrzebna latarka', 'images/attributes/torch.png', 'images/attributes/torch-no.png', 'images/attributes/torch-undef.png', 1, 0),
(82, 'EN', 'Torch', 'Torch needed', 'images/attributes/torch.png', 'images/attributes/torch-no.png', 'images/attributes/torch-undef.png', 1, 0),
(61, 'PL', 'Historyczna', 'Miejsce historyczne', 'images/attributes/monument.png', 'images/attributes/monument-no.png', 'images/attributes/monument-undef.png', 1, 0),
(61, 'EN', 'Monumental', 'Monumental place', 'images/attributes/monument.png', 'images/attributes/monument-no.png', 'images/attributes/monument-undef.png', 1, 0),
(80, 'PL', 'Okresowa/Płatna', 'Dostępna w określonych godzinach lub płatna', 'images/attributes/period.png', 'images/attributes/period-no.png', 'images/attributes/period-undef.png', 1, 0),
(80, 'EN', 'Periodical/Paid', 'Periodical/Paid', 'images/attributes/period.png', 'images/attributes/period-no.png', 'images/attributes/period-undef.png', 1, 0),
(83, 'PL', 'Sprzęt', 'Wymagany dodatkowy sprzęt', 'images/attributes/equip.png', 'images/attributes/equip-no.png', 'images/attributes/equip-undef.png', 1, 0),
(83, 'EN', 'Equipment', 'Take special equipment', 'images/attributes/equip.png', 'images/attributes/equip-no.png', 'images/attributes/equip-undef.png', 1, 0),
(81, 'PL', 'Łopatka', 'Potrzebna łopatka', 'images/attributes/dig.png', 'images/attributes/dig-no.png', 'images/attributes/dig-undef.png', 1, 0),
(81, 'EN', 'Shovel', 'You will need a shovel', 'images/attributes/dig.png', 'images/attributes/dig-no.png', 'images/attributes/dig-undef.png', 1, 0),
(44, 'PL', 'Niepelnosprawni', 'Dostępna dla niepełnosprawnych', 'images/attributes/disabled.png', 'images/attributes/disabled-no.png', 'images/attributes/disabled-undef.png', 1, 0),
(44, 'EN', 'for disabled', 'Accessible for disabled', 'images/attributes/disabled.png', 'images/attributes/disabled-no.png', 'images/attributes/disabled-undef.png', 1, 0),
(47, 'PL', 'Potrzebny kompas', 'Potrzebny kompas', 'images/attributes/compass.png', 'images/attributes/compass-no.png', 'images/attributes/compass-undef.png', 1, 0),
(47, 'EN', 'Compass', 'Compass', 'images/attributes/compass.png', 'images/attributes/compass-no.png', 'images/attributes/compass-undef.png', 1, 0),
(48, 'PL', 'Weź coś do pisania', 'Weź coś do pisania', 'images/attributes/pencil.png', 'images/attributes/pencil-no.png', 'images/attributes/pencil-undef.png', 1, 0),
(48, 'EN', 'Take something to wr', 'Take something to write', 'images/attributes/pencil.png', 'images/attributes/pencil-no.png', 'images/attributes/pencil-undef.png', 1, 0),
(49, 'PL', 'Magnes', 'Przyczepiona magnesem', 'images/attributes/magnet.png', 'images/attributes/magnet-no.png', 'images/attributes/magnet-undef.png', 1, 0),
(49, 'EN', 'Magnet', 'Fixed by magnet', 'images/attributes/magnet.png', 'images/attributes/magnet-no.png', 'images/attributes/magnet-undef.png', 1, 0),
(91, 'PL', 'Zalecane szukanie no', 'Zalecane szukanie nocą', 'images/attributes/night.png', 'images/attributes/night-no.png', 'images/attributes/night-undef.png', 1, 0),
(91, 'EN', 'Recommended at night', 'Recommended at night', 'images/attributes/night.png', 'images/attributes/night-no.png', 'images/attributes/night-undef.png', 1, 0),
(50, 'PL', 'Informacje MP3', 'Informacje zapisane w MP3', 'images/attributes/mp3.png', 'images/attributes/mp3-no.png', 'images/attributes/mp3-undef.png', 1, 0),
(50, 'EN', 'Information in MP3 ', 'Information in  MP3 file', 'images/attributes/mp3.png', 'images/attributes/mp3-no.png', 'images/attributes/mp3-undef.png', 1, 0),
(52, 'PL', 'Beacon', 'Beacon - Garmin chirp', 'images/attributes/beacon.png', 'images/attributes/beacon-no.png', 'images/attributes/beacon-undef.png', 1, 0),
(52, 'EN', 'Beacon', 'Beacon - Garmin chirp', 'images/attributes/beacon.png', 'images/attributes/beacon-no.png', 'images/attributes/beacon-undef.png', 1, 0),
(53, 'PL', 'Dead Drop USB', 'Dead Drop USB skrzynka', 'images/attributes/deaddrop.png', 'images/attributes/deaddrop-no.png', 'images/attributes/deaddrop-undef.png', 1, 0),
(53, 'EN', 'Dead Drop USB', 'Dead Drop USB cache', 'images/attributes/deaddrop.png', 'images/attributes/deaddrop-no.png', 'images/attributes/deaddrop-undef.png', 1, 0),
(54, 'EN', 'Benchmark ', 'Benchmark - geodetict point', 'images/attributes/benchmark.png', 'images/attributes/benchmark-no.png', 'images/attributes/benchmark-undef.png', 1, 0),
(54, 'PL', 'Punkt geodezyjny', 'Benchmark - punkt geodezyjny', 'images/attributes/benchmark.png', 'images/attributes/benchmark-no.png', 'images/attributes/benchmark-undef.png', 1, 0),
(55, 'PL', 'Scenariusz WIGO', 'Scenariusz WIGO', 'images/attributes/wigo.png', 'images/attributes/wigo-no.png', 'images/attributes/wigo-undef.png', 1, 0),
(55, 'EN', 'Cartridge', 'Cartridge ', 'images/attributes/wigo.png', 'images/attributes/wigo-no.png', 'images/attributes/wigo-undef.png', 1, 0),
(84, 'PL', 'Dostępna pieszo', 'Dostępna tylko pieszo', 'images/attributes/walk.png', 'images/attributes/walk-no.png', 'images/attributes/walk-undef.png', 1, 0),
(84, 'EN', 'Access via walk', 'Access only by walk', 'images/attributes/walk.png', 'images/attributes/walk-no.png', 'images/attributes/walk-undef.png', 1, 0),
(85, 'PL', 'Dostępna rowerem', 'Dostępna rowerem', 'images/attributes/bike.png', 'images/attributes/bike-no.png', 'images/attributes/bike-undef.png', 1, 0),
(85, 'EN', 'Bike', 'Bike', 'images/attributes/bike.png', 'images/attributes/bike-no.png', 'images/attributes/bike-undef.png', 1, 0),
(86, 'PL', 'Wymagana łódź', 'Dostępna tylko łodzią', 'images/attributes/boat.png', 'images/attributes/boat-no.png', 'images/attributes/boat-undef.png', 1, 0),
(86, 'EN', 'Boat', 'Boat', 'images/attributes/boat.png', 'images/attributes/boat-no.png', 'images/attributes/boat-undef.png', 1, 0),
(56, 'PL', 'Letterbox', 'Letterbox', 'images/attributes/letterbox.png', 'images/attributes/letterbox-no.png', 'images/attributes/letterbox-undef.png', 1, 0),
(56, 'EN', 'Letterbox', 'Letterbox', 'images/attributes/letterbox.png', 'images/attributes/letterbox-no.png', 'images/attributes/letterbox-undef.png', 1, 0);

--
-- Zrzut danych tabeli `cache_size`
--

INSERT INTO `cache_size` (`id`, `pl`, `en`) VALUES
(2, 'Mikro', 'Micro'),
(3, 'Mała', 'Small'),
(4, 'Normalna', 'Normal'),
(5, 'Duża', 'Large'),
(6, 'Bardzo duża', 'Very large'),
(7, 'Bez pojemnika', 'No container');

--
-- Zrzut danych tabeli `cache_status`
--

INSERT INTO `cache_status` (`id`, `pl`, `en`) VALUES
(1, 'Gotowa do szukania', 'Ready for search'),
(2, 'Tymczasowo niedostępna', 'Temporarily unavailable'),
(3, 'Zarchiwizowana', 'Archived'),
(4, 'Ukryta do czasu weryfikacji', 'Hidden by approvers to check'),
(5, 'Jeszcze niedostępna', 'Not yet available'),
(6, 'Zablokowana przez RR', 'Blocked by RR');

--
-- Zrzut danych tabeli `cache_type`
--

INSERT INTO `cache_type` (`id`, `sort`, `short`, `pl`, `en`, `icon_large`, `icon_small`, `color`) VALUES
(1, 5, 'Other', 'Skrzynka nietypowa', 'Unknown type', 'cache/unknown.png', 'cache/16x16-unknown.png', '#FFFF00'),
(2, 1, 'Trad.', 'Tradycyjna', 'Traditional', 'cache/traditional.png', 'cache/16x16-traditional.png', '#0000FF'),
(3, 2, 'Multi', 'Multicache', 'Multicache', 'cache/multi.png', 'cache/16x16-multi.png', '#00D400'),
(4, 8, 'Virt.', 'Wirtualna', 'Virtual ', 'cache/virtual.png', 'cache/16x16-virtual.png', '#00D5FF'),
(5, 7, 'ICam.', 'Webcam', 'Webcam ', 'cache/webcam.png', 'cache/16x16-webcam.png', '#00FFFF'),
(6, 6, 'Event', 'Wydarzenie', 'Event ', 'cache/event.png', 'cache/16x16-event.png', '#FF80FF'),
(7, 3, 'Quiz', 'Quiz', 'Quiz', 'cache/quiz.png', 'cache/16x16-quiz.png', '#FF8000'),
(8, 4, 'Moving', 'Mobilna', 'Moving', 'cache/moving.png', 'cache/16x16-moving.png', '#FF00FF'),
(9, 9, 'Podcast', 'Podcast cache', 'Podcast cache', 'cache/podcache.png', 'cache/16x16-podcache.png', '#FF00FF'),
(10, 10, 'own-cache', 'Own cache', 'Own cache', 'cache/owncache.png', 'cache/16x16-owncache.png', '#FF00FF');


--
-- Zrzut danych tabeli `countries`
--

INSERT INTO `countries` (`country_id`, `pl`, `en`, `short`, `list_default_pl`, `sort_pl`, `list_default_en`, `sort_en`) VALUES
(1, 'Andora', 'Andorra', 'AD', 0, 'andorra', 0, 'andorra'),
(2, 'Zjednoczone Emiraty Arabskie', 'United Arab Emirates', 'AE', 0, 'arab emirates, united', 0, 'arab emirates, united'),
(3, 'Afghanistan', 'Afghanistan', 'AF', 0, 'afghanistan', 0, 'afghanistan'),
(4, 'Antigua und Barbuda', 'Antigua And Barbuda', 'AG', 0, 'antigua und barbuda', 0, 'antigua and barbuda'),
(5, 'Anguilla', 'Anguilla', 'AI', 0, 'anguilla', 0, 'anguilla'),
(6, 'Albanien', 'Albania', 'AL', 0, 'albanien', 0, 'albania'),
(7, 'Armenien', 'Armenia', 'AM', 0, 'armenien', 0, 'armenia'),
(8, 'Niederländische Antillen', 'Netherlands Antilles', 'AN', 0, 'antillen, niederlaendische', 0, 'netherlands antilles'),
(9, 'Angola', 'Angola', 'AO', 0, 'angola', 0, 'angola'),
(10, 'Argentinien', 'Argentina', 'AR', 0, 'argentinien', 0, 'argentina'),
(11, 'Amerikanisch-Samoa', 'American Samoa', 'AS', 0, 'samoa-amerikanisch', 0, 'american samoa'),
(12, 'Austria', 'Austria', 'AT', 1, 'austria', 1, 'austria'),
(13, 'Australien', 'Australia', 'AU', 0, 'australien', 0, 'australia'),
(14, 'Aruba', 'Aruba', 'AW', 0, 'aruba', 0, 'aruba'),
(15, 'Aserbaidschan', 'Azerbaijan', 'AZ', 0, 'aserbaidschan', 0, 'azerbaijan'),
(16, 'Bosnien-Herzegowina', 'Bosnia And Herzegowina', 'BA', 0, 'bosnien-herzegowina', 0, 'bosnia and herzegowina'),
(17, 'Barbados', 'Barbados', 'BB', 0, 'barbados', 0, 'barbados'),
(18, 'Bangladesch', 'Bangladesh', 'BD', 0, 'bangladesch', 0, 'bangladesh'),
(19, 'Belgia', 'Belgium', 'BE', 1, 'Belgia', 1, 'belgium'),
(20, 'Burkina Faso', 'Burkina Faso', 'BF', 0, 'burkina faso', 0, 'burkina faso'),
(21, 'Bułgaria', 'Bulgaria', 'BG', 1, 'bułgaria', 1, 'bulgaria'),
(22, 'Bahrain', 'Bahrain', 'BH', 0, 'bahrain', 0, 'bahrain'),
(23, 'Burundi', 'Burundi', 'BI', 0, 'burundi', 0, 'burundi'),
(24, 'Benin', 'Benin', 'BJ', 0, 'benin', 0, 'benin'),
(25, 'Bermuda', 'Bermuda', 'BM', 0, 'bermuda', 0, 'bermuda'),
(26, 'Brunei', 'Brunei Darussalam', 'BN', 0, 'brunei', 0, 'brunei darussalam'),
(27, 'Bolivien', 'Bolivia', 'BO', 0, 'bolivien', 0, 'bolivia'),
(28, 'Brasilien', 'Brazil', 'BR', 0, 'brasilien', 0, 'brazil'),
(29, 'Bahamas', 'Bahamas', 'BS', 0, 'bahamas', 0, 'bahamas'),
(30, 'Bhutan', 'Bhutan', 'BT', 0, 'bhutan', 0, 'bhutan'),
(31, 'die Bouvetinseln', 'Bouvet Island', 'BV', 0, 'bouvetinseln, die', 0, 'bouvet island'),
(32, 'Botsuana', 'Botswana', 'BW', 0, 'botsuana', 0, 'botswana'),
(33, 'Białoruś', 'Belarus', 'BY', 1, 'Białoruś', 1, 'belarus'),
(34, 'Belize', 'Belize', 'BZ', 0, 'belize', 0, 'belize'),
(35, 'Kanada', 'Canada', 'CA', 0, 'kanada', 1, 'canada'),
(36, 'Kokosinseln', 'Cocos (Keeling) Islands', 'CC', 0, 'kokosinseln', 0, 'cocos (keeling) islands'),
(37, 'Zentralafrikanische Republik', 'Central African Republic', 'CF', 0, 'zentralafrikanische republik', 0, 'central african republic'),
(38, 'Kongo', 'Congo', 'CG', 0, 'kongo', 0, 'congo'),
(39, 'Szwajcaria', 'Switzerland', 'CH', 1, 'szwajcaria', 1, 'Switzerland'),
(40, 'Elfenbeinküste', 'Cote d''Ivoire', 'CI', 0, 'elfenbeinkueste', 0, 'Zimbabwe'),
(41, 'Cookinseln', 'Cook Islands', 'CK', 0, 'cookinseln', 0, 'cook islands'),
(42, 'Chile', 'Chile', 'CL', 0, 'chile', 0, 'chile'),
(43, 'Kamerun', 'Cameroon', 'CM', 0, 'kamerun', 0, 'cameroon'),
(44, 'Volksrepublik China', 'China', 'CN', 0, 'china, volksrepublik', 0, 'china'),
(45, 'Kolumbien', 'Colombia', 'CO', 0, 'kolumbien', 0, 'colombia'),
(46, 'Costa Rica', 'Costa Rica', 'CR', 0, 'costa rica', 0, 'costa rica'),
(47, 'Serbien und Montenegro', 'Serbia and Montenegro', 'CS', 0, 'serbien und montenegro', 0, 'serbia and montenegro'),
(48, 'Kuba', 'Cuba', 'CU', 0, 'kuba', 0, 'cuba'),
(49, 'Kapverden', 'Cape Verde', 'CV', 0, 'kapverden', 0, 'cape verde'),
(50, 'Weihnachtsinseln', 'Christmas Island', 'CX', 0, 'weihnachtsinseln', 0, 'christmas island'),
(51, 'Cypr', 'Cyprus', 'CY', 0, 'cypr', 0, 'cyprus'),
(52, 'Czeska Republika', 'Czech Republic', 'CZ', 1, 'czeska republika', 1, 'czech republic'),
(53, 'Niemcy', 'Germany', 'DE', 1, 'Niemcy', 1, 'germany'),
(54, 'Dschibuti', 'Djibouti', 'DJ', 0, 'dschibuti', 0, 'djibouti'),
(55, 'Dania', 'Denmark', 'DK', 1, 'dania', 1, 'denmark'),
(56, 'Dominikanische Republik', 'Dominican Republic', 'DO', 0, 'dominikanische republik', 0, 'dominican republic'),
(57, 'Algerien', 'Algeria', 'DZ', 0, 'algerien', 0, 'algeria'),
(58, 'Ecuador', 'Ecuador', 'EC', 0, 'ecuador', 0, 'ecuador'),
(59, 'Estonia', 'Estonia', 'EE', 1, 'Estonia', 1, 'estonia'),
(60, 'Egipt', 'Egypt', 'EG', 0, 'Egipt', 0, 'egypt'),
(61, 'Westsahara', 'Western Sahara', 'EH', 0, 'westsahara', 0, 'western sahara'),
(62, 'Eritrea', 'Eritrea', 'ER', 0, 'eritrea', 0, 'eritrea'),
(63, 'Hiszpania', 'Spain', 'ES', 1, 'hiszpania', 1, 'spain'),
(64, 'Äthiopien', 'Ethiopia', 'ET', 0, 'aethiopien', 0, 'ethiopia'),
(65, 'Finlandia', 'Finnland', 'FI', 1, 'finlandia', 1, 'finland'),
(66, 'Fidschi', 'Fiji', 'FJ', 0, 'fidschi', 0, 'fiji'),
(67, 'Falklandinseln', 'Falkland Islands (Malvinas)', 'FK', 0, 'falklandinseln', 0, 'falkland islands (malvinas)'),
(68, 'Mikronesien', 'Micronesia, Federated States Of', 'FM', 0, 'mikronesien', 0, 'micronesia, federated states of'),
(69, 'Färöer (zu Dänemark)', 'Faroe Islands', 'FO', 0, 'faeroeer (zu daenemark)', 0, 'faroe islands'),
(70, 'Francja', 'France', 'FR', 1, 'Francja', 1, 'france'),
(71, 'Gabun', 'Gabon', 'GA', 0, 'gabun', 0, 'gabon'),
(72, 'Wielka Brytania', 'United Kingdom (UK)', 'GB', 1, 'Wielka Brytania', 1, 'united kingdom (uk)'),
(73, 'Granada', 'Grenada', 'GD', 0, 'granada', 0, 'grenada'),
(74, 'Georgien', 'Georgia', 'GE', 0, 'georgien', 0, 'georgia'),
(75, 'Guayana', 'French Guiana', 'GF', 0, 'guayana', 0, 'french guiana'),
(76, 'Ghana', 'Ghana', 'GH', 0, 'ghana', 0, 'ghana'),
(77, 'Gibraltar', 'Gibraltar', 'GI', 0, 'gibraltar', 0, 'gibraltar'),
(78, 'Grönland', 'Greenland', 'GL', 0, 'groenland', 0, 'greenland'),
(79, 'Gambia', 'Gambia', 'GM', 0, 'gambia', 0, 'gambia'),
(80, 'Guinea', 'Guinea', 'GN', 0, 'guinea', 0, 'guinea'),
(81, 'Guadelope', 'Guadeloupe', 'GP', 0, 'guadelope', 0, 'guadeloupe'),
(82, 'Grecja', 'Greece', 'GR', 1, 'Grecja', 1, 'greece'),
(83, 'Südgeorgien und die Südlichen Sandwichinseln', 'South Georgia And The South Sandwich Islands', 'GS', 0, 'suedgeorgien und die suedlichen sandwichinseln', 0, 'south georgia and the south sandwich islands'),
(84, 'Guatemala', 'Guatemala', 'GT', 0, 'guatemala', 0, 'guatemala'),
(85, 'Guam', 'Guam', 'GU', 0, 'guam', 0, 'guam'),
(86, 'Guinea-Bissau', 'Guinea-Bissau', 'GW', 0, 'guinea-bissau', 0, 'guinea-bissau'),
(87, 'Guyana', 'Guyana', 'GY', 0, 'guyana', 0, 'guyana'),
(88, 'Honkong', 'Hong Kong', 'HK', 0, 'honkong', 0, 'hong kong'),
(89, 'Heard und McDonaldinseln', 'Heard And Mc Donald Islands', 'HM', 0, 'heard und mcdonaldinseln', 0, 'heard and mc donald islands'),
(90, 'Honduras', 'Honduras', 'HN', 0, 'honduras', 0, 'honduras'),
(91, 'Chorwacja', 'Croatia (local name: Hrvatska)', 'HR', 1, 'chorwacja', 1, 'croatia (local name: hrvatska)'),
(92, 'Haiti', 'Haiti', 'HT', 0, 'haiti', 0, 'haiti'),
(93, 'Węgry', 'Hungary', 'HU', 1, 'węgry', 0, 'hungary'),
(94, 'Indonesien', 'Indonesia', 'ID', 0, 'indonesien', 0, 'indonesia'),
(95, 'Irlandia', 'Ireland', 'IE', 1, 'irland', 1, 'ireland'),
(96, 'Israel', 'Israel', 'IL', 0, 'israel', 0, 'israel'),
(97, 'Indie', 'India', 'IN', 0, 'indien', 0, 'india'),
(98, 'Britisches Territorium im Indischen Ozean', 'British Indian Ocean Territory', 'IO', 0, 'britisches territorium im indischen ozean', 0, 'british indian ocean territory'),
(99, 'Irak', 'Iraq', 'IQ', 0, 'irak', 0, 'iraq'),
(100, 'Iran', 'Iran (Islamic Republic Of)', 'IR', 0, 'iran', 0, 'iran (islamic republic of)'),
(101, 'Island', 'Iceland', 'IS', 0, 'island', 0, 'iceland'),
(102, 'Włochy', 'Italy', 'IT', 1, 'Włochy', 1, 'italy'),
(103, 'Jamaika', 'Jamaica', 'JM', 0, 'jamaika', 0, 'jamaica'),
(104, 'Jordanien', 'Jordan', 'JO', 0, 'jordanien', 0, 'jordan'),
(105, 'Japan', 'Japan', 'JP', 0, 'japan', 0, 'japan'),
(106, 'Kenia', 'Kenya', 'KE', 0, 'kenia', 0, 'kenya'),
(107, 'Kirgistan', 'Kyrgyzstan', 'KG', 0, 'kirgistan', 0, 'kyrgyzstan'),
(108, 'Kambodscha', 'Cambodia', 'KH', 0, 'kambodscha', 0, 'cambodia'),
(109, 'Kiribati', 'Kiribati', 'KI', 0, 'kiribati', 0, 'kiribati'),
(110, 'Komoren', 'Comoros', 'KM', 0, 'komoren', 0, 'comoros'),
(111, 'St. Kitts und Nevis', 'Saint Kitts And Nevis', 'KN', 0, 'st. kitts und nevis', 0, 'saint kitts and nevis'),
(112, 'Demokratische Volksrepublik Korea', 'Korea, Democratic People''s Republic Of', 'KP', 0, 'korea, demokratische volksrepublik', 0, 'Zimbabwe'),
(113, 'Republik Korea', 'Korea, Republic Of', 'KR', 0, 'korea, republik', 0, 'korea, republic of'),
(114, 'Kuwait', 'Kuwait', 'KW', 0, 'kuwait', 0, 'kuwait'),
(115, 'Kaimaninseln', 'Cayman Islands', 'KY', 0, 'kaimaninseln', 0, 'cayman islands'),
(116, 'Kasachstan', 'Kazakhstan', 'KZ', 0, 'kasachstan', 0, 'kazakhstan'),
(117, 'Laos', 'Lao People''s Democratic Republic', 'LA', 0, 'laos', 0, 'Zimbabwe'),
(118, 'Libanon', 'Lebanon', 'LB', 0, 'libanon', 0, 'lebanon'),
(119, 'St. Lucia', 'Saint Lucia', 'LC', 0, 'st. lucia', 0, 'saint lucia'),
(120, 'Liechtenstein', 'Liechtenstein', 'LI', 0, 'liechtenstein', 0, 'liechtenstein'),
(121, 'Sri Lanka', 'Sri Lanka', 'LK', 0, 'sri lanka', 0, 'sri lanka'),
(122, 'Liberia', 'Liberia', 'LR', 0, 'liberia', 0, 'liberia'),
(123, 'Lesotho', 'Lesotho', 'LS', 0, 'lesotho', 0, 'lesotho'),
(124, 'Litwa', 'Lithuania', 'LT', 1, 'Litwa', 1, 'lithuania'),
(125, 'Luxemburg', 'Luxembourg', 'LU', 0, 'luxemburg', 0, 'luxembourg'),
(126, 'Łotwa', 'Latvia', 'LV', 0, 'lettland', 0, 'latvia'),
(127, 'Libyen', 'Libyan Arab Jamahiriya', 'LY', 0, 'libyen', 0, 'libyan arab jamahiriya'),
(128, 'Marokko', 'Morocco', 'MA', 0, 'marokko', 0, 'morocco'),
(129, 'Monako', 'Monaco', 'MC', 0, 'monaco', 0, 'monaco'),
(130, 'Mołdawia', 'Moldova, Republic Of', 'MD', 1, 'mołdawia', 0, 'moldova, republic of'),
(131, 'Madagaskar', 'Madagascar', 'MG', 0, 'madagaskar', 0, 'madagascar'),
(132, 'Marshallinseln', 'Marshall Islands', 'MH', 0, 'marshallinseln', 0, 'marshall islands'),
(133, 'Mazedonien', 'Macedonia, The Former Yugoslav Republic Of', 'MK', 0, 'mazedonien', 0, 'macedonia, the former yugoslav republic of'),
(134, 'Mali', 'Mali', 'ML', 0, 'mali', 0, 'mali'),
(135, 'Myanmar', 'Myanmar', 'MM', 0, 'myanmar', 0, 'myanmar'),
(136, 'Mongolei', 'Mongolia', 'MN', 0, 'mongolei', 0, 'mongolia'),
(137, 'Macau', 'Macau', 'MO', 0, 'macau', 0, 'macau'),
(138, 'Marianen', 'Northern Mariana Islands', 'MP', 0, 'marianen', 0, 'northern mariana islands'),
(139, 'Martinique', 'Martinique', 'MQ', 0, 'martinique', 0, 'martinique'),
(140, 'Mauretanien', 'Mauritania', 'MR', 0, 'mauretanien', 0, 'mauritania'),
(141, 'Montserrat', 'Montserrat', 'MS', 0, 'montserrat', 0, 'montserrat'),
(142, 'Malta', 'Malta', 'MT', 0, 'malta', 0, 'malta'),
(143, 'Mauritius', 'Mauritius', 'MU', 0, 'mauritius', 0, 'mauritius'),
(144, 'Malediven', 'Maldives', 'MV', 0, 'malediven', 0, 'maldives'),
(145, 'Malwai', 'Malawi', 'MW', 0, 'malwai', 0, 'malawi'),
(146, 'Mexiko', 'Mexico', 'MX', 0, 'mexiko', 0, 'mexico'),
(147, 'Malaysia', 'Malaysia', 'MY', 0, 'malaysia', 0, 'malaysia'),
(148, 'Mosambik', 'Mozambique', 'MZ', 0, 'mosambik', 0, 'mozambique'),
(149, 'Namibia', 'Namibia', 'NA', 0, 'namibia', 0, 'namibia'),
(150, 'Neukaledonien', 'New Caledonia', 'NC', 0, 'neukaledonien', 0, 'new caledonia'),
(151, 'Niger', 'Niger', 'NE', 0, 'niger', 0, 'niger'),
(152, 'Norfolkinseln', 'Norfolk Island', 'NF', 0, 'norfolkinseln', 0, 'norfolk island'),
(153, 'Nigeria', 'Nigeria', 'NG', 0, 'nigeria', 0, 'nigeria'),
(154, 'Nicaragua', 'Nicaragua', 'NI', 0, 'nicaragua', 0, 'nicaragua'),
(155, 'Holandia', 'Netherlands', 'NL', 1, 'holandia', 1, 'netherlands'),
(156, 'Norwegia', 'Norway', 'NO', 1, 'norwegia', 1, 'norway'),
(157, 'Nepal', 'Nepal', 'NP', 0, 'nepal', 0, 'nepal'),
(158, 'Nauru', 'Nauru', 'NR', 0, 'nauru', 0, 'nauru'),
(159, 'Niue', 'Niue', 'NU', 0, 'niue', 0, 'niue'),
(160, 'Neuseeland', 'New Zealand', 'NZ', 0, 'neuseeland', 0, 'new zealand'),
(161, 'Oman', 'Oman', 'OM', 0, 'oman', 0, 'oman'),
(162, 'Panama', 'Panama', 'PA', 0, 'panama', 0, 'panama'),
(163, 'Peru', 'Peru', 'PE', 0, 'peru', 0, 'peru'),
(164, 'Französisch-Polynesien', 'French Polynesia', 'PF', 0, 'franzoesisch-polynesien', 0, 'french polynesia'),
(165, 'Papua-Neuguinea', 'Papua New Guinea', 'PG', 0, 'papua-neuguinea', 0, 'papua new guinea'),
(166, 'Philippinen', 'Philippines', 'PH', 0, 'philippinen', 0, 'philippines'),
(167, 'Pakistan', 'Pakistan', 'PK', 0, 'pakistan', 0, 'pakistan'),
(168, 'Polska', 'Poland', 'PL', 1, 'polska', 1, 'poland'),
(169, 'St. Pierre und Miquelon', 'St. Pierre And Miquelon', 'PM', 0, 'st. pierre und miquelon', 0, 'st. pierre and miquelon'),
(170, 'die Pitcairninseln', 'Pitcairn', 'PN', 0, 'pitcairninseln, die', 0, 'pitcairn'),
(171, 'Puerto Rico', 'Puerto Rico', 'PR', 0, 'puerto rico', 0, 'puerto rico'),
(172, 'Portugalia', 'Portugal', 'PT', 1, 'portugalia', 1, 'portugal'),
(173, 'Palau', 'Palau', 'PW', 0, 'palau', 0, 'palau'),
(174, 'Portugalia', 'Paraguay', 'PY', 0, 'portugalia', 0, 'paraguay'),
(175, 'Katar', 'Qatar', 'QA', 0, 'katar', 0, 'qatar'),
(176, 'Réunion', 'Reunion', 'RE', 0, 'reunion', 0, 'reunion'),
(177, 'Rumunia', 'Romania', 'RO', 1, 'rumunia', 0, 'romania'),
(178, 'Russische Föderation', 'Russian Federation', 'RU', 0, 'russische foederation', 0, 'russian federation'),
(179, 'Ruanda', 'Rwanda', 'RW', 0, 'ruanda', 0, 'rwanda'),
(180, 'Saudi-Arabien', 'Saudi Arabia', 'SA', 0, 'saudi-arabien', 0, 'saudi arabia'),
(181, 'Salomonen', 'Solomon Islands', 'SB', 0, 'salomonen', 0, 'solomon islands'),
(182, 'Seychellen', 'Seychelles', 'SC', 0, 'seychellen', 0, 'seychelles'),
(183, 'Sudan', 'Sudan', 'SD', 0, 'sudan', 0, 'sudan'),
(184, 'Szwecja', 'Sweden', 'SE', 1, 'Szwecja', 1, 'sweden'),
(185, 'Singapur', 'Singapore', 'SG', 0, 'singapur', 0, 'singapore'),
(186, 'St. Helena', 'St. Helena', 'SH', 0, 'st. helena', 0, 'st. helena'),
(187, 'Słowenia', 'Slovenia', 'SI', 1, 'Słowenia', 1, 'slovenia'),
(188, 'Svalbard und Jan Mayen', 'Svalbard And Jan Mayen Islands', 'SJ', 0, 'svalbard und jan mayen', 0, 'svalbard and jan mayen islands'),
(189, 'Słowacja', 'Slovakia (Slovak Republic)', 'SK', 1, 'słowacja', 1, 'slovakia (slovak republic)'),
(190, 'Sierra Leone', 'Sierra Leone', 'SL', 0, 'sierra leone', 0, 'sierra leone'),
(191, 'San Marino', 'San Marino', 'SM', 0, 'san marino', 0, 'san marino'),
(192, 'Senegal', 'Senegal', 'SN', 0, 'senegal', 0, 'senegal'),
(193, 'Somalia', 'Somalia', 'SO', 0, 'somalia', 0, 'somalia'),
(194, 'Suriname', 'Suriname', 'SR', 0, 'suriname', 0, 'suriname'),
(195, 'Sao Tomé und Principe', 'Sao Tome And Principe', 'ST', 0, 'sao tomé und principe', 0, 'sao tome and principe'),
(196, 'El Salvador', 'El Salvador', 'SX', 0, 'el salvador', 0, 'el salvador'),
(197, 'Syrien', 'Syrian Arab Republic', 'SY', 0, 'syrien', 0, 'syrian arab republic'),
(198, 'Swasiland', 'Swaziland', 'SZ', 0, 'swasiland', 0, 'swaziland'),
(199, 'Turks- und Caicosinseln', 'Turks And Caicos Islands', 'TC', 0, 'turks- und caicosinseln', 0, 'turks and caicos islands'),
(200, 'Tschad', 'Chad', 'TD', 0, 'tschad', 0, 'chad'),
(201, 'Französische Süd- und Antarktisgebiete', 'French Southern Territories', 'TF', 0, 'franzoesische sued- und antarktisgebiete', 0, 'french southern territories'),
(202, 'Togo', 'Togo', 'TG', 0, 'togo', 0, 'togo'),
(203, 'Tajlandia', 'Thailand', 'TH', 0, 'thailand', 0, 'thailand'),
(204, 'Tadschikistan', 'Tajikistan', 'TJ', 0, 'tadschikistan', 0, 'tajikistan'),
(205, 'Tokelau', 'Tokelau', 'TK', 0, 'tokelau', 0, 'tokelau'),
(206, 'Turkmenistan', 'Turkmenistan', 'TM', 0, 'turkmenistan', 0, 'turkmenistan'),
(207, 'Tunezja', 'Tunisia', 'TN', 0, 'tunesien', 0, 'tunisia'),
(208, 'Tonga', 'Tonga', 'TO', 0, 'tonga', 0, 'tonga'),
(209, 'Turcja', 'Turkey', 'TR', 1, 'Turcja', 1, 'turkey'),
(210, 'Trinidad und Tobago', 'Trinidad And Tobago', 'TT', 0, 'trinidad und tobago', 0, 'trinidad and tobago'),
(211, 'Tuvalu', 'Tuvalu', 'TV', 0, 'tuvalu', 0, 'tuvalu'),
(212, 'Taiwan', 'Taiwan, Province Of China', 'TW', 0, 'taiwan', 0, 'taiwan, province of china'),
(213, 'Tansania', 'Tanzania, United Republic Of', 'TZ', 0, 'tansania', 0, 'tanzania, united republic of'),
(214, 'Ukraina', 'Ukraina', 'UA', 1, 'ukraina', 1, 'ukraine'),
(215, 'Uganda', 'Uganda', 'UG', 0, 'uganda', 0, 'uganda'),
(216, 'Amerikanisch-Ozeanien', 'United States Minor Outlying Islands', 'UM', 0, 'amerikanisch-ozeanien', 0, 'united states minor outlying islands'),
(217, 'USA', 'United States', 'US', 0, 'USA', 1, 'united states'),
(218, 'Uruguay', 'Uruguay', 'UY', 0, 'uruguay', 0, 'uruguay'),
(219, 'Usbekistan', 'Uzbekistan', 'UZ', 0, 'usbekistan', 0, 'uzbekistan'),
(220, 'der Heilige Stuhl (Vatikan)', 'Vatican City State (Holy See)', 'VA', 0, 'vatikan, der heilige stuhl', 0, 'vatican city state (holy see)'),
(221, 'St. Vincent und die Grenadinen', 'Saint Vincent And The Grenadines', 'VC', 0, 'st. vincent und die grenadinen', 0, 'saint vincent and the grenadines'),
(222, 'Venezuela', 'Venezuela', 'VE', 0, 'venezuela', 0, 'venezuela'),
(223, 'die Britischen Jungferninseln', 'Virgin Islands (British)', 'VG', 0, 'jungferninseln, die britischen', 0, 'virgin islands (british)'),
(224, 'die Amerikanischen Jungferninseln', 'Virgin Islands (U.S.)', 'VI', 0, 'jungferninseln, die amerikanischen', 0, 'virgin islands (u.s.)'),
(225, 'Vietnam', 'Viet Nam', 'VN', 0, 'vietnam', 0, 'viet nam'),
(226, 'Vanuatu', 'Vanuatu', 'VU', 0, 'vanuatu', 0, 'vanuatu'),
(227, 'Wallis und Futuna', 'Wallis And Futuna Islands', 'WF', 0, 'wallis und futuna', 0, 'wallis and futuna islands'),
(228, 'Samoa', 'Samoa', 'WS', 0, 'samoa', 0, 'samoa'),
(229, 'Jemen', 'Yemen', 'YE', 0, 'jemen', 0, 'yemen'),
(230, 'Mayotte', 'Mayotte', 'YT', 0, 'mayotte', 0, 'mayotte'),
(231, 'Südafrika', 'South Africa', 'ZA', 0, 'suedafrika', 0, 'south africa'),
(232, 'Sambia', 'Zambia', 'ZM', 0, 'sambia', 0, 'zambia'),
(233, 'Simbabwe', 'Zimbabwe', 'ZW', 0, 'simbabwe', 0, 'zimbabwe'),
(234, 'Rosja', 'Soviet Union', 'SU', 1, 'rosja', 1, 'soviet union');

--
-- Zrzut danych tabeli `email_schemas`
--

INSERT INTO `email_schemas` (`id`, `name`, `shortdesc`, `text`, `receiver`) VALUES
(1, 'cache_lost', 'Zaginięcie skrzynki', 'Witam, \r\nwpłynęło do nas zgłoszenie dotyczące skrzynki %cachename%. Proszę o sprawdzenie czy cache jest na swoim miejscu i o ewentualną reaktywację albo zmianę statusu. W razie pytań, proszę o kontakt na rr@opencaching.pl.\r\nPozdrawiam, \r\n%rr_member_name%', 1),
(6, 'virtual', 'Skrzynka wirtualna', 'Witam, \r\nwpłynęło do nas zgłoszenie dotyczące skrzynki %cachename%. Od 24.maja 2009 nie rejestrujemy już w Opencaching.pl nowych skrzynek wirtualnych. Do momentu zmiany typu skrzynki, będzie ona niewidoczna dla innych użytkowników. Po wprowadzeniu niezbędnych korekt, proszę o kontakt na rr@opencaching.pl.\r\nPozdrawiam, \r\n%rr_member_name%', 1),
(8, 'close_changed_status', 'Zamknięcie zgłoszenia po zmianie statusu skrzynki', 'Witam, \r\nw związku ze zgłoszeniem dotyczącym skrzynki %cachename% informujemy, że został zmieniony jej status. W razie jakichkolwiek pytań, proszę o kontakt na rr@opencaching.pl.\r\nPozdrawiam, \r\n%rr_member_name%', 2),
(7, 'notify', 'Potwierdzenie przyjęcia zgłoszenia', 'Witam, \r\nzajmujemy się zgłoszeniem dotyczącym skrzynki %cachename%. O wynikach poinformujemy w osobnej wiadomości.\r\nPozdrawiam, \r\n%rr_member_name%', 2);

--
-- Zrzut danych tabeli `languages`
--

INSERT INTO `languages` (`id`, `short`, `pl`, `en`, `list_default_pl`, `list_default_en`) VALUES
(1, 'DE', 'Niemiecki', 'German', 1, 1),
(2, 'EN', 'Angielski', 'English', 1, 1),
(3, 'FR', 'Francuski', 'French', 1, 1),
(4, 'ES', 'Hiszpański', 'Spanish', 1, 1),
(5, 'JP', 'Japonski', 'Japanese', 0, 0),
(6, 'NL', 'Holenderski', 'Dutch', 1, 1),
(7, 'BG', 'Bułgarski', 'Bulgarisch', 0, 0),
(8, 'BS', 'Bosnisch', 'Bosnisch', 0, 0),
(9, 'CE', 'Tschetschenisch', 'Tschetschenisch', 0, 0),
(10, 'CS', 'Czeski', 'Tschechisch', 0, 0),
(11, 'DA', 'Dunski', 'Dänisch', 1, 1),
(12, 'EL', 'Griechisch', 'Griechisch', 0, 0),
(13, 'EO', 'Esperanto', 'Esperanto', 0, 0),
(15, 'ET', 'Estnisch', 'Estnisch', 0, 0),
(16, 'EU', 'Baskisch', 'Baskisch', 0, 0),
(17, 'FI', 'Fiński', 'Finnish', 0, 0),
(18, 'HR', 'Chorwacji', 'Croatian', 0, 0),
(19, 'HU', 'Ungarisch', 'Ungarisch', 0, 0),
(20, 'IS', 'Isländisch', 'Isländisch', 0, 0),
(21, 'IT', 'Italienisch', 'Italienisch', 0, 0),
(22, 'LT', 'Litauisch', 'Litauisch', 0, 0),
(23, 'LV', 'Latvia', 'Latvia', 0, 0),
(24, 'NO', 'Norweski', 'Norwegian', 0, 0),
(25, 'PL', 'Polski', 'Polish', 1, 1),
(26, 'PT', 'Portugiesisch', 'Portugiesisch', 0, 0),
(27, 'RO', 'Rumuński', 'Romanian', 0, 0),
(28, 'RU', 'Rosyjski', 'Russian', 0, 0),
(29, 'SK', 'Slowacki', 'Slowakisch', 1, 0),
(30, 'SL', 'Slowenisch', 'Slowenisch', 0, 0),
(31, 'SV', 'Szwedzki', 'Schwedisch', 0, 0),
(32, 'TR', 'Turecki', 'Türkisch', 0, 0),
(33, 'UZ', 'Usbekisch', 'Usbekisch', 0, 0),
(34, 'VI', 'Vietnamesisch', 'Vietnamesisch', 0, 0);

--
-- Zrzut danych tabeli `logentries_types`
--

INSERT INTO `logentries_types` (`id`, `module`, `eventname`) VALUES
(1, 'watchlist', 'owner_notify'),
(2, 'watchlist', 'sendmail'),
(3, 'remindemail', 'sendmail'),
(4, 'approving', 'deletecache');

--
-- Zrzut danych tabeli `log_types`
--

INSERT INTO `log_types` (`id`, `cache_status`, `permission`, `pl`, `en`, `icon_small`) VALUES
(1, 0, 'C', 'Znaleziona', 'Found it', 'log/16x16-found.png'),
(2, 0, 'C', 'Nieznaleziona', 'Didn''t find it', 'log/16x16-dnf.png'),
(3, 0, 'A', 'Komentarz', 'Note', 'log/16x16-note.png'),
(7, 0, 'C', 'Uczestniczył', 'Attended', 'log/16x16-go.png'),
(8, 0, 'C', 'Zamierza uczestniczyć', 'Will attended', 'log/16x16-wattend.png'),
(5, 0, 'C', 'Potrzebny serwis', 'Needs maintenance', 'log/16x16-need-maintenance.png'),
(4, 0, 'C', 'Przeniesiona', 'Moved', 'log/16x16-moved.png'),
(10, 0, 'C', 'Gotowa do szukania', 'Ready to search', 'log/16x16-published.png'),
(11, 0, 'C', 'Niedostępna czasowo', 'Temporarily unavailable', 'log/16x16-temporary.png'),
(12, 0, 'C', 'Komentarz COG', 'OC Team comment', 'log/16x16-octeam.png'),
(9, 0, 'C', 'Zarchiwizowana', 'Archived', 'log/16x16-trash.png');



--
-- Zrzut danych tabeli `log_types_text`
--

INSERT INTO `log_types_text` (`id`, `log_types_id`, `lang`, `text_combo`, `text_listing`) VALUES
(1, 1, 'PL', 'Znaleziona', 'Znaleziona'),
(2, 2, 'PL', 'Nieznaleziona', 'Nieznaleziona'),
(3, 3, 'PL', 'Komentarz', 'Komentarz'),
(4, 4, 'PL', 'Przeniesiona', 'Skrzynka przeniesiona'),
(5, 5, 'PL', 'Potrzebny serwis', 'Potrzebny serwis skrzynki'),
(6, 6, 'PL', 'Wymagana archiwizacja', 'skrzynka wymaga archiwizacji'),
(7, 7, 'PL', 'Uczestniczył', 'uczestniczył w wydarzeniu'),
(8, 8, 'PL', 'Będzie uczestniczył', 'będzie uczestniczył w wydarzeniu'),
(13, 7, 'EN', 'attended', 'attended the event'),
(14, 8, 'EN', 'will attend', 'will attend the event'),
(15, 1, 'EN', 'Found it', 'Found it'),
(16, 2, 'EN', 'Didn''t find it', 'Didn''t find it'),
(17, 3, 'EN', 'Comment', 'Comment'),
(9, 9, 'PL', 'Zarchiwizowana', 'Zarchiwizowana'),
(10, 10, 'PL', 'Gotowa do szukania', 'Gotowa do szukania'),
(11, 11, 'PL', 'Niedostępna czasowo', 'Niedostępna czasowo'),
(12, 12, 'PL', 'Komentarz COG', 'Komentarz Centrum Obsługi Geocachera'),
(18, 4, 'EN', 'Moved', 'Cache moved'),
(19, 5, 'EN', 'Needs maintenance', 'Cache needs maintenance'),
(20, 9, 'EN', 'Archived', 'Cache archived'),
(21, 10, 'EN', 'Published', 'Cache reday to search'),
(22, 11, 'EN', 'Temporarily unavailable', 'Temporarily unavailable'),
(23, 12, 'EN', 'OC Team comment', 'OC Team comment');


--
-- Zrzut danych tabeli `nodes`
--

INSERT INTO `nodes` (`id`, `name`, `url`) VALUES
(1, 'Opencaching Deutschland', 'www.opencaching.de'),
(2, 'Opencaching Polska', 'www.opencaching.pl'),
(3, 'Opencaching Czechy', 'www.opencaching.cz'),
(4, 'Local Development', ''),
(5, 'Opencaching Entwicklung Deutschland', 'devel.opencaching.de'),
(6, 'Opencaching UK', 'www.opencaching.org.uk');

--
-- Zrzut danych tabeli `statpics`
--

INSERT INTO `statpics` (`id`, `tplpath`, `previewpath`, `description`, `maxtextwidth`) VALUES
(1, 'images/ocstats1.gif', 'images/ocstats1_prev.jpg', 'Standard', 60),
(2, 'images/ocstats2.gif', 'images/ocstats2_prev.jpg', 'Alternatywne 1', 50),
(3, 'images/ocstats3.gif', 'images/ocstats3_prev.jpg', 'Alternatywne 2', 50),
(4, 'images/ocstats4.gif', 'images/ocstats4_prev.jpg', 'Wąskie standard', 50),
(5, 'images/ocstats5.gif', 'images/ocstats5_prev.jpg', 'Wąskie alter. 2', 50),
(6, 'images/ocstats4.gif', 'images/ocstats4a_prev.jpg', 'Wąskie bez statystyki', 50),
(7, 'images/ocstats5.gif', 'images/ocstats5a_prev.jpg', 'Wąskie alter. 2 bez statystyki', 50);

--
-- Zrzut danych tabeli `sysconfig`
--

INSERT INTO `sysconfig` (`name`, `value`) VALUES
('geokrety_lastupdate', '2010-02-10 06:40:00'),
('importcaches_de_lastupdate', '2010-02-10 06:37:02'),
('importcaches_cz_lastupdate', '2003-10-01 00:00:01'),
('hidden_for_approval', '0');

--
-- Zrzut danych tabeli `sys_menu`
--

INSERT INTO `sys_menu` (`id`, `id_string`, `title`, `menustring`, `access`, `href`, `visible`, `parent`, `position`) VALUES
(1, 'MNU_START', 'Start', 'Start', 0, 'index.php', 1, 0, 1),
(2, 'MNU_START_ABOUTGC', 'About Geocaching', 'About Geocaching', 0, 'articles.php?page=aboutgc', 1, 1, 1),
(3, 'MNU_START_REGISTER', 'Register', 'Register', 0, 'register.php', 1, 1, 2),
(4, 'MNU_START_NEWS', 'News', 'News', 0, 'news.php', 1, 1, 3),
(5, 'MNU_START_NEWCACHES', 'New caches', 'New caches', 0, 'newcaches.php', 1, 1, 4),
(6, 'MNU_START_NEWCACHES_WITHOUTOWN', 'Abrout', 'Abrout', 0, 'newcachesrest.php', 1, 5, 1),
(7, 'MNU_START_NEWLOGS', 'New logs', 'New logs', 0, 'newlogs.php', 1, 1, 5),
(8, 'MNU_START_IMPRINT', 'Imprint', 'Imprint', 0, 'articles.php?page=impressum', 1, 1, 6),
(9, 'MNU_MYPROFILE', 'My profile', 'My profile', 0, 'myhome.php', 1, 0, 2),
(10, 'MNU_CACHES', 'Caches', 'Caches', 0, 'search.php', 1, 0, 3),
(11, 'MNU_INFO', 'Informations', 'Informations', 0, 'http://cms.opencaching.de', 1, 0, 4),
(12, 'MNU_ADMIN', 'Admin', 'Admin', 1, 'admin.php', 1, 0, 5),
(13, 'MNU_ADMIN_MENU', 'Edit menu', 'Edit menu', 1, 'menu.php', 1, 12, 1),
(21, 'MNU_CACHES_HIDE', 'Create a cache', 'Create a cache', 0, 'newcache.php', 1, 10, 2),
(22, 'MNU_CACHES_HIDE_DESCRIPTION', 'Description', 'Description', 0, 'articles.php?page=cacheinfo', 1, 21, 1),
(23, 'MNU_CACHES_HIDE_HTMLTAGS', 'HTML Tags', 'HTML Tags', 0, 'articles.php?page=htmltags', 1, 21, 2),
(20, 'MNU_CACHES_SEARCH', 'Search', 'Search', 0, 'search.php', 1, 10, 1),
(24, 'MNU_ADMIN_MENU_NEWITEM', 'New Item', 'New Item', 0, 'newitem.php', 0, 13, 1);

--
-- Zrzut danych tabeli `watches_waitingtypes`
--

INSERT INTO `watches_waitingtypes` (`id`, `watchtype`) VALUES
(1, 'ownerlog'),
(2, 'cache_watches');

--
-- Zrzut danych tabeli `user`
--
INSERT INTO `user` (`user_id`, `username`, `password`, `email`, `latitude`, `longitude`, `last_modified`, `login_faults`, `last_login`, `last_login_mobile`,`login_id`, `is_active_flag`, `was_loggedin`, `country`, `pmr_flag`, `new_pw_code`, `new_pw_date`, `date_created`, `new_email_code`, `new_email_date`, `new_email`, `post_news`, `hidden_count`, `log_notes_count`, `founds_count`, `notfounds_count`, `uuid`,`uuid_mobile`,`cache_watches`, `permanent_login_flag`, `watchmail_mode`, `watchmail_hour`, `watchmail_nextmail`, `watchmail_day`, `activation_code`, `statpic_logo`, `statpic_text`, `cache_ignores`, `power_trail_email`, `notify_radius`, `admin`, `guru`, `node`, `stat_ban`, `description`, `rules_confirmed`, `get_bulletin`, `ozi_filips`, `hide_flag`) VALUES
(-1, 'SYSTEM', 'cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e', 'system@localhost', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00',NULL, 1, 1, 'pl', NULL, NULL, NULL, '2006-05-25 12:18:04', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 'ZZZZZZZZ-ZZZZ-ZZZZ-ZZZZ-ZZZZZZZZZZZZ', 'ZZZZZZZZ-ZZZZ-ZZZZ-ZZZZ-ZZZZZZZZZZZZ',NULL, NULL, 1, 0, '2006-05-25 05:01:10', 0, '', 0, 'Opencaching', 0, 0, 0, 0, 0, 4, 0, NULL, 0, 1, NULL, 0),
(1, 'octeam', 'b4f6e69d2bf00f007b45ffbbac019934440ac18adc76ee81de22b60e1486f106d5d64855aa2e82062af3e245fac5a55ca1c2f8ddd863fa07b8987e27c0a4e91a', 'octeam@localhost', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00','0000-00-00 00:00:00', NULL, 1, 1, 'pl', NULL, NULL, NULL, '2006-05-25 12:18:04', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 'ZZZZZZZZ-ZZZZ-ZZZZ-ZZZZ-ZZZZZZZZZZZZ','ZZZZZZZZ-ZZZZ-ZZZZ-ZZZZ-ZZZZZZZZZZZZ', NULL, NULL, 1, 0, '2006-05-25 05:01:10', 0, '', 0, 'Opencaching', 0, 0, 0, 1, 0, 4, 0, NULL, 0, 1, NULL, 0),
(2, 'ocuser', '3bea787e35dceba03bcdddb79858a5c73b1a2c450349d697002c7f455ac9ea7b81b3ec5b1fcf4708d682b3bc200ba3b9cefc9757c6f96ec3ecbb41ac196e90d1', 'ocuser@localhost', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00','0000-00-00 00:00:00',NULL, 1, 1, 'pl', NULL, NULL, NULL, '2006-05-25 12:18:04', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 'ZZZZZZZZ-ZZZZ-ZZZZ-ZZZZ-ZZZZZZZZZZZZ','ZZZZZZZZ-ZZZZ-ZZZZ-ZZZZ-ZZZZZZZZZZZZ', NULL, NULL, 1, 0, '2006-05-25 05:01:10', 0, '', 0, 'Opencaching', 0, 0, 0, 0, 0, 4, 0, NULL, 0, 1, NULL, 0);

-- Zrzut danych tabeli `waypoint_type`
--

INSERT INTO `waypoint_type` (`id`, `pl`, `en`, `icon`) VALUES
(-1, 'Proszę wybrać typ waypointa', 'Select one waypoint', ''),
(1, 'Punkt fizyczny', 'Physical point', 'images/waypoints/wp_physical.png'),
(2, 'Punkt wirtualny', 'Virtual point', 'images/waypoints/wp_virtual.png'),
(3, 'Punkt końcowy', 'Final location', 'images/waypoints/wp_final.png'),
(4, 'Interesujące miejsce', 'Interesting place', 'images/waypoints/wp_reference.png'),
(5, 'Parking', 'Parking area', 'images/waypoints/wp_parking.png');



