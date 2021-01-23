<?php

namespace src\Models\Voting;

use src\Models\BaseObject;
use src\Utils\Generators\ColorGenerator;

/**
 * Model of election results
 */
class ElectionResult extends BaseObject
{
    private $votes;
    private $countOfVotes;
    private $countOfVoters;
    private $options;
    private $optionsColors;
    private $election;

    public function __construct(Election $election)
    {
        parent::__construct();

        $this->election = $election;
        $this->countOfVoters = Voter::getVotersCount($election);

        // prepare the table to count votes
        $this->votes = [];
        /** @var $opt ChoiceOption */
        foreach (ChoiceOption::getOptionsForElection($election) as $opt) {
            $this->votes[$opt->getOptionId()] = [];
            $this->options[$opt->getOptionId()] = $opt;
        }

        // prepare colors for charts
        $colorSet = ColorGenerator::niceSetLightBg(count($this->options));
        foreach ($this->options as $optId => $opt) {
            $this->optionsColors[$optId] = array_pop($colorSet);
         }

        // load votes
        $this->countOfVotes = 0;
        /** @var $vote Vote */
        foreach (Vote::getAllVotes($election) as $vote) {
            $this->votes[$vote->getOptId()][] = $vote;
            $this->countOfVotes++;
        }

        // sort optons by number of votes
        uasort($this->options, function (ChoiceOption $optA, ChoiceOption $optB) {
            // this is automatically binding into closure
            $votesOnA = $this->getOptVotesCount($optA);
            $votesOnB = $this->getOptVotesCount($optB);
            if ($votesOnA == $votesOnB) {
                return 0;
            }
            return ($votesOnA > $votesOnB) ? -1 : 1;
        });

    }

    public function prepareForSerialization()
    {
        parent::prepareForSerialization();
        $this->election->prepareForSerialization();
        foreach ($this->votes as $votesOnOpt) {
            foreach ($votesOnOpt as $vote) {
                $vote->prepareForSerialization();
            }
        }

        foreach ($this->options as $opt) {
            $opt->prepareForSerialization();
        }
    }

    public function getOptionsList(): array
    {
        return $this->options;
    }

    public function getOptVotesCount(ChoiceOption $opt): int
    {
        if (!isset($this->votes[$opt->getOptionId()])) {
            return 0;
        }
        return count($this->votes[$opt->getOptionId()]);
    }

    public function getOptPercent(ChoiceOption $opt): int
    {
        if ($this->getVotesNum() == 0) {
            return 0;
        }
        return $this->getOptVotesCount($opt) / $this->getVotesNum() * 100;
    }

    public function getVotesNum(): int
    {
        return $this->countOfVotes;
    }

    public function getVotersNum(): int
    {
        return $this->countOfVoters;
    }

    public function getListOfOptNamesAsJson(): string
    {
        return json_encode(array_values(
            array_map(function(ChoiceOption $opt) {
                return $opt->getName();
            }, $this->options)));
    }

    public function getListOfVotesCountAsJson(): string
    {
        return json_encode(array_values(
            array_map(function(ChoiceOption $opt) {
                return $this->getOptVotesCount($opt);
            }, $this->options)));
    }

    public function getListOfVotesInTimeJson(ChoiceOption $opt): string
    {
        /*
         *  genrate list of votes in format:
         *    {
         *      'x': timestamp as string
         *      'y': current count of votes
         *    }
         */

        $result = [];
        $currentVotes = 0;

        $ts = $this->election->getStartDate()->getTimestamp();
        $result[] = ['x' => "$ts", 'y' => $currentVotes];

        /** @var $vote Vote */
        foreach ($this->votes[$opt->getOptionId()] as $vote) {
            $currentVotes++;
            $ts = $vote->getDate()->getTimestamp();
            $result[] = ['x' => "$ts", 'y' => $currentVotes];
        }
        return json_encode(array_values($result));
    }

    public function getColorForOption(ChoiceOption $opt): string
    {
        return $this->optionsColors[$opt->getOptionId()];
    }
}