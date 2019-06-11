
-- First make sure there will be uniqueness
UPDATE `cache_attrib` SET id=id+1000;
UPDATE `caches_attributes` set attrib_id=attrib_id+1000;

-- ========================================================================
-- OCRO
-- Attribute renumbering
-- ========================================================================

-- ========================================================================
-- Common attributes between GC and OC

-- Periodical/Paid (to be renamed)
UPDATE `cache_attrib` SET id=2 WHERE id=1080;
UPDATE `caches_attributes` SET attrib_id=2 WHERE attrib_id=1080;

-- Boat
UPDATE `cache_attrib` SET id=4 WHERE id=1086;
UPDATE `caches_attributes` SET attrib_id=4 WHERE attrib_id=1086;

-- Children
UPDATE `cache_attrib` SET id=6 WHERE id=1059;
UPDATE `caches_attributes` SET attrib_id=6 WHERE attrib_id=1059;

-- Long hike
UPDATE `cache_attrib` SET id=9 WHERE id=1025;
UPDATE `caches_attributes` SET attrib_id=9 WHERE attrib_id=1025;

-- Available 24/7
UPDATE `cache_attrib` SET id=13 WHERE id=1038;
UPDATE `caches_attributes` SET attrib_id=13 WHERE attrib_id=1038;

-- Not available 24/7
UPDATE `cache_attrib` SET id=9013 WHERE id=1039;
UPDATE `caches_attributes` SET attrib_id=9013 WHERE attrib_id=1039;

-- Recommended at night
UPDATE `cache_attrib` SET id=14 WHERE id=1091;
UPDATE `caches_attributes` SET attrib_id=14 WHERE attrib_id=1091;

-- Ticks
UPDATE `cache_attrib` SET id=19 WHERE id=1014;
UPDATE `caches_attributes` SET attrib_id=19 WHERE attrib_id=1014;

-- Hunting grounds
UPDATE `cache_attrib` SET id=22 WHERE id=1012;
UPDATE `caches_attributes` SET attrib_id=22 WHERE attrib_id=1012;

-- Danger
UPDATE `cache_attrib` SET id=23 WHERE id=1009;
UPDATE `caches_attributes` SET attrib_id=23 WHERE attrib_id=1009;

-- Wheelchair accessible
UPDATE `cache_attrib` SET id=24 WHERE id=1044;
UPDATE `caches_attributes` SET attrib_id=24 WHERE attrib_id=1044;

-- Parking available
UPDATE `cache_attrib` SET id=25 WHERE id=1018;
UPDATE `caches_attributes` SET attrib_id=25 WHERE attrib_id=1018;

-- Drinking water available
UPDATE `cache_attrib` SET id=27 WHERE id=1020;
UPDATE `caches_attributes` SET attrib_id=27 WHERE attrib_id=1020;

-- Bycicles allowed
UPDATE `cache_attrib` SET id=32 WHERE id=1085;
UPDATE `caches_attributes` SET attrib_id=32 WHERE attrib_id=1085;

-- Thorns
UPDATE `cache_attrib` SET id=39 WHERE id=1013;
UPDATE `caches_attributes` SET attrib_id=39 WHERE attrib_id=1013;

-- Stealth required
UPDATE `cache_attrib` SET id=40 WHERE id=1045;
UPDATE `caches_attributes` SET attrib_id=40 WHERE attrib_id=1045;

-- Flashlight needed
UPDATE `cache_attrib` SET id=44 WHERE id=1082;
UPDATE `caches_attributes` SET attrib_id=44 WHERE attrib_id=1082;

-- Field puzzle
UPDATE `cache_attrib` SET id=47 WHERE id=1155;
UPDATE `caches_attributes` SET attrib_id=47 WHERE attrib_id=1155;

-- Special tools
UPDATE `cache_attrib` SET id=51 WHERE id=1046;
UPDATE `caches_attributes` SET attrib_id=51 WHERE attrib_id=1046;

-- Wireless beacon
UPDATE `cache_attrib` SET id=60 WHERE id=1052;
UPDATE `caches_attributes` SET attrib_id=60 WHERE attrib_id=1052;

-- ========================================================================
-- Common attributes between OCPL and OCDE

-- OC Only cache
UPDATE `cache_attrib` SET id=106 WHERE id=1006;
UPDATE `caches_attributes` SET attrib_id=106 WHERE attrib_id=1006;

-- Letterbox
UPDATE `cache_attrib` SET id=108 WHERE id=1008;
UPDATE `caches_attributes` SET attrib_id=108 WHERE attrib_id=1008;

-- Compass
UPDATE `cache_attrib` SET id=147 WHERE id=1047;
UPDATE `caches_attributes` SET attrib_id=147 WHERE attrib_id=1047;

-- ========================================================================
-- OCPL only attributes

-- One minute cache
UPDATE `cache_attrib` SET id=201 WHERE id=1040;
UPDATE `caches_attributes` SET attrib_id=201 WHERE attrib_id=1040;

-- GeoHotel
UPDATE `cache_attrib` SET id=202 WHERE id=1043;
UPDATE `caches_attributes` SET attrib_id=202 WHERE attrib_id=1043;

-- Bring your own pen
UPDATE `cache_attrib` SET id=203 WHERE id=1048;
UPDATE `caches_attributes` SET attrib_id=203 WHERE attrib_id=1048;

-- Magnet
UPDATE `cache_attrib` SET id=204 WHERE id=1049;
UPDATE `caches_attributes` SET attrib_id=204 WHERE attrib_id=1049;

-- MP3 file
UPDATE `cache_attrib` SET id=205 WHERE id=1050;
UPDATE `caches_attributes` SET attrib_id=205 WHERE attrib_id=1050;

-- Offset cache
UPDATE `cache_attrib` SET id=206 WHERE id=1051;
UPDATE `caches_attributes` SET attrib_id=206 WHERE attrib_id=1051;

-- USB dead drop
UPDATE `cache_attrib` SET id=207 WHERE id=1053;
UPDATE `caches_attributes` SET attrib_id=207 WHERE attrib_id=1053;

-- Benchmark
UPDATE `cache_attrib` SET id=208 WHERE id=1054;
UPDATE `caches_attributes` SET attrib_id=208 WHERE attrib_id=1054;

-- Wherigo cartridge
UPDATE `cache_attrib` SET id=209 WHERE id=1055;
UPDATE `caches_attributes` SET attrib_id=209 WHERE attrib_id=1055;

-- Nature
UPDATE `cache_attrib` SET id=210 WHERE id=1060;
UPDATE `caches_attributes` SET attrib_id=210 WHERE attrib_id=1060;

-- Monument
UPDATE `cache_attrib` SET id=211 WHERE id=1061;
UPDATE `caches_attributes` SET attrib_id=211 WHERE attrib_id=1061;

-- Shovel
UPDATE `cache_attrib` SET id=212 WHERE id=1081;
UPDATE `caches_attributes` SET attrib_id=212 WHERE attrib_id=1081;

-- Walk
UPDATE `cache_attrib` SET id=213 WHERE id=1084;
UPDATE `caches_attributes` SET attrib_id=213 WHERE attrib_id=1084;

-- ========================================================================
-- Special attributes

-- Log password
UPDATE `caches_attributes` SET attrib_id=999 WHERE attrib_id=1099;

