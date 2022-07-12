<?php

namespace src\Controllers;

use Exception;
use src\Models\GeoKret\GeoKretLog;
use src\Models\GeoKret\GeoKretyApi;
use src\Utils\Debug\Debug;

/**
 * This class processing GeoKrety logs queue (stored in DB).
 * It tries to send each GK log and remove processed logs from queue;
 */
class GeoKretyLogController extends BaseController
{
    public const CONNECTION_TIMEOUT = 30;

    private $lockFile;

    private string $lockFileName;

    private bool $lockAcquired = false;

    private bool $printDebugMsgs = false;

    public function __construct()
    {
        parent::__construct();
        $this->lockFileName = $this->ocConfig->getDynamicFilesPath() . '/tmp/geoKretyLogProcessing.lock';
    }

    public function __destruct()
    {
        if ($this->lockAcquired) {
            $this->unlock();
        }
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        // this controlled is planned to be called only from cron (not by router)
        return false;
    }

    public function index()
    {
        // There is nothing to do here
        // This controller is called by other scripts
    }

    /**
     * Process GK queue - this is an entry point to this controller
     * @param $runFrom - path to script which call GK processing
     */
    public function runQueueProcessing(string $runFrom)
    {
        if (! $this->tryLock()) {
            $this->debug("Fatal error: Can't lock queue processing! Another instance is running?!");
            Debug::errorLog("GK-queue-processing ERROR: can't lock queue! Source: " . $runFrom);

            return;
        }

        $this->lockAcquired = true;
        $this->debug('Lock has been acquired.');

        $logsProcessed = 0;
        $queueLen = GeoKretLog::GetDbQueueLength();

        $this->debug("GK logs in queue: {$queueLen}");

        while ($logsProcessed < $queueLen
            && 0 < count($geoKretyLogs = GeoKretLog::GetLast50LogsFromDb())) {
            $idsToRemove = [];
            $idsToUpdate = [];

            foreach ($geoKretyLogs as $gkl) {
                $responseData = $this->sendLog($gkl);

                if (is_null($responseData)) {
                    // connection error!
                    $this->debug("Can't connect to GK API - give up for now!");
                    $this->updateDbQueue($idsToRemove, $idsToUpdate);

                    return;
                }

                if ($this->isResponseOK($responseData, $gkl)) {
                    $idsToRemove[] = $gkl->getId();
                } else {
                    $idsToUpdate[] = $gkl->getId();
                }
                $logsProcessed++;
            } //foreach

            $this->updateDbQueue($idsToRemove, $idsToUpdate);
        }
    }

    public function enableDebugMsgs()
    {
        $this->printDebugMsgs = true;
    }

    private function updateDbQueue(array $idsToRemove, array $idsToUpdate)
    {
        //remove processed entries and update date of last try for others
        GeoKretLog::RemoveFromQueueByIds($idsToRemove);
        GeoKretLog::UpdateLastTryForIds($idsToUpdate);
    }

    private function buildPostParams(GeoKretLog $geoKretyLog): array
    {
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
            'comment' => substr($geoKretyLog->getComment(), 0, 80)
                . ' (autom. log oc.' . substr($this->ocConfig->getAbsolute_server_URI(), -3, 2) . ')',
            'wpt' => $geoKretyLog->getGeoCache()->getWaypointId(),
            'app' => 'Opencaching',
            'app_ver' => 'PL',
        ];
    }

    /**
     * return true if log was accepted by GK API
     */
    private function isResponseOK(string $responseData, GeoKretLog $gkLog): bool
    {
        $gkLogDesc = $gkLog->getDescription();

        // geoKrety returns invalid xml - fix it.
        $responseData = str_replace('<head/>', '', $responseData);

        try {
            $responseXML = simplexml_load_string($responseData);
        } catch (Exception $e) {
            $this->debug($gkLogDesc . 'ERROR: Incorrect XML in response from GK API!');

            return false;
        }

        if (! $responseXML) {
            $this->debug($gkLogDesc . 'ERROR: Empty response from GK API XML!');

            return false;
        }

        if (empty($responseXML->errors->error)) {
            $this->debug($gkLogDesc . '...OK');

            return true;
        }

        foreach ($responseXML->errors->error as $error) {
            $errorMsg = $error->__toString();

            if (! empty($errorMsg)) {
                if ($this->isItDuplicatedLogError($errorMsg)) {
                    // this is duplicated log - skip processing of this entry
                    return true;
                }

                $this->debug($gkLogDesc . 'ERROR: ' . $errorMsg);
            }
        }

        return false;
    }

    /**
     * check if this error is about log duplication
     */
    private function isItDuplicatedLogError(string $msg): bool
    {
        return $msg == 'There is an entry with this date. Correct the date or the hour.';
    }

    /**
     * Send GK log request to GK API.
     * Try 5 times and then give up.
     *
     * @return string|null - response from GK API or null on connect error
     */
    private function sendLog(GeoKretLog $geoKretyLog): ?string
    {
        $tries = 0;

        while ($tries++ <= 5) {
            $opts = ['http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($this->buildPostParams($geoKretyLog)),
                'timeout' => self::CONNECTION_TIMEOUT,
            ],
            ];

            $context = stream_context_create($opts);
            $result = file_get_contents(GeoKretyApi::GEOKRETY_URL . '/ruchy.php', false, $context);

            if ($result !== false) {
                // connection OK, return results
                return $result;
            }

            // can't connect - try again...
        }

        return null;
    }

    private function tryLock(): bool
    {
        $this->lockFile = fopen($this->lockFileName, 'w');

        return flock($this->lockFile, LOCK_EX | LOCK_NB);
    }

    private function unlock()
    {
        $this->debug('Unlocking GK processing...');
        fclose($this->lockFile);
    }

    private function debug(string $msg)
    {
        if ($this->printDebugMsgs) {
            echo $msg . "<br/>\n";
        }
    }
}
