<?php

use src\Models\OcConfig\OcConfig;
use src\Utils\Database\OcDb;

class powerTrailBase
{
    /** declare types and id of types of geoPaths: */
    public const GEODRAW = 1;

    public const TOURING = 2;

    public const NATURE = 3;

    public const TEMATIC = 4;

    public const commentsPaginateCount = 5;

    private const cCountForMaxMagnifier = 50;

    public const iconPath = '/images/blue/';

    /**
     * check if user $userId is owner of $powerTrailId.
     * @return0 or 1
     */
    public static function checkIfUserIsPowerTrailOwner($userId, $powerTrailId)
    {
        $db = OcDb::instance();
        $query = 'SELECT count(*) AS `checkResult` FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1 AND `userId` = :2';
        $s = $db->multiVariableQuery($query, $powerTrailId, $userId);
        $result = $db->dbResultFetchAll($s);

        return $result[0]['checkResult'];
    }

    /**
     * here power Trail types
     */
    public static function getPowerTrailTypes(): array
    {
        return [
            self::GEODRAW => [
                'geopathTypeName' => self::getConstName(self::GEODRAW),
                'translate' => 'cs_typeGeoDraw',
                'icon' => self::iconPath . 'footprintRed.png',
            ],
            self::TOURING => [
                'geopathTypeName' => self::getConstName(self::TOURING),
                'translate' => 'cs_typeTouring',
                'icon' => self::iconPath . 'footprintBlue.png',
            ],
            self::NATURE => [
                'geopathTypeName' => self::getConstName(self::NATURE),
                'translate' => 'cs_typeNature',
                'icon' => self::iconPath . 'footprintGreen.png',
            ],
            self::TEMATIC => [
                'geopathTypeName' => self::getConstName(self::TEMATIC),
                'translate' => 'cs_typeThematic',
                'icon' => self::iconPath . 'footprintYellow.png',
            ],
        ];
    }

    private static function getConstName($constValue)
    {
        $cClass = new ReflectionClass(__CLASS__);
        $constants = $cClass->getConstants();

        foreach ($constants as $name => $value) {
            if ($value == $constValue) {
                return $name;
            }
        }

        return '';
    }

    /**
     * here power Trail icons
     */
    public static function getPowerTrailIconsByType(): array
    {
        return [
            1 => 'footprintRed.png',
            2 => 'footprintBlue.png',
            3 => 'footprintGreen.png',
            4 => 'footprintYellow.png',
        ];
    }

    public static function cacheSizePoints(): array
    {
        return [
            2 => 2.5,   // Micro
            3 => 2, // Small
            4 => 1.5,   // Normal [from 1 to 3 litres]
            5 => 1, // Large [from 3 to 10 litres]
            6 => 0.5,   // Very large [more than 10 litres]
            7 => 0, // Bez pojemnika
        ];
    }

    public static function cacheTypePoints(): array
    {
        return [
            1 => 2, //Other
            2 => 2, //Trad.
            3 => 3, //Multi
            4 => 1, //Virt.
            5 => 0.2, //ICam.
            6 => 0, //Event
            7 => 4, //Quiz
            8 => 2, //Moving
            9 => 1, //podcast
            10 => 1, //own
        ];
    }

    public static function checkUserConquestedPt($userId, $ptId)
    {
        $db = OcDb::instance();
        $q = 'SELECT count(*) AS `c` FROM PowerTrail_comments
            WHERE userId = :1 AND PowerTrailId = :2 AND `commentType` =2 AND deleted !=1 ';
        $s = $db->multiVariableQuery($q, $userId, $ptId);
        $response = $db->dbResultFetchOneRowOnly($s);

        return $response['c'];
    }

    public static function getPoweTrailCompletedCountByUser($userId): int
    {
        $queryPt = 'SELECT count(`PowerTrailId`) AS `ptCount` FROM `PowerTrail_comments`
            WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($queryPt, $userId);
        $ptCount = $db->dbResultFetchOneRowOnly($s);

        return (int) $ptCount['ptCount'];
    }

    public static function getUserPoints($userId): float
    {
        $queryPt = 'SELECT sum( `points` ) AS sum
                    FROM powerTrail_caches
                    WHERE `PowerTrailId` IN (
                        SELECT `PowerTrailId` AS `ptCount` FROM `PowerTrail_comments`
                        WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1
                    )
                    AND `cacheId` IN (
                        SELECT `cache_id` FROM `cache_logs`
                        WHERE `type` =1 AND `user_id` =:1
                    )';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($queryPt, $userId);
        $points = $db->dbResultFetchOneRowOnly($s);

        return round($points['sum'], 2);
    }

    /**
     * calculate magnifier used for counting points for placing caches of geoPath
     *
     * math function y=ax+b
     * where x1=1 y1=1 and x2=$w, y2=2
     */
    public static function calculateMagnifier($x)
    {
        $w = self::cCountForMaxMagnifier;
        $b = (2 - $w) / (-$w + 1);

        return (1 - $b) * $x + $b;
    }

    public static function getOwnerPoints($userId): array
    {
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
        $pointsDetails = [];

        foreach ($points as $ptPoints) {
            $magnifier = self::calculateMagnifier($ptPoints['cacheCount']);
            $earnedPoints = $ptPoints['pointsSum'] * $magnifier;
            $pointsDetails[$ptPoints['PowerTrailId']] = [
                'cacheCount' => $ptPoints['cacheCount'],
                'pointsSum' => $ptPoints['pointsSum'],
                'magnifier' => $magnifier,
                'pointsEarned' => $earnedPoints,
                'ptName' => $ptPoints['name'],
            ];
            $totalPoint += $earnedPoints;
            $geoPathCount++;
        }

        return ['totalPoints' => round($totalPoint, 2), 'geoPathCount' => $geoPathCount, 'pointsDetails' => $pointsDetails];
    }

    public static function checkForPowerTrailByCache($cacheId): array
    {
        $queryPt = 'SELECT `id`, `name`, `image` FROM `PowerTrail` WHERE `id` IN ( SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` =:1 ) AND `status` = 1 ';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($queryPt, $cacheId);

        return $db->dbResultFetchAll($s);
    }

    public static function getPtOwners($ptId): array
    {
        $query = 'SELECT user_id, username, email, power_trail_email FROM `user` WHERE user_id IN (SELECT `userId` FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1 ) ';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($query, $ptId);
        $dbResult = $db->dbResultFetchAll($s);
        $result = [];

        foreach ($dbResult as $ptOwner) {
            $result[$ptOwner['user_id']] = $ptOwner;
        }

        return $result;
    }

    public static function getPtDbRow($ptId)
    {
        $query = 'SELECT * FROM `PowerTrail` WHERE `id` = :1 LIMIT 1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($query, $ptId);

        return $db->dbResultFetchOneRowOnly($s);
    }

    public static function getPtCacheCount($ptId)
    {
        $q = 'SELECT count( * ) AS `count` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($q, $ptId);
        $answer = $db->dbResultFetchOneRowOnly($s);

        return $answer['count'];
    }

    public static function getUserDetails($userId)
    {
        $q = 'SELECT * FROM `user` WHERE `user_id` =:1 LIMIT 1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($q, $userId);

        return $db->dbResultFetchOneRowOnly($s);
    }

    public static function getSingleComment($commentId)
    {
        $query = 'SELECT * FROM `PowerTrail_comments` WHERE `id` = :1 LIMIT 1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($query, $commentId);

        return $db->dbResultFetchOneRowOnly($s);
    }

    public static function getPtCaches($PtId): array
    {
        $db = OcDb::instance();
        $q = 'SELECT powerTrail_caches.isFinal, caches . * , user.username FROM  `caches` , user, powerTrail_caches WHERE cache_id IN ( SELECT  `cacheId` FROM  `powerTrail_caches`
                WHERE  `PowerTrailId` =:1) AND user.user_id = caches.user_id AND powerTrail_caches.cacheId = caches.cache_id
                ORDER BY caches.name';
        $s = $db->multiVariableQuery($q, $PtId);

        return $db->dbResultFetchAll($s);
    }

    public static function getPtCachesIds($PtId): array
    {
        $q = 'SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($q, $PtId);
        $r = $db->dbResultFetchAll($s);

        $result = [];

        foreach ($r as $c) {
            $result[] = $c['cacheId'];
        }

        return $result;
    }

    /**
     * remove unwanted chars from pt names
     * (for gpx filenames)
     */
    public static function clearPtNames($ptName): string
    {
        $ptName = ucwords(strtolower($ptName));
        $ptName = str_replace('♥', 'Serduszko', $ptName);
        $ptName = str_replace(' ', '', $ptName);

        return trim($ptName);
    }

    public static function getLeadingUser($ptId)
    {
        $q = 'SELECT  `username`, `user_id` FROM  `user`
            WHERE  `user_id` = (
                SELECT  `userId` FROM  `PowerTrail_actionsLog`
                WHERE  `actionType` =1 AND  `PowerTrailId` =:1 LIMIT 1
            ) LIMIT 1';
        $db = OcDb::instance();
        $s = $db->multiVariableQuery($q, $ptId);

        return $db->dbResultFetchOneRowOnly($s);
    }

    public static function getAllPt($filter): array
    {
        $sortOder = 'ASC';
        $sortBy = 'name';
        $db = OcDb::instance();

        $minCachesCount = $db->quote(OcConfig::geopathMinCacheCount());

        $q = 'SELECT * FROM `PowerTrail` WHERE cacheCount >= ' . $minCachesCount . ' ' . $filter . '
                ORDER BY ' . $sortBy . ' ' . $sortOder . ' ';

        $s = $db->multiVariableQuery($q);

        return $db->dbResultFetchAll($s);
    }

    private static function getAllActionTypes(): array
    {
        return [
            1 => 'create new Power Trail',
            2 => 'attach cache to PowerTrail',
            3 => 'remove cache from PowerTrail',
            4 => 'add another owner to PowerTrail',
            5 => 'remove owner from PowerTrail',
            6 => 'change PowerTrail status',
        ];
    }

    public static function getActionType(int $action): string
    {
        return self::getAllActionTypes()[$action] ?? '';
    }
}
