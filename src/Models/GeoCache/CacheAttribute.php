<?php
namespace src\Models\GeoCache;

use src\Utils\Debug\Debug;
use src\Models\OcConfig\OcConfig;

/**
 * Simple class to store attributes of the geocaches.
 * This class contais superset of attributes used by all OCPL code nodes.
 * Each node support only subset of attributes presents below.
 *
 * More details about OC attributes: https://wiki.opencaching.eu/index.php?title=Cache_attributes
 *
 * This class doesn't have connection to DB.
 */
class CacheAttribute
{

    // attribute name = attribute ID
    /** Access or parking fee */
    public const FEE = 2;
    /** Climbing gear requried */
    public const RAPPELING = 3;
    /** Boat required */
    public const BOAT = 4;
    /** Diving equipment required */
    public const DIVING = 5;
    /** Suitable for children */
    public const CHILDREN = 6;
    /** Long walk or hike */
    public const HIKING = 9;
    /** Some climbing (no gear needed) */
    public const CLIMBING = 10;
    /** Swamp or marsh. May require wading */
    public const WADING = 11;
    /** Swimming required */
    public const SWIMMING = 12;
    /** Available 24/7 */
    public const AVAILABLE247 = 13;
    /** Recommended at night */
    public const NIGHT = 14;
    /** Available during winter */
    public const WINTER = 15;
    /** Poisonous plants */
    public const POISON = 17;
    /** Dangerous animals */
    public const ANIMALS = 18;
    /** Ticks */
    public const TICKS = 19;
    /** Abandoned mines */
    public const MINE = 20;
    /** Cliffs / falling rocks hazard */
    public const CLIFF = 21;
    /** Hunting grounds */
    public const HUNTING = 22;
    /** Dangerous area */
    public const DANGER = 23;
    /** Wheelchair accessible */
    public const WHEELCHAIR = 24;
    /** Parking area nearby */
    public const PARKING = 25;
    /** Public transportation */
    public const TRANSPORT = 26;
    /** Drinking water nearby */
    public const WATER = 27;
    /** Public restrooms nearby */
    public const RESTROOMS = 28;
    /** Public phone nerby */
    public const PHONE = 29;
    /** Bycicles allowed */
    public const BIKE = 32;
    /** Thorns */
    public const THORNS = 39;
    /** Stealth required */
    public const STEALTH = 40;
    /** Flashlight required */
    public const FLASHLIGHT = 44;
    /** Truck / RV allowed */
    public const TRUCK = 46;
    /** Puzzle / Mystery */
    public const RIDDLE = 47;
    /** UV light required */
    public const UV = 48;
    /** Special tool / equipment required */
    public const TOOLS = 51;
    /** Night cache - can only be found at night */
    public const NIGHTONLY = 52;
    /** Park and grab */
    public const DRIVEIN = 53;
    /** Abandoned structure / ruin */
    public const RUIN = 54;
    /** Wireless beacon / Garmin Chirp™ */
    public const BEACON = 60;
    /** Tree climbing required */
    public const TREE = 64;
    /** Only loggable at Opencaching */
    public const OCONLY = 106;
    /** Letterbox */
    public const LETTERBOX = 108;
    /** Active railway nearby */
    public const TRAIN = 110;
    /** First aid available */
    public const FIRSTAID = 123;
    /** Hilly area */
    public const STEEP = 127;
    /** Point of interest */
    public const POI = 130;
    /** Moving target */
    public const MOVING = 131;
    /** Webcam */
    public const WEBCAM = 132;
    /** Wihin enclosed rooms (caves, buildings etc.) */
    public const INDOOR = 133;
    /** Under water */
    public const UNDERWATER = 134;
    /** No GPS required */
    public const NOGPS = 135;
    /** Overnight stay necessary */
    public const OVERNIGHT = 137;
    /** Not available during high tide */
    public const TIDE = 142;
    /** Nature preserve / Breeding season */
    public const PRESERVE = 143;
    /** Compass required */
    public const COMPASS = 147;
    /** Cave equipment required */
    public const CAVE = 150;
    /** Internet research required */
    public const WIKI = 154;
    /** Mathematical or logical problem */
    public const MATH = 156;
    /** Quick and easy cache */
    public const QUICK = 201;
    /** GeoHotel for trackables */
    public const GEOHOTEL = 202;
    /** Bring your own pen */
    public const PEN = 203;
    /** Attached using magnet(s) */
    public const MAGNETIC = 204;
    /** Information in MP3 file */
    public const MP3 = 205;
    /** Container placed at an offset from given coordinates */
    public const OFFSET = 206;
    /** Dead Drop USB container */
    public const USB = 207;
    /** Benchmark - geodetic point */
    public const BENCHMARK = 208;
    /** Wherigo cartridge to play */
    public const WHERIGO = 209;
    /** Hidden in natural surroundings */
    public const NATURE = 210;
    /** Monument or historic site */
    public const MONUMENT = 211;
    /** Shovel required */
    public const SHOVEL = 212;
    /** Access only by walk */
    public const WALK = 213;
    /** Rated on Handicaching.com */
    public const HANDICACHING = 214;
    /** Contains a Munzee */
    public const MUNZEE = 215;
    /** Contains advertising */
    public const ADS = 216;
    /** Military training area, some access restrictions - check before visit */
    public const MILITARY = 217;
    /** Caution, area under video surveillance */
    public const MONITORING = 218;
    /** Suitable to hold trackables */
    public const TRACKABLES = 219;
    /** Officially designated historical monument */
    public const HISTORIC = 220;
    /** Dogs not allowed */
    public const NODOGS = 9001;
    /** Only available at specified time */
    public const NOTAVAILABLE247 = 9013;
    /** NOT recommended at night */
    public const DAY = 9014;
    /** NOT available during winter */
    public const NOTINWINTER = 9015;
    /** Available at all seasons */
    public const ALLSEASONS = 9062;

    /**
     * Return translation key for given attribute
     *
     * @param integer $attr - * param or ID of attribute
     * @return string
     */
    public static function getTrKey(int $attr): string
    {
        switch ($attr) {
            case self::FEE:  return 'at_fee';
            case self::RAPPELING:  return 'at_rappeling';
            case self::BOAT:  return 'at_boat';
            case self::DIVING:  return 'at_diving';
            case self::CHILDREN:  return 'at_children';
            case self::HIKING:  return 'at_hiking';
            case self::CLIMBING:  return 'at_climbing';
            case self::WADING:  return 'at_wading';
            case self::SWIMMING:  return 'at_swimming';
            case self::AVAILABLE247:  return 'at_available247';
            case self::NIGHT:  return 'at_night';
            case self::WINTER:  return 'at_winter';
            case self::POISON:  return 'at_poison';
            case self::ANIMALS:  return 'at_animals';
            case self::TICKS:  return 'at_ticks';
            case self::MINE:  return 'at_mine';
            case self::CLIFF:  return 'at_cliff';
            case self::HUNTING:  return 'at_hunting';
            case self::DANGER:  return 'at_danger';
            case self::WHEELCHAIR:  return 'at_wheelchair';
            case self::PARKING:  return 'at_parking';
            case self::TRANSPORT:  return 'at_transport';
            case self::WATER:  return 'at_water';
            case self::RESTROOMS:  return 'at_restrooms';
            case self::PHONE: return 'at_phone';
            case self::BIKE:  return 'at_bike';
            case self::THORNS:  return 'at_thorn';
            case self::STEALTH:  return 'at_stealth';
            case self::FLASHLIGHT:  return 'at_flashlight';
            case self::TRUCK:  return 'at_truck';
            case self::RIDDLE:  return 'at_riddle';
            case self::UV:  return 'at_uv';
            case self::TOOLS:  return 'at_tools';
            case self::NIGHTONLY:  return 'at_nightonly';
            case self::DRIVEIN:  return 'at_drivein';
            case self::RUIN:  return 'at_ruin';
            case self::BEACON:  return 'at_beacon';
            case self::TREE:  return 'at_tree';
            case self::OCONLY:  return 'at_oconly';
            case self::LETTERBOX:  return 'at_letterbox';
            case self::TRAIN:  return 'at_train';
            case self::FIRSTAID:  return 'at_firstaid';
            case self::STEEP:  return 'at_steep';
            case self::POI:  return 'at_poi';
            case self::MOVING:  return 'at_moving';
            case self::WEBCAM:  return 'at_webcam';
            case self::INDOOR:  return 'at_indoor';
            case self::UNDERWATER:  return 'at_underwater';
            case self::NOGPS:  return 'at_nogps';
            case self::OVERNIGHT: return 'at_overnight';
            case self::TIDE:  return 'at_tide';
            case self::PRESERVE:  return 'at_preserve';
            case self::COMPASS:  return 'at_compass';
            case self::CAVE:  return 'at_cave';
            case self::WIKI:  return 'at_wiki';
            case self::MATH:  return 'at_math';
            case self::QUICK:  return 'at_quick';
            case self::GEOHOTEL:  return 'at_geohotel';
            case self::PEN:  return 'at_pen';
            case self::MAGNETIC:  return 'at_magnetic';
            case self::MP3:  return 'at_mp3';
            case self::OFFSET:  return 'at_offset';
            case self::USB:  return 'at_usb';
            case self::BENCHMARK:  return 'at_benchmark';
            case self::WHERIGO:  return 'at_wherigo';
            case self::NATURE:  return 'at_nature';
            case self::MONUMENT:  return 'at_monument';
            case self::SHOVEL:  return 'at_shovel';
            case self::WALK:  return 'at_walk';
            case self::HANDICACHING:  return 'at_handicaching';
            case self::MUNZEE:  return 'at_munzee';
            case self::ADS:  return 'at_ads';
            case self::MILITARY:  return 'at_military';
            case self::MONITORING:  return 'at_monitoring';
            case self::TRACKABLES:  return 'at_trackables';
            case self::HISTORIC:  return 'at_historic';
            case self::NODOGS:  return 'at_nodogs';
            case self::NOTAVAILABLE247:  return 'at_notAvailable247';
            case self::DAY:  return 'at_day';
            case self::NOTINWINTER:  return 'at_notinwinter';
            case self::ALLSEASONS:  return 'at_allseasons';
            default:
                Debug::errorLog("Uknown geocache attribute: $attr");
                return "at_oconly";
        }
    }

    /**
     * Return icon name for given attribute
     *
     * @param integer $attr - * param or ID of attribute
     * @param string subfolder - optional subfolder name in /images/cacheAttributes/
     * @return string
     */
    public static function getIcon(int $attr, string $subfolder=null): string
    {
        // usually each node has it's own set of attributes icons
        if (!$subfolder) {
            $subfolder = OcConfig::getOcNode();
        }
        return "/images/cacheAttributes/$subfolder/".self::getIconFileName($attr).'.png';
    }

    /**
     * Return icon name for given attribute
     *
     * @param integer $attr - * param or ID of attribute
     * @return string
     */
    private function getIconFileName(int $attr): string
    {
        switch ($attr) {
            case self::FEE:  return 'at_fee';
            case self::RAPPELING:  return 'at_rappeling';
            case self::BOAT:  return 'at_boat';
            case self::DIVING:  return 'at_diving';
            case self::CHILDREN:  return 'at_children';
            case self::HIKING:  return 'at_hiking';
            case self::CLIMBING:  return 'at_climbing';
            case self::WADING:  return 'at_wading';
            case self::SWIMMING:  return 'at_swimming';
            case self::AVAILABLE247:  return 'at_available247';
            case self::NIGHT:  return 'at_night';
            case self::WINTER:  return 'at_winter';
            case self::POISON:  return 'at_poison';
            case self::ANIMALS:  return 'at_animals';
            case self::TICKS:  return 'at_ticks';
            case self::MINE:  return 'at_mine';
            case self::CLIFF:  return 'at_cliff';
            case self::HUNTING:  return 'at_hunting';
            case self::DANGER:  return 'at_danger';
            case self::WHEELCHAIR:  return 'at_wheelchair';
            case self::PARKING:  return 'at_parking';
            case self::TRANSPORT:  return 'at_transport';
            case self::WATER:  return 'at_water';
            case self::RESTROOMS:  return 'at_restrooms';
            case self::PHONE: return 'at_phone';
            case self::BIKE:  return 'at_bike';
            case self::THORNS:  return 'at_thorns';
            case self::STEALTH:  return 'at_stealth';
            case self::FLASHLIGHT:  return 'at_flashlight';
            case self::TRUCK:  return 'at_truck';
            case self::RIDDLE:  return 'at_riddle';
            case self::UV:  return 'at_uv';
            case self::TOOLS:  return 'at_tools';
            case self::NIGHTONLY:  return 'at_nightonly';
            case self::DRIVEIN:  return 'at_drivein';
            case self::RUIN:  return 'at_ruin';
            case self::BEACON:  return 'at_beacon';
            case self::TREE:  return 'at_tree';
            case self::OCONLY:  return 'at_oconly';
            case self::LETTERBOX:  return 'at_letterbox';
            case self::TRAIN:  return 'at_train';
            case self::FIRSTAID:  return 'at_firstaid';
            case self::STEEP:  return 'at_steep';
            case self::POI:  return 'at_poi';
            case self::MOVING:  return 'at_moving';
            case self::WEBCAM:  return 'at_webcam';
            case self::INDOOR:  return 'at_indoor';
            case self::UNDERWATER:  return 'at_underwater';
            case self::NOGPS:  return 'at_nogps';
            case self::OVERNIGHT: return 'at_overnight';
            case self::TIDE:  return 'at_tide';
            case self::PRESERVE:  return 'at_preserve';
            case self::COMPASS:  return 'at_compass';
            case self::CAVE:  return 'at_cave';
            case self::WIKI:  return 'at_wiki';
            case self::MATH:  return 'at_math';
            case self::QUICK:  return 'at_quick';
            case self::GEOHOTEL:  return 'at_geohotel';
            case self::PEN:  return 'at_pen';
            case self::MAGNETIC:  return 'at_magnetic';
            case self::MP3:  return 'at_mp3';
            case self::OFFSET:  return 'at_offset';
            case self::USB:  return 'at_usb';
            case self::BENCHMARK:  return 'at_benchmark';
            case self::WHERIGO:  return 'at_wherigo';
            case self::NATURE:  return 'at_nature';
            case self::MONUMENT:  return 'at_monument';
            case self::SHOVEL:  return 'at_shovel';
            case self::WALK:  return 'at_walk';
            case self::HANDICACHING:  return 'at_handicaching';
            case self::MUNZEE:  return 'at_munzee';
            case self::ADS:  return 'at_ads';
            case self::MILITARY:  return 'at_military';
            case self::MONITORING:  return 'at_monitoring';
            case self::TRACKABLES:  return 'at_trackables';
            case self::HISTORIC:  return 'at_historic';
            case self::NODOGS:  return 'at_nodogs';
            case self::NOTAVAILABLE247:  return 'at_notavailable247';
            case self::DAY:  return 'at_day';
            case self::NOTINWINTER:  return 'at_notinwinter';
            case self::ALLSEASONS:  return 'at_allseasons';
            default:
                Debug::errorLog("Unknown geocache attribute: $attr");
                return "at_oconly";
        }
    }

}

