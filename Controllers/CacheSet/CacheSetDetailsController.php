<?php

namespace Controllers\CacheSet;

use Controllers\BaseController;


class CacheSetDetailsController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return TRUE;
    }

    public function index()
    {

    }

}

