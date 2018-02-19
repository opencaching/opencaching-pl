<?php
namespace lib\Objects\Admin;

use Utils\Generators\Uuid;
use lib\Objects\BaseObject;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\GeoCacheLogCommons;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\User\User;
use Controllers\Admin\ReportsController;

class Report extends BaseObject
{
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
    private $objectType = ReportCommons::OBJECT_CACHE;

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
    private $powerTrailId = null;

    /**
     * Type of the report (reason - see ReportCommons::TYPE_*)
     *
     * @var int
     */
    private $type = null;

    /**
     * Text content of the report
     *
     * @var string
     */
    private $content = '';

    /**
     * History of the report - OBSOLETE, for historic compatibility only.
     * Use ReportLog instead!
     *
     * @var string
     */
    private $note = '';

    /**
     * Status of the report (see ReportCommons::STATUS_*)
     *
     * @var int
     */
    private $status = ReportCommons::STATUS_NEW;

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
    private $uuid = null;

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

    /*
     * Getters
     */

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
        if ($this->userSubmit == null && $this->dataLoaded) {
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
        if ($this->userLeader == null && $this->dataLoaded) {
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
        if ($this->userLastChange == null && $this->dataLoaded) {
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
        if ($this->cache == null && $this->dataLoaded) {
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
        return ReportCommons::ReportTypeTranslationKey($this->type);
    }
    
    /**
     * Returns translation key of status of the current report
     *
     * @return string
     */
    public function getReportStatusTranslationKey()
    {
        return ReportCommons::ReportStatusTranslationKey($this->status);
    }

    public function getSecretLink()
    {
        return null; //TODO!!!
    }

    /*
     * Setters
     */

    public function setUserIdSubmit($userIdSubmit)
    {
        $this->userIdSubmit = $userIdSubmit;
        unset($this->userSubmit);
        $this->userSubmit = null;
    }

    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;
    }

    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;
        unset($this->cache);
        $this->cache = null;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setDateSubmit($dateSubmit)
    {
        unset($this->dateSubmit);
        $this->dateSubmit = $dateSubmit;
    }

    /**
     * Returns CSS class name coresponding to delay of report management.
     * It counts how many days report is unassigned or has no activity.
     *
     * @return string
     */
    public function getReportStyle()
    {
        if ($this->status == ReportCommons::STATUS_IN_PROGRESS || $this->status == ReportCommons::STATUS_LOOK_HERE) {
            $interval = $this->dateLastChange->diff(new \DateTime('now'))->days;
        } elseif ($this->status == ReportCommons::STATUS_NEW) {
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
        return ReportCommons::getReportStatusClass($this->status);
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
     * @return string
     */
    public function getLinkToReport()
    {
        return ReportCommons::getLinkToReport($this->id);
    }

    /**
     * Check if report has active polls. If no -> returns ReportCommons::POLLS_NOACTIVE
     * If yes, checks if user not yet voted at least one of them.
     * If not voted - returns ReportCommons::POLLS_ACTIVE. If voted all polls -
     * returns ReportCommons::POLLS_ACTIVE_VOTED
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
            return ReportCommons::POLLS_NOACTIVE;
        }
        foreach ($polls as $poll) {
            if (! $poll->userVoted()) {
                return ReportCommons::POLLS_ACTIVE;
            }
        }
        return ReportCommons::POLLS_ACTIVE_VOTED;
    }

    /**
     * Changes leader of report to $newLeader, changes "last change" data and sends notifications
     *
     * @param int $newLeader
     * @return boolean
     */
    public function changeLeader($newLeader)
    {
        if (! $this->dataLoaded) {
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
        if (! $this->isReportWatched($oldLeaderId) && ! is_null($oldLeaderId)) { // If previeous leader don't watch this report - inform him anyway
            ReportEmailSender::sendReportWatch($this, new User(['userId' => $oldLeaderId]), $logId);
        }
        if ($this->status == ReportCommons::STATUS_NEW) { // If sb assign user to new report -> change status to "In progress"
            $this->changeStatus(ReportCommons::STATUS_IN_PROGRESS);
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
        if (! $this->dataLoaded) {
            return false;
        }
        if ($newStatus != ReportCommons::STATUS_LOOK_HERE && ! empty(ReportPoll::getActivePolls($this->id))) { // If polls are active - status should be Look Here!
            return false;
        }
        $this->status = $newStatus;
        $this->updateLastChanged();
        $this->saveReport();
        $logId = ReportLog::addLog($this->id, ReportLog::TYPE_CHANGESTATUS, tr($this->getReportStatusTranslationKey()));
        if (! $silent) {
            // Send notification about new status
            if ($this->status == ReportCommons::STATUS_LOOK_HERE) {
                $userlist = ReportCommons::getOcTeamArray();
                foreach ($userlist as $user) { // Send mails to all OC Team members
                    if ($user['user_id'] != $this->userIdLastChange) { // Don't notify logged user
                        $usr = new User(['userId' => $user['user_id']]);
                        ReportEmailSender::sendReportLookHere($this, $usr);
                        unset($usr);
                    }
                }
            } else { //Status changed NOT to look here
                $this->sendWatchEmails($logId); // If it is not change to "Look here", send standard watch mails
                if ($this->userIdLeader != ReportCommons::USER_NOBODY && self::getCurrentUser()->getUserId() != $this->userIdLeader && ! $this->isReportWatched($this->userIdLeader)) {
                    // If somebody change status of the report assigned to another user - inform leader even if he don't watch this report
                    ReportEmailSender::sendReportWatch($this, $this->getUserLeader(), $logId);
                }
            }
        }
        if ($this->userIdLeader == null && $newStatus != ReportCommons::STATUS_NEW && $newStatus != ReportCommons::STATUS_LOOK_HERE) {
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
        if (! $this->dataLoaded) {
            return false;
        }
        $submittedNote = strip_tags($submittedNote, '<br>');
        $submittedNote = nl2br($submittedNote);
        $this->updateLastChanged();
        $this->saveReport();
        $logId = ReportLog::addLog($this->id, ReportLog::TYPE_NOTE, $submittedNote);
        $this->sendWatchEmails($logId);
        if ($this->userIdLeader != ReportCommons::USER_NOBODY && self::getCurrentUser()->getUserId() != $this->userIdLeader && ! $this->isReportWatched($this->userIdLeader)) {
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
        if ($this->userIdLeader != ReportCommons::USER_NOBODY && self::getCurrentUser()->getUserId() != $this->userIdLeader && ! $this->isReportWatched($this->userIdLeader)) {
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
        if ($this->status != ReportCommons::STATUS_LOOK_HERE) {
            $this->changeStatus(ReportCommons::STATUS_LOOK_HERE, true);
        }
        $userlist = ReportCommons::getOcTeamArray();
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
        if (! $this->isDataComplete()) {
            return null;
        }
        if ($this->uuid == null || $this->uuid == '') {
            $this->uuid = Uuid::create();
        }
        if (ReportCommons::isValidReportId($this->id)) {
            return $this->saveToDb();
        } else {
            return $this->insertToDb();
        }
    }

    private function isDataComplete($checkId = false)
    {
        if ($checkId && $this->id === null) {
            return false;
        } elseif ($this->userIdSubmit === null
            || ($this->cacheId === null && $this->powerTrailId === null)
            || $this->type === null) {
            return false;
        }
        $this->dataLoaded = true;
        return true;
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
        $params = $this->buildSaveQueryParams();
        $params['id']['value'] = (int) $this->id;
        $params['id']['data_type'] = 'integer';
        return (self::db()->paramQuery($query, $params) !== null);
    }

    /**
     * Inserts object to DB. Use saveReport() instead of this!
     *
     * @return int
     */
    private function insertToDb() {
        $query = '
            INSERT INTO `reports`
            (`uuid`, `object_type`, `user_id`, `cache_id`,
            `PowerTrail_id`, `type`, `text`,
            `note`, `submit_date`, `status`, `secret`,
            `changed_by`, `changed_date`, `responsible_id`)
            VALUES
            (:uuid, :object_type, :user_id, :cache_id,
            :PowerTrail_id, :type, :text,
            :note, :submit_date, :status, :secret,
            :changed_by, :changed_date, :responsible_id)';
        if (self::db()->paramQuery($query, $this->buildSaveQueryParams()) == null) {
            return null;
        }
        $this->id = self::db()->lastInsertId();
        return $this->id;
    }

    private function buildSaveQueryParams()
    {
        $params = [];
        $params['uuid']['value'] = $this->uuid;
        $params['uuid']['data_type'] = ($this->uuid == null) ? 'null' : 'string';
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
        $params['submit_date']['value'] = ($this->dateSubmit !== null) ? $this->dateSubmit->format(OcConfig::instance()->getDbDateTimeFormat()) : 'NOW()';
        $params['submit_date']['data_type'] = 'string';
        $params['status']['value'] = $this->status;
        $params['status']['data_type'] = 'integer';
        $params['secret']['value'] = $this->secret;
        $params['secret']['data_type'] = ($this->secret !== null) ? 'string' : 'null';
        $params['changed_by']['value'] = ($this->userIdLastChange !== null) ? $this->userIdLastChange : 0;
        $params['changed_by']['data_type'] = 'integer';
        if ($this->dateLastChange === null) {
            $params['changed_date']['value'] =  null;
            $params['changed_date']['data_type'] = 'null';
        } else {
            $params['changed_date']['value'] = $this->dateLastChange->format(OcConfig::instance()->getDbDateTimeFormat());
            $params['changed_date']['data_type'] = 'string';
        }
        $params['responsible_id']['value'] = $this->userIdLeader;
        $params['responsible_id']['data_type'] = ($this->userIdLeader !== null) ? 'integer' : 'null';
        return $params;
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
                    $this->userIdLeader = ($val == ReportCommons::USER_NOBODY) ? null : $val;
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
        $this->isDataComplete(true);
    }

    public static function fromDbRowFactory(array $dbRow)
    {
        $n = new self();
        $n->loadFromDbRow($dbRow);
        return $n;
    }
}
