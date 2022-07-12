<?php

namespace src\Controllers;

class ViewBadgeShowPositionsController extends BaseController
{
    private $sCode = '';

    public function isCallableFromRouter(string $actionName): bool
    {
        // all public methods can be called by router
        return true;
    }

    public function index()
    {
        $this->redirectNotLoggedUsers();

        $userid = $_REQUEST['user_id'] ?? $this->loggedUser->getUserId();

        $badge_id = $_REQUEST['badge_id'];

        $ctrlMeritBadge = new MeritBadgeController();
        $meritBadge = $ctrlMeritBadge->buildMeritBadge($badge_id);

        if ($meritBadge->getCfgShowPositions() == '') {
            return '';
        }

        $this->prepareCode();

        $this->setVar('user_id', $userid);
        $this->setVar('badge_id', $badge_id);

        return $this->sCode;
    }

    private function prepareCode()
    {
        $this->sCode = file_get_contents(__DIR__ . '/../../src/Views/badge_show_positions.tpl.php');
        $this->sCode = tpl_do_translate($this->sCode);
    }

    private function setVar($name, $value)
    {
        $this->sCode = mb_ereg_replace('{' . $name . '}', $value, $this->sCode);
    }
}
