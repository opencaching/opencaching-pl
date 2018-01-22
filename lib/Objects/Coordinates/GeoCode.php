<?php

namespace lib\Objects\Coordinates;

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
     * Reverse geocoder based on Google Geocoding API
     * @see https://developers.google.com/maps/documentation/geocoding/intro#ReverseGeocoding
     *
     * @param Coordinates $coords
     * @return GeoCode|NULL
     */
    public static function fromGoogleApi(Coordinates $coords)
    {
        global $googlemap_key, $lang; //TODO: refactor configs

        $lat = $coords->getLatitude();
        $lon = $coords->getLongitude();

        if(empty($googlemap_key)){
            return null;
        }

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lon" .
        "&key=$googlemap_key&language=$lang&result_type=administrative_area_level_1";

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
