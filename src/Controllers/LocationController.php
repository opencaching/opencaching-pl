<?php

namespace src\Controllers;

use src\Models\Coordinates\NutsLocation;
use src\Utils\Gis\Countries;

/**
 * This controller is a helper which provides data around location, Gis etc.
 */


class LocationController extends BaseController
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

    public function getRegionsByCountryCodeAjax($countryCode)
    {
        $this->checkUserLoggedAjax();

        if(!Countries::isKnownCountryCode($countryCode)){
            $this->ajaxErrorResponse("Unknown country code.");
        }

        $regions = NutsLocation::getRegionsListByCountryCode($countryCode);

        $this->ajaxSuccessResponse("Countries", ['regions' => $regions]);
    }


}
