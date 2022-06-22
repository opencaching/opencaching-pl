<?php

namespace src\Controllers\Admin;

use src\Controllers\Core\ViewBaseController;
use src\Utils\Uri\Uri;

class GeoCacheApprovalAdminController extends ViewBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->redirectNotLoggedUsers();

        if (! $this->loggedUser->hasOcTeamRole()) {
            $this->view->redirect('/');
        }
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        // all public methods can be called by router
        return true;
    }

    /**
     * Initial, static view. The rest of operations are performed by API
     */
    public function index()
    {
        $this->view->loadJQuery();
        $this->view->loadJQueryUI();
        $this->view->addHeaderChunk('momentJs');
        $this->view->addHeaderChunk('handlebarsJs');
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/views/admin/geocacheApproval/geocache_approval.css'
            )
        );
        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime(
                '/views/admin/geocacheApproval/geocache_approval.js'
            )
        );
        $this->view->setTemplate('admin/geocacheApproval/geocacheApproval');
        $this->view->setVar('currentUserId', $this->loggedUser->getUserId());
        $this->view->buildView();
    }
}
