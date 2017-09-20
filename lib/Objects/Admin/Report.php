<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\User\User;
use lib\Objects\OcConfig\OcConfig;
use Utils\Generators\Uuid;

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

    const USER_NOBODY = 0;
    const USER_ALL = - 1;
    const USER_YOU = - 2;
    const USER_YOU2 = - 3;

    // Types of objects reported
    const OBJECT_CACHE = 1;
    const OBJECT_POWERTRAIL = 2;

    /**
     * ID of the report
     *
     * @var int
     */
    private $id = null;

    /**
     * Id of who submits the report
     *
     * @var int
     */
    private $userIdSubmit = null;

    /**
     * User who submits the report
     *
     * @var User
     */
    private $userSubmit = null;

    /**
     * Id of user who is leader of the report
     *
     * @var int
     */
    private $userIdLeader = null;

    /**
     * User who is leader of the report
     *
     * @var User
     */
    private $userLeader = null;

    /**
     * Id of user who last changed status of the report
     *
     * @var int
     */
    private $userIdChangeStatus = null;

    /**
     * User who last changed status of the report
     *
     * @var User
     */
    private $userChangeStatus = null;

    /**
     * Type of object reported
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
     * Cache reported
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
     * Type of the report
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
     * History of the report
     *
     * @var string
     */
    private $note;

    /**
     * Status of the report
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
     * Date of last status change
     *
     * @var \DateTime
     */
    private $dateChangeStatus = null;

    /**
     *
     * @var string
     */
    private $uuid;

    public function __construct(array $params = array())
    {
        parent::__construct();
        if (isset($params['reportId'])) {
            $this->loadById($params['reportId']);
        }
    }

    private function loadById($reportId)
    {
        $query = 'SELECT * FROM `reports` WHERE id = :1 LIMIT 1';
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
                    $this->userIdChangeStatus = ($val == 0) ? null : $val;
                    break;
                case 'changed_date':
                    $this->dateChangeStatus = ($val == null || $val == '') ? null : new \DateTime($val);
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

    public function getUserIdSubmit()
    {
        return $this->userIdSubmit;
    }

    public function getUserSubmit()
    {
        if ($this->userSubmit == null && $this->isDataLoaded()) {
            $this->userSubmit = new User(array(
                'userId' => $this->userIdSubmit
            ));
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
            $this->userLeader = new User(array(
                'userId' => $this->userIdLeader
            ));
        }
        return $this->userLeader;
    }

    public function getUserIdChangeStatus()
    {
        return $this->userIdChangeStatus;
    }

    public function getUserChangeStatus()
    {
        if ($this->userChangeStatus == null && $this->isDataLoaded()) {
            $this->userChangeStatus = new User(array(
                'userId' => $this->userIdChangeStatus
            ));
        }
        return $this->userChangeStatus;
    }

    public function getCacheId()
    {
        return $this->cacheId;
    }

    public function getCache()
    {
        if ($this->cache == null && $this->isDataLoaded()) {
            $this->cache = new GeoCache(array(
                'cacheId' => $this->cacheId
            ));
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

    public function getDateChangeStatus()
    {
        return $this->dateChangeStatus;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getObjectType()
    {
        return $this->objectType;
    }

    public function getPowerTrailId()
    {
        return $this->powerTrailId;
    }

    public function getReportTypeTranslationKey()
    {
        return self::ReportTypeTranslationKey($this->type);
    }

    public function getReportStatusTranslationKey()
    {
        return self::ReportStatusTranslationKey($this->status);
    }

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

    public static function ReportUserTranslationKey($status)
    {
        switch ($status) {
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

    public static function getReports(User $currentUser, $waypoint = '', $type = self::DEFAULT_REPORTS_TYPE, $status = self::DEFAULT_REPORTS_STATUS, $user = self::DEFAULT_REPORTS_USER, $offset = 0, $limit = self::REPORTS_PER_PAGE)
    {
        $params = array();
        $params['limit']['value'] = $limit;
        $params['limit']['data_type'] = 'integer';
        $params['offset']['value'] = $offset;
        $params['offset']['data_type'] = 'integer';
        $query = 'SELECT reports.* FROM reports';
        if ($waypoint != '' and ! is_null($waypoint)) {
            $query .= ' INNER JOIN caches ON reports.cache_id = caches.cache_id
                    WHERE (caches.wp_oc LIKE :waypoint OR caches.name LIKE :waypoint)';
            $params['waypoint']['value'] = '%' . $waypoint . '%';
            $params['waypoint']['data_type'] = 'string';
        } else {
            $query .= ' WHERE 1';
        }
        if ($type == self::TYPE_ALL) {
            $query .= '';
        } else {
            $query .= ' AND reports.type = :type';
            $params['type']['value'] = $type;
            $params['type']['data_type'] = 'integer';
        }
        if ($status == self::STATUS_ALL) {
            $query .= '';
        } elseif ($status == self::STATUS_OPEN) {
            $query .= ' AND reports.status != :status';
            $params['status']['value'] = self::STATUS_CLOSED;
            $params['status']['data_type'] = 'integer';
        } else {
            $query .= ' AND reports.status = :status';
            $params['status']['value'] = $status;
            $params['status']['data_type'] = 'integer';
        }
        if ($user == self::USER_ALL) {
            $query .= '';
        } elseif ($user == self::USER_YOU) {
            $query .= ' AND reports.responsible_id = :user';
            $params['user']['value'] = $currentUser->getUserId();
            $params['user']['data_type'] = 'integer';
        } elseif ($user == self::USER_YOU2) {
            $query .= ' AND (reports.responsible_id = :user OR reports.responsible_id IS NULL OR reports.status = :statuslook)';
            $params['user']['value'] = $currentUser->getUserId();
            $params['user']['data_type'] = 'integer';
            $params['statuslook']['value'] = self::STATUS_LOOK_HERE;
            $params['statuslook']['data_type'] = 'integer';
        } elseif ($user == self::USER_NOBODY) {
            $query .= ' AND (reports.responsible_id IS NULL OR reports.status = :statuslook)';
            $params['statuslook']['value'] = self::STATUS_LOOK_HERE;
            $params['statuslook']['data_type'] = 'integer';
        } else {
            $query .= ' AND reports.responsible_id = :user';
            $params['user']['value'] = $user;
            $params['user']['data_type'] = 'integer';
        }
        $query .= ' ORDER BY reports.id DESC LIMIT :limit OFFSET :offset';
        $stmt = self::db()->paramQuery($query, $params);

        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    public static function getReportsCounts(User $currentUser, $waypoint = '', $type = self::DEFAULT_REPORTS_TYPE, $status = self::DEFAULT_REPORTS_STATUS, $user = self::DEFAULT_REPORTS_USER)
    {
        $params = array();
        $query = "SELECT COUNT(*) FROM reports";
        if ($waypoint != '' and ! is_null($waypoint)) {
            $query .= ' INNER JOIN caches ON reports.cache_id = caches.cache_id
                    WHERE (caches.wp_oc LIKE :waypoint OR caches.name LIKE :waypoint)';
            $params['waypoint']['value'] = '%' . $waypoint . '%';
            $params['waypoint']['data_type'] = 'string';
        } else {
            $query .= ' WHERE 1';
        }
        if ($type == self::TYPE_ALL) {
            $query .= ' ';
        } else {
            $query .= ' AND reports.type = :type';
            $params['type']['value'] = $type;
            $params['type']['data_type'] = 'integer';
        }
        if ($status == self::STATUS_ALL) {
            $query .= '';
        } elseif ($status == self::STATUS_OPEN) {
            $query .= ' AND reports.status != :status';
            $params['status']['value'] = self::STATUS_CLOSED;
            $params['status']['data_type'] = 'integer';
        } else {
            $query .= ' AND reports.status = :status';
            $params['status']['value'] = $status;
            $params['status']['data_type'] = 'integer';
        }
        if ($user == self::USER_ALL) {
            $query .= '';
        } elseif ($user == self::USER_YOU) {
            $query .= ' AND reports.responsible_id = :user';
            $params['user']['value'] = $currentUser->getUserId();
            $params['user']['data_type'] = 'integer';
        } elseif ($user == self::USER_YOU2) {
            $query .= ' AND (reports.responsible_id = :user OR reports.responsible_id IS NULL OR reports.status = :statuslook)';
            $params['user']['value'] = $currentUser->getUserId();
            $params['user']['data_type'] = 'integer';
            $params['statuslook']['value'] = self::STATUS_LOOK_HERE;
            $params['statuslook']['data_type'] = 'integer';
        } elseif ($user == self::USER_NOBODY) {
            $query .= ' AND (reports.responsible_id IS NULL OR reports.status = :statuslook)';
            $params['statuslook']['value'] = self::STATUS_LOOK_HERE;
            $params['statuslook']['data_type'] = 'integer';
        } else {
            $query .= ' AND reports.responsible_id = :user';
            $params['user']['value'] = $user;
            $params['user']['data_type'] = 'integer';
        }
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
            $result .= '<option value="' . self::USER_NOBODY . '" selected="selected"></option>';
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
        $query = 'SELECT COUNT(*) FROM reports WHERE id = :reportid';
        $params = array();
        $params['reportid']['value'] = $reportId;
        $params['reportid']['data_type'] = 'integer';
        if (self::db()->paramQueryValue($query, 0, $params) == '1') {
            return true;
        }
        return false;
    }

    /**
     * Returns name of CSS class coresponding to delay of report management.
     * It counts how many days report is unassigned or has no activity.
     *
     * @return string
     */
    public function getReportStyle()
    {
        if ($this->status == self::STATUS_IN_PROGRESS || $this->status == self::STATUS_LOOK_HERE) {
            $interval = $this->dateChangeStatus->diff(new \DateTime('now'))->days;
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
     * Used in i.e.
     * report list to show status in graphic form
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
    private static function getOcTeamArray()
    {
        $query = "SELECT user_id, username FROM user WHERE admin = 1 AND is_active_flag = 1 ORDER BY username";
        $stmt = self::db()->simpleQuery($query);
        return self::db()->dbResultFetchAll($stmt);
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

    public function changeLeader($newLeader)
    {
        if (! $this->isDataLoaded()) {
            return false;
        }
        $this->userIdLeader = $newLeader;
        unset($this->userLeader);
        $this->userLeader = new User(['userId' => $newLeader]);
        $this->updateLastChanged();
        $log = '[' . $this->dateChangeStatus->format(OcConfig::instance()->getDatetimeFormat()) . '] <strong>';
        $log .= $this->userChangeStatus->getUserName();
        $log .= ' ' . tr('admin_reports_tpl_leaderhdr') . ': ' . $this->userLeader->getUserName() . '</strong><br>';
        if ($this->status == self::STATUS_NEW) {
            $this->status = self::STATUS_IN_PROGRESS;
            $log .= '[' . $this->dateChangeStatus->format(OcConfig::instance()->getDatetimeFormat()) . '] <strong>';
            $log .= $this->userChangeStatus->getUserName();
            $log .= ' ' . tr('admin_reports_tpl_statushdr') . ' ' . tr($this->getReportStatusTranslationKey()) . '</strong><br>';
        }
        $this->addLog($log);
// TODO: Jesli nowym prowadzacym nie jest osoba, ktora zmieniła lidera - powiadom nowego lidera
// TODO: Powiadom osoby, które obserwują, z wyjątkiem nowego lidera i zalogowanego użytkownika
    }

    public function changeStatus($newStatus)
    {
        if (! $this->isDataLoaded()) {
            return false;
        }
        $this->status = $newStatus;
        $this->updateLastChanged();
        $log = '[' . $this->dateChangeStatus->format(OcConfig::instance()->getDatetimeFormat()) . '] <strong>';
        $log .= $this->userChangeStatus->getUserName();
        $log .= ' ' . tr('admin_reports_tpl_statushdr') . ': ' . tr($this->getReportStatusTranslationKey()) . '</strong><br>';
        $this->addLog($log);
// TODO: Jesli zmiana na Zajrzyj tu - wyslij maila do wszystkich
// TODO: Powiadom osoby, ktore obserwuja, za wyjątkiem zalogowanego użytkownika
    }

    public function addNote($submittedNote) {
        if (! $this->isDataLoaded()) {
            return false;
        }
        $submittedNote = \userInputFilter::purifyHtmlString($submittedNote);
        $this->updateLastChanged();
        $log = '[' . $this->dateChangeStatus->format(OcConfig::instance()->getDatetimeFormat()) . '] <strong>';
        $log .= $this->userChangeStatus->getUserName();
        $log .= ' ' . tr('admin_reports_tpl_infohdr') . ':</strong><br>' . $submittedNote . '<br>';
        $this->addLog($log);
// TODO: Powiadom osoby, które obserwują
    }

    private function updateLastChanged() {
        unset($this->dateChangeStatus);
        $this->dateChangeStatus = new \DateTime('now');
        $this->userIdChangeStatus = $this->getCurrentUser()->getUserId();
        unset($this->userChangeStatus);
        $this->userChangeStatus = $this->getCurrentUser();
    }
    private function addLog($log) {
        if (! $this->isDataLoaded()) {
            return false;
        }
        $this->note = $log . '<br>' . $this->note;
        $this->saveReport();
    }

    public function saveReport() {
        if (! $this->isDataLoaded()) {
            return false;
        }
        if ($this->uuid == null || $this->uuid == "") {
            $this->uuid = Uuid::create();
        }
        if (self::isValidReportId($this->id)) {
            return $this->saveToDb();
        } else {
            return $this->insertToDb();
        }
    }

    private function saveToDb() {
        $query = "
            UPDATE `reports`
             SET `object_type` = :object_type,
            `user_id` = :user_id,
            `cache_id` = :cache_id,
            `PowerTrail_id` = :PowerTrail_id,
            `type` = :type,
            `text` = :text,
            `note` = :note,
            `submit_date` = :submit_date,
            `status` = :status,
            `changed_by` = :changed_by,
            `changed_date` = :changed_date,
            `responsible_id` = :responsible_id
            WHERE `id` = :id";
        $params = [];
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
        $params['changed_by']['value'] = (int) $this->userIdChangeStatus;
        $params['changed_by']['data_type'] = 'integer';
        $params['changed_date']['value'] = $this->dateChangeStatus->format(OcConfig::instance()->getDbDateTimeFormat());
        $params['changed_date']['data_type'] = 'string';
        $params['responsible_id']['value'] = $this->userIdLeader;
        $params['responsible_id']['data_type'] = ($this->userIdLeader == null) ? 'null' : 'integer';
        $params['id']['value'] = (int) $this->id;
        $params['id']['data_type'] = 'integer';
        return (self::db()->paramQuery($query, $params) !== null);
    }

    private function insertToDb() {
// TODO: Sprawdzić!!!
        $query = "
            INSERT INTO `reports`
            (`object_type`, `user_id`, `cache_id`,
            `PowerTrail_id`, `type`, `text`,
            `note`, `submit_date`, `status`,
            `changed_by`, `changed_date`, `responsible_id`)
            VALUES
            (:object_type, :user_id, :cache_id,
            :PowerTrail_id, :type, :text,
            :note, :submit_date, :status,
            :changed_by, :changed_date, :responsible_id)";
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
        $params['changed_by']['value'] = $this->userIdChangeStatus;
        $params['changed_by']['data_type'] = 'integer';
        $params['changed_date']['value'] = $this->dateChangeStatus->format(OcConfig::instance()->getDbDateTimeFormat());
        $params['changed_date']['data_type'] = 'string';
        $params['responsible_id']['value'] = $this->userIdLeader;
        $params['responsible_id']['data_type'] = 'integer';
        $params['id']['value'] = $this->id;
        $params['id']['data_type'] = 'integer';
        self::db()->paramQuery($query, $params);
        return self::db()->lastInsertId();
    }
}
