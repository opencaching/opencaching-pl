<?php

namespace src\Models\Voting;

use src\Models\BaseObject;
use src\Models\User\User;
use src\Utils\DateTime\OcDateTime;

class Vote extends BaseObject
{
    private $optId;
    private $date;

    private function __construct()
    {
        parent::__construct();
    }

    public static function saveToDb (Election $election, User $user, array $votes)
    {
        if(empty($votes)) {
            // there is nothing to save
            return;
        }

        $electionId = $election->getElectionId();
        $userId = $user->getUserId();
        $hash = md5(strval($userId));
        $date = OcDateTime::now()->getTimestamp();

        $query = 'INSERT INTO vote_votes (electionId, optionId, date, hash) VALUES ';
        $values = [];
        foreach ($votes as $optionId) {
            $values[] = "($electionId, $optionId, FROM_UNIXTIME($date), '$hash')";
        }
        $query .= implode(',', $values);

        self::db()->simpleQuery($query);
    }

    /**
     * Returns array of Vote with all votes of given selection
     *
     * @param Election $election
     * @return array
     */
    public static function getAllVotes(Election $election): array
    {
        $rs = self::db()->multiVariableQuery(
            "SELECT optionId, date FROM vote_votes WHERE electionId = :1 ORDER BY date ASC",
            $election->getElectionId());

        return self::db()->dbFetchAllAsObjects($rs, function ($row){
            $vote = new self();
            $vote->optId = $row['optionId'];
            $vote->date = new \DateTime($row['date']);
            return $vote;
        });
    }

    /**
     * @return mixed
     */
    public function getOptId(): int
    {
        return $this->optId;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

}
