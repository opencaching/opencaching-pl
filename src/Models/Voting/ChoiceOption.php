<?php

namespace src\Models\Voting;

use src\Models\BaseObject;
use src\Utils\Debug\Debug;

/**
 * Description of user
 *
 *
 *         CREATE TABLE IF NOT EXISTS vote_choiceOptions (
            `optionId` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique option id',
            `electionId` int(11) NOT NULL COMMENT 'id of election',
            `name` text NOT NULL COMMENT 'name of the option',
            `description` text NOT NULL COMMENT 'description of the option',
            `link` text NOT NULL COMMENT 'link added to option',
            `orderIdx` int(11) NOT NULL COMMENT 'order of the option on the list',
            PRIMARY KEY (`optionId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

 */
class ChoiceOption extends BaseObject
{
    private $optionId;
    private $electionId;
    private $name;
    private $description;
    private $orderIdx;
    private $link;


    public function __construct()
    {
        parent::__construct();
    }

    public static function getOptionsForElection(Election $election): array
    {
        $db = self::db();
        $stmt = $db->multiVariableQuery(
            "SELECT * FROM vote_choiceOptions WHERE electionId = :1 ORDER BY orderIdx ASC",
            $election->getElectionId()
            );

        return $db->dbFetchAllAsObjects($stmt, function (array $row) {
            $obj = new self();
            $obj->loadFromDbRow($row);
            return $obj;
        });
    }

    /**
     * Returns TRUE if given option is a valid option for given elections
     *
     * @param Election $election
     * @param int $optionId
     */
    public static function checkOption(Election $election, int $optionId): bool
    {
        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(*) FROM vote_choiceOptions WHERE optionId = :1 AND electionId = :2",
            0,
            $optionId,
            $election->getElectionId()
            ) > 0;
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
                case 'optionId':
                    $this->optionId = $value;
                    break;
                case 'link':
                    $this->link = $value;
                    break;
                case 'orderIdx':
                    $this->orderIdx = $value;
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
     * @return mixed
     */
    public function getOptionId()
    {
        return $this->optionId;
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
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getOrderIdx()
    {
        return $this->orderIdx;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

}
