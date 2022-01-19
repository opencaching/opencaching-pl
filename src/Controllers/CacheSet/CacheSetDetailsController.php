<?php

namespace src\Controllers\CacheSet;

use src\Controllers\BaseController;


class CacheSetDetailsController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter(string $actionName)
    {
        // all public methods can be called by router
        return TRUE;
    }

    public function index()
    {

    }

}
