<?php
use Utils\Database\OcDb;
/**
 *
 */
class powerTrailBase{

    /**
     * declare types and id of types of geoPaths:
     */
    const GEODRAW = 1;
    const TOURING = 2;
    const NATURE = 3;
    const TEMATIC = 4;

    const powerTrailLogoFileName = 'powerTrailLogoId';
    const commentsPaginateCount = 5;
    const cCountForMaxMagnifier = 50;
    const iconPath = 'tpl/stdstyle/images/blue/';

    public static function minimumCacheCount(){
        include __DIR__.'/../lib/settings.inc.php';
        return $powerTrailMinimumCacheCount['current'];
    }

    public static function historicMinimumCacheCount(){
        include __DIR__.'/../lib/settings.inc.php';
        $min = $powerTrailMinimumCacheCount['current'];
        foreach ($powerTrailMinimumCacheCount['old'] as $date) {
            //var_dump($date['dateFrom'], $ptPublished, $date['dateTo']);
            if ($min > $date['limit']) {
                $min = $date['limit'];
            }
        }
        return $min;
    }

    public static function userMinimumCacheFoundToSetNewPowerTrail(){
        include __DIR__.'/../lib/settings.inc.php';
        return $powerTrailUserMinimumCacheFoundToSetNewPowerTrail;
    }

    public $logActionTypes = array (
        1 => array (
            'type' => 'create new Power Trail',
        ),
        2 => array (
            'type' => 'attach cache to PowerTrail',
        ),
        3 => array (
            'type' => 'remove cache from PowerTrail',
        ),
        4 => array (
            'type' => 'add another owner to PowerTrail',
        ),
        5 => array (
            'type' => 'remove owner from PowerTrail',
        ),
        6 => array (
            'type' => 'change PowerTrail status',
        ),
    );

    function __construct() {
        //include __DIR__.'/../lib/settings.inc.php';
        //$this->userMinimumCacheFoundToSetNewPowerTrail = $userMinimumCacheFoundToSetNewPowerTrail;
    }

    /**
     * check if user $userId is owner of $powerTrailId.
     * @return 0 or 1
     */
    public static function checkIfUserIsPowerTrailOwner($userId, $powerTrailId){
        $db = OcDb::instance();
        $query = 'SELECT count(*) AS `checkResult` FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1 AND `userId` = :2' ;
        $s = $db->multiVariableQuery($query, $powerTrailId, $userId);
        $result = $db->dbResultFetchAll($s);
        return $result[0]['checkResult'];
    }

    /**
     * here power Trail types
     */
    public static function getPowerTrailTypes(){
        return array (
            self::GEODRAW => array (
                'geopathTypeName' => self::getConstName(self::GEODRAW),
                'translate' => 'pt004',
                'icon' => self::iconPath.'footprintRed.png',
            ),
            self::TOURING => array (
                'geopathTypeName' => self::getConstName(self::TOURING),
                'translate' => 'pt005',
                'icon' => self::iconPath.'footprintBlue.png',
            ),
            self::NATURE => array (
                'geopathTypeName' => self::getConstName(self::NATURE),
                'translate' => 'pt067',
                'icon' => self::iconPath.'footprintGreen.png',
            ),
            self::TEMATIC => array (
                'geopathTypeName' => self::getConstName(self::TEMATIC),
                'translate' => 'pt079',
                'icon' => self::iconPath.'footprintYellow.png',
            ),
        );
    }

    private static function getConstName($constValue) {
        $cClass = new ReflectionClass (__CLASS__);
        $constants = $cClass->getConstants();
        foreach ($constants as $name => $value){
            if ($value == $constValue){
                return $name;
            }
        }
        return null;
    }
     /**
     * here power Trail icons
     */

    public static function getPowerTrailIconsByType() {
        $ret = array (
            1 => 'footprintRed.png',
            2 => 'footprintBlue.png',
            3 => 'footprintGreen.png',
            4 => 'footprintYellow.png',
        );
        return $ret;
    }



    /**
     * here power Trail status
     */
    public static function getPowerTrailStatus(){
        return array (
            1 => array ( // public
                'translate' => 'pt006',
            ),
            2 => array ( // not yet available
                'translate' => 'pt007',
            ),
            4 => array ( // service
                'translate' => 'pt219',
            ),
            3 => array ( // archived
                'translate' => 'pt212',
            ),

        );

    }

    /**
     * here comment types
     */
    public static function getPowerTrailComments(){
        return array (
            1 => array ( //comment
                'translate' => 'pt056',
                'color' => '#000000',
            ),
            2 => array ( // conquested
                'translate' => 'pt057',
                'color' => '#00CC00',
            ),
            3 => array ( // geoPath Publishing
                'translate' => 'pt214',
                'color' => '#0000CC',
            ),
            4 => array ( // geoPath temp. closed
                'translate' => 'pt216',
                'color' => '#CC0000',
            ),
            5 => array ( // geoPath Closure (permanent)
                'translate' => 'pt213',
                'color' => '#CC0000',
            ),
        );
    }

    public static function cacheSizePoints() {
        return array (
        2 => 2.5,   # Micro
        3 => 2, # Small
        4 => 1.5,   # Normal [from 1 to 3 litres]
        5 => 1, # Large [from 3 to 10 litres]
        6 => 0.5,   # Very large [more than 10 litres]
        7 => 0, # Bez pojemnika
    );
    }

    public static function cacheTypePoints() {
        return array (
            1 => 2, #Other
            2 => 2, #Trad.
            3 => 3, #Multi
            4 => 1, #Virt.
            5 => 0.2, #ICam.
            6 => 0, #Event
            7 => 4, #Quiz
            8 => 2, #Moving
            9 => 1, #podcast
            10 => 1, #own
        );
    }

    public static function checkUserConquestedPt($userId, $ptId){
        $db = OcDb::instance();
        $q = 'SELECT count(*) AS `c` FROM PowerTrail_comments
            WHERE userId = :1 AND PowerTrailId = :2 AND `commentType` =2 AND deleted !=1 ';
        $s = $db->multiVariableQuery($q, $userId, $ptId);
        $response = $db->dbResultFetchOneRowOnly($s);
        return $response['c'];
    }

    public static function getPoweTrailCompletedCountByUser($userId) {
        $queryPt = "SELECT count(`PowerTrailId`) AS `ptCount` FROM `PowerTrail_comments`
            WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1";
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($queryPt, $userId);
        $ptCount = $db->dbResultFetchOneRowOnly($s);
        return (int) $ptCount['ptCount'];
    }

    public static function getUserPoints($userId) {
        $queryPt = "SELECT sum( `points` ) AS sum
                    FROM powerTrail_caches
                    WHERE `PowerTrailId` IN (
                        SELECT `PowerTrailId` AS `ptCount` FROM `PowerTrail_comments`
                        WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1
                    )
                    AND `cacheId` IN (
                        SELECT `cache_id` FROM `cache_logs`
                        WHERE `type` =1 AND `user_id` =:1
                    )";
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($queryPt, $userId);
        $points = $db->dbResultFetchOneRowOnly($s);
        return round($points['sum'],2);
    }

    /**
     * calculate magnifier used for counting points for placing caches of geoPath
     *
     * math function y=ax+b
     * where x1=1 y1=1 and x2=$w, y2=2
     */
    public static function calculateMagnifier($x){
            $w = self::cCountForMaxMagnifier;
            $b = (2-$w)/(-$w+1);
            return (1-$b)*$x+$b;
        }

    public static function getOwnerPoints($userId){
        $query = 'SELECT
                    round(sum(`powerTrail_caches`.`points`),2) AS `pointsSum`,
                    count( `powerTrail_caches`.`cacheId` ) AS `cacheCount`,
                    `powerTrail_caches`.`PowerTrailId`,
                    `PowerTrail`.`name`
                FROM
                    `powerTrail_caches`,
                    `PowerTrail`
                WHERE
                        `powerTrail_caches`.`cacheId`
                IN ( SELECT `cache_id` FROM `caches` WHERE `user_id` =:1)
                AND     `PowerTrail`.`id` = `powerTrail_caches`.`PowerTrailId`
                AND     `PowerTrail`.`status` != 2
                GROUP BY `PowerTrailId`';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($query, $userId);
        $points = $db->dbResultFetchAll($s);
        $totalPoint = 0;
        $geoPathCount = 0;
        $pointsDetails = array();
        foreach ($points as $ptPoints) {
            $magnifier = self::calculateMagnifier($ptPoints['cacheCount']);
            $earnedPoints = $ptPoints['pointsSum']*$magnifier;
            $pointsDetails[$ptPoints['PowerTrailId']] = array(
                'cacheCount' => $ptPoints['cacheCount'],
                'pointsSum' => $ptPoints['pointsSum'],
                'magnifier' => $magnifier,
                'pointsEarned' => $earnedPoints,
                'ptName' => $ptPoints['name'],
            );
            $totalPoint += $earnedPoints;
            $geoPathCount++;
        }
        return array('totalPoints' => round($totalPoint,2), 'geoPathCount' => $geoPathCount, 'pointsDetails' => $pointsDetails);
    }

    public static function checkForPowerTrailByCache($cacheId){
        $queryPt = 'SELECT `id`, `name`, `image` FROM `PowerTrail` WHERE `id` IN ( SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` =:1 ) AND `status` = 1 ';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($queryPt, $cacheId);
        return $db->dbResultFetchAll($s);
    }

    public static function getPtOwners($ptId) {
        $query = 'SELECT user_id, username, email, power_trail_email FROM `user` WHERE user_id IN (SELECT `userId` FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1 ) ';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($query, $ptId);
        $dbResult = $db->dbResultFetchAll($s);
        $result = array();
        foreach ($dbResult as $ptOwner) {
            $result[$ptOwner['user_id']] = $ptOwner;
        }
        return $result;
    }

    public static function getPtDbRow($ptId) {
        $query = 'SELECT * FROM `PowerTrail` WHERE `id` = :1 LIMIT 1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($query, $ptId);
        return $db->dbResultFetchOneRowOnly($s);
    }

    public static function getPtCacheCount($ptId) {
        $q = 'SELECT count( * ) AS `count` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($q, $ptId);
        $answer = $db->dbResultFetchOneRowOnly($s);
        return $answer['count'];
    }

    public static function getUserDetails($userId) {
        $q = 'SELECT * FROM `user` WHERE `user_id` =:1 LIMIT 1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($q, $userId);
        $answer = $db->dbResultFetchOneRowOnly($s);
        return $answer;
    }

    public static function getSingleComment($commentId) {
        $query = 'SELECT * FROM `PowerTrail_comments` WHERE `id` = :1 LIMIT 1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($query, $commentId);
        return $db->dbResultFetchOneRowOnly($s);
    }

    public static function getCachePoints($cacheData){
        $typePoints = self::cacheTypePoints();
        $sizePoints = self::cacheSizePoints();
        $typePoints = $typePoints[$cacheData['type']];
        $sizePoints = $sizePoints[$cacheData['size']];
        $url = 'http://maps.googleapis.com/maps/api/elevation/xml?locations='.$cacheData['latitude'].','.$cacheData['longitude'].'&sensor=false';
        $altitude = simplexml_load_file($url);
        $altitude = round($altitude->result->elevation);
        if ($altitude <= 400) $altPoints = 1;
        else $altPoints = 1+($altitude-400)/200 ;
        $difficPoint = round($cacheData['difficulty']/3,2);
        $terrainPoints = round($cacheData['terrain']/3,2);
        return ($altPoints + $typePoints + $sizePoints + $difficPoint + $terrainPoints);
    }


    public static function recalculateCenterAndPoints($caches){
        $points = 0;
        $lat = 0;
        $lon = 0;
        $counter = 0;
        foreach ($caches as $cache){
            $points += self::getCachePoints($cache);
            $lat += $cache['latitude'];
            $lon += $cache['longitude'];
            $counter++;
        }

        if($counter>0){
            $result['avgLat'] = $lat/$counter;
            $result['avgLon'] = $lon/$counter;
        } else {
            $result['avgLat'] = 0;
            $result['avgLon'] = 0;
        }
        $result['points'] = $points;
        $result['cacheCount'] = $counter;
        return $result;
    }

    public static function writePromoPt4mainPage($oldPtId){
        $q = 'SELECT * FROM `PowerTrail` WHERE `id` != :1 AND `status` = 1 AND `cacheCount` >= '.self::historicMinimumCacheCount().' ORDER BY `id` ASC';

        $db = OcDb::instance();
        $s = $db->multiVariableQuery($q, $oldPtId);
        $r = $db->dbResultFetchAll($s);
        foreach ($r as $pt) {
            if ($pt['id'] > $oldPtId){
                return $pt;
            }
        }
        return $r[0];
    }

    public static function getPtCaches($PtId){
        $db = OcDb::instance();
        $q = 'SELECT powerTrail_caches.isFinal, caches . * , user.username FROM  `caches` , user, powerTrail_caches WHERE cache_id IN ( SELECT  `cacheId` FROM  `powerTrail_caches` WHERE  `PowerTrailId` =:1) AND user.user_id = caches.user_id AND powerTrail_caches.cacheId = caches.cache_id ORDER BY caches.name';
        $s = $db->multiVariableQuery($q, $PtId);
        return $db->dbResultFetchAll($s);
    }

    public static function getPtCachesIds($PtId){
        $q = 'SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($q, $PtId);
        $r = $db->dbResultFetchAll($s);

        foreach ($r as $c) {
            $result[] = $c['cacheId'];
        }
        return $result;
    }

    /**
     * remove unwanted chars from pt names
     * (for gpx filenames)
     */
    public static function clearPtNames($ptName){
        $ptName = ucwords(strtolower($ptName));
        $ptName = str_replace('♥', 'Serduszko', $ptName);
        $ptName = str_replace(' ', '', $ptName);
        $ptName = trim($ptName);
        return $ptName;
    }

    public static function getLeadingUser($ptId){
        $q = "SELECT  `username`, `user_id` FROM  `user`
            WHERE  `user_id` = (
                SELECT  `userId` FROM  `PowerTrail_actionsLog`
                WHERE  `actionType` =1 AND  `PowerTrailId` =:1 LIMIT 1
            ) LIMIT 1";
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($q, $ptId);
        return $db->dbResultFetchOneRowOnly($s);
    }

    public static function getAllPt($filter){
        $sortOder = 'ASC';
        $sortBy = 'name';

        $q = 'SELECT * FROM `PowerTrail` WHERE cacheCount >= '.self::historicMinimumCacheCount() .' '.$filter.' ORDER BY '.$sortBy.' '.$sortOder.' ';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($q);
        return $db->dbResultFetchAll($s);
    }
}
