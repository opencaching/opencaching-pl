<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;

class ReportWatches extends BaseObject
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Turns OFF watch of report (given by id) for user identyfied by userId
     *
     * @param int $reportId
     * @param int $userId
     */
    public static function turnWatchOnByReportId($reportId, $userId)
    {
        $query = "
            INSERT INTO `reports_watches` (`report_id`, `user_id`, `date_created`)
            VALUES (:reportId, :userId, NOW())
            ON DUPLICATE KEY UPDATE `date_created` = NOW()";
        $params = [];
        $params['reportId']['value'] = $reportId;
        $params['reportId']['data_type'] = 'integer';
        $params['userId']['value'] = $userId;
        $params['userId']['data_type'] = 'integer';
        self::db()->paramQuery($query, $params);
    }

    /**
     * Turns ON watch of report (given by id) for user identyfied by userId
     *
     * @param int $reportId
     * @param int $userId
     */
    public static function turnWatchOffByReportId($reportId, $userId)
    {
        $query = "
            DELETE FROM `reports_watches`
            WHERE report_id = :reportId AND user_id = :userId";
        $params = [];
        $params['reportId']['value'] = $reportId;
        $params['reportId']['data_type'] = 'integer';
        $params['userId']['value'] = $userId;
        $params['userId']['data_type'] = 'integer';
        self::db()->paramQuery($query, $params);
    }

    /**
     * Check if user has watches report with given $reportId
     *
     * @param int $reportId
     * @param int $userId
     * @return boolean
     */
    public static function isReportWatchedByReportId($reportId, $userId)
    {
        $query = "
            SELECT COUNT(*) FROM `reports_watches`
            WHERE report_id = :reportId AND user_id = :userId";
        $params = [];
        $params['reportId']['value'] = $reportId;
        $params['reportId']['data_type'] = 'integer';
        $params['userId']['value'] = $userId;
        $params['userId']['data_type'] = 'integer';
        return (self::db()->paramQueryValue($query, 0, $params) > 0);
    }

    /**
     * Returns array with user_id of users which watch given report
     *
     * @param int $reportId
     * @return array
     */
    public static function getWatchersByReportId($reportId)
    {
        $query = "
            SELECT `user_id` FROM `reports_watches`
            WHERE report_id = :reportId";
        $params = [];
        $params['reportId']['value'] = $reportId;
        $params['reportId']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);
        return (self::db()->dbResultFetchAll($stmt));

    }
}
