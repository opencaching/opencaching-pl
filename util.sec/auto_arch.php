<?php
/**
 * This script handles cache auto archivisation process
 * It should be called from CRON quite often (to not delay messages)
 */

use Utils\Database\OcDb;
use Utils\Log\Log;

$rootpath = __DIR__ . '/../';
require_once($rootpath . 'lib/common.inc.php');

class AutoArch
{
    private $step = array(
        "START" => 0,
        "AFTER_FIRST_MAIL_SENT" => 1,
        "AFTER_SECOND_MAIL_SENT" => 2,
        "ARCH_COMPLETE" => 3
    );

    /**
     *  @var $ocConfig \lib\Objects\OcConfig\OcConfig
     */
    private $ocConfig;
    private $stylepath;

    public function __construct($stylepath)
    {
        $this->ocConfig = lib\Objects\OcConfig\OcConfig::instance();
        $this->stylepath = $stylepath;
    }

  /**
     * step:
     *   1: first notification (4 months)
     *   2: second notification (5 months)
     *   3: autoarchive finished
     */
    public function run()
    {
        $this->removeLastEditedCachesFromList();

        $db = OcDb::instance();

        $s = $db->simpleQuery(
            "SELECT cache_id, last_modified FROM caches WHERE status = 2 AND last_modified < now() - interval 4 month");

        $chachesToProcess = $db->dbResultFetchAll($s);
        foreach ($chachesToProcess as $rs) {

            $s = $db->multiVariableQuery(
                "SELECT step FROM cache_arch WHERE cache_id = :1 LIMIT 1", (int) $rs['cache_id']);
            $step_array = $db->dbResultFetchOneRowOnly($s);

            if ($step_array) {
                $step = (int) $step_array['step'];
            } else {
                $step = $this->step["START"];
            }
            if (strtotime($rs['last_modified']) < time() - 6 * 31 * 24 * 60 * 60 && $step == $this->step["AFTER_SECOND_MAIL_SENT"]) {
                $this->archiveGeocache($rs);
            } elseif (strtotime($rs['last_modified']) < time() - 5 * 31 * 24 * 60 * 60 && $step == $this->step["AFTER_FIRST_MAIL_SENT"]) {
                $this->proceedSecondStep($rs);
            } elseif (strtotime($rs['last_modified']) < time() - 4 * 31 * 24 * 60 * 60 && $step == $this->step["START"]) {
                $this->proceedFirstStep($rs);
            }
        }
    }


    private function sendEmail($step, $cacheid)
    {
        $octeamEmailAddress = $this->ocConfig->getOcteamEmailAddress();
        $siteName = $this->ocConfig->getSiteName();
        $cache = new \lib\Objects\GeoCache\GeoCache(array('cacheId' => (int) $cacheid));
        switch ($step) {
            case $this->step["START"]:
                $email_content = file_get_contents($this->stylepath . '/email/arch1.email');
                break;
            case $this->step["AFTER_FIRST_MAIL_SENT"]:
                $email_content = file_get_contents($this->stylepath . '/email/arch2.email');
                break;
            case $this->step["AFTER_SECOND_MAIL_SENT"]:
                $email_content = file_get_contents($this->stylepath . '/email/arch3.email');
                break;
        }
        $email_content = mb_ereg_replace('{server}', $this->ocConfig->getAbsolute_server_URI(), $email_content);
        $email_content = mb_ereg_replace('{autoArchive_01}', tr('autoArchive_01'), $email_content);
        $email_content = mb_ereg_replace('{autoArchive_02}', tr('autoArchive_02'), $email_content);
        $email_content = mb_ereg_replace('{autoArchive_03}', tr('autoArchive_03'), $email_content);
        $email_content = mb_ereg_replace('{autoArchive_04}', tr('autoArchive_04'), $email_content);
        $email_content = mb_ereg_replace('{autoArchive_05}', tr('autoArchive_05'), $email_content);
        $email_content = mb_ereg_replace('{autoArchive_06}', tr('autoArchive_06'), $email_content);
        $email_content = mb_ereg_replace('{autoArchive_07}', tr('autoArchive_07'), $email_content);
        $email_content = mb_ereg_replace('{autoArchive_08}', tr('autoArchive_08'), $email_content);
        $email_content = mb_ereg_replace('{autoArchive_09}', tr('autoArchive_09'), $email_content);
        $email_content = mb_ereg_replace('{autoArchive_10}', tr('autoArchive_10'), $email_content);
        $email_content = mb_ereg_replace('{cachename}', $cache->getCacheName(), $email_content);
        $email_content = mb_ereg_replace('{cache_wp}', $cache->getWaypointId(), $email_content);
        $email_content = mb_ereg_replace('{cacheid}', $cacheid, $email_content);
        $email_content = mb_ereg_replace('{octeamEmailsSignature}', $this->ocConfig->getOcteamEmailsSignature(), $email_content);
        $emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
        $emailheaders .= "From: $siteName <$octeamEmailAddress>\r\n";
        $emailheaders .= "Reply-To: $siteName <$octeamEmailAddress>";
        $status = mb_send_mail($cache->getOwner()->getEmail(), tr('autoArchive_11'), $email_content, $emailheaders);
        if(!$status){
            error_log(__FILE__.':'.__LINE__.': Mail sending failure: to:'.$cache->getOwner()->getEmail());
        }
        Log::logentry('autoarchive', 6, $cache->getOwner()->getUserId(), $cache->getCacheId(), 0, 'Sending mail to ' . $cache->getOwner()->getEmail(), array('status' => $status));
    }

    private function loadCachesToProcess()
    {
        $db = OcDb::instance();

        $s = $db->simpleQuery(
            "SELECT cache_arch.step, caches.cache_id, caches.name, user.username
            FROM `cache_arch`, caches, user
            WHERE (step=1 OR step=2 OR step=3)
                AND (caches.cache_id = cache_arch.cache_id)
                AND (caches.user_id = user.user_id)
            ORDER BY step ASC");
        return $db->dbResultFetchAll($s);
    }

    /**
     * anulowanie procedury archiwizacji, jeśli opis skrzynki został zmodyfikowany w ciągu 6 miesięcy
     */
    private function removeLastEditedCachesFromList()
    {
        $db = OcDb::instance();

        $s = $db->simpleQuery(
            'SELECT caches.cache_id as cacheId FROM caches, cache_arch
            WHERE cache_arch.cache_id = caches.cache_id
                AND last_modified >= now() - interval 4 month ');
        $cachesToRm = $db->dbResultFetchAll($s);
        foreach ($cachesToRm as $cacheToRm) {
            $delSqlQuery = "DELETE FROM cache_arch WHERE cache_id = :1 LIMIT 1";
            $db->multiVariableQuery($delSqlQuery, (int) $cacheToRm['cacheId']);
        }
    }

    /**
     * 6 months from last
     * @param type $rs
     */
    private function archiveGeocache($rs)
    {
        global $oc_nodeid;
        $db = OcDb::instance();

        $statusSqlQuery = "REPLACE INTO cache_arch (cache_id, step) VALUES ( :1, :2 )";
        $archSqlQuery = "UPDATE caches SET status = 3 WHERE cache_id= :1 " ;
        $logSqlQuery = "INSERT INTO cache_logs (cache_id, uuid, user_id, type, date, last_modified,
                                    date_created, text, owner_notified, node)
                        VALUES ( :1, :2, '-1', 9,NOW(),NOW(), NOW(), :3, 1, :4)";

        $db->beginTransaction();

        $db->multiVariableQuery($statusSqlQuery, (int) $rs['cache_id'], $this->step["ARCH_COMPLETE"]);
        $db->multiVariableQuery($archSqlQuery, (int) $rs['cache_id']);
        $db->multiVariableQuery($logSqlQuery, (int) $rs['cache_id'],  create_uuid(), tr('autoArchive_12'), $oc_nodeid);

        if ($db->commit()) {
            $this->sendEmail($this->step["AFTER_SECOND_MAIL_SENT"], $rs['cache_id']);
        }
    }

    /**
     * second notification
     * @param type $rs
     */
    private function proceedSecondStep($rs)
    {
        $this->updateCacheStepInDb($rs, $this->step["AFTER_SECOND_MAIL_SENT"]);
        $this->sendEmail($this->step["AFTER_FIRST_MAIL_SENT"], $rs['cache_id']);
    }

    /**
     * first notification
     */
    private function proceedFirstStep($rs)
    {
        $this->updateCacheStepInDb($rs, $this->step["AFTER_FIRST_MAIL_SENT"]);
        $this->sendEmail($this->step["START"], $rs['cache_id']);
    }

    private function updateCacheStepInDb($rs, $step)
    {
        $db = OcDb::instance();
        $db->multiVariableQuery(
            "REPLACE INTO cache_arch (cache_id, step) VALUES (:1, :2 )", (int) $rs['cache_id'], $step);
    }

    /**
     * archive all events older than 2 months
     */
    function ArchEvent()
    {
        $db = OcDb::instance();
        $db->simpleQuery(
            "UPDATE caches SET status = 3
            WHERE status<>3 AND type = 6 AND date_hidden < now() - interval 2 month");
    }
}


$autoArch = new AutoArch($stylepath);
$autoArch->run();
$autoArch->ArchEvent();
