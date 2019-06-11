
-- First make sure there will be uniqueness
UPDATE `cache_attrib` SET id=id+1000;
UPDATE `caches_attributes` set attrib_id=attrib_id+1000;

-- ========================================================================
-- OCUS
-- Attribute renumbering
-- ========================================================================

-- ========================================================================
-- Common attributes between GC and OC

-- Periodical/Paid (to be renamed)
UPDATE `cache_attrib` SET id=2 WHERE id=1043;
UPDATE `caches_attributes` SET attrib_id=2 WHERE attrib_id=1043;

-- Children
UPDATE `cache_attrib` SET id=6 WHERE id=1041;
UPDATE `caches_attributes` SET attrib_id=6 WHERE attrib_id=1041;

-- Not available 24/7
UPDATE `cache_attrib` SET id=9013 WHERE id=1080;
UPDATE `caches_attributes` SET attrib_id=9013 WHERE attrib_id=1080;

-- Poison plants
UPDATE `cache_attrib` SET id=17 WHERE id=1050;
UPDATE `caches_attributes` SET attrib_id=17 WHERE attrib_id=1050;

-- Dangerous animals / snakes
UPDATE `cache_attrib` SET id=18 WHERE id=1046;
UPDATE `caches_attributes` SET attrib_id=18 WHERE attrib_id=1046;

-- Ticks
UPDATE `cache_attrib` SET id=19 WHERE id=1045;
UPDATE `caches_attributes` SET attrib_id=19 WHERE attrib_id=1045;

-- Danger
UPDATE `cache_attrib` SET id=23 WHERE id=1090;
UPDATE `caches_attributes` SET attrib_id=23 WHERE attrib_id=1090;

-- Wheelchair accessible
UPDATE `cache_attrib` SET id=24 WHERE id=1044;
UPDATE `caches_attributes` SET attrib_id=24 WHERE attrib_id=1044;

-- Thorns
UPDATE `cache_attrib` SET id=39 WHERE id=1047;
UPDATE `caches_attributes` SET attrib_id=39 WHERE attrib_id=1047;

-- Stealth required
UPDATE `cache_attrib` SET id=40 WHERE id=1094;
UPDATE `caches_attributes` SET attrib_id=40 WHERE attrib_id=1094;

-- Flashlight needed
UPDATE `cache_attrib` SET id=44 WHERE id=1082;
UPDATE `caches_attributes` SET attrib_id=44 WHERE attrib_id=1082;

-- Truck/RV
UPDATE `cache_attrib` SET id=46 WHERE id=1042;
UPDATE `caches_attributes` SET attrib_id=46 WHERE attrib_id=1042;

-- Special tools
UPDATE `cache_attrib` SET id=51 WHERE id=1083;
UPDATE `caches_attributes` SET attrib_id=51 WHERE attrib_id=1083;

-- Night cache
UPDATE `cache_attrib` SET id=52 WHERE id=1091;
UPDATE `caches_attributes` SET attrib_id=52 WHERE attrib_id=1091;

-- Wireless beacon
UPDATE `cache_attrib` SET id=60 WHERE id=1052;
UPDATE `caches_attributes` SET attrib_id=60 WHERE attrib_id=1052;

-- ========================================================================
-- Common attributes between OCPL and OCDE

-- OC Only cache
UPDATE `cache_attrib` SET id=106 WHERE id=1006;
UPDATE `caches_attributes` SET attrib_id=106 WHERE attrib_id=1006;
UPDATE `cache_attrib` SET id=106 WHERE id=1092;
UPDATE `caches_attributes` SET attrib_id=106 WHERE attrib_id=1092;

-- Letterbox
UPDATE `cache_attrib` SET id=108 WHERE id=1081;
UPDATE `caches_attributes` SET attrib_id=108 WHERE attrib_id=1081;

-- Compass
UPDATE `cache_attrib` SET id=147 WHERE id=1096;
UPDATE `caches_attributes` SET attrib_id=147 WHERE attrib_id=1096;

-- ========================================================================
-- OCPL only attributes

-- One minute cache
UPDATE `cache_attrib` SET id=201 WHERE id=1040;
UPDATE `caches_attributes` SET attrib_id=201 WHERE attrib_id=1040;

-- GeoHotel
UPDATE `cache_attrib` SET id=202 WHERE id=1053;
UPDATE `caches_attributes` SET attrib_id=202 WHERE attrib_id=1053;

-- Bring your own pen
UPDATE `cache_attrib` SET id=203 WHERE id=1048;
UPDATE `caches_attributes` SET attrib_id=203 WHERE attrib_id=1048;

-- Magnet
UPDATE `cache_attrib` SET id=204 WHERE id=1049;
UPDATE `caches_attributes` SET attrib_id=204 WHERE attrib_id=1049;

-- Offset cache
UPDATE `cache_attrib` SET id=206 WHERE id=1051;
UPDATE `caches_attributes` SET attrib_id=206 WHERE attrib_id=1051;

-- US Benchmark
UPDATE `cache_attrib` SET id=208 WHERE id=1055;
UPDATE `caches_attributes` SET attrib_id=208 WHERE attrib_id=1055;

-- Nature
UPDATE `cache_attrib` SET id=210 WHERE id=1060;
UPDATE `caches_attributes` SET attrib_id=210 WHERE attrib_id=1060;

-- Monument
UPDATE `cache_attrib` SET id=211 WHERE id=1061;
UPDATE `caches_attributes` SET attrib_id=211 WHERE attrib_id=1061;

-- Munzee
UPDATE `cache_attrib` SET id=215 WHERE id=1056;
UPDATE `caches_attributes` SET attrib_id=215 WHERE attrib_id=1056;

-- Contains ads
UPDATE `cache_attrib` SET id=216 WHERE id=1095;
UPDATE `caches_attributes` SET attrib_id=216 WHERE attrib_id=1095;

-- ========================================================================
-- Special attributes

-- Log password
UPDATE `caches_attributes` SET attrib_id=999 WHERE attrib_id=1099;

