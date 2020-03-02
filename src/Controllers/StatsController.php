<?php

namespace src\Controllers;


use src\Utils\DateTime\Year;
use src\Models\User\MultiUserQueries;
use src\Models\GeoCache\MultiCacheStats;
use src\Models\Pictures\StatPic;
use src\Models\User\User;

class StatsController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index()
    {}

    public function internalStats($year=null)
    {
        // only for logged users
        $this->redirectNotLoggedUsers();

        if (!$this->loggedUser->hasAdvUserRole()) {
            $this->index();
            exit;
        }

        if(is_null($year)) {
            $year = Year::current();
        }

        $this->view->setTemplate('stats/internalStats');
        $this->view->setVar('year', $year);

        $allUsers = MultiUserQueries::getCountOfNewUsers($year);
        $allUsers['sum'] = 0;
        foreach ($allUsers as $value) {
            $allUsers['sum'] += $value;
        }
        $this->view->setVar('allNewUsersPerMonth', $allUsers);

        $allUsers = MultiUserQueries::getCountOfNewUsers($year, true);
        $allUsers['sum'] = 0;
        foreach ($allUsers as $value) {
            $allUsers['sum'] += $value;
        }
        $this->view->setVar('newActiveUsersPerMonth', $allUsers);

        $allCaches = MultiCacheStats::getNewCachesCountMonthly($year);
        $allCaches['sum'] = 0;
        foreach ($allCaches as $value) {
            $allCaches['sum'] += $value;
        }
        $this->view->setVar('newCachesPerMonth', $allCaches);

        $this->view->buildView();
    }

    /**
     * Redirect to user statPic banner
     * Banner will be created if necessary
     *
     * @param integer $userId
     */
    public function statPic($userId)
    {
        if (!is_numeric($userId)) {
            $this->displayCommonErrorPageAndExit("improper userId");
        }

        if (1 || !StatPic::isStatPicPresent ($userId)) { //XXXY
            $user = User::fromUserIdFactory($userId);
            if (is_null($user)) {
                $this->displayCommonErrorPageAndExit('Unknown user');
            }

            StatPic::generateStatPic($user);
        }

        $this->view->redirect(StatPic::getStatPicUrl($userId));
    }
}
