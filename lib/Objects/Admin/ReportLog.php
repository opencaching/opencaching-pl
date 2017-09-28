<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;
use lib\Objects\User\User;
use lib\Objects\OcConfig\OcConfig;

class ReportLog extends BaseObject
{

    /**
     * Log types
     */
    // Just note
    const TYPE_NOTE = 1;

    // User x changed status to y
    const TYPE_CHANGESTATUS = 2;

    // User x changed leader to x/y
    const TYPE_CHANGELEADER = 3;

    // User x send mail to the report submitter
    const TYPE_MAILTO_SUBMITTER = 4;

    // User x send mail to cacheowner
    const TYPE_MAILTO_CACHEOWNER = 5;

    // User x send mail to cacheowner and report submitter
    const TYPE_MAILTO_BOTH = 6;

    // User x created a poll
    const TYPE_POLL = 7;

    // User x canceled the poll
    const TYPE_POLL_CANCEL = 8;

    // User x added OC Team log to the cache
    const TYPE_CACHELOG_ADD = 9;

    /**
     * Id of ReportLog
     * @var int
     */
    private $id = null;

    /**
     * Id of report associated with ReportLog
     * @var int
     */
    private $reportId;

    /**
     * Report object, asocciated with ReportLog
     * @var Report
     */
    private $report;

    /**
     * Type of log. See self::TYPE_*
     * @var int
     */
    private $type;

    /**
     * Content of log
     * @var string
     */
    private $content = null;

    /**
     * UserId of user who submitted ReportLog
     * @var int
     */
    private $userId;

    /**
     * User object of user who submitted ReportLog
     * @var User
     */
    private $user = null;

    /**
     * Poll associated with ReportLog (if any, null otherwise)
     * @var int
     */
    private $pollId = null;

    /**
     * ReportPoll object - asocciated with ReportLog when report is type TYPE_POLL
     * @var ReportPoll
     */
    private $poll;

    /**
     * DateTime object of ReportLog creation date
     * @var \DateTime
     */
    private $dateCreated;

    public function __construct(array $params = [])
    {
        parent::__construct();
        if (isset($params['logId'])) {
            $this->loadById($params['logId']);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function GetReportId()
    {
        return $this->reportId;
    }

    public function getReport()
    {
        if ($this->report == null && $this->dataLoaded) {
            $this->report = new Report( ['reportId' => $this->reportId]);
        }
        return $this->report;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUser()
    {
        if ($this->user == null && $this->dataLoaded) {
            $this->user = new User( ['userId' => $this->userId]);
        }
        return $this->user;
    }

    public function getPollId()
    {
        return $this->pollId;
    }

    public function getPoll()
    {
        if ($this->poll == null && $this->dataLoaded) {
            $this->poll = new ReportPoll( ['pollId' => $this->pollId]);
        }
        return $this->poll;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function setReportId($reportId)
    {
        $this->reportId = $reportId;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        unset($this->user);
        $this->user = null;
    }

    public function setPollId($pollId)
    {
        $this->pollId = $pollId;
    }

    /**
     * Returns ready - HTML formatted log for reports
     * Appearance of log entry depends of log type
     *
     * @return string
     */
    public function getFormattedLog() {
        if (! $this->dataLoaded) {
            return null;
        }
        $content = '';
        switch ($this->type) {
            case self::TYPE_NOTE:
                $header = tr('admin_reports_tpl_note') . ':';
                $content = $this->content;
                break;
            case self::TYPE_CHANGESTATUS:
                $header = tr('admin_reports_tpl_status') . ': ' . $this->content;
                break;
            case self::TYPE_CHANGELEADER:
                $header = tr('admin_reports_tpl_leader') . ': ' . $this->content;
                break;
            case self::TYPE_MAILTO_SUBMITTER:
                $header = tr('admin_reports_tpl_mail2sub');
                $content = $this->content;
                break;
            case self::TYPE_MAILTO_CACHEOWNER:
                $header = tr('admin_reports_tpl_mail2co');
                $content = $this->content;
                break;
            case self::TYPE_MAILTO_BOTH:
                $header = tr('admin_reports_tpl_mail2all');
                $content = $this->content;
                break;
            case self::TYPE_POLL:
                $header = tr('admin_reports_tpl_poll');
                $header .= ' (' . $this->getPoll()->getDateStart()->format(OcConfig::instance()->getDatetimeFormat());
                $header .= ' - ' . $this->getPoll()->getDateEnd()->format(OcConfig::instance()->getDatetimeFormat()) . ')';
                if ($this->getPoll()->isPollActive()) { // Poll is active, hide some data
                    $content = tr('admin_reports_lbl_question') . ': <em>' . $this->getPoll()->getQuestion() . '</em><br>';
                    $content .= tr('admin_reports_lbl_ans') . ' 1: <i>' . $this->getPoll()->getAns1() . '</i><br>';
                    $content .= tr('admin_reports_lbl_ans') . ' 2: <i>' . $this->getPoll()->getAns2() . '</i><br>';
                    if ($this->getPoll()->getAns3() != null) {
                        $content .= tr('admin_reports_lbl_ans') . ' 3: <i>' . $this->getPoll()->getAns3() . '</i><br>';
                    }
                } else { // Poll has ended. Show results
                    if ($this->getPoll()->getVotesCount() > 0) { // People voted in poll
                        $content = '<div id="chart-poll-' . $this->getPoll()->getId() . '" class="report-poll"></div>';
                        $content .= tr('admin_reports_lbl_voters') . ': ' . $this->getPoll()->getVotersList(',');
                    } else { // Nobody voted
                        $content = tr('admin_reports_lbl_question') . ': <em>' . $this->getPoll()->getQuestion() . '</em><br>';
                        $content .= tr('admin_reports_lbl_ans') . ' 1: <i>' . $this->getPoll()->getAns1() . '</i><br>';
                        $content .= tr('admin_reports_lbl_ans') . ' 2: <i>' . $this->getPoll()->getAns2() . '</i><br>';
                        if ($this->getPoll()->getAns3() != null) {
                            $content .= tr('admin_reports_lbl_ans') . ' 3: <i>' . $this->getPoll()->getAns3() . '</i><br>';
                        }
                        $content .= '<strong>' . tr('admin_reports_poll_novotes') . '</strong>';
                    }
                }
                break;
            case self::TYPE_POLL_CANCEL:
                $header = tr('admin_reports_tpl_pollcancel');
                $header .= ' (' . $this->getPoll()->getDateStart()->format(OcConfig::instance()->getDatetimeFormat());
                $header .= ' - ' . $this->getPoll()->getDateEnd()->format(OcConfig::instance()->getDatetimeFormat()) . ')';
                $content = tr('admin_reports_lbl_question') . ': <em>' . $this->getPoll()->getQuestion() . '</em><br>';
                $content .= tr('admin_reports_lbl_ans') . ' 1: <i>' . $this->getPoll()->getAns1() . '</i><br>';
                $content .= tr('admin_reports_lbl_ans') . ' 2: <i>' . $this->getPoll()->getAns2() . '</i><br>';
                if ($this->getPoll()->getAns3() != null) {
                    $content .= tr('admin_reports_lbl_ans') . ' 3: <i>' . $this->getPoll()->getAns3() . '</i><br>';
                }
                break;
            case self::TYPE_CACHELOG_ADD:
                $header = tr('admin_reports_tpl_log') . ':';
                $content = $this->content;
                break;
        }
        $header = mb_ereg_replace('{user}', $this->getUser()->getUserName(), $header);
        $output = '[' . $this->dateCreated->format(OcConfig::instance()->getDatetimeFormat()) . '] ';
        $output .= '<strong>' . $header . '</strong>';
        if ($content != null || $content != '') {
            $output .= '<br>' . $content;
        }
        $output .= '<br>';
        return $output;
    }

    /**
     * This is a static version of getFormattedLog()
     *
     * @param int $logId
     * @return string
     */
    public static function getFormattedLogById($logId) {
        $log = new ReportLog(['logId' => $logId]);
        $output = $log->getFormattedLog();
        unset($log);
        return $output;
    }

    /**
     * Returns array of ReportLog object for given $reportId
     *
     * @param int $reportId
     * @return ReportLog[]
     */
    public static function getLogs($reportId) {
        $query = '
            SELECT *
            FROM `reports_log`
            WHERE `report_id` = :report_id
            ORDER BY `id` DESC';
        $params = [];
        $params['report_id']['value'] = $reportId;
        $params['report_id']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);

        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    /**
     * Adds new ReportLog to DB, returns id of new log
     *
     * @param int $reportId
     * @param int $type
     * @param string $content
     * @param int $pollId
     * @return int
     */
    public static function addLog($reportId, $type, $content = null, $pollId = null)
    {
        if (!in_array($type, self::getTypeArray())) {
            return null;
        }
        $newlog = new ReportLog();
        $newlog->setReportId($reportId);
        $newlog->setType($type);
        $newlog->setUserId(self::getCurrentUser()->getUserId());
        $newlog->setContent($content);
        $newlog->setPollId($pollId);
        $id = $newlog->insertToDb();
        unset($newlog);
        return $id;
    }

    /**
     * Returns array of allowed ReportLog types
     *
     * @return string[]
     */
    public static function getTypeArray()
    {
        return ([
            self::TYPE_NOTE,
            self::TYPE_CHANGESTATUS,
            self::TYPE_CHANGELEADER,
            self::TYPE_MAILTO_SUBMITTER,
            self::TYPE_MAILTO_CACHEOWNER,
            self::TYPE_MAILTO_BOTH,
            self::TYPE_POLL,
            self::TYPE_POLL_CANCEL,
            self::TYPE_CACHELOG_ADD
        ]);
    }

    private function loadFromDbRow(array $dbRow)
    {
        foreach ($dbRow as $key => $val) {
            switch ($key) {
                case 'id':
                    $this->id = (int) $val;
                    $this->dataLoaded = true;
                    break;
                case 'report_id':
                    $this->reportId = $val;
                    unset($this->report);
                    $this->report = null;
                    break;
                case 'type':
                    $this->type = (int) $val;
                    break;
                case 'content':
                    $this->content = $val;
                    break;
                case 'user_id':
                    $this->userId = $val;
                    unset($this->user);
                    $this->user = null;
                    break;
                case 'poll_id':
                    $this->pollId = (int) $val;
                    unset($this->poll);
                    $this->poll = null;
                    break;
                case 'date_created':
                    $this->dateCreated = new \DateTime($val);
                    break;
                default:
                    error_log(__METHOD__ . ": Unknown column: $key");
            }
        }
    }

    private function loadById($reportId)
    {
        $query = 'SELECT * FROM `reports_log` WHERE id = :id LIMIT 1';
        $params = [];
        $params['id']['value'] = $reportId;
        $params['id']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);
        $dbRow = self::db()->dbResultFetch($stmt);
        if (is_array($dbRow)) {
            $this->loadFromDbRow($dbRow);
        } else {
            $this->dataLoaded = false;
        }
    }

    private static function fromDbRowFactory(array $dbRow)
    {
        $n = new self();
        $n->loadFromDbRow($dbRow);
        return $n;
    }

    /**
     * Inserts ReportLog as new log in DB, returns id of new ReportLog Id
     *
     * @return int
     */
    private function insertToDb() {
        $query = '
            INSERT INTO `reports_log`
            (`report_id`, `type`, `content`, `user_id`, `poll_id`, `date_created`)
            VALUES
            (:report_id, :type, :content, :user_id, :poll_id, NOW())';
        $params = [];
        $params['report_id']['value'] = $this->reportId;
        $params['report_id']['data_type'] = 'integer';
        $params['type']['value'] = $this->type;
        $params['type']['data_type'] = 'integer';
        $params['content']['value'] = $this->content;
        $params['content']['data_type'] = ($this->content == null) ? 'null' : 'string';
        $params['user_id']['value'] = $this->userId;
        $params['user_id']['data_type'] = 'integer';
        $params['poll_id']['value'] = $this->pollId;
        $params['poll_id']['data_type'] = ($this->pollId == null) ? 'null' : 'integer';
        self::db()->paramQuery($query, $params);
        $this->id = self::db()->lastInsertId();
        return $this->id;
    }
}
