<?php

namespace Controllers;

/**
 * This controller will provide geocache downloads like GPX.
 */

class CacheExportController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public method can be called by router
        return true;
    }

    public function index()
    {
        // there is nothing to do here yet...
    }

    /** Provide localized form for "OKAPI GPX" */
    public function okapiGpxForm()
    {
        $this->view->setTemplate('okapiGpxForm');
        $this->view->buildOnlySelectedTpl();
    }
}
