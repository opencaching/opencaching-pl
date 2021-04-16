<?php

namespace src\Models\Voting;

use DateTime;
use src\Models\BaseObject;
use src\Models\User\User;
use src\Utils\Debug\Debug;
use src\Utils\DateTime\OcDateTime;

/**
 * Model of elections - contains information related to election process
 */
class Election extends BaseObject
{
    private $electionId;
    private $name;
    private $startDate;
    private $endDate;
    private $voterCriteria;
    private $electionRules;
    private $description;

    private function __construct()
    {
        parent::__construct();
    }

    public static function fromElectionIdFactory(int $electionId): Election
    {
        $db = self::db();
        $stmt = $db->multiVariableQuery(
            "SELECT * FROM vote_elections WHERE electionId = :1 LIMIT 1", $electionId);

        $elections =  $db->dbFetchAllAsObjects($stmt, function (array $row) {
            $obj = new self();
            $obj->loadFromDbRow($row);
            return $obj;
        });

        if (empty($elections)) {
            return null;
        }

        return array_pop($elections);
    }

    public static function getElectionsList(): array
    {
        $db = self::db();
        $stmt = $db->multiVariableQuery(
            "SELECT electionId, name, startDate, endDate, description FROM vote_elections");

        return $db->dbFetchAllAsObjects($stmt, function (array $row) {
            $obj = new self();
            $obj->loadFromDbRow($row);
            return $obj;
        });
    }

    /**
     * Returns TRUE is this elections is active now (users can send their votes
     *
     * @return bool
     */
    public function isActiveNow(): bool
    {
        return OcDateTime::isPast($this->getStartDate()) && OcDateTime::isFuture($this->getEndDate());
    }

    /**
     * Save votes send by user for this selection
     *
     * @param User $user         logged user
     * @param array $votes       Array of choiceOption ids
     * @param string $errorMsg   Translation string to display for user if eror occured
     * @return bool              Returns TRUE if votes were saved successfully
     */
    public function saveVotes(User $user, array $votes, &$errorMsg): bool
    {
        // check if election is still active
        if (!$this->isActiveNow()) {
            $errorMsg = tr('vote_inactiveVoting');
            return false;
        }

        // check if user has already voted
        if ($this->hasUserAlreadyVoted($user)) {
            $errorMsg = tr('vote_alreadyVoted');
            return false;
        }

        // check if user pass criteria to vote
        if (!$this->validateCriteriaForUser($user)) {
            $errorMsg = tr('vote_criteriaNotPassed');
            return false;
        }

        // check if every vote is a valid option for this election
        foreach ($votes as $vote) {
            if (!ChoiceOption::checkOption($this, $vote)) {
                $errorMsg = tr('vote_invalidVote').". [Incorrect option]";
                return false;
            }
        }

        // check rules in context of votes
        if (!$this->electionRules->validatesVotesArr($votes)) {
            $errorMsg = tr('vote_invalidVote').". [Rules conflict]";
            return false;
        }

        // OK votes can be saved
        if (!$this->db->beginTransaction()) {
            $errorMsg = tr('vote_internalError');
            return false;
        }
        // save fact that user voted
        Voter::saveToDb($user, $this);
        // save votes
        if(!empty($votes)) {
            Vote::saveToDb($this, $user, $votes);
        }
        $this->db->commit();

        return true;
    }

    private function loadFromDbRow(array $dbRow)
    {
        foreach ($dbRow as $key => $value) {
            switch ($key) {
                case 'electionId':
                    $this->electionId = $value;
                    break;
                case 'name':
                    $this->name = $value;
                    break;
                case 'startDate':
                    $this->startDate = new DateTime($value);
                    break;
                case 'endDate':
                    $this->endDate = new DateTime($value);
                    break;
                case 'voterCriteria':
                    $this->voterCriteria = new VoterCriteria($value);
                    break;
                case 'electionRules':
                    $this->electionRules = new ElectionRules($value);
                    break;
                case 'description':
                    $this->description = $value;
                    break;
                default:
                    Debug::errorLog("Unknown column: $key");
            }
        }
    }

    /**
     * Returns TRUE if user has already voted
     *
     * @param User $user
     * @return bool
     */
    public function hasUserAlreadyVoted(User $user): bool
    {
        return Voter::hasUserAlreadyVoted($user, $this);
    }

    /**
     * Returns TRUE if user account pass criteria defined for this election
     *
     * @param User $user
     * @return bool
     */
    public function validateCriteriaForUser(User $user): bool
    {
        return $this->voterCriteria->validateUser($user, $this);
    }

    public function isDone (): bool
    {
        return OcDateTime::isBefore($this->getEndDate());
    }

    /**
     * Returns max number of votes or null if max number of votes doesn't matter
     */
    public function getMaxAllowedNumOfVotes ()
    {
        return  $this->electionRules->votesPerUser;
    }

    /**
     * Returns false if user can send vote without any option selected
     * @return bool
     */
    public function isEmptyVoteDisallowed (): bool
    {
        return $this->electionRules->disallowNoVotes ?? false;
    }

    /**
     * Returns TRUE if user must select maximum allowed number of votes
     * @return bool
     */
    public function isPartialVoteDisallowed (): bool
    {
        return $this->electionRules->disallowLessVotes ?? false;
    }


    /**
     * @return mixed
     */
    public function getElectionId()
    {
        return $this->electionId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return mixed
     */
    public function getVoterCriteriaJson()
    {
        return $this->voterCriteriaJson;
    }

    /**
     * @return mixed
     */
    public function getElectionRulesJson()
    {
        return $this->electionRulesJson;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

}

/**
 * Internal class to represents detailed election rules
 *
 * Criteria are defined in JSON object saved in DB in format:
 *  {
 *      "votesPerUser": 2,
 *      "disallowLessVotes": true,
 *      "disallowNoVotes": true
 *  }
 *
 * If some rule is not set in JSON it is always passed
 *
 * This is stored this way to easy define additional rules without DB modifications
 */
class ElectionRules
{
    public $votesPerUser = null;
    public $disallowLessVotes = null;
    public $disallowNoVotes = null;

    public function __construct ($jsonStr) {
        $arr = json_decode($jsonStr, true);
        if (is_array($arr)) {
            foreach ($arr as $key => $val) {
                switch ($key){
                    case 'votesPerUser':
                        $this->votesPerUser = $val;
                        break;
                    case 'disallowLessVotes':
                        $this->disallowLessVotes = $val;
                        break;
                    case 'disallowNoVotes':
                        $this->disallowNoVotes = $val;
                        break;
                    default:
                        Debug::errorLog("Unknown key: $key");
                }
            }// foreach
        }
    }

    /**
     * Returns TRUE if given array of votes passes election rules
     *
     * @param array $votes  Array contains ids of choiceOptions
     * @return bool
     */
    public function validatesVotesArr(array $votes): bool
    {
        if ($this->votesPerUser && $this->votesPerUser < count($votes)) {
            // too many votes - something strange happen...
            return false;
        }

        if ($this->disallowLessVotes && $this->votesPerUser > count($votes)) {
            // too less votes - user chose less votes than expected what is not allowed
            return false;
        }

        if ($this->disallowNoVotes && empty($votes)) {
            // there are no votes - user doesn't choose any option
            return false;
        }

        // seems everything is OK
        return true;
    }
}

/**
 * Internal class to represents criteria which must pass the user to be able to vote
 *
 * Criteria are defined in JSON object saved in DB in format:
 *  {
 *      "founds": 1000,
 *      "daysWithOc": 30
 *  }
 * If some criteria is not set in JSON it is always passed
 *
 * This is stored this way to easy define additional criteria without DB modifications
 */
class VoterCriteria
{
    public $founds = null;          // if user has at least this number of founds
    public $daysWithOc = null;      // if user signed up at least this number days before

    public function __construct ($jsonStr) {
        $arr = json_decode($jsonStr, true);
        if (is_array($arr)) {
            foreach ($arr as $key => $val) {
                switch ($key){
                    case 'founds':
                        $this->founds = $val;
                        break;
                    case 'daysWithOc':
                        $this->daysWithOc = $val;
                        break;
                    default:
                        Debug::errorLog("Unknown key: $key");
                }
            }// foreach
        }
    }

    /**
     * Check if user pass criteria defined in this class
     *
     * @param User $user
     * @param Election $election
     * @return bool
     */
    public function validateUser (User $user, Election $election): bool {
        // validate founds
        if ($this->founds) {
            if ($user->getFoundGeocachesCount() < $this->founds) {
                return false;
            }
        }

        if ($this->daysWithOc) {
            $tmpDate = clone $election->getStartDate(); // to not modify original date
            $lastDayToVote = $tmpDate->modify('-'.$this->daysWithOc.' days');
            if (OcDateTime::isAfter($user->getDateCreated(), $lastDayToVote)) {
                return false;
            }
        }

        // all criteria are passed
        return true;
    }
}
