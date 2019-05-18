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
     * For eache given cacheId find its geopath (if geocache belomgs to any)
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
}
