<?php

namespace lib\Objects\GeoKret;

use Utils\Database\OcDb;
/**
 * This class contain methods used to communicate with Geokrety, via Geokrety Api
 * (http://geokrety.org/api.php)
 *
 * @author Andrzej Łza Woźniak 2012, 2013
 *
 */
class GeoKretyApi
{
    const GEOKRETY_URL = 'https://geokrety.org';


    private $secid = null;
    private $cacheWpt = null;
    private $maxID = null;
    private $server = null;
    private $rtEmailAddress = null;
    private $GeoKretyDeveloperEmailAddress = null;
    private $operationTypes = array(
        'TakeUserGeokrets' => 1,
        'TakeGeoKretsInCache' => 2,
        'LogGeokrety' => 3,
    );
    private $connectionTimeout = 16;

    function __construct($secid = null, $cacheWpt = null)
    {
        include 'lib/settingsGlue.inc.php';
        $this->server = $absolute_server_URI;
        $this->secid = $secid;
        $this->cacheWpt = $cacheWpt;
        $this->rtEmailAddress = $dberrormail;
        $this->geoKretyDeveloperEmailAddress = $geoKretyDeveloperEmailAddress;
    }

    /**
     * sends request to geokrety and receive all geokrets in user inventory
     *
     * @return array contains all geokrets in user inventory
     */
    private function TakeUserGeokrets()
    {
        $url = self::GEOKRETY_URL."/export2.php?secid=$this->secid&inventory=1";
        $xml = $this->connect($url, $this->operationTypes[__FUNCTION__]);
        libxml_use_internal_errors(true);
        if ($xml) {
            try {
                $result = simplexml_load_string($xml);
            } catch (Exception $e) {
                $this->storeErrorsInDb($this->operationTypes[__FUNCTION__], $url);
                // $this->emailOnError($e->getMessage(), $url, $result, 'function: '.__FUNCTION__ .'line # '.__LINE__.' in '.__FILE__);
                return false;
            }
        } else {
            $result = false;
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
        $xml = $this->connect($url, $this->operationTypes[__FUNCTION__]);

        if ($xml) {
            libxml_use_internal_errors(true);
            try {
                $result = simplexml_load_string($xml);
            } catch (Exception $e) {
                $this->storeErrorsInDb($this->operationTypes[__FUNCTION__], $url);
                //$this->emailOnError($e->getMessage(), $url, $result, 'function: '.__FUNCTION__ .' line # '.__LINE__.' in '.__FILE__);
                return false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Make html table-formatted list of user geokrets. ready to display anywhere.
     * @return string (html)
     */
    public function MakeGeokretList()
    {
        $krety = $this->TakeUserGeokrets();
        $lista = tr('GKApi23') . ': ' . count($krety->geokrety->geokret) . '<br>';
        $lista .= '<table>';
        foreach ($krety->geokrety->geokret as $kret) {
            $lista .= '<tr><td></td><td><a href="'.self::GEOKRETY_URL.'/konkret.php?id=' . $kret->attributes()->id . '">' . $kret . '</a></td></tr>';
        }
        $lista .= '</table>';
        echo $lista;
    }

    /**
     * generate html-formatted list of all geokrets in user inventory.
     * This string is used in logging cache (log.php, log_cache.tpl.php)
     *
     * @return string (html)
     */
    public function MakeGeokretSelector($cachename)
    {
        $krety = $this->TakeUserGeokrets();
        if ($krety === false)
            return tr('GKApi28');

        $selector = '<table class="table">';
        $MaxNr = 0;
        $jsclear = 'onclick="this.value=\'\'" onblur="formDefault(this)"';
        foreach ($krety->geokrety->geokret as $kret) {
            $MaxNr++;
            $selector .= '<tr class="form-group-sm">
                            <td>
                              <a href="'.self::GEOKRETY_URL.'/konkret.php?id=' . $kret->attributes()->id . '" class="links">' . $kret . '</a>
                            </td>
                            <td>
                              <select id="GeoKretSelector' . $MaxNr . '" name="GeoKretIDAction' . $MaxNr . '[action]" onchange="GkActionMoved(' . $MaxNr . ')" class="form-control input120"><option value="-1">' . tr('GKApi13') . '</option><option value="0">' . tr('GKApi12') . '</option><option value="5">' . tr('GKApi14') . '</option></select>
                              <input type="hidden" name="GeoKretIDAction' . $MaxNr . '[nr]" value="' . $kret->attributes()->nr . '"><span id="GKtxt' . $MaxNr . '" style="display: none">&nbsp;' . tr('GKApi25') . ': <input type="text" name="GeoKretIDAction' . $MaxNr . '[tx]" maxlength="80" class="form-control input200" value="' . tr('GKApi24') . ' ' . $cachename . '" ' . $jsclear . ' /></span>
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
        if ($krety == false)
            return tr('GKApi28');
        if (count($krety->geokrety->geokret) == 0)
            return tr('GKApi29');
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



    private function emailOnError($error = '', $Tablica = '', $result, $errorLocation = 'Unknown error location')
    {
        $message = "GeoKretyApi error report: \r\n " . $error . "\n
             \r\n location of error: $errorLocation \n
             \r\n Array sent to geokrety:\r\n\r\n $Tablica \r\n\r\n  geokrety.org returned wrong result (ansfer is not in xml, or there is no ansfer). \r\n
            date/time: " . date('Y-m-d H:i:s') . "
            GeoKrety.org ansfer (there was no ansfer if empty): \r\n \r\n $result ";

        $headers = 'From: GeoKretyAPI on opencaching' . "\r\n" .
                'Reply-To: rt@opencaching.pl' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        // check if working developer server or production.
        if ($this->server == 'http://local.opencaching.pl/') {
            $rtAddress = array('user@ocpl-devel');
        } else {
            $rtAddress = array($this->rtEmailAddress, $this->geoKretyDeveloperEmailAddress);
        }

        foreach ($rtAddress as $email) {
            mail($email, 'GeoKretyApi returned error', $message, $headers);
        }
    }

    private function xml2array($xml)
    {
        $arr = array();

        foreach ($xml->children() as $r) {
            $t = array();
            if (count($r->children()) == 0) {
                $arr[$r->getName()] = strval($r);
            } else {
                $arr[$r->getName()][] = $this->xml2array($r);
            }
        }
        return $arr;
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
        @$response = file_get_contents($url, false, $context);
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
        $db = OcDb::instance();
        $query = "INSERT INTO `GeoKretyAPIerrors`(`dateTime`, `operationType`, `dataSent`, `response`)
                  VALUES (NOW(),:1,:2,:3)";
        $db->multiVariableQuery($query, $operationType, addslashes(serialize($dataSent)), addslashes(serialize($response)));
    }

    public function setGeokretyTimeout($newTimeout)
    {
        $this->connectionTimeout = $newTimeout;
    }

    public static function getErrorsFromDb()
    {
        $db = OcDb::instance();
        $s = $db->simpleQuery("SELECT * FROM `GeoKretyAPIerrors` WHERE 1");
        return $db->dbResultFetchAll($s);
    }

    public static function removeDbRows($rowsString)
    {
        $db = OcDb::instance();
        $query = "DELETE FROM `GeoKretyAPIerrors` WHERE id in ($rowsString)";
        $db->simpleQuery($query);
    }

    public function mailToRT($errorArray)
    {
        $message = "GeoKrety Api timeout errors: date/time: " . date('Y-m-d H:i:s') . "\r\n \r\n";
        $message .= print_r($errorArray, true);
        $headers = 'From: GeoKretyAPI on opencaching.pl <noreply@opencaching.pl>' . "\r\n" .
                'Reply-To: rt@opencaching.pl' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
        $rtAddress = array(
            // $this->rtEmailAddress, // send also debug email to rt
            $this->geoKretyDeveloperEmailAddress
        );

        foreach ($rtAddress as $email) {
            $send = mail($email, 'GeoKretyApi errors report ' . date('Y-m-d H:i:s'), $message, $headers);
            if (!$send)
                return false;
        }
        return true;
    }

    public function getOperationTypes()
    {
        return $this->operationTypes;
    }

}


