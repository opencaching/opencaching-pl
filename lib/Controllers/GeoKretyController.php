<?php

namespace lib\Controllers;

use lib\Objects\GeoKret\GeoKretLog;
use lib\Objects\GeoKret\GeoKretLogError;
use Utils\Database\OcDb;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\User\User;
use lib\Objects\GeoCache\GeoCache;



/**
 * Description of GeoKretyController
 *
 * @author Łza
 */
class GeoKretyController
{

     private $connectionTimeout = 30;

    /**
     *
     * @param array $geoKretogs
     * (array of GeoKretLog)
     */
    public function enqueueGeoKretLogs($geoKretogs)
    {
        /* @var $geoKretLog GeoKretLog */
        $query = 'INSERT INTO `geokret_log`(`log_date_time`, `enqueue_date_time`, `user_id`, `geocache_id`, `log_type`, `comment`, `tracking_code`, `geokret_id`, `geokret_name`) VALUES ';
        $paramId = 1;
        foreach ($geoKretogs as $geoKretLog) {
            $query .= '(:'.$paramId++.', NOW(), :'.$paramId++.','
                 . ' :'.$paramId++.', :'.$paramId++.', :'.$paramId++.', :'.$paramId++.','
                 . ' :'.$paramId++.', :'.$paramId++.'),';
            $queryParams[] = $geoKretLog->getLogDateTime()->format('Y-m-d H:i:s');
            $queryParams[] = $geoKretLog->getUser()->getUserId();
            $queryParams[] = $geoKretLog->getGeoCache()->getCacheId();
            $queryParams[] = $geoKretLog->getLogType();
            $queryParams[] = $geoKretLog->getComment();
            $queryParams[] = $geoKretLog->getTrackingCode();
            $queryParams[] = $geoKretLog->getGeoKretId();
            $queryParams[] = $geoKretLog->getGeoKretName();
        }
        $query = rtrim($query,',');
        $this->executeInsertQuery($query, $queryParams);
    }

    public function buildPostParams(GeoKretLog $geoKretyLog)
    {
        $ocConfig = OcConfig::instance();
        return [
            'secid' => $geoKretyLog->getUser()->getGeokretyApiSecid(),
            'nr' => $geoKretyLog->getTrackingCode(),
            'id' => $geoKretyLog->getGeoKretId(),
            'nm' => $geoKretyLog->getGeoKretName(),
            'formname' => 'ruchy',
            'logtype' => $geoKretyLog->getLogType(),
            'data' => $geoKretyLog->getLogDateTime()->format('Y-m-d'),
            'godzina' => $geoKretyLog->getLogDateTime()->format('H'),
            'minuta' => $geoKretyLog->getLogDateTime()->format('i'),
            'comment' => substr($geoKretyLog->getComment(), 0, 80) . ' (autom. log oc.' . substr($ocConfig->getAbsolute_server_URI(), -3, 2) . ')',
            'wpt' => $geoKretyLog->getGeoCache()->getWaypointId(),
            'app' => 'Opencaching',
            'app_ver' => 'PL'
        ];
    }

   /**
     * Function logs Geokret on geokrety.org using GeoKretyApi.
     * @author Łza
     * @param array $GeokretyArray
     * @return boolean
     */
    public function logGeokretyFromQueue()
    {
        $result = [];
        $safeCheck = 0;
        while(count($geoKretyLogs = $this->loadGeokretyLogsFromDb()) > 0) {
            foreach ($geoKretyLogs as $geoKretyLog) {
                $loggingResult = $this->sendLog($geoKretyLog);
                $this->parseGeokretLogActionResult($loggingResult, $geoKretyLog);
                if($geoKretyLog->isLoggingError() === false) {
                    $this->removeLogFromQueue($geoKretyLog);
                } else {
                    $result[] = $geoKretyLog;
                }
            }
            if($safeCheck++ > 500){
                break;
            }
        }
        return $result;
    }

    private function removeLogFromQueue(GeoKretLog $geoKretyLog)
    {
        $query = 'DELETE FROM `geokret_log` WHERE id = :1';
        $db = OcDb::instance();
        $db->multiVariableQuery($query, $geoKretyLog->getId());
    }

    private function parseGeokretLogActionResult($geokretLogActionResult, GeoKretLog $geoKretyLog)
    {
        $geokretLogActionResultXml = $this->geokretyXmlFix($geokretLogActionResult);
        try {
            @$gkResponse = simplexml_load_string($geokretLogActionResultXml);
            $exceptionMessage = '';
        } catch (Exception $e) {
            $exceptionMessage = $e->getMessage();
        }
        if ($gkResponse) {
            $this->processGeokretyResponse($gkResponse, $geoKretyLog);
        } else { /* error, notify user */
            $geoKretLogError = new GeoKretLogError();
            $geoKretLogError->errorMessage = 'No response From Geokrety webservice. '.$exceptionMessage;
            $geoKretyLog->appendGeoKretLogErrors($geoKretLogError);
        }
    }

    private function processGeokretyResponse($gkResponse, GeoKretLog $geoKretyLog)
    {
        $errArr = $gkResponse->errors->error;
        foreach ($errArr as $error) {
            $geoKretLogError = new GeoKretLogError();
            $msg = $error->__toString();
            if($msg != ''){
                $this->checkDoubleLoggingMessage($msg, $geoKretyLog);
                $geoKretLogError->errorMessage = $msg;
                $geoKretyLog->appendGeoKretLogErrors($geoKretLogError);
            }
        }
    }

    /**
     * remove log from queue when geokrety responed with doulbe logging error message.
     * @param type $param
     */
    private function checkDoubleLoggingMessage($msg, GeoKretLog $geoKretyLog)
    {
        if($msg == 'There is an entry with this date. Correct the date or the hour.'){
            $this->removeLogFromQueue($geoKretyLog);
        }
    }

    /**
     * geoKrety returns by default not valid xml. Fix it.
     *
     * @param type $xml
     * @return type
     */
    private function geokretyXmlFix($xml)
    {
        $notValidNode = '<head/>';
        return str_replace($notValidNode, '', $xml);
    }

    private function sendLog($geoKretyLog)
    {
        $postdata = http_build_query($this->buildPostParams($geoKretyLog));
        $opts = ['http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => $this->connectionTimeout,
            ],
        ];

        $context = stream_context_create($opts);
        @$result = file_get_contents('http://geokrety.org/ruchy.php', false, $context);
        if (!$result) {
            $geoKretLogError = new GeoKretLogError();
            $geoKretLogError->errorMessage = 'Unable to connect to Geokrety Webservice';
            $geoKretyLog->appendGeoKretLogErrors($geoKretLogError);
//            $this->storeErrorsInDb($this->operationTypes[__FUNCTION__], $GeokretyArray);
        }
        return $result;
    }


    private function builtGeokretyLogFromDbRow($gkLogRow)
    {
        $geoKretyLog = new GeoKretLog();
        $geoKretyLog
            ->setId($gkLogRow['id'])
            ->setLogDateTime(new \DateTime($gkLogRow['log_date_time']))
            ->setEnqueueDatetime(new \DateTime($gkLogRow['enqueue_date_time']))
            ->setUser(new User(['userId' => $gkLogRow['user_id']]))
            ->setGeoCache(new GeoCache(['cacheId' => $gkLogRow['geocache_id']]))
            ->setLogType($gkLogRow['log_type'])
            ->setComment($gkLogRow['comment'])
            ->setTrackingCode($gkLogRow['tracking_code'])
            ->setGeoKretId($gkLogRow['geokret_id'])
            ->setGeoKretName($gkLogRow['geokret_name'])
        ;
        return $geoKretyLog;
    }

    private function loadGeokretyLogsFromDb()
    {
        $query = 'SELECT * FROM `geokret_log` WHERE 1 LIMIT 50';
        $db = OcDb::instance();
        $dbResult = $db->dbResultFetchAll($db->simpleQuery($query));
        $geoKretyLogs = [];
        foreach ($dbResult as $gkLogRow) {
            $geoKretyLogs[] = $this->builtGeokretyLogFromDbRow($gkLogRow);
        }
        return $geoKretyLogs;
    }

    private function executeInsertQuery($query, $params)
    {
        $db = OcDb::instance();
        $db->multiVariableQuery($query, $params);
    }

    private function buildMultipleLogXml($geoKretyLogs)
    {
        $xmlDom = new \DOMDocument('1.0', 'utf-8');
        $gkRoot = $xmlDom->createElement('geoKrety');
        $xmlDom->appendChild($gkRoot);
        $geokretyLogsNode = $gkRoot->appendChild($xmlDom->createElement('geokretyLogs'));
        $applicationDetailsNode = $xmlDom->createElement('applicationDetails');
        $gkRoot->appendChild($applicationDetailsNode);
        $applicationDetailsNode->appendChild($xmlDom->createElement('name', 'Opencaching'));
        $applicationDetailsNode->appendChild($xmlDom->createElement('version', 'PL'));
        $applicationDetailsNode->appendChild($xmlDom->createElement('generated', date('Y-m-d H:i:s')));
        $gkRoot->appendChild($geokretyLogsNode);

        $ocConfig = OcConfig::instance();
        foreach ($geoKretyLogs as $geoKretyLog) {
            $geoKretyLogNode = $xmlDom->createElement('geokretyLog');
            $geokretyLogsNode->appendChild($geoKretyLogNode);
            $geoKretyLogNode->appendChild($xmlDom->createElement('secid', $geoKretyLog->getUser()->getGeokretyApiSecid()));
            $geoKretyLogNode->appendChild($xmlDom->createElement('trackingCode', $geoKretyLog->getTrackingCode()));
            $geoKretyLogNode->appendChild($xmlDom->createElement('logtype', $geoKretyLog->getLogType()));
            $geoKretyLogNode->appendChild($xmlDom->createElement('date',  $geoKretyLog->getLogDateTime()->format('Y-m-d')));
            $geoKretyLogNode->appendChild($xmlDom->createElement('hour', $geoKretyLog->getLogDateTime()->format('H')));
            $geoKretyLogNode->appendChild($xmlDom->createElement('minute', $geoKretyLog->getLogDateTime()->format('H')));
            $geoKretyLogNode->appendChild($xmlDom->createElement('comment', substr($geoKretyLog->getComment(), 0, 80) . ' (autom. log oc.' . substr($ocConfig->getAbsolute_server_URI(), -3, 2) . ')' ));
            $geoKretyLogNode->appendChild($xmlDom->createElement('geocacheWaypoint',  $geoKretyLog->getGeoCache()->getWaypointId() ));
        }
        return $xmlDom->saveXML();
    }

}
