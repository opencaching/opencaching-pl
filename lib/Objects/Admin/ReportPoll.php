<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;
use lib\Objects\OcConfig\OcConfig;

class ReportPoll extends BaseObject
{

    // Configuration
    const POLL_INTERVAL_MIN = 3;

    const POLL_INTERVAL_MAX = 7;

    /**
     * ID of the poll
     *
     * @var int
     */
    private $id = null;

    /**
     * ID of the report asocciated with this poll
     *
     * @var int
     */
    private $reportId;

    /**
     * Report object asocciated with this poll
     *
     * @var Report
     */
    private $report = null;

    /**
     * Start time of the poll (creation date)
     *
     * @var \DateTime
     */
    private $dateStart;

    /**
     * End time of the poll
     *
     * @var \DateTime
     */
    private $dateEnd;

    /**
     * Poll's main question
     *
     * @var string
     */
    private $question;

    /**
     * Text of proposed answer 1
     *
     * @var string
     */
    private $ans1;

    /**
     * Text of proposed answer 2
     *
     * @var string
     */
    private $ans2;

    /**
     * Text of proposed answer 3 or null if poll has only 2 answers
     *
     * @var string
     */
    private $ans3 = null;

    public function __construct(array $params = [])
    {
        parent::__construct();
        if (isset($params['pollId'])) {
            $this->loadById($params['pollId']);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getReportId()
    {
        return $this->reportId;
    }

    public function getReport()
    {
        if ($this->report == null && $this->dataLoaded) {
            $this->report = new Report([
                'reportId' => $this->reportId
            ]);
        }
        return $this->report;
    }

    public function getDateStart()
    {
        return $this->dateStart;
    }

    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function getAns1()
    {
        return $this->ans1;
    }

    public function getAns2()
    {
        return $this->ans2;
    }

    public function getAns3()
    {
        return $this->ans3;
    }

    /**
     * Check if pool is active (users can vote)
     *
     * @return boolean
     */
    public function isPollActive()
    {
        if (! $this->dataLoaded) {
            return null;
        }
        if ($this->dateEnd > new \DateTime('now')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Terminate (cancel) poll - if poll is active and noone voted
     *
     * @return boolean
     */
    public function cancelPoll()
    {
        if (! $this->dataLoaded || ! $this->isPollActive() || ($this->getVotesCount() > 0)) {
            return false;
        }
        unset($this->dateEnd);
        $this->dateEnd = new \DateTime('now');
        $query = '
            UPDATE `reports_poll`
            SET `date_end` = :date_end
            WHERE `id` = :id';
        $params = [];
        $params['id']['value'] = $this->id;
        $params['id']['data_type'] = 'integer';
        $params['date_end']['value'] = $this->dateEnd->format(OcConfig::instance()->getDbDateTimeFormat());
        $params['date_end']['data_type'] = 'string';
        return (self::db()->paramQuery($query, $params) !== null);
    }

    /**
     * Adds vote to DB
     *
     * @param int $vote
     * @return boolean
     */
    public function addVote($vote)
    {
        if (! $this->isDataLoaded()) {
            return false;
        } elseif ($this->isPollActive() && ! $this->userVoted() && $vote >= 1 && ($vote <= ($this->ans3 === null) ? 2 : 3)) {
            $this->saveVote($vote);
            return true;
        }
        return false;
    }

    /**
     * Returns bool if logged user already votes current poll
     *
     * @return boolean
     */
    public function userVoted()
    {
        if ($this->id == null) {
            return false;
        }
        $query = '
            SELECT COUNT(*)
            FROM `reports_poll_votes`
            WHERE `poll_id` = :poll_id AND `user_id` = :user_id';
        $params = [];
        $params['poll_id']['value'] = $this->id;
        $params['poll_id']['data_type'] = 'integer';
        $params['user_id']['value'] = self::getCurrentUser()->getUserId();
        $params['user_id']['data_type'] = 'integer';
        return (self::db()->paramQueryValue($query, 0, $params) > 0);
    }

    /**
     * Creates new poll, returns pollId
     *
     * @param int $reportId
     * @param int $period
     *            // in days
     * @param string $question
     * @param string $ans1
     * @param string $ans2
     * @param string $ans3
     *            // or null
     * @return int
     */
    public static function createPoll($reportId, $period, $question, $ans1, $ans2, $ans3 = null)
    {
        if (! ($period >= self::POLL_INTERVAL_MIN && $period <= self::POLL_INTERVAL_MAX)) {
            return null;
        } elseif (! Report::isValidReportId($reportId)) {
            return null;
        }
        $poll = new ReportPoll();
        $poll->reportId = $reportId;
        $poll->question = strip_tags($question);
        $poll->ans1 = strip_tags($ans1);
        $poll->ans2 = strip_tags($ans2);
        $poll->ans3 = ($ans3 == '' || $ans3 === null) ? null : strip_tags($ans3);
        $poll->dateStart = new \DateTime('now');
        $poll->dateEnd = clone $poll->dateStart;
        $poll->dateEnd->add(new \DateInterval('P' . (int) $period . 'D'));
        $poll->dataLoaded = true;
        $pollId = $poll->insertToDb();
        unset($poll);
        return $pollId;
    }

    /**
     * Lists all active polls on the report
     * Returns array of ReportPoll object for given $reportId
     *
     * @param int $reportId
     * @return ReportPoll[]
     */
    public static function getActivePolls($reportId)
    {
        return self::getPollsObj($reportId, true);
    }

    /**
     * Lists all polls after voting term on the report
     * Returns array of ReportPoll object for given $reportId
     *
     * @param int $reportId
     * @return ReportPoll[]
     */
    public static function getInActivePolls($reportId)
    {
        return self::getPollsObj($reportId, false);
    }

    /**
     * Return JavaScript code for Google Charts to visualise results of the polls
     * Used in report_show.tpl.php
     *
     * @return string
     */
    public function getJsCode()
    {
        $content = 'function drawpoll' . $this->id . '() {
            var data = new google.visualization.DataTable();
            data.addColumn("string", "' . tr('admin_reports_lbl_ans') . '");
            data.addColumn("number", "' . tr('admin_reports_lbl_votes') . '");
            data.addRows([
            ["' . $this->ans1 . '", ' . addslashes($this->getVotesCount(1)) . '],
            ["' . $this->ans2 . '", ' . addslashes($this->getVotesCount(2)) . ']';
        if ($this->ans3 !== null) {
            $content .= ',["' . $this->ans3 . '", ' . addslashes($this->getVotesCount(3)) . ']';
        }
        $content .= ']);
            var options = {title:"' . addslashes($this->question) . '"};
            var chart = new google.visualization.PieChart(document.getElementById("chart-poll-' . $this->id . '"));
            chart.draw(data, options);
            }';
        return $content;
    }

    /**
     * Returns array of userid of voters in given poll
     *
     * @param int $pollId
     * @return string
     */
    public static function getVotersArray($pollId)
    {
        if (! self::isValidPollId($pollId)) {
            return [];
        }
        $query = '
            SELECT `user_id`
            FROM `reports_poll_votes`
            WHERE `poll_id` = :poll_id';
        $params = [];
        $params['poll_id']['value'] = $pollId;
        $params['poll_id']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);
        return self::db()->dbResultFetchAll($stmt);
    }

    /**
     * Returns string contains usernames who already voted this poll
     * usernames are separated by $delimiter
     *
     * @param string $delimiter
     * @return string
     */
    public function getVotersList($delimiter = ',')
    {
        if (! $this->dataLoaded) {
            return '';
        }
        $query = '
            SELECT `user`.`username`
            FROM `reports_poll_votes`
            INNER JOIN `user` ON `reports_poll_votes`.`user_id` = `user`.`user_id`
            WHERE `reports_poll_votes`.`poll_id` = :poll_id
            ORDER BY `user`.`username` ASC';
        $params = [];
        $params['poll_id']['value'] = $this->id;
        $params['poll_id']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);
        $voters = self::db()->dbResultFetchAll($stmt);
        $first = true;
        $content = '';
        foreach ($voters as $voter) {
            if ($first) {
                $first = false;
            } else {
                $content .= $delimiter . ' ';
            }
            $content .= $voter['username'];
        }
        return $content;
    }

    /**
     * Returns number of votes for $option.
     * $option should be 1..3
     * If $option is null - retunrs number of voters.
     *
     * @param int $option
     * @return int
     */
    public function getVotesCount($option = null)
    {
        if (($this->isPollActive() || $this->id === null) && $option != null) { // Don't publish voters list if poll is active!
            return 0;
        }
        if ($option !== null && ($option < 1 || $option > 3)) { // Incorrect $option
            return 0;
        }
        $params = [];
        $query = '
            SELECT COUNT(*)
            FROM `reports_poll_votes`
            WHERE `poll_id` = :poll_id';
        if ($option !== null) {
            $query .= ' AND `vote` = :vote';
            $params['vote']['value'] = $option;
            $params['vote']['data_type'] = 'integer';
        }
        $params['poll_id']['value'] = $this->id;
        $params['poll_id']['data_type'] = 'integer';
        return self::db()->paramQueryValue($query, 0, $params);
    }

    /**
     * Returns percent of voters who vote for $option
     *
     * @param int $option
     * @return float
     */
    public function getVotesPercent($option, $precision = 1)
    {
        if ($this->isPollActive() || $this->id === null) { // Don't publish voters list if poll is active!
            return 0;
        }
        if ($option < 1 || $option > 3) { // Incorrect $option
            return 0;
        }
        $percent = (float) $this->getVotesCount($option) / (float) $this->getVotesCount(null) * 100;
        return round($percent, $precision);
    }

    /**
     * Returns bool - if given parameter is a valid poll ID
     *
     * @param int $pollId
     * @return boolean
     */
    public static function isValidPollId($pollId)
    {
        if (! is_numeric($pollId)) {
            return false;
        }
        $query = 'SELECT COUNT(*) FROM `reports_poll` WHERE `id` = :pollid';
        $params = [];
        $params['pollid']['value'] = $pollId;
        $params['pollid']['data_type'] = 'integer';
        if (self::db()->paramQueryValue($query, 0, $params) == '1') {
            return true;
        }
        return false;
    }

    /**
     * Generates <option></option> list for period of poll
     *
     * @return string
     */
    public static function generatePollIntervalSelect()
    {
        $result = '';
        for ($i = self::POLL_INTERVAL_MIN; $i <= self::POLL_INTERVAL_MAX; $i ++) {
            $result .= '<option value="' . $i . '">' . $i . '</option>';
        }
        return $result;
    }

    /**
     * Inserts ReportPoll as new poll in DB, returns id of new ReportPoll
     *
     * @return int
     */
    private function insertToDb()
    {
        $query = '
            INSERT INTO `reports_poll`
            (`report_id`, `date_start`, `date_end`, `question`, `ans1`, `ans2`, `ans3`)
            VALUES
            (:report_id, :date_start, :date_end, :question, :ans1, :ans2, :ans3)';
        $params = [];
        $params['report_id']['value'] = $this->reportId;
        $params['report_id']['data_type'] = 'integer';
        $params['date_start']['value'] = $this->dateStart->format(OcConfig::instance()->getDbDateTimeFormat());
        $params['date_start']['data_type'] = 'string';
        $params['date_end']['value'] = $this->dateEnd->format(OcConfig::instance()->getDbDateTimeFormat());
        $params['date_end']['data_type'] = 'string';
        $params['question']['value'] = $this->question;
        $params['question']['data_type'] = 'string';
        $params['ans1']['value'] = $this->ans1;
        $params['ans1']['data_type'] = 'string';
        $params['ans2']['value'] = $this->ans2;
        $params['ans2']['data_type'] = 'string';
        $params['ans3']['value'] = $this->ans3;
        $params['ans3']['data_type'] = ($this->ans3 == null) ? 'null' : 'string';
        self::db()->paramQuery($query, $params);
        $this->id = self::db()->lastInsertId();
        return $this->id;
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
                case 'date_start':
                    $this->dateStart = new \DateTime($val);
                    break;
                case 'date_end':
                    $this->dateEnd = new \DateTime($val);
                    break;
                case 'question':
                    $this->question = $val;
                    break;
                case 'ans1':
                    $this->ans1 = $val;
                    break;
                case 'ans2':
                    $this->ans2 = $val;
                    break;
                case 'ans3':
                    $this->ans3 = $val;
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

    private function loadById($pollId)
    {
        $query = 'SELECT * FROM `reports_poll` WHERE id = :id LIMIT 1';
        $params = [];
        $params['id']['value'] = $pollId;
        $params['id']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);
        $dbRow = self::db()->dbResultFetch($stmt);
        if (is_array($dbRow)) {
            $this->loadFromDbRow($dbRow);
        } else {
            $this->dataLoaded = false;
        }
    }

    private static function getPollsObj($reportId, $active = true)
    {
        $query = '
            SELECT *
            FROM `reports_poll`
            WHERE `report_id` = :report_id AND `date_end`';
        if ($active) {
            $query .= ' > NOW()';
        } else {
            $query .= ' <= NOW()';
        }
        $query .= ' ORDER BY `id` DESC';
        $params = [];
        $params['report_id']['value'] = $reportId;
        $params['report_id']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);
        
        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    /**
     * Saves vote to DB.
     * All checks are done in addVote()
     *
     * @param int $vote
     */
    private function saveVote($vote)
    {
        $query = '
            INSERT INTO `reports_poll_votes`
            (`poll_id`, `user_id`, `vote`, `date_created`)
            VALUES
            (:poll_id, :user_id, :vote, NOW())
            ON DUPLICATE KEY UPDATE
            `vote` = :vote, `date_created` = NOW()';
        $params = [];
        $params['poll_id']['value'] = $this->id;
        $params['poll_id']['data_type'] = 'integer';
        $params['user_id']['value'] = self::getCurrentUser()->getUserId();
        $params['user_id']['data_type'] = 'integer';
        $params['vote']['value'] = $vote;
        $params['vote']['data_type'] = 'integer';
        self::db()->paramQuery($query, $params);
    }
}
