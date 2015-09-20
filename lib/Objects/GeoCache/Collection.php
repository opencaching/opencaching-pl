<?php
/**
 * Created by PhpStorm.
 * User: £za
 * Date: 2015-09-20
 * Time: 19:37
 */

namespace lib\Objects\GeoCache;


class Collection extends \ArrayObject
{
    private $isReady = false;

    private $geocachesIdArray = array();

    public function append($geoCache)
    {
        if($geoCache instanceof GeoCache){
            parent::append($geoCache);
            $this->isReady = true;
        }
    }

    public function isReady()
    {
        return $this->isReady;
    }

    /**
     * Set array contain geocaches identifiers (equivalent to database caches.cache_id)
     * @param array $geocachesIdArray
     * @return Collection
     */
    public function setGeocachesIdArray(array $geocachesIdArray)
    {
        $this->geocachesIdArray = $geocachesIdArray;
        return $this;
    }


}