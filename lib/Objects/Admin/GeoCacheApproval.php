<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;
use lib\Objects\GeoCache\GeoCacheCommons;

class GeoCacheApproval extends BaseObject
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function getWaitingForApprovalCount()
    {
        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(status) FROM caches WHERE status = :1 ",
            0, GeoCacheCommons::STATUS_WAITAPPROVERS);
    }

    public static function getInReviewCount()
    {
        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(*)
             FROM caches
                JOIN approval_status USING(cache_id)
             WHERE caches.status = :1",
             0, GeoCacheCommons::STATUS_WAITAPPROVERS);
    }

}
