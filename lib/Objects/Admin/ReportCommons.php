<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;
use lib\Objects\User\User;

class ReportCommons extends BaseObject
{

    /*
     * Default config
     */
    // Number of reports on the reports page (pagination)
    const REPORTS_PER_PAGE = 25;

    // Default selected report type on the reports page
    const DEFAULT_REPORTS_TYPE = self::TYPE_ALL;

    // Default selected report status on the reports page
    const DEFAULT_REPORTS_STATUS = self::STATUS_OPEN;

    // Default selected report leader on the reports page
    const DEFAULT_REPORTS_USER = self::USER_YOU2;

    /*
     * Statuses of reports
     */
    // New report - submitted by user
    const STATUS_NEW = 0;

    // "In progress", (also has leader user)
    const STATUS_IN_PROGRESS = 1;

    // Closed / archived state
    const STATUS_CLOSED = 2;

    // Look here. May not have leader
    const STATUS_LOOK_HERE = 3;

    // Virtual status - all of above
    const STATUS_ALL = - 1;

    // Virtual status - all statuses but not STATUS_CLOSED
    const STATUS_OPEN = - 2;

    /*
     * "Virtual" types of leaders.
     * Used in report seachring
     */
    // Leader not assigned
    const USER_NOBODY = 0;

    // All users + not assigned
    const USER_ALL = - 1;

    // Current logged user
    const USER_YOU = - 2;

    // Current logged user + not assigned
    const USER_YOU2 = - 3;

    /*
     * Types of objects reported
     */
    // Geocache
    const OBJECT_CACHE = 1;

    // Powertrail / CacheSet / Geopath
    const OBJECT_POWERTRAIL = 2;

    /*
     * Indicate active poll status of the reports
     */
    // Report has no active polls
    const POLLS_NOACTIVE = 0;

    // Report has active poll(s) but logged user already voted
    const POLLS_ACTIVE_VOTED = 1;

    // Report has active poll(s) and logged user not voted one or more of them
    const POLLS_ACTIVE = 2;

    /*
     * Recipients of reports
     */
    // Report only to cacheowner
    const RECIPIENT_OWNER = 1;

    // Report to OC Team
    const RECIPIENT_OCTEAM = 2;

    /*
     * Types of reports
     */
    // Invalid location of the cache
    const TYPE_INCORRECT_PLACE = 1;

    // The cache requires archiving
    const TYPE_NEED_ARCHIVE = 2;

    // Copyright violation
    const TYPE_COPYRIGHT = 3;

    // Other
    const TYPE_OTHER = 4;

    // "Virtual" type ALL for search <select>
    const TYPE_ALL = - 1;

    /**
     * Returns URI of the report (maybe we will use routing in future?)
     *
     * @param int $reportId
     * @return string
     */
    public static function getLinkToReport($reportId)
    {
        return '/admin_reports.php?action=showreport&id=' . $reportId;
    }

    /*
     * DB related operations
     */

    /**
     * Returns bool - if given parameter is a valid report ID
     *
     * @param int $reportId
     * @return boolean
     */
    public static function isValidReportId($reportId)
    {
        if (! is_numeric($reportId)) {
            return false;
        }
        $query = 'SELECT COUNT(*) FROM `reports` WHERE `id` = :reportid';
        $params = [];
        $params['reportid']['value'] = $reportId;
        $params['reportid']['data_type'] = 'integer';
        if (self::db()->paramQueryValue($query, 0, $params) == '1') {
            return true;
        }
        return false;
    }

    /**
     * Counts reports with given status.
     * "Virtual" statuses are supported
     *
     * @param int $status
     * @return int
     */
    public static function getReportsCountByStatus($status)
    {
        $params = [];
        $query = '
            SELECT COUNT(*)
            FROM `reports`';
        switch ($status) {
            case self::STATUS_ALL:
                break;
            case self::STATUS_OPEN:
                $query .= ' WHERE `status` != :status';
                $params['status']['value'] = self::STATUS_CLOSED;
                $params['status']['data_type'] = 'int';
                break;
            default:
                $query .= ' WHERE `status` = :status';
                $params['status']['value'] = (int) $status;
                $params['status']['data_type'] = 'int';
                break;
        }
        return self::db()->paramQueryValue($query, 0, $params);
    }

    /**
     * Returns array of Reports objects fulfilling given conditions
     *
     * @param User $currentUser
     * @param string $waypoint
     * @param int $type
     * @param int $status
     * @param int $user
     * @param int $offset
     * @param int $limit
     * @return Report[]
     */
    public static function getReports(User $currentUser, $waypoint = '', $type = self::DEFAULT_REPORTS_TYPE, $status = self::DEFAULT_REPORTS_STATUS, $user = self::DEFAULT_REPORTS_USER, $offset = 0, $limit = self::REPORTS_PER_PAGE)
    {
        $params = [];
        $params['limit']['value'] = $limit;
        $params['limit']['data_type'] = 'integer';
        $params['offset']['value'] = $offset;
        $params['offset']['data_type'] = 'integer';
        $query = 'SELECT `reports`.* FROM `reports`';
        if ($waypoint != '' and ! is_null($waypoint)) {
            $query .= ' INNER JOIN `caches` ON `reports`.`cache_id` = `caches`.`cache_id`
                    WHERE (`caches`.`wp_oc` LIKE :waypoint OR `caches`.`name` LIKE :waypoint)';
            $params['waypoint']['value'] = '%' . $waypoint . '%';
            $params['waypoint']['data_type'] = 'string';
        } else {
            $query .= ' WHERE 1';
        }
        if ($type == self::TYPE_ALL) {
            $query .= '';
        } else {
            $query .= ' AND `reports`.`type` = :type';
            $params['type']['value'] = $type;
            $params['type']['data_type'] = 'integer';
        }
        if ($status == self::STATUS_ALL) {
            $query .= '';
        } elseif ($status == self::STATUS_OPEN) {
            $query .= ' AND `reports`.`status` != :status';
            $params['status']['value'] = self::STATUS_CLOSED;
            $params['status']['data_type'] = 'integer';
        } else {
            $query .= ' AND `reports`.`status` = :status';
            $params['status']['value'] = $status;
            $params['status']['data_type'] = 'integer';
        }
        if ($user == self::USER_ALL) {
            $query .= '';
        } elseif ($user == self::USER_YOU) {
            $query .= ' AND (`reports`.`responsible_id` = :user  OR `reports`.`status` = :statuslook)';
            $params['user']['value'] = $currentUser->getUserId();
            $params['user']['data_type'] = 'integer';
            $params['statuslook']['value'] = self::STATUS_LOOK_HERE;
            $params['statuslook']['data_type'] = 'integer';
        } elseif ($user == self::USER_YOU2) {
            $query .= ' AND (`reports`.`responsible_id` = :user OR `reports`.`responsible_id` IS NULL OR `reports`.`responsible_id` = 0 OR `reports`.`status` = :statuslook)';
            $params['user']['value'] = $currentUser->getUserId();
            $params['user']['data_type'] = 'integer';
            $params['statuslook']['value'] = self::STATUS_LOOK_HERE;
            $params['statuslook']['data_type'] = 'integer';
        } elseif ($user == self::USER_NOBODY) {
            $query .= ' AND (`reports`.`responsible_id` IS NULL OR `reports`.`status` = :statuslook)';
            $params['statuslook']['value'] = self::STATUS_LOOK_HERE;
            $params['statuslook']['data_type'] = 'integer';
        } else {
            $query .= ' AND `reports`.`responsible_id` = :user';
            $params['user']['value'] = $user;
            $params['user']['data_type'] = 'integer';
        }
        $query .= ' ORDER BY `reports`.`id` DESC LIMIT :limit OFFSET :offset';
        $stmt = self::db()->paramQuery($query, $params);
        
        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return Report::fromDbRowFactory($row);
        });
    }

    /**
     * Counts total number of rows for parameters like in getReports, but without limit.
     * For use in pagination / counters
     *
     * @param User $currentUser
     * @param string $waypoint
     * @param int $type
     * @param int $status
     * @param int $user
     * @return int
     */
    public static function getReportsCounts(User $currentUser, $waypoint = '', $type = self::DEFAULT_REPORTS_TYPE, $status = self::DEFAULT_REPORTS_STATUS, $user = self::DEFAULT_REPORTS_USER)
    {
        $params = [];
        $query = 'SELECT COUNT(*) FROM `reports`';
        if ($waypoint != '' and ! is_null($waypoint)) {
            $query .= ' INNER JOIN `caches` ON `reports`.`cache_id` = `caches`.`cache_id`
                    WHERE (`caches`.`wp_oc` LIKE :waypoint OR `caches`.`name` LIKE :waypoint)';
            $params['waypoint']['value'] = '%' . $waypoint . '%';
            $params['waypoint']['data_type'] = 'string';
        } else {
            $query .= ' WHERE 1';
        }
        if ($type == self::TYPE_ALL) {
            $query .= ' ';
        } else {
            $query .= ' AND `reports`.`type` = :type';
            $params['type']['value'] = $type;
            $params['type']['data_type'] = 'integer';
        }
        if ($status == self::STATUS_ALL) {
            $query .= '';
        } elseif ($status == self::STATUS_OPEN) {
            $query .= ' AND `reports`.`status` != :status';
            $params['status']['value'] = self::STATUS_CLOSED;
            $params['status']['data_type'] = 'integer';
        } else {
            $query .= ' AND `reports`.`status` = :status';
            $params['status']['value'] = $status;
            $params['status']['data_type'] = 'integer';
        }
        if ($user == self::USER_ALL) {
            $query .= '';
        } elseif ($user == self::USER_YOU) {
            $query .= ' AND (`reports`.`responsible_id` = :user  OR `reports`.`status` = :statuslook)';
            $params['user']['value'] = $currentUser->getUserId();
            $params['user']['data_type'] = 'integer';
            $params['statuslook']['value'] = self::STATUS_LOOK_HERE;
            $params['statuslook']['data_type'] = 'integer';
        } elseif ($user == self::USER_YOU2) {
            $query .= ' AND (`reports`.`responsible_id` = :user OR `reports`.`responsible_id` IS NULL OR `reports`.`responsible_id` = 0 OR `reports`.`status` = :statuslook)';
            $params['user']['value'] = $currentUser->getUserId();
            $params['user']['data_type'] = 'integer';
            $params['statuslook']['value'] = self::STATUS_LOOK_HERE;
            $params['statuslook']['data_type'] = 'integer';
        } elseif ($user == self::USER_NOBODY) {
            $query .= ' AND (`reports`.`responsible_id` IS NULL OR `reports`.`status` = :statuslook)';
            $params['statuslook']['value'] = self::STATUS_LOOK_HERE;
            $params['statuslook']['data_type'] = 'integer';
        } else {
            $query .= ' AND `reports`.`responsible_id` = :user';
            $params['user']['value'] = $user;
            $params['user']['data_type'] = 'integer';
        }
        return self::db()->paramQueryValue($query, 0, $params);
    }
    
    /**
     * Returns Report[] of reports watched by $user
     *
     * @param User $user
     * @param int $offset
     * @param int $limit
     * @return Report[]
     */
    public static function getWatchedReports(User $user, $offset = 0, $limit = self::REPORTS_PER_PAGE)
    {
        $query = '
            SELECT `reports`.*
            FROM `reports_watches`
            INNER JOIN `reports`
            ON `reports`.`id` = `reports_watches`.`report_id`
            WHERE `reports_watches`.`user_id` = :user_id
            ORDER BY `reports_watches`.`report_id` DESC
            LIMIT :limit OFFSET :offset';
        $params = [];
        $params['user_id']['value'] = $user->getUserId();
        $params['user_id']['data_type'] = 'integer';
        $params['limit']['value'] = $limit;
        $params['limit']['data_type'] = 'integer';
        $params['offset']['value'] = $offset;
        $params['offset']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);
        
        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return Report::fromDbRowFactory($row);
        });
    }
    
    /**
     * Returns count of watched reports by $user
     *
     * @param User $user
     * @return int
     */
    public static function getWatchedReportsCount(User $user)
    {
        $query = '
            SELECT COUNT(*)
            FROM `reports_watches`
            WHERE `reports_watches`.`user_id` = :user_id';
        $params = [];
        $params['user_id']['value'] = $user->getUserId();
        $params['user_id']['data_type'] = 'integer';
        return self::db()->paramQueryValue($query, 0, $params);
    }

    /*
     * Generators of allowed types/status etc.
     * arrays
     */
    
    /**
     * Returns array of alloved/valid report types
     *
     * @param boolean $includeVirtual
     * @return int[]
     */
    public static function getTypesArray($includeVirtual = false)
    {
        $types = [
            self::TYPE_INCORRECT_PLACE,
            self::TYPE_NEED_ARCHIVE,
            self::TYPE_COPYRIGHT,
            self::TYPE_OTHER
        ];
        if ($includeVirtual) {
            $types = array_merge([
                self::TYPE_ALL
            ], $types);
        }
        return $types;
    }

    /**
     * Returns array of all allowed report statuses
     *
     * @param boolean $includeVirtual
     * @return int[]
     */
    public static function getStatusesArray($includeVirtual = false)
    {
        $statuses =  [
            self::STATUS_NEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_LOOK_HERE,
            self::STATUS_CLOSED
        ];
        if ($includeVirtual) {
            $statuses = array_merge([
                self::STATUS_ALL,
                self::STATUS_OPEN
                ], $statuses);
        }
        return $statuses;
    }

    /**
     * Returns array of all allowed report recipients
     *
     * @return int[]
     */
    public static function getReportRecipientsArray()
    {
        return [
            self::RECIPIENT_OWNER,
            self::RECIPIENT_OCTEAM
        ];
    }

    /**
     * Returns array of admin users.
     * Array consist of user_id and username
     *
     * @return array
     */
    public static function getOcTeamArray()
    {
        $query = '
            SELECT `user_id`, `username`
            FROM `user`
            WHERE `admin` = 1 AND `is_active_flag` = 1
            ORDER BY username';
        $stmt = self::db()->simpleQuery($query);
        return self::db()->dbResultFetchAll($stmt);
    }

    /*
     * Generators of translation keys / class names
     */
    
    /**
     * Returns translation key for given report type
     *
     * @param int $type
     * @return string
     */
    public static function reportTypeTranslationKey($type)
    {
        switch ($type) {
            case self::TYPE_INCORRECT_PLACE:
                return 'reports_reason_01';
            case self::TYPE_NEED_ARCHIVE:
                return 'reports_reason_02';
            case self::TYPE_COPYRIGHT:
                return 'reports_reason_03';
            case self::TYPE_OTHER:
                return 'reports_reason_04';
            case self::TYPE_ALL:
                return 'admin_reports_all';
        }
    }

    /**
     * Returns translation key of given status of the report
     *
     * @param int $status
     * @return string
     */
    public static function ReportStatusTranslationKey($status)
    {
        switch ($status) {
            case self::STATUS_NEW:
                return 'reports_status_01';
            case self::STATUS_IN_PROGRESS:
                return 'reports_status_02';
            case self::STATUS_CLOSED:
                return 'reports_status_03';
            case self::STATUS_LOOK_HERE:
                return 'reports_status_04';
            case self::STATUS_OPEN:
                return 'admin_reports_sts_open';
            case self::STATUS_ALL:
                return 'admin_reports_all';
        }
    }
    
    /**
     * Returns translation key for given "virtual" user type
     *
     * @param int $status
     * @return string
     */
    public static function ReportUserTranslationKey($virtUser)
    {
        switch ($virtUser) {
            case self::USER_NOBODY:
                return 'admin_reports_usr_nobody';
            case self::USER_YOU:
                return 'admin_reports_usr_you';
            case self::USER_YOU2:
                return 'admin_reports_usr_you2';
            case self::STATUS_ALL:
                return 'admin_reports_usr_all';
        }
    }

    /**
     * Returns CSS class name corresponding to given status of report
     * Used in i.e.
     * report list to show status in graphic form
     *
     * @param int $status
     * @return string
     */
    public static function getReportStatusClass($status)
    {
        switch ($status) {
            case self::STATUS_NEW:
                return 'report-status-new';
            case self::STATUS_IN_PROGRESS:
                return 'report-status-inprogress';
            case self::STATUS_CLOSED:
                return 'report-status-closed';
            case self::STATUS_LOOK_HERE:
                return 'report-status-lookhere';
        }
    }

    /*
     * Generators of <option> lists
     */
    
    /**
     * Generates <option></option> list of all types of reports
     *
     * @param int $default
     *            // Type which should be selected by default
     * @param boolean $includeVirtual
     * @return string
     */
    public static function generateTypeSelect($default = self::DEFAULT_REPORTS_TYPE, $includeVirtual = false)
    {
        $types = self::getTypesArray($includeVirtual);
        $result = '';
        foreach ($types as $type) {
            $result .= '<option value="' . $type . '"';
            if ($type == $default) {
                $result .= ' selected="selected"';
            }
            $result .= '>' . tr(self::reportTypeTranslationKey($type)) . '</option>';
        }
        return $result;
    }

    /**
     * Generates <option></option> list of available report statuses
     *
     * @param bool $includeVirtual  // if true - add virtual "All" nad "Not closed" statuses
     * @param int $default  // status which should be selected by default
     * @return string
     */
    public static function generateStatusSelect($includeVirtual = true, $default = self::DEFAULT_REPORTS_STATUS)
    {
        $statuses = self::getStatusesArray($includeVirtual);
        $result = '';
        foreach ($statuses as $status) {
            $result .= '<option value="' . $status . '"';
            if ($status == $default) {
                $result .= ' selected="selected"';
            }
            $result .= '>' . tr(self::ReportStatusTranslationKey($status)) . '</option>';
        }
        return $result;
    }
    /**
     * Generates <option></option> list of OC Team users
     *
     * @param bool $onlyOcTeam  // if false - add virtual users like "All users", "Not assigned" etc.
     * @param int $default  // userId of user which sould be selected on list
     * @return string
     */
    public static function generateUserSelect($onlyOcTeam = false, $default = self::DEFAULT_REPORTS_USER)
    {
        $result = '';
        if ($default == null) {
            $result .= '<option value="' . self::USER_NOBODY . '" selected="selected">---</option>';
        }
        if (! $onlyOcTeam) {
            $users = [
                self::USER_ALL,
                self::USER_NOBODY,
                self::USER_YOU,
                self::USER_YOU2
            ];
            foreach ($users as $user) {
                $result .= '<option value="' . $user . '"';
                if ($user == $default) {
                    $result .= ' selected="selected"';
                }
                $result .= '>' . tr(self::ReportUserTranslationKey($user)) . '</option>';
            }
        }
        $users = self::getOcTeamArray();
        foreach ($users as $user) {
            $result .= '<option value="' . $user['user_id'] . '"';
            if ($user['user_id'] == $default) {
                $result .= ' selected="selected"';
            }
            $result .= '>' . $user['username'] . '</option>';
        }
        return $result;
    }
    
}
