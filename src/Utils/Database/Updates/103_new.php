<?php

/**
 * Tables used by internal OC voting system
 */
namespace src\Utils\Database\Updates;

class C16126270995966 extends UpdateScript
{
    public function getProperties()
    {
        return [
            // see /docs/DbUpdate.md
            'uuid' => '3835F75B-A46A-26B0-F728-E4A0FC610851',
            'run' => 'auto',
        ];
    }

    // IMPORTANT:
    // Any output by 'echo', 'print' etc. will be PUBLIC (see #1923).
    // Do not output any sensitive information.

    public function run()
    {
        $this->db->simpleQueries("
            DROP TABLE IF EXISTS `vote_voters`;
            DROP TABLE IF EXISTS `vote_votes`;
            DROP TABLE IF EXISTS `vote_choiceOptions`;
            DROP TABLE IF EXISTS `vote_elections`;

            CREATE TABLE `vote_elections` (
              `electionId` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id of the record',
              `name` text NOT NULL COMMENT 'election name',
              `startDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'beginning of election ',
              `endDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'end of election ',
              `voterCriteria` text NOT NULL COMMENT 'JSON with criteria for voters',
              `electionRules` text NOT NULL COMMENT 'JSON with election data',
              `description` text NOT NULL COMMENT 'description of election to display',
              PRIMARY KEY (`electionId`)
            ) ENGINE=InnoDB COMMENT='Voting data';

            CREATE TABLE `vote_choiceOptions` (
              `optionId` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique option id',
              `electionId` int(11) NOT NULL COMMENT 'id of election',
              `name` text NOT NULL COMMENT 'name of the option',
              `description` text COMMENT 'description of the option',
              `link` text COMMENT 'link added to option',
              `orderIdx` int(11) NOT NULL COMMENT 'order of the option on the list',
              PRIMARY KEY (`optionId`),
              KEY `FK_vote_choiceOptions_vote_elections` (`electionId`),
              CONSTRAINT `FK_vote_choiceOptions_vote_elections` FOREIGN KEY (`electionId`) REFERENCES `vote_elections` (`electionId`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB COMMENT='Options available in the voting';

            CREATE TABLE `vote_votes` (
              `voteId` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique id of the vote',
              `electionId` int(11) NOT NULL COMMENT 'id of the election',
              `optionId` int(11) NOT NULL COMMENT 'option selected in voting ',
              `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date of the vote',
              `hash` text COMMENT 'optional hash to store additional data',
              PRIMARY KEY (`voteId`),
              KEY `FK_vote_votes_vote_elections` (`electionId`),
              KEY `FK_vote_votes_vote_choiceOptions` (`optionId`),
              CONSTRAINT `FK_vote_votes_vote_choiceOptions` FOREIGN KEY (`optionId`) REFERENCES `vote_choiceOptions` (`optionId`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `FK_vote_votes_vote_elections` FOREIGN KEY (`electionId`) REFERENCES `vote_elections` (`electionId`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB COMMENT='Votes from elections';

            CREATE TABLE `vote_voters` (
              `userId` int(11) NOT NULL COMMENT 'id of the user (from user table)',
              `electionId` int(11) NOT NULL COMMENT 'id of election (from vote_elections)',
              `ip` varchar(15) NOT NULL COMMENT 'IP address of the user',
              `additionalData` text NOT NULL COMMENT 'Additional data for user verification',
              PRIMARY KEY (`userId`,`electionId`),
              KEY `FK_vote_voters_vote_elections` (`electionId`),
              CONSTRAINT `FK_vote_voters_user` FOREIGN KEY (`userId`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
              CONSTRAINT `FK_vote_voters_vote_elections` FOREIGN KEY (`electionId`) REFERENCES `vote_elections` (`electionId`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB COMMENT='User participation in votings';
            ");

        echo "...done";
    }
};

return new C16126270995966;
