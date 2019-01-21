<?php

namespace lib\Objects\Coordinates;

use lib\Objects\OcConfig\OcConfig;
use Utils\Debug\Debug;
use Exception;
use Utils\I18n\I18n;

class GeoCode
{

    private $countryCode = null;
    private $countryName = null;
    private $admCode = null;
    private $admName = null;

    //
    private function __construct()
    {}

    /**
     * function provides information whether any geocode service is available or not
     *
     */
    public static function isGeocodeServiceAvailable() {
        $config = OcConfig::instance();
        $config->getMapConfig();
        $ors_key = $config->getMapConfig()['keys']['OpenRouteService'];

        return !empty($ors_key);
    }

    /**
     * @throws Exception if there is some problem with fetching data from OpenRouteService
     */
    public static function fromOpenRouteService($place) {

        $config = OcConfig::instance();
        $config->getMapConfig();
        $ors_key = $config->getMapConfig()['keys']['OpenRouteService'];

        if(empty($ors_key)) {
            Debug::errorLog("No api_key for OpenRouteService in configuration. Check /Config/map.default.php");
            return null;
        }

        $input = urlencode($place);
        $url = "https://api.openrouteservice.org/geocode/search?api_key=$ors_key&text=$input";
        $data = @file_get_contents($url);

        if (!$data) {
            Debug::errorLog("Problem with fetching data from ".$url);
            throw new Exception("Problem with fetching data from OpenRouteService");
            return;
        }

        $resp = json_decode($data);

        // response
        $results = [];

        if(!empty($resp) && isset($resp->features) && is_array($resp->features)) {
            foreach($resp->features as $feature) {
                $result = new GeoCodeServiceResult();

                // skip incorrect feature
                if(!isset($feature->bbox, $feature->properties)
                    || !is_array($feature->bbox)
                    || !is_object($feature->properties)) {
                    continue;
                }

                // skip useless feature
                // according to OpenRouteService documentation localadmin means 'local administrative boundaries'
                // it seems to have very narrow bbox though, not quite suitable
                if(isset($feature->properties->layer) && $feature->properties->layer == 'localadmin') {
                    continue;
                }

                $result->bbox = $feature->bbox;

                if(isset($feature->properties->name)) {
                    $result->name = $feature->properties->name;
                }

                if(isset($feature->properties->layer)) {
                    $result->layer = $feature->properties->layer;
                }

                if(isset($feature->properties->country_a)) {
                    $result->countryCode = $feature->properties->country_a;
                }

                if(isset($feature->properties->region)) {
                    $result->region = $feature->properties->region;
                }

                array_push($results, $result);
            }
        }

        return $results;
    }

    /**
     * Reverse geocoder based on Google Geocoding API
     * @see https://developers.google.com/maps/documentation/geocoding/intro#ReverseGeocoding
     *
     * @param Coordinates $coords
     * @return GeoCode|NULL
     */
    public static function fromGoogleApi(Coordinates $coords)
    {
        global $googlemap_key; //TODO: refactor configs

        $lat = $coords->getLatitude();
        $lon = $coords->getLongitude();

        if(empty($googlemap_key)){
            return null;
        }

        $language = I18n::getCurrentLang();
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lon" .
        "&key=$googlemap_key&language=$language&result_type=administrative_area_level_1";

        $data = @file_get_contents($url);
        $resp = json_decode($data);

        if($resp->status != 'OK'){
            //error!
        }

        $instance = new self();

        // this JSON is a little bit complicated - find administrative_area_level_1 record
        foreach($resp->results as $record){
            if( in_array('administrative_area_level_1', $record->types) ){

                $address = $record->address_components;
                foreach($address as $level){
                    if(in_array('administrative_area_level_1', $level->types) ){
                        $instance->admCode = $level->short_name;
                        $instance->admName = $level->long_name;
                    }
                    if(in_array('country', $level->types) ){
                        $instance->countryCode = $level->short_name;
                        $instance->countryName = $level->long_name;
                    }
                }
            }
        }

        return $instance;

    }

    /**
     * Reverse geocoding based on MapQuest service
     * @see https://developer.mapquest.com/documentation/geocoding-api/ for details
     * @param Coordinates $coords
     * @return GeoCode|null - result object or NULL
     */
    public static function fromMapQuestApi(Coordinates $coords)
    {
        global $config;
        $key = $config['maps']['mapQuestKey'];

        if(empty($key)){
            return null;
        }

        $lat = $coords->getLatitude();
        $lon = $coords->getLongitude();

        $url="https://www.mapquestapi.com/geocoding/v1/reverse?key=$key".
        "&location=$lat%2C$lon&outFormat=json&thumbMaps=false";

        $data = @file_get_contents($url);
        $resp = json_decode($data);

        $instance = new self();
        if(is_array($resp->results) && !empty($resp->results)){
            $data = $resp->results[0];
            if(is_array($data->locations) && !empty($data->locations)){
                $data = $data->locations[0];
                $instance->countryCode = $data->adminArea1;
                $instance->admCode = $data->adminArea3;
            }
        }

        $instance->countryName = $instance->countryCode;
        $instance->admName = $instance->admCode;

        return $instance;
    }

    public function getCountryCode(){
        return $this->countryCode;
    }

    public function getCountryName(){
        return $this->countryName;
    }

    public function getAdmCode(){
        return $this->admCode;
    }

    public function getAdmName(){
        return $this->admName;
    }

    public function getDescription($separator='-'){
        if($this->countryName){
            $country = $this->countryName;
        }else{
            $country = '?';
        }

        if($this->admName){
            $adm = $this->admName;
        }else{
            $adm = '?';
        }

        return $country.$separator.$adm;

    }
}

class GeoCodeServiceResult {
    public $name;
    public $layer;
    public $countryCode;
    public $region;
    public $bbox;
}
