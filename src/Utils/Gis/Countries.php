<?php
namespace src\Utils\Gis;

use src\Models\OcConfig\OcConfig;

class Countries
{
    const ALL_COUNTRIES_JSON = __DIR__."/../../resources/gis/allCountriesCodes.json";


    /**
     * Returns list of countries - all countries by default
     *
     * @param $onlyDefaultsCountries - if true returns only default countries codes (based on config)
     *
     * @return array
     */
    public static function getCountriesTranslatedList($onlyDefaultsCountries = false)
    {
        if (!$onlyDefaultsCountries) {
            return OcConfig::getSiteDefaultCountriesList();
        }

        // read countries list from file
        $allCountriesStr = file_get_contents(self::ALL_COUNTRIES_JSON);
        return json_decode($allCountriesStr);
    }

}