<?php
namespace src\Utils\Gis;

use src\Models\OcConfig\OcConfig;
use src\Utils\Cache\OcMemCache;
use src\Utils\I18n\I18n;

class Countries
{
    const ALL_COUNTRIES_JSON = __DIR__."/../../../resources/gis/allCountriesCodes.json";

    /**
     * Returns list of countries - all countries by default
     *
     * @param $onlyDefaultsCountries - if true returns only default countries codes (based on config)
     *
     * @return array
     */
    public static function getCountriesList($onlyDefaultsCountries = false)
    {
        if ($onlyDefaultsCountries) {
            return OcConfig::getSiteDefaultCountriesList();
        }

        // read countries list from file
        return OcMemCache::getOrCreate("allCountriesList", 3600, function(){
            $allCountriesStr = file_get_contents(self::ALL_COUNTRIES_JSON);
            return json_decode($allCountriesStr);
        });
    }

    /**
     * Returns true if country code is on the list of countries
     * @param string $countryCode
     */
    public static function isKnownCountryCode($countryCode)
    {
        return array_search($countryCode, self::getCountriesList());
    }

    /**
     * Returns country name in curent language based on country code
     *
     * @param string $countryCode
     * @return string
     */
    public static function getNameLocalized($countryCode)
    {
        return tr(strtoupper($countryCode));
    }

    /**
     * Returns country name in curent language based on country code
     *
     * @param string $countryCode
     * @return string
     */
    public static function getNameEn($countryCode)
    {
        return I18n::translatePhrase(strtoupper($countryCode), 'en');
    }

}