<?php
namespace lib\Objects\PowerTrail;

use \lib\Database\DataBaseSingleton;
use \lib\Objects\Coordinates\Coordinates;
use lib\Objects\GeoCache\ Collection;
use lib\Objects\GeoCache\GeoCache;

class PowerTrail
{

    const TYPE_GEODRAW = 1;
    const TYPE_TOURING = 2;
    const TYPE_NATURE = 3;
    const TYPE_TEMATIC = 4;

    private $id;
    private $name;
    private $image;
    private $type;
    private $centerCoordinates;
    private $status;
    /* @var $dateCreated \DateTime */
    private $dateCreated;
    private $cacheCount;
    private $description;
    private $perccentRequired;
    private $conquestedCount;
    private $points;
    private $geocaches;
    private $owners;
    
    
    public function __construct(array $params)
    {
        if (isset($params['id'])) {
            $this->id = (int) $params['id'];
            $this->loadDataFromDb();
        } elseif (isset($params['dbRow'])) {
            $this->setFieldsByUsedDbRow($params['dbRow']);
        } else {
            $this->centerCoordinates = new Coordinates();
        }
        $this->geocaches = new Collection();
    }

    private function loadDataFromDb()
    {
        $db = \lib\Database\DataBaseSingleton::Instance();
        $ptq = 'SELECT * FROM `PowerTrail` WHERE `id` = :1 LIMIT 1';
        $db->multiVariableQuery($ptq, $this->id);
        $this->setFieldsByUsedDbRow($db->dbResultFetch());
    }

    private function setFieldsByUsedDbRow(array $dbRow)
    {
        $this->centerCoordinates = new Coordinates();
        $this->centerCoordinates
                    ->setLatitude($dbRow['centerLatitude'])
                    ->setLongitude($dbRow['centerLongitude']);
        $this->id = (int) $dbRow['id'];
        $this->name = $dbRow['name'];
        $this->image = $dbRow['image'];
        $this->type = (int) $dbRow['type'];
        $this->status = (int) $dbRow['status'];
        $this->dateCreated = new \DateTime($dbRow['dateCreated']);
        $this->cacheCount = (int) $dbRow['cacheCount'];
        $this->description = $dbRow['description'];
        $this->perccentRequired = $dbRow['perccentRequired'];
        $this->conquestedCount = (int) $dbRow['conquestedCount'];
        $this->points = $dbRow['points'];



    }

    public static function CheckForPowerTrailByCache($cacheId)
    {
        $queryPt = 'SELECT `id`, `name`, `image`, `type` FROM `PowerTrail` WHERE `id` IN ( SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` =:1 ) AND `status` = 1 ';
        $db = DataBaseSingleton::Instance();
        $db->multiVariableQuery($queryPt, $cacheId);

        return $db->dbResultFetchAll();
    }

    public static function GetPowerTrailIconsByType($typeId)
    {
        $imgPath = '/tpl/stdstyle/images/blue/';
        $icon = '';
        switch ($typeId) {
            case self::TYPE_GEODRAW:
                $icon = 'footprintRed.png';
                break;
            case self::TYPE_TOURING:
                $icon = 'footprintBlue.png';
                break;
            case self::TYPE_NATURE:
                $icon = 'footprintGreen.png';
                break;
            case self::TYPE_TEMATIC:
                $icon = 'footprintYellow.png';
                break;
        }
        return $imgPath . $icon;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getFootIcon()
    {
        return self::GetPowerTrailIconsByType($this->type);
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getPowerTrailUrl()
    {
        $url = '/powerTrail.php?ptAction=showSerie&ptrail=';
        return $url . $this->id;
    }

    /**
     * @return  Collection
     */
    public function getGeocaches()
    {
        if(!$this->geocaches->isReady()){
            $db = DataBaseSingleton::Instance();
            $query = 'SELECT powerTrail_caches.isFinal, caches . * , user.username FROM  `caches` , user, powerTrail_caches WHERE cache_id IN ( SELECT  `cacheId` FROM  `powerTrail_caches` WHERE  `PowerTrailId` =:1) AND user.user_id = caches.user_id AND powerTrail_caches.cacheId = caches.cache_id ORDER BY caches.name';
            $db->multiVariableQuery($query, $this->id);
            $geoCachesDbResult = $db->dbResultFetchAll();
            foreach ($geoCachesDbResult as $geoCacheDbRow) {
                $geocache = new GeoCache();
                $geocache->loadFromRow($geoCacheDbRow)->setIsPowerTrailPart(true);
                $geocache->setPowerTrail($this);
                if($geoCacheDbRow['isFinal'] == 1){
                    $geocache->setIsPowerTrailFinalGeocache(true);
                }
                $this->geocaches[] = $geocache;
            }
        }
        return $this->geocaches;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getConquestedCount()
    {
        return $this->conquestedCount;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getCacheCount()
    {
        return $this->cacheCount;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return mixed
     */
    public function getPerccentRequired()
    {
        return $this->perccentRequired;
    }

    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @return Coordinates
     */
    public function getCenterCoordinates()
    {
        return $this->centerCoordinates;
    }




}