<?php
/**
 * Simple voting for OC. OCPL is going to use this to elect OCTEAM.
 */
namespace src\Controllers;

use src\Models\Voting\Election;
use src\Utils\DateTime\OcDateTime;
use src\Models\Voting\ChoiceOption;
use src\Models\Voting\ElectionResult;
use src\Utils\Cache\OcMemCache;

class VotingController extends BaseController
{
    public function __construct($param) {
        parent::__construct();
        $this->redirectNotLoggedUsers();
    }

    public function isCallableFromRouter(string $actionName)
    {
        return true;
    }

    /**
     * Display list of available elections
     */
    public function index()
    {
        // list of elections
        $allElections = Election::getElectionsList();

        $this->view->setVar('elections', $allElections);
        $this->view->setTemplate('voting/listOfElections');
        $this->view->buildView();
    }

    /**
     * Display election view
     */
    public function election(int $electionId)
    {
        // check election
        $election = Election::fromElectionIdFactory($electionId);
        if (!$election) {
            $this->displayCommonErrorPageAndExit("No such election");
        }
        $this->view->setVar('election', $election);
        $this->view->addLocalCss('/views/voting/voting.css');

        // check if we are after the voting
        if (OcDateTime::isPast($election->getEndDate())) {
            $this->displayResults($election);
            exit;
        }

        $this->view->loadJQuery();

        // retrive options data
        $options = ChoiceOption::getOptionsForElection($election);
        $this->view->setVar('optionsArr', $options);

        // check if we are before voting
        if (OcDateTime::isPast($election->getStartDate())) {
            // voting is now active
            if ($election->hasUserAlreadyVoted($this->loggedUser)){
                // user has already voted
                $this->view->setVar('alreadyVoted', true);
            } else {
                if ($election->validateCriteriaForUser($this->loggedUser)) {
                    // user is able to vote
                    $this->view->setVar('enableVoting', true);
                } else {
                    $this->view->setVar('votingCriteriaConflict', true);
                }
            }
        }

        $this->view->setTemplate('voting/election');
        $this->view->buildView();
    }

    /**
     * Save votes to DB.
     * Votes are in var POST[votes]
     *
     * @param int $electionId
     *
     */
    public function saveVote(int $electionId)
    {
        $election = Election::fromElectionIdFactory($electionId);
        if (!$election) {
            $this->ajaxErrorResponse(tr('vote_saveResultInternalError').". [No such election]");
        }

        $votes = $_POST['votes'] ?? [];

        // check votes
        foreach ($votes as $vote) {
            if(!is_numeric($vote)) {
                $this->ajaxErrorResponse(tr('vote_saveResultInternalError').". [Unknown format]");
            }
        }

        $errorMsg = '';
        if (!$election->saveVotes($this->loggedUser, $votes, $errorMsg)) {
            $this->ajaxErrorResponse($errorMsg);
        }
        $this->ajaxSuccessResponse();
    }

    /**
     * Display election results view
     */
    private function displayResults(Election $election)
    {
        $this->view->setTemplate('voting/results');
        $this->view->addHeaderChunk('chartsJs');

        $electionId = $election->getElectionId();

        $results = OcMemCache::getOrCreate(__METHOD__."($electionId)", 3600, function () use ($election) {
            $elResults = new ElectionResult($election);
            $elResults->prepareForSerialization();
            return $elResults;
        });

        $this->view->setVar('results', $results);
        $this->view->buildView();
    }
}
