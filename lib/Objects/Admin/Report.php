<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\User\User;
use lib\Objects\OcConfig\OcConfig;
use Utils\Generators\Uuid;
use lib\Objects\GeoCache\GeoCacheLogCommons;

class Report extends BaseObject
{

    // Default config
    const REPORTS_PER_PAGE = 25;
    const DEFAULT_REPORTS_TYPE = self::TYPE_ALL;
    const DEFAULT_REPORTS_STATUS = self::STATUS_OPEN;
    const DEFAULT_REPORTS_USER = self::USER_YOU2;

    // Types of reports
    const TYPE_INCORRECT_PLACE = 1;
    const TYPE_NEED_ARCHIVE = 2;
    const TYPE_COPYRIGHT = 3;
    const TYPE_OTHER = 4;
    const TYPE_ALL = - 1;

    // Statuses of reports
    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_CLOSED = 2;
    const STATUS_LOOK_HERE = 3;
    const STATUS_ALL = - 1;
    const STATUS_OPEN = - 2;  // = not STATUS_CLOSED

    // "Virtual" types of users
    const USER_NOBODY = 0;
    const USER_ALL = - 1;
    const USER_YOU = - 2;
    const USER_YOU2 = - 3;

    // Types of objects reported
    const OBJECT_CACHE = 1;
    const OBJECT_POWERTRAIL = 2;

    // Polls status of report
    const POLLS_NOACTIVE = 0; // Report has no active polls
    const POLLS_ACTIVE_VOTED = 1; // Report has active poll(s) but logged user already voted
    const POLLS_ACTIVE = 2; // Report has active poll(s) and logged user not voted one or more of them

    /**
     * ID of the report
     *
     * @var int
     */
    private $id = null;

    /**
     * Id of user who submits the report
     *
     * @var int
     */
    private $userIdSubmit = null;

    /**
     * User object - who submits the report
     *
     * @var User
     */
    private $userSubmit = null;

    /**
     * Id of user who is the leader of the report
     *
     * @var int
     */
    private $userIdLeader = null;

    /**
     * User object of the leader of the report
     *
     * @var User
     */
    private $userLeader = null;

    /**
     * Id of user who last changed the report
     *
     * @var int
     */
    private $userIdLastChange = null;

    /**
     * User who last changed the report
     *
     * @var User
     */
    private $userLastChange = null;

    /**
     * Type of object reported (cache or geopath)
     *
     * @var int
     */
    private $objectType = self::OBJECT_CACHE;

    /**
     * Id of cache reported
     *
     * @var int
     */
    private $cacheId = null;

    /**
     * Cache object reported
     *
     * @var GeoCache
     */
    private $cache = null;

    /**
     * PowerTrail reported
     *
     * @var int
     */
    private $powerTrailId;

    /**
     * Type of the report (reason - see self::TYPE_*)
     *
     * @var int
     */
    private $type;

    /**
     * Text content of the report
     *
     * @var string
     */
    private $content;

    /**
     * History of the report - OBSOLETE, for historic compatibility only.
     * Use ReportLog instead!
     *
     * @var string
     */
    private $note;

    /**
     * Status of the report (see self::STATUS_*)
     *
     * @var int
     */
    private $status = self::STATUS_NEW;

    /**
     * Date of report submit
     *
     * @var \DateTime
     */
    private $dateSubmit = null;

    /**
     * Date of last report change
     *
     * @var \DateTime
     */
    private $dateLastChange = null;

    /**
     * UUID for OKAPI
     *
     * @var string
     */
    private $uuid;

    /**
     * "secret" used in user's links to reports
     *
     * @var string
     */
    private $secret = null;

    public function __construct(array $params = [])
    {
        parent::__construct();
        if (isset($params['reportId'])) {
            $this->loadById($params['reportId']);
        }
    }

    private function loadById($reportId)
    {
        $query = 'SELECT * FROM `reports` WHERE `id` = :1 LIMIT 1';
        $stmt = self::db()->multiVariableQuery($query, $reportId);
        $dbRow = self::db()->dbResultFetch($stmt);

        if (is_array($dbRow)) {
            $this->loadFromDbRow($dbRow);
        } else {
            $this->dataLoaded = false;
        }
    }

    private function loadFromDbRow(array $dbRow)
    {
        foreach ($dbRow as $key => $val) {
            switch ($key) {
                case 'id':
                    $this->id = (int) $val;
                    $this->dataLoaded = true;
                    break;
                case 'user_id':
                    $this->userIdSubmit = $val;
                    break;
                case 'cache_id':
                    $this->cacheId = $val;
                    break;
                case 'type':
                    $this->type = $val;
                    break;
                case 'text':
                    $this->content = $val;
                    break;
                case 'note':
                    $this->note = $val;
                    break;
                case 'submit_date':
                    $this->dateSubmit = new \DateTime($val);
                    break;
                case 'status':
                    $this->status = $val;
                    break;
                case 'changed_by':
                    $this->userIdLastChange = ($val == 0) ? null : $val;
                    break;
                case 'changed_date':
                    $this->dateLastChange = ($val == null || $val == '') ? null : new \DateTime($val);
                    break;
                case 'responsible_id':
                    $this->userIdLeader = ($val == self::USER_NOBODY) ? null : $val;
                    break;
                case 'uuid':
                    $this->uuid = $val;
                    break;
                case 'object_type':
                    $this->objectType = $val;
                    break;
                case 'PowerTrail_id':
                    $this->powerTrailId = $val;
                    break;
                case 'secret':
                    $this->secret = $val;
                    break;
                default:
                    error_log(__METHOD__ . ": Unknown column: $key");
            }
        }
    }

    private static function fromDbRowFactory(array $dbRow)
    {
        $n = new self();
        $n->loadFromDbRow($dbRow);
        return $n;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns userId of user who submits the report
     *
     * @return int
     */
    public function getUserIdSubmit()
    {
        return $this->userIdSubmit;
    }

    /**
     * Returns User object od user who submits the report
     *
     * @return \lib\Objects\User\User
     */
    public function getUserSubmit()
    {
        if ($this->userSubmit == null && $this->isDataLoaded()) {
            $this->userSubmit = new User([ 'userId' => $this->userIdSubmit ]);
        }
        return $this->userSubmit;
    }

    public function getUserIdLeader()
    {
        return $this->userIdLeader;
    }

    public function getUserLeader()
    {
        if ($this->userLeader == null && $this->isDataLoaded()) {
            $this->userLeader = new User([ 'userId' => $this->userIdLeader]);
        }
        return $this->userLeader;
    }

    public function getUserIdLastChange()
    {
        return $this->userIdLastChange;
    }

    public function getUserLastChange()
    {
        if ($this->userLastChange == null && $this->isDataLoaded()) {
            $this->userLastChange = new User([ 'userId' => $this->userIdLastChange ]);
        }
        return $this->userLastChange;
    }

    public function getCacheId()
    {
        return $this->cacheId;
    }

    public function getCache()
    {
        if ($this->cache == null && $this->isDataLoaded()) {
            $this->cache = new GeoCache([ 'cacheId' => $this->cacheId ]);
        }
        return $this->cache;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDateSubmit()
    {
        return $this->dateSubmit;
    }

    public function getDateLastChange()
    {
        return $this->dateLastChange;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function getObjectType()
    {
        return $this->objectType;
    }

    public function getPowerTrailId()
    {
        return $this->powerTrailId;
    }

    /**
     * Returns translation key of type of the current report
     *
     * @return string
     */
    public function getReportTypeTranslationKey()
    {
        return self::ReportTypeTranslationKey($this->type);
    }

    /**
     * Returns translation key of status of the current report
     *
     * @return string
     */
    public function getReportStatusTranslationKey()
    {
        return self::ReportStatusTranslationKey($this->status);
    }

    /**
     * Returns translation key for given report type
     *
     * @param int $type
     * @return string
     */
    public static function ReportTypeTranslationKey($type)
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
            $query .= ' AND `reports`.`responsible_id` = :user';
            $params['user']['value'] = $currentUser->getUserId();
            $params['user']['data_type'] = 'integer';
        } elseif ($user == self::USER_YOU2) {
            $query .= ' AND (`reports`.`responsible_id` = :user OR `reports`.`responsible_id` IS NULL OR `reports`.`status` = :statuslook)';
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
            return self::fromDbRowFactory($row);
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
            $query .= ' AND `reports`.`responsible_id` = :user';
            $params['user']['value'] = $currentUser->getUserId();
            $params['user']['data_type'] = 'integer';
        } elseif ($user == self::USER_YOU2) {
            $query .= ' AND (`reports`.`responsible_id` = :user OR `reports`.`responsible_id` IS NULL OR `reports`.`status` = :statuslook)';
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
            return self::fromDbRowFactory($row);
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

    /**
     * Generates <option></option> list of all types of reports
     *
     * @param int $default  // Type which should be selected
     * @return string
     */
    public static function generateTypeSelect($default = self::DEFAULT_REPORTS_TYPE)
    {
        $types = [
            self::TYPE_ALL,
            self::TYPE_INCORRECT_PLACE,
            self::TYPE_NEED_ARCHIVE,
            self::TYPE_COPYRIGHT,
            self::TYPE_OTHER
        ];
        $result = '';
        foreach ($types as $type) {
            $result .= '<option value="' . $type . '"';
            if ($type == $default) {
                $result .= ' selected="selected"';
            }
            $result .= '>' . tr(self::ReportTypeTranslationKey($type)) . '</option>';
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
        $statuses = [];
        if ($includeVirtual) {
            $statuses = [ self::STATUS_ALL, self::STATUS_OPEN ];
        }
        $statuses = array_merge($statuses, self::getStatusesArray());
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
     * Returns CSS class name coresponding to delay of report management.
     * It counts how many days report is unassigned or has no activity.
     *
     * @return string
     */
    public function getReportStyle()
    {
        if ($this->status == self::STATUS_IN_PROGRESS || $this->status == self::STATUS_LOOK_HERE) {
            $interval = $this->dateLastChange->diff(new \DateTime('now'))->days;
        } elseif ($this->status == self::STATUS_NEW) {
            $interval = $this->dateSubmit->diff(new \DateTime('now'))->days;
        } else {
            $interval = 0;
        }
        if ($interval >= 7) {
            return 'report-status-error';
        } elseif ($interval >= 5) {
            return 'report-status-warning';
        }
        return '';
    }

    /**
     * Method returns CSS class name corresponding to status of report object
     * Used in i.e. report list to show status in graphic form
     *
     * @return string
     */
    public function getStatusClass()
    {
        return self::getReportStatusClass($this->status);
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

    /**
     * Returns array of all allowed report statuses
     * 
     * @return int[]
     */
    public static function getStatusesArray()
    {
        return [
            self::STATUS_NEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_LOOK_HERE,
            self::STATUS_CLOSED
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

    /**
     * Counts reports with given status. "Virtual" statuses are allowed
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
     * Method check if report is wathed by given userId
     *
     * @param int $userId
     * @return boolean
     */
    public function isReportWatched($userId)
    {
        return ReportWatches::isReportWatchedByReportId($this->id, $userId);
    }

    /**
     * Turns ON watch of report for given user
     *
     * @param int $userId
     */
    public function turnWatchOn($userId)
    {
        ReportWatches::turnWatchOnByReportId($this->id, $userId);
    }

    /**
     * Turns OFF watch of report for given user
     *
     * @param int $userId
     */
    public function turnWatchOff($userId)
    {
        ReportWatches::turnWatchOffByReportId($this->id, $userId);
    }

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

    /**
     * Check if report has active polls. If no -> returns self::POLLS_NOACTIVE
     * If yes, checks if user not yet voted at least one of them.
     * If not voted - returns self::POLLS_ACTIVE. If voted all polls -
     * returns self::POLLS_ACTIVE_VOTED
     *
     * @return int
     */
    public function getPollStatus()
    {
        if (! $this->dataLoaded) {
            return null;
        }
        $polls = ReportPoll::getActivePolls($this->id);
        if (empty($polls)) {
            return self::POLLS_NOACTIVE;
        }
        foreach ($polls as $poll) {
            if (! $poll->userVoted()) {
                return self::POLLS_ACTIVE;
            }
        }
        return self::POLLS_ACTIVE_VOTED;
    }

    /**
     * Changes leader of report to $newLeader, changes "last change" data and sends notifications
     *
     * @param int $newLeader
     * @return boolean
     */
    public function changeLeader($newLeader)
    {
        if (! $this->isDataLoaded()) {
            return false;
        }
        $oldLeaderId = $this->userIdLeader;
        $this->userIdLeader = $newLeader;
        unset($this->userLeader);
        $this->userLeader = new User(['userId' => $newLeader]);
        $this->updateLastChanged();
        $this->saveReport();
        $logId = ReportLog::addLog($this->id, ReportLog::TYPE_CHANGELEADER, $this->getUserLeader()->getUserName());
        if ($this->userIdLeader == $this->userIdLastChange) { // Assign report to yourself
            $this->sendWatchEmails($logId);
        } else { // Assign report to other user
            ReportEmailSender::sendReportNewLeader($this, $this->getUserLeader());
            $this->sendWatchEmails($logId, [ $this->userIdLeader ]);
        }
        if (! $this->isReportWatched($oldLeaderId)) { // If previeous leader don't watch this report - inform him anyway
            ReportEmailSender::sendReportWatch($this, new User(['userId' => $oldLeaderId]), $logId);
        }
        if ($this->status == self::STATUS_NEW) { // If sb assign user to new report -> change status to "In progress"
            $this->changeStatus(self::STATUS_IN_PROGRESS);
        }
        return true;
    }

    /**
     * Changes status of report, changes "last change" data and sends notifications (if silent == false)
     *
     * @param int $newStatus
     * @param boolean $silent
     * @return boolean
     */
    public function changeStatus($newStatus, $silent = false)
    {
        if (! $this->isDataLoaded()) {
            return false;
        }
        if ($newStatus != self::STATUS_LOOK_HERE && ! empty(ReportPoll::getActivePolls($this->id))) { // If polls are active - status should be Look Here!
            return false;
        }
        $this->status = $newStatus;
        $this->updateLastChanged();
        $this->saveReport();
        $logId = ReportLog::addLog($this->id, ReportLog::TYPE_CHANGESTATUS, tr($this->getReportStatusTranslationKey()));
        if (! $silent) {
            // Send notification about new status
            if ($this->status == self::STATUS_LOOK_HERE) {
                $userlist = Report::getOcTeamArray();
                foreach ($userlist as $user) { // Send mails to all OC Team members
                    if ($user['user_id'] != $this->userIdLastChange) { // Don't notify logged user
                        $usr = new User(['userId' => $user['user_id']]);
                        ReportEmailSender::sendReportLookHere($this, $usr);
                        unset($usr);
                    }
                }
            } else { //Status changed NOT to look here
                $this->sendWatchEmails($logId); // If it is not change to "Look here", send standard watch mails
                if ($this->userIdLeader != self::USER_NOBODY && self::getCurrentUser()->getUserId() != $this->userIdLeader && ! $this->isReportWatched($this->userIdLeader)) {
                    // If somebody change status of the report assigned to another user - inform leader even if he don't watch this report
                    ReportEmailSender::sendReportWatch($this, $this->getUserLeader(), $logId);
                }
            }
        }
        if ($this->userIdLeader == null && $newStatus != self::STATUS_NEW) {
            // If sbd changes status to other than "New", and report has no leader -
            // Set current logged user as leader!
            $this->changeLeader($this->getCurrentUser()->getUserId());
        }
        return true;
    }

    /**
     * Adds $submittedNote as note to the report
     *
     * @param string $submittedNote
     * @return boolean
     */
    public function addNote($submittedNote) {
        if (! $this->isDataLoaded()) {
            return false;
        }
        $submittedNote = strip_tags($submittedNote, '<br>');
        $submittedNote = nl2br($submittedNote);
        $this->updateLastChanged();
        $this->saveReport();
        $logId = ReportLog::addLog($this->id, ReportLog::TYPE_NOTE, $submittedNote);
        $this->sendWatchEmails($logId);
        if ($this->userIdLeader != self::USER_NOBODY && self::getCurrentUser()->getUserId() != $this->userIdLeader && ! $this->isReportWatched($this->userIdLeader)) {
            // If somebody adds note to the report assigned to another user - inform leader even if he don't watch this report
            ReportEmailSender::sendReportWatch($this, $this->getUserLeader(), $logId);
        }
        return true;
    }

    /**
     * Sends e-mail OC Team -> user. $recipient is one of ReportEmailTemplate::RECIPIENT_*
     *
     * @param int $recipient
     * @param string $content
     */
    public function sendEmail($recipient, $content)
    {
        $content = strip_tags($content, '<br>');
        $content = nl2br($content);
        switch ($recipient) {
            case ReportEmailTemplate::RECIPIENT_ALL:
                ReportEmailSender::sendMailToUser($this, $this->getCache()->getOwner(), $content);
                ReportEmailSender::sendMailToUser($this, $this->getUserSubmit(), $content);
                $logId = ReportLog::addLog($this->id, ReportLog::TYPE_MAILTO_BOTH, $content);
                break;
            case ReportEmailTemplate::RECIPIENT_SUBMITTER:
                ReportEmailSender::sendMailToUser($this, $this->getUserSubmit(), $content);
                $logId = ReportLog::addLog($this->id, ReportLog::TYPE_MAILTO_SUBMITTER, $content);
                break;
            case ReportEmailTemplate::RECIPIENT_CACHEOWNER:
                ReportEmailSender::sendMailToUser($this, $this->getCache()->getOwner(), $content);
                $logId = ReportLog::addLog($this->id, ReportLog::TYPE_MAILTO_CACHEOWNER, $content);
                break;
        }
        $this->updateLastChanged();
        $this->saveReport();
        $this->sendWatchEmails($logId);
        if ($this->userIdLeader != self::USER_NOBODY && self::getCurrentUser()->getUserId() != $this->userIdLeader && ! $this->isReportWatched($this->userIdLeader)) {
            // If somebody adds note to the report assigned to another user - inform leader even if he don't watch this report
            ReportEmailSender::sendReportWatch($this, $this->getUserLeader(), $logId);
        }
    }

 /**
  * Create new poll in report.
  *
  * @param int $period  // in days
  * @param string $question
  * @param string $ans1
  * @param string $ans2
  * @param string $ans3
  */
    public function createPoll($period, $question, $ans1, $ans2, $ans3 = null)
    {
        $pollId = ReportPoll::createPoll($this->id, $period, $question, $ans1, $ans2, $ans3);
        $this->updateLastChanged();
        $this->saveReport();
        ReportLog::addLog($this->id, ReportLog::TYPE_POLL, null, $pollId);
        if ($this->status != self::STATUS_LOOK_HERE) {
            $this->changeStatus(self::STATUS_LOOK_HERE, true);
        }
        $userlist = Report::getOcTeamArray();
        foreach ($userlist as $user) { // Send mails to all OC Team members
            if ($user['user_id'] != $this->userIdLastChange) { // Don't notify logged user
                ReportEmailSender::sendNewPoll(new ReportPoll(['pollId' => $pollId]), new User(['userId' => $user['user_id']]));
            }
        }
    }

    // TODO: This method should use GeoCacheLog, but for now this object has no code to save obj.
    /**
     * Adds $content as OC Team log to reported cache
     *
     * @param string $content
     * @return boolean
     */
    public function addOcTeamLog($content = '')
    {
        $query = '
            INSERT INTO `cache_logs`
            (`cache_id`, `user_id`, `type`, `date`, `text`, `text_html`,
            `text_htmledit`, `last_modified`, `uuid`, `date_created`, `node`)
            VALUES
            (:cache_id, :user_id, :type, NOW(), :text, 1, 1, NOW(), :uuid,
            NOW(), :node)';
        $params = [];
        $params['cache_id']['value'] = $this->cacheId;
        $params['cache_id']['data_type'] = 'integer';
        $params['user_id']['value'] = $this->getCurrentUser()->getUserId();
        $params['user_id']['data_type'] = 'integer';
        $params['type']['value'] = GeoCacheLogCommons::LOGTYPE_ADMINNOTE;
        $params['type']['data_type'] = 'integer';
        $params['text']['value'] = nl2br(strip_tags($content));
        $params['text']['data_type'] = 'string';
        $params['uuid']['value'] = Uuid::create();
        $params['uuid']['data_type'] = 'string';
        $params['node']['value'] = OcConfig::instance()->getOcNodeId();
        $params['node']['data_type'] = 'string';
        return (self::db()->paramQuery($query, $params) !== null);
    }

    /**
     * "Touch" report. Update last change date and last change user.
     * This method don't save "touched" report!
     */
    public function updateLastChanged() {
        unset($this->dateLastChange);
        $this->dateLastChange = new \DateTime('now');
        $this->userIdLastChange = $this->getCurrentUser()->getUserId();
        unset($this->userLastChange);
        $this->userLastChange = $this->getCurrentUser();
    }

    /**
     * Sends $logId to users who watch report except logged user and users in $excludeUsers array of userId's
     *
     * @param int $logId
     * @param array $excludeUsers
     */
    public function sendWatchEmails($logId, $excludeUsers = []) {
        $userlist = ReportWatches::getWatchersByReportId($this->id);
        foreach ($userlist as $user) {
            if (in_array($user['user_id'], $excludeUsers) || $user['user_id'] == self::getCurrentUser()->getUserId()) {
                continue;
            }
            ReportEmailSender::sendReportWatch($this, new User(['userId' => $user['user_id']]), $logId);
        }
    }

    /**
     * Saves report to DB
     *
     * @return boolean
     */
    public function saveReport() {
        if (! $this->isDataLoaded()) {
            return false;
        }
        if ($this->uuid == null || $this->uuid == '') {
            $this->uuid = Uuid::create();
        }
        if (self::isValidReportId($this->id)) {
            return $this->saveToDb();
        } else {
            return $this->insertToDb();
        }
    }

    /**
     * Saves object to DB. If you want to save Report - use saveReport()!
     *
     * @return boolean
     */
    private function saveToDb() {
        $query = '
            UPDATE `reports`
             SET `uuid` = :uuid,
            `object_type` = :object_type,
            `user_id` = :user_id,
            `cache_id` = :cache_id,
            `PowerTrail_id` = :PowerTrail_id,
            `type` = :type,
            `text` = :text,
            `note` = :note,
            `submit_date` = :submit_date,
            `status` = :status,
            `secret` = :secret,
            `changed_by` = :changed_by,
            `changed_date` = :changed_date,
            `responsible_id` = :responsible_id
            WHERE `id` = :id';
        $params = [];
        $params['uuid']['value'] = $this->uuid;
        $params['uuid']['data_type'] = ($this->uuid == null) ? 'null' : 'string';
        $params['object_type']['value'] = (int) $this->objectType;
        $params['object_type']['data_type'] = 'integer';
        $params['user_id']['value'] = (int) $this->userIdSubmit;
        $params['user_id']['data_type'] = 'integer';
        $params['cache_id']['value'] = $this->cacheId;
        $params['cache_id']['data_type'] = ($this->cacheId == null) ? 'null' : 'integer';
        $params['PowerTrail_id']['value'] = $this->powerTrailId;
        $params['PowerTrail_id']['data_type'] = ($this->powerTrailId == null) ? 'null' : 'integer';
        $params['type']['value'] = (int) $this->type;
        $params['type']['data_type'] = 'integer';
        $params['text']['value'] = $this->content;
        $params['text']['data_type'] = 'string';
        $params['note']['value'] = $this->note;
        $params['note']['data_type'] = 'string';
        $params['submit_date']['value'] = $this->dateSubmit->format(OcConfig::instance()->getDbDateTimeFormat());
        $params['submit_date']['data_type'] = 'string';
        $params['status']['value'] = (int) $this->status;
        $params['status']['data_type'] = 'integer';
        $params['secret']['value'] = $this->secret;
        $params['secret']['data_type'] = 'string';
        $params['changed_by']['value'] = (int) $this->userIdLastChange;
        $params['changed_by']['data_type'] = 'integer';
        $params['changed_date']['value'] = $this->dateLastChange->format(OcConfig::instance()->getDbDateTimeFormat());
        $params['changed_date']['data_type'] = 'string';
        $params['responsible_id']['value'] = $this->userIdLeader;
        $params['responsible_id']['data_type'] = ($this->userIdLeader == null) ? 'null' : 'integer';
        $params['id']['value'] = (int) $this->id;
        $params['id']['data_type'] = 'integer';
        return (self::db()->paramQuery($query, $params) !== null);
    }

    /**
     * Inserts object to DB. Use saveReport() instead of this!
     * NOT TESTED!!!
     * @return int
     */
    private function insertToDb() {
        $query = '
            INSERT INTO `reports`
            (`object_type`, `user_id`, `cache_id`,
            `PowerTrail_id`, `type`, `text`,
            `note`, `submit_date`, `status`, `secret`,
            `changed_by`, `changed_date`, `responsible_id`)
            VALUES
            (:object_type, :user_id, :cache_id,
            :PowerTrail_id, :type, :text,
            :note, :submit_date, :status, :secret
            :changed_by, :changed_date, :responsible_id)';
        $params = [];
        $params['object_type']['value'] = $this->objectType;
        $params['object_type']['data_type'] = 'integer';
        $params['user_id']['value'] = $this->userIdSubmit;
        $params['user_id']['data_type'] = 'integer';
        $params['cache_id']['value'] = $this->cacheId;
        $params['cache_id']['data_type'] = ($this->cacheId == null) ? 'null' : 'integer';
        $params['PowerTrail_id']['value'] = $this->powerTrailId;
        $params['PowerTrail_id']['data_type'] = ($this->powerTrailId == null) ? 'null' : 'integer';
        $params['type']['value'] = $this->type;
        $params['type']['data_type'] = 'integer';
        $params['text']['value'] = $this->content;
        $params['text']['data_type'] = 'string';
        $params['note']['value'] = $this->note;
        $params['note']['data_type'] = 'string';
        $params['submit_date']['value'] = $this->dateSubmit->format(OcConfig::instance()->getDbDateTimeFormat());
        $params['submit_date']['data_type'] = 'string';
        $params['status']['value'] = $this->status;
        $params['status']['data_type'] = 'integer';
        $params['secret']['value'] = $this->secret;
        $params['secret']['data_type'] = 'string';
        $params['changed_by']['value'] = $this->userIdLastChange;
        $params['changed_by']['data_type'] = 'integer';
        $params['changed_date']['value'] = $this->dateLastChange->format(OcConfig::instance()->getDbDateTimeFormat());
        $params['changed_date']['data_type'] = 'string';
        $params['responsible_id']['value'] = $this->userIdLeader;
        $params['responsible_id']['data_type'] = 'integer';
        $params['id']['value'] = $this->id;
        $params['id']['data_type'] = 'integer';
        self::db()->paramQuery($query, $params);
        $this->id = self::db()->lastInsertId();
        return $this->id;
    }
}
