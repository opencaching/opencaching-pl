<?php
namespace src\Models\CacheSet;


use src\Models\BaseObject;

/**
 * This is model of geopath logo upload
 *
 */
class MultiGeopathsStats extends BaseObject
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * For eache given cacheId find its geopath (if geocache belongs to any)
     * @param array $cacheIds
     *
     */
    public static function getGeopathForEachGeocache(array $cacheIds)
    {

        if (empty($cacheIds)) {
            return [];
        }

        $db = self::db();

        $cacheIdsStr = $db->quoteString(implode(',', $cacheIds));
        $allowedGeopathStatus = implode(',', [
            CacheSet::STATUS_OPEN, CacheSet::STATUS_INSERVICE
        ]);

        $rs = $db->multiVariableQuery(
            "SELECT pt.id, pt.name, pt.type, pt.status, pt.image,
                    ptc.cacheId
             FROM PowerTrail AS pt
                JOIN powerTrail_caches AS ptc ON pt.id = ptc.PowerTrailId
             WHERE ptc.cacheId IN ($cacheIdsStr)
                AND pt.status IN ($allowedGeopathStatus)");

        return $db->dbResultFetchAll($rs);
    }

    /**
     * Return the list of geopath objects based on give list of ids
     *
     * @param array $geopathIds
     */
    public static function getGeopathsByIds(array $geopathIds)
    {
        if (empty($geopathIds)) {
            return [];
        }

        $db = self::db();

        $gpIdsStr = implode(',', $geopathIds);
        $limit = count($geopathIds);

        $rs = $db->simpleQuery(
            "SELECT * FROM  PowerTrail WHERE id IN ($gpIdsStr) LIMIT $limit");

        $result = [];
        while($data = $db->dbResultFetch($rs)) {
            $path = new CacheSet();
            $path->loadFromDbRow($data);
            $result[$path->getId()] = $path;
        }

        return $result;
    }

    public static function getDuplicatedCachesList ()
    {
        $db = self::db();

        $rs = $db->simpleQuery("SELECT COUNT(*) as c, cacheId FROM powerTrail_caches GROUP BY cacheId HAVING c > 1");
        $caches = $db->dbFetchOneColumnArray($rs, 'cacheId');

        return $caches;
    }
}
