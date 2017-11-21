<?php

namespace lib\Objects\CacheSet;


use lib\Objects\Coordinates\Coordinates;
use Utils\Database\OcDb;
use lib\Objects\OcConfig\OcConfig;
use Utils\Database\QueryBuilder;
use lib\Objects\GeoCache\GeoCache;

class CacheSet extends CacheSetCommon
{

    private $id;
    private $uuid;
    private $name;
    private $image;
    private $type;
    private $centerCoordinates;
    private $status;
    private $dateCreated;
    private $cacheCount;
    private $description;
    private $perccentRequired;
    private $conquestedCount;

    public function __construct()
    {
        parent::__construct();
    }

    private function loadDataFromDb($fields = null)
    {
        if (is_null($fields)) {
            $fields = "*"; // default select all fields
        }

        $s = $this->db->multiVariableQuery(
            "SELECT $fields FROM PowerTrail WHERE id=:1 LIMIT 1", $this->id);

        if($row = $db->dbResultFetch($s)){
            $this->loadFromDbRow($row);
            return true;
        }
        return false;
    }

    private function loadFromDbRow(array $dbRow)
    {
        foreach ($dbRow as $key => $val) {
            switch ($key) {
                case 'id':
                    $this->id = (int) $val;
                    break;
                case 'name':
                    $this->name = $val;
                    break;
                case 'image':
                    if($val === ''){
                        /* no image was loaded by user, set default image */
                        $val = 'tpl/stdstyle/images/blue/powerTrailGenericLogo.png';
                    }
                    $this->image = $val;
                    break;
                case 'type':
                    $this->type = (int) $val;
                    break;
                case 'status':
                    $this->status = (int) $val;
                    break;
                case 'dateCreated':
                    $this->dateCreated = new \DateTime($val);
                    break;
                case 'cacheCount':
                    $this->cacheCount = (int) $val;
                    break;
                case 'description':
                    $this->description = $val;
                    break;
                case 'perccentRequired':
                    $this->perccentRequired = $val;
                    break;
                case 'conquestedCount':
                    $this->conquestedCount = $val;
                    break;
                case 'points':
                    $this->points = $val;
                    break;
                case 'uuid':
                    $this->uuid = $val;
                    break;

                case 'centerLatitude':
                case 'centerLongitude':
                    // cords are handled below...
                    break;
                default:
                    error_log(__METHOD__ . ": Unknown column: $key");
            }
        }

        // and the coordinates..
        if (isset($dbRow['centerLatitude'], $dbRow['centerLongitude'])) {
            $this->centerCoordinates = Coordinates::FromCoordsFactory(
                $dbRow['centerLatitude'], $dbRow['centerLongitude']);
        }
    }

    private static function FromDbRowFactory(array $dbRow)
    {
        $gp = new self();
        $gp->loadFromDbRow($dbRow);
        return $gp;
    }

    /**
     * Returns list of all cache-sets
     * @return array
     */
    public static function GetAllCacheSets($statusIn=array(), $offset=null, $limit=null)
    {

        if(empty($statusIn)){
            $statusIn = array(CacheSetCommon::STATUS_OPEN);
        }


        $query = QueryBuilder::instance()
            ->select("*")
            ->from("PowerTrail")
            ->where()
                ->in("status", $statusIn)
            ->limit($limit, $offset)
            ->build();

        $stmt = self::db()->simpleQuery($query);

        return self::db()->dbFetchAllAsObjects($stmt, function ($row){
            return self::FromDbRowFactory($row);
        });
    }

    public static function GetAllCacheSetsCount(array $statusIn = array())
    {

        $query = QueryBuilder::instance()
            ->select("COUNT(*)")
            ->from("PowerTrail")
            ->where()
                ->in("status", $statusIn)
            ->build();

        return self::db()->simpleQueryValue($query,0);
    }

    public static function getCacheSetUrlById($id){
        return "/powerTrail.php?ptAction=showSerie&ptrail=$id";
    }


    public function getId()
    {
        return $this->id;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getIcon()
    {
        return self::GetTypeIcon($this->type);
    }

    public function getTypeTranslation()
    {
        return tr(self::GetTypeTranslationKey($this->type));
    }

    public function getStatusTranslation()
    {
        return tr(self::GetStatusTranslationKey($this->status));
    }

    public function getCreationDate()
    {
        return $this->dateCreated;
    }

    public function getCreationDateString()
    {
        global $dateFormat;
        return $this->dateCreated->format($dateFormat);
    }

    public function getCacheCount()
    {
        return $this->cacheCount;
    }

    public function getGainedCount()
    {
        return $this->conquestedCount;
    }

    /**
     *
     * @return NULL|\lib\Objects\Coordinates\Coordinates
     */
    public function getCoordinates()
    {
        return $this->centerCoordinates;
    }


    /**
     * Return all cacheSets which has less active caches than required ratio for completion
     * @return array
     */
    public static function getCacheSetsToArchive()
    {
        $db = self::db();
        $rs = $db->simpleQuery(
            "SELECT * FROM (
                SELECT pt.id, pt.type, pt.name,
                    100*count(*)/pt.cacheCount AS currentRatio,
                    pt.perccentRequired AS ratioRequired
                FROM PowerTrail AS pt
                JOIN powerTrail_caches AS ptc ON ptc.PowerTrailId = pt.id
                JOIN caches AS c ON ptc.cacheId = c.cache_id
                WHERE pt.status = ".CacheSet::STATUS_OPEN."
                    AND c.status = ".GeoCache::STATUS_READY."
                GROUP BY pt.id
            ) AS allPts WHERE currentRatio <= ratioRequired");

        return $db->dbResultFetchAll($rs, OcDb::FETCH_ASSOC);
    }

}

