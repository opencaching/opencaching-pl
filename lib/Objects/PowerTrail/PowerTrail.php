<?php

namespace lib\Objects\PowerTrail;

use \lib\Objects\Coordinates\Coordinates;
use lib\Objects\GeoCache\Collection;
use lib\Objects\GeoCache\GeoCache;
use Utils\Database\OcDb;

class PowerTrail extends \lib\Objects\BaseObject
{

    const TYPE_GEODRAW = 1;
    const TYPE_TOURING = 2;
    const TYPE_NATURE = 3;
    const TYPE_TEMATIC = 4;
    const STATUS_OPEN = 1;
    const STATUS_UNAVAILABLE = 2;
    const STATUS_CLOSED = 3;
    const STATUS_INSERVICE = 4;

    private $id;
    private $name;
    private $image;
    private $type;
    private $centerCoordinates;
    private $status;

    /* @var $dateCreated \DateTime */
    private $dateCreated;
    private $cacheCount;
    private $activeGeocacheCount = 0;
    private $archivedGeocacheCount = 0;
    private $unavailableGeocacheCount = 0;
    private $description;
    private $perccentRequired;
    private $conquestedCount;
    private $points;

    /**
     *  @var \lib\Objects\GeoCache\Collection
     */
    private $geocaches;
    private $owners = false;
    private $powerTrailConfiguration;

    public function __construct(array $params)
    {
        if (isset($params['id'])) {
            $this->id = (int) $params['id'];

            if (isset($params['fieldsStr'])) {
                $this->loadDataFromDb($params['fieldsStr']);
            } else {
                $this->loadDataFromDb();
            }
        } elseif (isset($params['dbRow'])) {
            $this->setFieldsByUsedDbRow($params['dbRow']);
        } else {
            $this->centerCoordinates = new Coordinates();
        }
        $this->geocaches = new Collection();
    }

    private function loadDataFromDb($fields = null)
    {
        $db = OcDb::instance();

        if (is_null($fields)) {
            // default select all fields
            $fields = "*";
        }

        $ptq = "SELECT $fields FROM `PowerTrail` WHERE `id` = :1 LIMIT 1";
        $s = $db->multiVariableQuery($ptq, $this->id);

        if ($db->rowCount($s) != 1) {
           //no such powertrail in DB?
           $this->dataLoaded = false; //mark object as NOT containing data
           return;
        }

        $this->setFieldsByUsedDbRow($db->dbResultFetch($s));
    }

    private function setFieldsByUsedDbRow(array $dbRow)
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
                    if($val === ''){ /* no image was loaded by user, set default image */
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
            $this->centerCoordinates = new Coordinates();
            $this->centerCoordinates->setLatitude($dbRow['centerLatitude'])->setLongitude($dbRow['centerLongitude']);
        }

        $this->dataLoaded = true; //mark object as containing data

    }

    public static function CheckForPowerTrailByCache($cacheId)
    {
        $queryPt = 'SELECT `id`, `name`, `image`, `type` FROM `PowerTrail` WHERE `id` IN ( SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` =:1 ) AND `status` = 1 ';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($queryPt, $cacheId);

        return $db->dbResultFetchAll($s);
    }

    public static function GetPowerTrailIconsByType($typeId = null)
    {
        $imgPath = '/tpl/stdstyle/images/blue/';
        $icon = '';
        if($typeId === null){
            $typeId = $this->type;
        }
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
        if (!$this->geocaches->isReady()) {

            $db = OcDb::instance();
            $query = 'SELECT powerTrail_caches.isFinal, caches . * , user.username FROM  `caches` , user, powerTrail_caches WHERE cache_id IN ( SELECT  `cacheId` FROM  `powerTrail_caches` WHERE  `PowerTrailId` =:1) AND user.user_id = caches.user_id AND powerTrail_caches.cacheId = caches.cache_id ORDER BY caches.name';
            $s = $db->multiVariableQuery($query, $this->id);
            $geoCachesDbResult = $db->dbResultFetchAll($s);

            $geocachesIdArray = array();
            foreach ($geoCachesDbResult as $geoCacheDbRow) {
                $geocache = new GeoCache();
                $geocache->loadFromRow($geoCacheDbRow)->setIsPowerTrailPart(true);
                $geocache->setPowerTrail($this);
                if ($geoCacheDbRow['isFinal'] == 1) {
                    $geocache->setIsPowerTrailFinalGeocache(true);
                }
                $this->geocaches[] = $geocache;
                $geocachesIdArray[] = $geocache->getCacheId();
            }
            $this->geocaches->setIsReady(true);
            $this->geocaches->setGeocachesIdArray($geocachesIdArray);
            $this->caculateGeocachesCountByStatus();
        }
        return $this->geocaches;
    }

    private function loadPtOwners()
    {
        $query = 'SELECT `userId`, `privileages`, username FROM `PowerTrail_owners`, user WHERE `PowerTrailId` = :1 AND PowerTrail_owners.userId = user.user_id';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($query, $this->id);
        $ownerDb = $db->dbResultFetchAll($s);
        foreach ($ownerDb as $user) {
            $owner = new Owner($user);
            $owner->setPrivileages($user['privileages']);
            $this->owners[] = $owner;
        }
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

    /**
     * @param mixed $powerTrailConfiguration
     * @return PowerTrail
     */
    public function setPowerTrailConfiguration($powerTrailConfiguration)
    {
        $this->powerTrailConfiguration = $powerTrailConfiguration;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwners()
    {
        if (!$this->owners) {
            $this->loadPtOwners();
        }
        return $this->owners;
    }

    /**
     * check if specified user is owner of the powerTrail
     * @param integer $userId
     * @return bool
     */
    public function isUserOwner($userId)
    {
        $owners = $this->getOwners();
        foreach ($owners as $owner) {
            if ($userId == $owner->getUserId()) {
                return true;
            }
        }
        return false;
    }

    public function getFoundCachsByUser($userId)
    {
        $cachesFoundByUser = array();
        $sqlInStString = $this->buildSqlStringOfAllPtGeocachesId();
        if($sqlInStString !== ''){
            $query = 'SELECT `cache_id` AS `geocacheId` FROM `cache_logs` WHERE `cache_id` in (' . $sqlInStString . ') AND `deleted` = 0 AND `user_id` = :1 AND `type` = "1" ';
            $db = OcDb::instance();
            $s = $db->multiVariableQuery($query, (int) $userId);
            $cachesFoundByUser = $db->dbResultFetchAll($s);
        }

        return $cachesFoundByUser;
    }

    /**
     * check if real cache count in pt is equal stored in db.
     */
    public function checkCacheCount()
    {

        $db = OcDb::instance();
        $s = $db->multiVariableQuery(
            'SELECT count(*) as `cacheCount` FROM `caches`
            WHERE `cache_id` IN (
                SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1
            )',
            $this->id);

        $answer = $db->dbResultFetch($s);
        if ($answer['cacheCount'] != $this->cacheCount) {
            $updateQuery = 'UPDATE `PowerTrail` SET `cacheCount` =:1  WHERE `id` = :2 ';
            $db->multiVariableQuery($updateQuery, $answer['cacheCount'], $this->id);
        }
    }

    /**
     * disable (set status to 4) geoPaths witch has not enough cacheCount.
     */
    public function disablePowerTrailBecauseCacheCountTooLow()
    {
//        $text = tr('pt227').tr('pt228');
//        print 'pt #'.$this->id.', caches in pt: '.$this->cacheCount.'; min. caches limit: '. $this->getPtMinCacheCountLimit().'<br>';
        if ($this->cacheCount < $this->getPtMinCacheCountLimit()) {
//            $text .= tr('pt227').tr('pt228');

            print '[test only] geoPath #<a href="powerTrail.php?ptAction=showSerie&ptrail=' . $this->id . '">' . $this->id .' '. $this->name . ' </a> (geoPtah cache count=' . $this->cacheCount . ' is lower than minimum=' . $this->getPtMinCacheCountLimit() . ') <br/>';
//            $db = OcDb::instance();
//            $queryStatus = 'UPDATE `PowerTrail` SET `status`= :1 WHERE `id` = :2';
//            $db->multiVariableQuery($queryStatus, 4, $pt['id']);
//            $query = 'INSERT INTO `PowerTrail_comments`(`userId`, `PowerTrailId`, `commentType`, `commentText`, `logDateTime`, `dbInsertDateTime`, `deleted`) VALUES
//            (-1, :1, 4, :2, NOW(), NOW(),0)';
//            $db->multiVariableQuery($query, $pt['id'], $text);
//            sendEmail::emailOwners($pt['id'], 4, date('Y-m-d H:i:s'), $text, 'newComment');
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * get minimum cache limit from period of time when ptWas published
     */
    private function getPtMinCacheCountLimit()
    {
        foreach ($this->powerTrailConfiguration['old'] as $date) { //find interval path was created
            if ($this->dateCreated->getTimestamp() >= $date['dateFrom'] && $this->dateCreated->getTimestamp() < $date['dateTo']) { // patch was created here
                return $date['limit'];
            }
        }
        return false;
    }

    /**
     * disable geoPaths, when its WIS > active caches count.
     */
    public function disableUncompletablePt($serverUrl)
    {

        $this->getGeocaches();
        $requiredGeocacheCount = $this->caclulateRequiredGeocacheCount();

        if ($this->perccentRequired < \lib\Controllers\PowerTrailController::MINIMUM_PERCENT_REQUIRED) { // disable power trail witch too low percent required
            print '<span style="color: orange"> geoPath #<a href="' . $serverUrl . 'powerTrail.php?ptAction=showSerie&ptrail=' . $this->id . '">' . $this->id .' '. $this->name . '</a> will be put in service because too low perccentRequired. (Current Percent:' . $this->perccentRequired . ' Required: ' . \lib\Controllers\PowerTrailController::MINIMUM_PERCENT_REQUIRED . ') [<a href="' . $serverUrl . '/powerTrailCOG.php?ptSelector=' . $this->id . '">cog link</a>]</span><br/>';
        }

        if ($this->activeGeocacheCount < $requiredGeocacheCount) {
            if ($this->archivedGeocacheCount > $requiredGeocacheCount) { // close powerTrail permanent
                print '<span style="color: red"> geoPath #<a href="' . $serverUrl . 'powerTrail.php?ptAction=showSerie&ptrail=' . $this->id . '">' . $this->id .' '. $this->name . '</a> will be closed permanently. Total cache count: ' . $this->cacheCount . ' / Active geocaches: ' . $this->activeGeocacheCount . ' / Required: ' . $requiredGeocacheCount . '. / Archived geocaches: ' . $this->archivedGeocacheCount . ' [<a href="' . $serverUrl . '/powerTrailCOG.php?ptSelector=' . $this->id . '">cog link</a>]</span><br/>';
//              $text = tr('pt227').tr('pt234');
//              ddd($text);
            }
            if ($this->unavailableGeocacheCount >= ($this->cacheCount - $requiredGeocacheCount)) { // disable powerTrail for service only
                print '<span style="color: black"> geoPath #<a href="' . $serverUrl . 'powerTrail.php?ptAction=showSerie&ptrail=' . $this->id . '">' . $this->id .' '. $this->name . '</a> will be put in service (uncompletable) Total cache count: ' . $this->cacheCount . ' / Active geocaches: ' . $this->activeGeocacheCount . ' / Required: ' . (($this->cacheCount * $this->perccentRequired) / 100) . '  / Archived geocaches: ' . $this->archivedGeocacheCount . ' [<a href="' . $serverUrl . '/powerTrailCOG.php?ptSelector=' . $this->id . '">cog link</a>]</span><br/>';
//              $db->multiVariableQuery('UPDATE `PowerTrail` SET `status`= :1 WHERE `id` = :2', self::STATUS_INSERVICE, $this->id);
                //$query = 'INSERT INTO `PowerTrail_comments`(`userId`, `PowerTrailId`, `commentType`, `commentText`, `logDateTime`, `dbInsertDateTime`, `deleted`) VALUES (-1, :1, 4, :2, NOW(), NOW(),0)';
                $text = tr('pt227') . tr('pt234');
//          d($text);
                // $db->multiVariableQuery($query, $pt['id'], $text);
                //emailOwners($pt['id'], 4, date('Y-m-d H:i:s'), $text, 'newComment');
            }
            return true;
        }
        return false;
    }

    private function caclulateRequiredGeocacheCount()
    {
        return ($this->cacheCount * $this->perccentRequired) / 100;
    }

    public function getPowerTrailCachesLogsForCurrentUser()
    {
        $db = OcDb::instance();
        $qr = 'SELECT `cache_id`, `date`, `text_html`, `text`  FROM `cache_logs` WHERE `cache_id` IN ( SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` = :1) AND `user_id` = :2 AND `deleted` = 0 AND `type` = 1';
        isset($_SESSION['user_id']) ? $userId = $_SESSION['user_id'] : $userId = 0;
        $s = $db->multiVariableQuery($qr, $this->id, $userId);
        $powerTrailCacheLogsArr = $db->dbResultFetchAll($s);

        $powerTrailCachesUserLogsByCache = array();
        foreach ($powerTrailCacheLogsArr as $log) {
            $powerTrailCachesUserLogsByCache[$log['cache_id']] = array(
                'date' => $log['date'],
                'text_html' => $log['text_html'],
                'text' => $log['text'],
            );
        }
        return $powerTrailCachesUserLogsByCache;
    }

    private function buildSqlStringOfAllPtGeocachesId()
    {
        $this->getGeocaches();
        $geocachesIdArray = $this->geocaches->getGeocachesIdArray();
        $geocachesStr = '';
        foreach ($geocachesIdArray as $geocacheId) {
            $geocachesStr .= $geocacheId . ',';
        }
        return rtrim($geocachesStr, ',');
    }

    private function caculateGeocachesCountByStatus()
    {
        $this->activeGeocacheCount = 0;
        $this->archivedGeocacheCount = 0;
        $this->unavailableGeocacheCount = 0;
        foreach ($this->geocaches as $geocache) {
            switch ($geocache->getStatus()) {
                case GeoCache::STATUS_READY:
                    $this->activeGeocacheCount++;
                    break;
                case GeoCache::STATUS_ARCHIVED:
                    $this->archivedGeocacheCount++;
                    break;
                default:
                    $this->unavailableGeocacheCount++;
                    break;
            }
        }
    }
    function getActiveGeocacheCount()
    {
        return $this->activeGeocacheCount;
    }

    function getArchivedGeocacheCount()
    {
        return $this->archivedGeocacheCount;
    }

    function getUnavailableGeocacheCount()
    {
        return $this->unavailableGeocacheCount;
    }

    /**
     *
     */
    public function increaseConquestedCount()
    {
        $this->conquestedCount++;
        $db = OcDb::instance();
        $query = 'UPDATE `PowerTrail` SET `PowerTrail`.`conquestedCount`= (SELECT COUNT(*) FROM `PowerTrail_comments` WHERE `PowerTrail_comments`.`PowerTrailId` = :1 AND `PowerTrail_comments`.`commentType` = 2 AND `PowerTrail_comments`.`deleted` = 0 ) WHERE `PowerTrail`.`id` = :1 ';
        $db->multiVariableQuery($query, $this->id);
    }

    public function isAlreadyConquestedByUser(\lib\Objects\User\User $user)
    {
        $db = OcDb::instance();
        $mySqlRequest = 'SELECT count(*) AS `ptConquestCount` FROM `PowerTrail_comments` WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1 AND `PowerTrailId` = :2';
        $s = $db->multiVariableQuery($mySqlRequest, $user->getUserId(), $this->getId());
        $mySqlResult = $db->dbResultFetch($s);
        if ($mySqlResult['ptConquestCount'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function setAndStoreStatus($status)
    {
        $this->status = $status;
        $db = OcDb::instance();
        $query = 'UPDATE `PowerTrail` SET `status` = :1 WHERE `PowerTrail`.`id` = :2 ';
        $db->multiVariableQuery($query, $status, $this->id);
    }

    /**
     *
     * Check if this power trail meet criteria to be opened
     *
     * Criteria:
     * - percent required > minimum percent required
     * - active geocahes Count >= required geocache count
     * - minimum geocaches count >= required geocaches count set in settings.inc.php
     *
     * @return boolean
     */
    public function canBeOpened()
    {
        if($this->perccentRequired < \lib\Controllers\PowerTrailController::MINIMUM_PERCENT_REQUIRED){
            return false;
        }
        if($this->activeGeocacheCount < $this->caclulateRequiredGeocacheCount()){
            return false;
        }
        return true;
    }
}
