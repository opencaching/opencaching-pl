
-- First make sure there will be uniqueness
UPDATE `cache_attrib` SET id=id+1000;
UPDATE `caches_attributes` set attrib_id=attrib_id+1000;

-- ========================================================================
-- OCUK
-- Attribute renumbering
-- ========================================================================

-- ========================================================================
-- Common attributes between GC and OC

-- Periodical/Paid (to be renamed)
UPDATE `cache_attrib` SET id=2 WHERE id=1036;
UPDATE `caches_attributes` SET attrib_id=2 WHERE attrib_id=1036;

-- Climbing gear required
UPDATE `cache_attrib` SET id=3 WHERE id=1049;
UPDATE `caches_attributes` SET attrib_id=3 WHERE attrib_id=1049;

-- Boat required
UPDATE `cache_attrib` SET id=4 WHERE id=1086;
UPDATE `caches_attributes` SET attrib_id=4 WHERE attrib_id=1086;

-- Diving equipment required
UPDATE `cache_attrib` SET id=5 WHERE id=1051;
UPDATE `caches_attributes` SET attrib_id=5 WHERE attrib_id=1051;

-- Children
UPDATE `cache_attrib` SET id=6 WHERE id=1059;
UPDATE `caches_attributes` SET attrib_id=6 WHERE attrib_id=1059;

-- Long walk / hike
UPDATE `cache_attrib` SET id=9 WHERE id=1025;
UPDATE `caches_attributes` SET attrib_id=9 WHERE attrib_id=1025;

-- Some climbing (no gear required)
UPDATE `cache_attrib` SET id=10 WHERE id=1028;
UPDATE `caches_attributes` SET attrib_id=10 WHERE attrib_id=1028;

-- Swamp, marsh or wadingChildren
UPDATE `cache_attrib` SET id=11 WHERE id=1026;
UPDATE `caches_attributes` SET attrib_id=11 WHERE attrib_id=1026;

-- Swimming required
UPDATE `cache_attrib` SET id=12 WHERE id=1029;
UPDATE `caches_attributes` SET attrib_id=12 WHERE attrib_id=1029;

-- Availably 24/7
UPDATE `cache_attrib` SET id=13 WHERE id=1038;
UPDATE `caches_attributes` SET attrib_id=13 WHERE attrib_id=1038;

-- Not available 24/7
UPDATE `cache_attrib` SET id=9013 WHERE id=1039;
UPDATE `caches_attributes` SET attrib_id=9013 WHERE attrib_id=1039;

-- Not recommended at night
UPDATE `cache_attrib` SET id=9014 WHERE id=1040;
UPDATE `caches_attributes` SET attrib_id=9014 WHERE attrib_id=1040;

-- Available in winter
UPDATE `cache_attrib` SET id=15 WHERE id=1044;
UPDATE `caches_attributes` SET attrib_id=15 WHERE attrib_id=1044;

-- Poison plants
UPDATE `cache_attrib` SET id=17 WHERE id=1016;
UPDATE `caches_attributes` SET attrib_id=17 WHERE attrib_id=1016;

-- Dangerous animals / snakes
UPDATE `cache_attrib` SET id=18 WHERE id=1017;
UPDATE `caches_attributes` SET attrib_id=18 WHERE attrib_id=1017;

-- Ticks
UPDATE `cache_attrib` SET id=19 WHERE id=1014;
UPDATE `caches_attributes` SET attrib_id=19 WHERE attrib_id=1014;

-- Abandoned mine
UPDATE `cache_attrib` SET id=20 WHERE id=1015;
UPDATE `caches_attributes` SET attrib_id=20 WHERE attrib_id=1015;

-- Cliffs / rocks
UPDATE `cache_attrib` SET id=21 WHERE id=1011;
UPDATE `caches_attributes` SET attrib_id=21 WHERE attrib_id=1011;

-- Hunting grounds
UPDATE `cache_attrib` SET id=22 WHERE id=1012;
UPDATE `caches_attributes` SET attrib_id=22 WHERE attrib_id=1012;

-- Danger
UPDATE `cache_attrib` SET id=23 WHERE id=1009;
UPDATE `caches_attributes` SET attrib_id=23 WHERE attrib_id=1009;

-- Parking nearby
UPDATE `cache_attrib` SET id=25 WHERE id=1018;
UPDATE `caches_attributes` SET attrib_id=25 WHERE attrib_id=1018;

-- Public transportation
UPDATE `cache_attrib` SET id=26 WHERE id=1019;
UPDATE `caches_attributes` SET attrib_id=26 WHERE attrib_id=1019;

-- Drinking water nearby
UPDATE `cache_attrib` SET id=27 WHERE id=1020;
UPDATE `caches_attributes` SET attrib_id=27 WHERE attrib_id=1020;

-- Public restrooms nearby
UPDATE `cache_attrib` SET id=28 WHERE id=1021;
UPDATE `caches_attributes` SET attrib_id=28 WHERE attrib_id=1021;

-- Public phone nearby
UPDATE `cache_attrib` SET id=29 WHERE id=1022;
UPDATE `caches_attributes` SET attrib_id=29 WHERE attrib_id=1022;

-- Thorns
UPDATE `cache_attrib` SET id=39 WHERE id=1013;
UPDATE `caches_attributes` SET attrib_id=39 WHERE attrib_id=1013;

-- Flashlight needed
UPDATE `cache_attrib` SET id=44 WHERE id=1082;
UPDATE `caches_attributes` SET attrib_id=44 WHERE attrib_id=1082;

-- Field puzzle
UPDATE `cache_attrib` SET id=47 WHERE id=1155;
UPDATE `caches_attributes` SET attrib_id=47 WHERE attrib_id=1155;

-- Special tools
UPDATE `cache_attrib` SET id=51 WHERE id=1046;
UPDATE `caches_attributes` SET attrib_id=51 WHERE attrib_id=1046;

-- Night cache
UPDATE `cache_attrib` SET id=52 WHERE id=1001;
UPDATE `caches_attributes` SET attrib_id=52 WHERE attrib_id=1001;

-- Park'n'grab
UPDATE `cache_attrib` SET id=53 WHERE id=1024;
UPDATE `caches_attributes` SET attrib_id=53 WHERE attrib_id=1024;

-- Available all seasons
UPDATE `cache_attrib` SET id=9062 WHERE id=1042;
UPDATE `caches_attributes` SET attrib_id=9062 WHERE attrib_id=1042;

-- ========================================================================
-- Common attributes between OCPL and OCDE

-- OC Only cache
UPDATE `cache_attrib` SET id=106 WHERE id=1006;
UPDATE `caches_attributes` SET attrib_id=106 WHERE attrib_id=1006;

-- Letterbox
UPDATE `cache_attrib` SET id=108 WHERE id=1081;
UPDATE `caches_attributes` SET attrib_id=108 WHERE attrib_id=1081;

-- Active railway nearby
UPDATE `cache_attrib` SET id=110 WHERE id=1010;
UPDATE `caches_attributes` SET attrib_id=110 WHERE attrib_id=1010;

-- First aid available
UPDATE `cache_attrib` SET id=123 WHERE id=1023;
UPDATE `caches_attributes` SET attrib_id=123 WHERE attrib_id=1023;

-- Hilly area
UPDATE `cache_attrib` SET id=127 WHERE id=1027;
UPDATE `caches_attributes` SET attrib_id=127 WHERE attrib_id=1027;

-- Point of interest
UPDATE `cache_attrib` SET id=130 WHERE id=1030;
UPDATE `caches_attributes` SET attrib_id=130 WHERE attrib_id=1030;

-- Has moving target
UPDATE `cache_attrib` SET id=131 WHERE id=1031;
UPDATE `caches_attributes` SET attrib_id=131 WHERE attrib_id=1031;

-- Webcam
UPDATE `cache_attrib` SET id=132 WHERE id=1032;
UPDATE `caches_attributes` SET attrib_id=132 WHERE attrib_id=1032;

-- Indoors
UPDATE `cache_attrib` SET id=133 WHERE id=1033;
UPDATE `caches_attributes` SET attrib_id=133 WHERE attrib_id=1033;

-- Hidden underwater
UPDATE `cache_attrib` SET id=134 WHERE id=1034;
UPDATE `caches_attributes` SET attrib_id=134 WHERE attrib_id=1034;

-- No GPS required
UPDATE `cache_attrib` SET id=135 WHERE id=1035;
UPDATE `caches_attributes` SET attrib_id=135 WHERE attrib_id=1035;

-- Overnight stay necessary
UPDATE `cache_attrib` SET id=137 WHERE id=1037;
UPDATE `caches_attributes` SET attrib_id=137 WHERE attrib_id=1037;

-- Not available during high tide
UPDATE `cache_attrib` SET id=142 WHERE id=1041;
UPDATE `caches_attributes` SET attrib_id=142 WHERE attrib_id=1041;

-- Nature preserve / breeding ground
UPDATE `cache_attrib` SET id=143 WHERE id=1043;
UPDATE `caches_attributes` SET attrib_id=143 WHERE attrib_id=1043;

-- Compass required
UPDATE `cache_attrib` SET id=147 WHERE id=1047;
UPDATE `caches_attributes` SET attrib_id=147 WHERE attrib_id=1047;

-- Cave equipment required
UPDATE `cache_attrib` SET id=150 WHERE id=1050;
UPDATE `caches_attributes` SET attrib_id=150 WHERE attrib_id=1050;

-- Aircraft required
UPDATE `cache_attrib` SET id=153 WHERE id=1156;
UPDATE `caches_attributes` SET attrib_id=153 WHERE attrib_id=1156;

-- Wiki
UPDATE `cache_attrib` SET id=154 WHERE id=1054;
UPDATE `caches_attributes` SET attrib_id=154 WHERE attrib_id=1054;

-- Mathematical problem
UPDATE `cache_attrib` SET id=156 WHERE id=1056;
UPDATE `caches_attributes` SET attrib_id=156 WHERE attrib_id=1056;

-- Other cache type
UPDATE `cache_attrib` SET id=157 WHERE id=1057;
UPDATE `caches_attributes` SET attrib_id=157 WHERE attrib_id=1057;

-- Ask owner for start conditions
UPDATE `cache_attrib` SET id=158 WHERE id=1058;
UPDATE `caches_attributes` SET attrib_id=158 WHERE attrib_id=1058;

-- ========================================================================
-- OCPL only attributes

-- Rated at handicaching.com
UPDATE `cache_attrib` SET id=214 WHERE id=1157;
UPDATE `caches_attributes` SET attrib_id=214 WHERE attrib_id=1157;

-- Munzee
UPDATE `cache_attrib` SET id=215 WHERE id=1158;
UPDATE `caches_attributes` SET attrib_id=215 WHERE attrib_id=1158;

-- ========================================================================
-- Special attributes

-- Log password
UPDATE `caches_attributes` SET attrib_id=999 WHERE attrib_id=1099;

