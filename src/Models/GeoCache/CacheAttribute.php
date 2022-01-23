<?php
namespace src\Models\GeoCache;

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
    public const RAPELLING = 3;
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
    public const TREE_CLIMBING = 64;
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

    // Configuration data for each attribute: translation key + icon name
    private const CONFIG = [
        self::FEE => ['trKey' => 'at_fee', 'icon' => 'at_fee.png'],
        self::RAPELLING => ['trKey' => 'at_rapelling', 'icon' => 'at_rapelling.png'],
        self::BOAT => ['trKey' => 'at_boat', 'icon' => 'at_boat.png'],
        self::DIVING => ['trKey' => 'at_diving', 'icon' => 'at_diving.png'],
        self::CHILDREN => ['trKey' => 'at_children', 'icon' => 'at_children.png'],
        self::HIKING => ['trKey' => 'at_hiking', 'icon' => 'at_hiking.png'],
        self::CLIMBING => ['trKey' => 'at_climbing', 'icon' => 'at_climbing.png'],
        self::WADING => ['trKey' => 'at_wading', 'icon' => 'at_wading.png'],
        self::SWIMMING => ['trKey' => 'at_swimming', 'icon' => 'at_swimming.png'],
        self::AVAILABLE247 => ['trKey' => 'at_available247', 'icon' => 'at_available247.png'],
        self::NIGHT => ['trKey' => 'at_night', 'icon' => 'at_night.png'],
        self::WINTER => ['trKey' => 'at_winter', 'icon' => 'at_winter.png'],
        self::POISON => ['trKey' => 'at_poison', 'icon' => 'at_poison.png'],
        self::ANIMALS => ['trKey' => 'at_animals', 'icon' => 'at_animals.png'],
        self::TICKS => ['trKey' => 'at_ticks', 'icon' => 'at_ticks.png'],
        self::MINE => ['trKey' => 'at_mine', 'icon' => 'at_mine.png'],
        self::CLIFF => ['trKey' => 'at_cliff', 'icon' => 'at_cliff.png'],
        self::HUNTING => ['trKey' => 'at_hunting', 'icon' => 'at_hunting.png'],
        self::DANGER => ['trKey' => 'at_danger', 'icon' => 'at_danger.png'],
        self::WHEELCHAIR => ['trKey' => 'at_wheelchair', 'icon' => 'at_wheelchair.png'],
        self::PARKING => ['trKey' => 'at_parking', 'icon' => 'at_parking.png'],
        self::TRANSPORT => ['trKey' => 'at_transport', 'icon' => 'at_transport.png'],
        self::WATER => ['trKey' => 'at_water', 'icon' => 'at_water.png'],
        self::RESTROOMS => ['trKey' => 'at_restrooms', 'icon' => 'at_restrooms.png'],
        self::PHONE => ['trKey' => 'at_phone', 'icon' => 'at_phone.png'],
        self::BIKE => ['trKey' => 'at_bike', 'icon' => 'at_bike.png'],
        self::THORNS => ['trKey' => 'at_thorns', 'icon' => 'at_thorns.png'],
        self::STEALTH => ['trKey' => 'at_stealth', 'icon' => 'at_stealth.png'],
        self::FLASHLIGHT => ['trKey' => 'at_flashlight', 'icon' => 'at_flashlight.png'],
        self::TRUCK => ['trKey' => 'at_truck', 'icon' => 'at_truck.png'],
        self::RIDDLE => ['trKey' => 'at_riddle', 'icon' => 'at_riddle.png'],
        self::UV => ['trKey' => 'at_uv', 'icon' => 'at_uv.png'],
        self::TOOLS => ['trKey' => 'at_tools', 'icon' => 'at_tools.png'],
        self::NIGHTONLY => ['trKey' => 'at_nightonly', 'icon' => 'at_nightonly.png'],
        self::DRIVEIN => ['trKey' => 'at_drivein', 'icon' => 'at_drivein.png'],
        self::RUIN => ['trKey' => 'at_ruin', 'icon' => 'at_ruin.png'],
        self::BEACON => ['trKey' => 'at_beacon', 'icon' => 'at_beacon.png'],
        self::TREE_CLIMBING => ['trKey' => 'at_tree_climbing', 'icon' => 'at_tree_climbing.png'],
        self::OCONLY => ['trKey' => 'at_oconly', 'icon' => 'at_oconly.png'],
        self::LETTERBOX => ['trKey' => 'at_letterbox', 'icon' => 'at_letterbox.png'],
        self::TRAIN => ['trKey' => 'at_train', 'icon' => 'at_train.png'],
        self::FIRSTAID => ['trKey' => 'at_firstaid', 'icon' => 'at_firstaid.png'],
        self::STEEP => ['trKey' => 'at_steep', 'icon' => 'at_steep.png'],
        self::POI => ['trKey' => 'at_poi', 'icon' => 'at_poi.png'],
        self::MOVING => ['trKey' => 'at_moving', 'icon' => 'at_moving.png'],
        self::WEBCAM => ['trKey' => 'at_webcam', 'icon' => 'at_webcam.png'],
        self::INDOOR => ['trKey' => 'at_indoor', 'icon' => 'at_indoor.png'],
        self::UNDERWATER => ['trKey' => 'at_underwater', 'icon' => 'at_underwater.png'],
        self::NOGPS => ['trKey' => 'at_nogps', 'icon' => 'at_nogps.png'],
        self::OVERNIGHT => ['trKey' => 'at_overnight', 'icon' => 'at_overnight.png'],
        self::TIDE => ['trKey' => 'at_tide', 'icon' => 'at_tide.png'],
        self::PRESERVE => ['trKey' => 'at_preserve', 'icon' => 'at_preserve.png'],
        self::COMPASS => ['trKey' => 'at_compass', 'icon' => 'at_compass.png'],
        self::CAVE => ['trKey' => 'at_cave', 'icon' => 'at_cave.png'],
        self::WIKI => ['trKey' => 'at_wiki', 'icon' => 'at_wiki.png'],
        self::MATH => ['trKey' => 'at_math', 'icon' => 'at_math.png'],
        self::QUICK => ['trKey' => 'at_quick', 'icon' => 'at_quick.png'],
        self::GEOHOTEL => ['trKey' => 'at_geohotel', 'icon' => 'at_geohotel.png'],
        self::PEN => ['trKey' => 'at_pen', 'icon' => 'at_pen.png'],
        self::MAGNETIC => ['trKey' => 'at_magnetic', 'icon' => 'at_magnetic.png'],
        self::MP3 => ['trKey' => 'at_mp3', 'icon' => 'at_mp3.png'],
        self::OFFSET => ['trKey' => 'at_offset', 'icon' => 'at_offset.png'],
        self::USB => ['trKey' => 'at_usb', 'icon' => 'at_usb.png'],
        self::BENCHMARK => ['trKey' => 'at_benchmark', 'icon' => 'at_benchmark.png'],
        self::WHERIGO => ['trKey' => 'at_wherigo', 'icon' => 'at_wherigo.png'],
        self::NATURE => ['trKey' => 'at_nature', 'icon' => 'at_nature.png'],
        self::MONUMENT => ['trKey' => 'at_monument', 'icon' => 'at_monument.png'],
        self::SHOVEL => ['trKey' => 'at_shovel', 'icon' => 'at_shovel.png'],
        self::WALK => ['trKey' => 'at_walk', 'icon' => 'at_walk.png'],
        self::HANDICACHING => ['trKey' => 'at_handicaching', 'icon' => 'at_handicaching.png'],
        self::MUNZEE => ['trKey' => 'at_munzee', 'icon' => 'at_munzee.png'],
        self::ADS => ['trKey' => 'at_ads', 'icon' => 'at_ads.png'],
        self::MILITARY => ['trKey' => 'at_military', 'icon' => 'at_military.png'],
        self::MONITORING => ['trKey' => 'at_monitoring', 'icon' => 'at_monitoring.png'],
        self::TRACKABLES => ['trKey' => 'at_trackables', 'icon' => 'at_trackables.png'],
        self::HISTORIC => ['trKey' => 'at_historic', 'icon' => 'at_historic.png'],
        self::NODOGS => ['trKey' => 'at_nodogs', 'icon' => 'at_nodogs.png'],
        self::NOTAVAILABLE247 => ['trKey' => 'at_notavailable247', 'icon' => 'at_notavailable247.png'],
        self::DAY => ['trKey' => 'at_day', 'icon' => 'at_day.png'],
        self::NOTINWINTER => ['trKey' => 'at_notinwinter', 'icon' => 'at_notinwinter.png'],
        self::ALLSEASONS => ['trKey' => 'at_allseasons', 'icon' => 'at_allseasons.png'],
    ];

    /**
     * Return translation key for given attribute
     *
     * @param integer $attr - * param or ID of attribute
     * @return string
     */
    public static function getTrKey(int $attr): string
    {
        if (!isset(self::CONFIG[$attr])) {
            // CONFIG is not defined for given attribute
            throw new \Exception("Attribute is not defined: ".$attr);
        }

        return self::CONFIG[$attr]['trKey'];
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
        if (!isset(self::CONFIG[$attr])) {
            // CONFIG is not defined for given attribute
            throw new \Exception("Attribute is not defined: ".$attr);
        }

        // usually each node has it's own set of attributes icons
        if (!$subfolder) {
            $subfolder = OcConfig::getOcNode();
        }

        return "/images/cacheAttributes/$subfolder/".self::CONFIG[$attr]['icon'];
    }
}

