<?php

namespace src\Models\GeoKret;

use src\Models\BaseObject;
use ErrorException;

/**
 * This class contain methods used to communicate with Geokrety, via Geokrety Api
 * (http://geokrety.org/api.php)
 *
 * @author Andrzej Łza Woźniak 2012, 2013
 *
 */
class GeoKretyApi extends BaseObject
{
    const GEOKRETY_URL = 'https://geokrety.org';

    // Operation types
    const OPERATION_TAKE_USER_GEOKRETS = 1;
    const OPERATION_TAKE_GEOKRETS_IN_CACHE = 2;

    private $secId = null;
    private $cacheWpt = null;
    private $maxID = null;
    private $connectionTimeout = 16;

    function __construct($secId = null, $cacheWpt = null)
    {
        parent::__construct();
        $this->secId = $secId;
        $this->cacheWpt = $cacheWpt;
    }

    /**
     * sends request to geokrety and receive all geokrets in user inventory
     *
     * @return array contains all geokrets in user inventory
     */
    private function TakeUserGeokrets()
    {
        $url = self::GEOKRETY_URL."/export2.php?secid=$this->secId&inventory=1";
        $xml = $this->connect($url, self::OPERATION_TAKE_USER_GEOKRETS);
        libxml_use_internal_errors(true);
        if ($xml) {
            try {
                $result = simplexml_load_string($xml);
            } catch (\Exception $e) {
                $this->storeErrorsInDb(self::OPERATION_TAKE_USER_GEOKRETS, $url);
                return [];
            }
        } else {
            $result = [];
        }
        return $result;
    }

    /**
     * sends request to geokrety and receive all geokrets in specified cache
     *
     * @return array contains all geokrets in cache
     */
    private function TakeGeoKretsInCache()
    {
        $url = self::GEOKRETY_URL."/export2.php?wpt=$this->cacheWpt";
        $xml = $this->connect($url, self::OPERATION_TAKE_GEOKRETS_IN_CACHE);

        if ($xml) {
            libxml_use_internal_errors(true);
            try {
                $result = simplexml_load_string($xml);
            } catch (\Exception $e) {
                $this->storeErrorsInDb(self::OPERATION_TAKE_GEOKRETS_IN_CACHE, $url);

                return [];
            }
        } else {
            $result = [];
        }

        return $result;
    }

    /**
     * generate html-formatted list of all geokrets in user inventory.
     * This string is used in logging cache (log.php, log_cache.tpl.php)
     *
     * @param string $cacheName
     * @return string (html)
     */
    public function MakeGeokretSelector($cacheName)
    {
        $krety = $this->TakeUserGeokrets();
        if (empty($krety)) {
            return tr('GKApi28');
        }
        $selector = '<table class="table">';
        $MaxNr = 0;
        $jsclear = 'onclick="this.value=\'\'" onblur="formDefault(this)"';
        foreach ($krety->geokrety->geokret as $kret) {
            $MaxNr++;
            $selector .= '<tr class="form-group-sm geoKretLog">
                            <td>
                              <a href="'.self::GEOKRETY_URL.'/konkret.php?id=' . $kret->attributes()->id . '" class="links">' . $kret . '</a>
                            </td>
                            <td>
                              <select id="GeoKretSelector' . $MaxNr . '" name="GeoKretIDAction' . $MaxNr . '[action]" onchange="GkActionMoved(' . $MaxNr . ')" class="form-control input200"><option value="-1">' . tr('GKApi13') . '</option><option value="0">' . tr('GKApi12') . '</option><option value="5">' . tr('GKApi14') . '</option></select>
                              <input type="hidden" name="GeoKretIDAction' . $MaxNr . '[nr]" value="' . $kret->attributes()->nr . '"><span id="GKtxt' . $MaxNr . '" style="display: none">&nbsp;' . tr('GKApi25') . ': <input type="text" name="GeoKretIDAction' . $MaxNr . '[tx]" maxlength="80" class="form-control input200" value="' . tr('GKApi24') . ' ' . $cacheName . '" ' . $jsclear . ' /></span>
                              <input type="hidden" name="GeoKretIDAction' . $MaxNr . '[id]" value="' . $kret->attributes()->id . '">
                              <input type="hidden" name="GeoKretIDAction' . $MaxNr . '[nm]" value="' . $kret . '" />
                             </td>
                         </tr>';
        }
        $selector .= '</table>';
        $selector .= '<input type="hidden" name=MaxNr value="' . $MaxNr . '">';
        $this->maxID = $MaxNr; //value set for use in MakeGeokretInCacheSelector method.
        return $selector;
    }

    public function MakeGeokretInCacheSelector($cachename)
    {
        $krety = $this->TakeGeoKretsInCache();
        if (empty($krety)) {
            return tr('GKApi28');
        }
        if (count($krety->geokrety->geokret) == 0) {
            return tr('GKApi29');
        }
        $selector = '<table class="table">';
        $MaxNr = $this->maxID;
        $jsclear = 'onclick="this.value=\'\'" onblur="formDefault(this)"';
        foreach ($krety->geokrety->geokret as $kret) {
            $MaxNr++;
            $selector .= '<tr class="form-group-sm">
                            <td>
                              <a href="'.self::GEOKRETY_URL.'/konkret.php?id=' . $kret->attributes()->id . '" class="links">' . $kret . '</a>
                            </td>
                            <td>
                              <select id="GeoKretSelector' . $MaxNr . '" name="GeoKretIDAction' . $MaxNr . '[action]" onchange="GkActionMoved(' . $MaxNr . ')" class="form-control input120"><option value="-1">' . tr('GKApi13') . '</option><option value="1">' . tr('GKApi15') . '</option><option value="2">' . tr('GKApi16') . '</option><option value="3">' . tr('GKApi17') . '</option></select>
                              <span id="GKtxt' . $MaxNr . '" style="display: none">&nbsp; tracking code: <input type="text" maxlength="6" size="6" class="form-control input50" name="GeoKretIDAction' . $MaxNr . '[nr]">&nbsp; ' . tr('GKApi25') . ': <input type="text" name="GeoKretIDAction' . $MaxNr . '[tx]" maxlength="40" class="form-control input200" value="' . tr('GKApi26') . ' ' . $cachename . '" ' . $jsclear . ' /></span>
                              <input type="hidden" name="GeoKretIDAction' . $MaxNr . '[id]" value="' . $kret->attributes()->id . '" />
                              <input type="hidden" name="GeoKretIDAction' . $MaxNr . '[nm]" value="' . $kret . '" />
                            </td>
                         </tr>';
        }
        $selector .= '</table>';
        $selector .= '<input type="hidden" name=MaxNr value="' . $MaxNr . '">';
        return $selector;
    }

    private function connect($url, $operationType)
    {
        $opts = array('http' =>
            array(
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'timeout' => $this->connectionTimeout,
            )
        );
        $context = stream_context_create($opts);
        
        try{
            $response = FALSE; // TEM DISABLE: @file_get_contents($url, false, $context);
        } catch (Exception $e){
            $response = FALSE;
        }
        
        if ($response) {
            $result = $response;
        } else {
            $this->storeErrorsInDb($operationType, $url);
            $result = false;
        }
        return $result;
    }

    private function storeErrorsInDb($operationType, $dataSent, $response = null)
    {
        $query = "INSERT INTO `GeoKretyAPIerrors`(`dateTime`, `operationType`, `dataSent`, `response`)
                  VALUES (NOW(),:1,:2,:3)";
        $this->db->multiVariableQuery($query, $operationType, addslashes(serialize($dataSent)),
            addslashes(serialize($response)));
    }

}
