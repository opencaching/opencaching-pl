<?php

use lib\Objects\GeoCache\GeoCache;

/**
 * class is used for correct coordinates of geopath center point. (point used to represent geoPtah
 * location on map.
 */
class powerTrail_cCorection
{
    private $userId = 0;
    private $cacheId = 0;
    private $latitude = 0;
    private $longitude = 0;
    private $userArray = array();
    private $cacheType = 0;

    public function __construct($params)
    {
        $this->latitude = (float) $params['latitude'];
        $this->longitude = (float) $params['longitude'];
        $this->userId = (int) $_SESSION['user_id'];
        $this->cacheType = (int) $params['type'];
        $this->cacheId = (int) $params['cache_id'];
        $userrCollection = UserCollection::Instance();
        $this->userArray = $userrCollection->getUserCollection();
        $this->process();
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    private function process()
    {
        if($this->cacheType == GeoCache::TYPE_TRADITIONAL || $this->cacheType == GeoCache::TYPE_MULTICACHE){
            if(in_array($this->userId, $this->userArray)){
                $this->updateCoords();
            }
        }
    }

    private function takeDistance(){
        $lastchar = substr($this->cacheId, -1);
        switch ($lastchar) {
            case 0:
                return 40;
            case 1:
                return 45;
            case 2:
                return 50;
            case 3:
                return 52;
            case 4:
                return 65;
            case 5:
                return 57;
            case 6:
                return 60;
            case 7:
                return 70;
            case 8:
                return 75;
            case 9:
                return 56;
        }
    }

    private function updateCoords(){
        $meters = $this->takeDistance(); //Number of meters to calculate coords for north/south/east/west
        $equatorCircumference = 6371000; //meters
        $polarCircumference = 6356800; //meters
        $mPerDegLong = 360 / $polarCircumference;
        $radLat = ($this->latitude * M_PI / 180); //convert to radians, cosine takes a radian argument and not a degree argument
        $mPerDegLat = 360 / (cos($radLat) * $equatorCircumference);
        $degDiffLong = $meters * $mPerDegLong;  //Number of degrees latitude as you move north/south along the line of longitude
        $degDiffLat = $meters * $mPerDegLat; //Number of degrees longitude as you move east/west along the line of latitude
        $this->calcNewCoords($degDiffLong, $degDiffLat);
    }

    private function getDirectionInteger() {
        $direction = substr($this->cacheId, 0, 1);
        if($direction == 0 ) {
            return 5;
        }
        if($direction == 9) {
             return 8;
        }
        return $direction;
    }

    private function calcNewCoords($degDiffLong, $degDiffLat) {
        $direction = $this->getDirectionInteger();
        switch ($direction) {
            case 1:
                $this->latitude = $this->latitude + $degDiffLat;
                break;
            case 2:
                $this->longitude = $this->longitude + $degDiffLong;
                break;
            case 3:
                $this->latitude = $this->latitude - $degDiffLat;
                break;
            case 4:
                $this->longitude = $this->longitude - $degDiffLong;
                break;
            case 5:
                $this->latitude = $this->latitude + $degDiffLat;
                $this->longitude = $this->longitude + $degDiffLong;
                break;
            case 6:
                $this->latitude = $this->latitude + $degDiffLat;
                $this->longitude = $this->longitude - $degDiffLong;
                break;
            case 7:
                $this->latitude = $this->latitude - $degDiffLat;
                $this->longitude = $this->longitude - $degDiffLong;
                break;
            case 8:
                $this->latitude = $this->latitude - $degDiffLat;
                $this->longitude = $this->longitude + $degDiffLong;
        }
    }

}

final class UserCollection
{

    private $userArray = array();

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new UserCollection();
        }
        return $inst;
    }


    private function __construct()
    {
        include __DIR__.'/../lib/settingsGlue.inc.php';
        if(isset($userCollection)) {
            $this->userArray = $userCollection;
        }
    }

    public function getUserCollection()
    {
        return $this->userArray;
    }
}

