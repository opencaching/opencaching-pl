<?php

namespace src\Models\Voting;

use src\Models\BaseObject;
use src\Models\User\User;

/**
 * Description of voting user
 */
class Voter extends BaseObject
{
    private $electionId;
    private $userId;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns TRUE if given user voted in given election
     *
     * @param User $user
     * @param Election $election
     * @return bool
     */
    public static function hasUserAlreadyVoted(User $user, Election $election): bool
    {
        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(*) FROM vote_voters WHERE userId = :1 AND electionId = :2",
            0, $user->getUserId(), $election->getElectionId()) > 0;
    }

    /**
     * Save fact that given user has voted in given elections
     *
     * @param User $user
     * @param Election $election
     */
    public static function saveToDb(User $user, Election $election)
    {
        self::db()->multiVariableQuery("INSERT INTO vote_voters (electionId, userId, ip, additionalData)
                                        VALUES (:1, :2, :3, :4)",
        $election->getElectionId(), $user->getUserId(), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
    }

    public static function getVotersCount(Election $election): int
    {
        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(*) FROM vote_voters WHERE electionId = :1", 0, $election->getElectionId());
    }
}
