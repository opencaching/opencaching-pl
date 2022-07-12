<?php

namespace src\Controllers\CacheSet;

use src\Controllers\BaseController;

class CacheSetDetailsController extends BaseController
{
    public function isCallableFromRouter(string $actionName): bool
    {
        // all public methods can be called by router
        return true;
    }

    public function index()
    {
    }
}
