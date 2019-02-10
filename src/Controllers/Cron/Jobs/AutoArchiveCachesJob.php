<?php
/**
 * This script handles cache auto archiving process
 * It should be called from CRON quite often (to not delay messages)
 */

use src\Controllers\Cron\Jobs\Job;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheLog;
use src\Utils\Email\Email;
use src\Utils\Email\EmailFormatter;
use src\Utils\Generators\Uuid;
use src\Models\OcConfig\OcConfig;

class AutoArchiveCachesJob extends Job
{
    const STEP_0_STAGED = 0;
    const STEP_1_FIRST_MAIL_SENT = 1;
    const STEP_2_SECOND_MAIL_SENT = 2;
    const STEP_3_ARCHIVED = 3;
    const ARCHIVE_EVENT = 100;

    const TEMPLATE_DIR = __DIR__.'/../../../../tpl/stdstyle/email/autoarchive/';

    public function run()
    {
        $this->processCaches();

        $this->processEvents();
    }

    /**
     * Processes caches to auto archive in 3 steps procedure:
     *   1: first notification (4 months)
     *   2: second notification (5 months)
     *   3: auto archive
     */
    private function processCaches()
    {
        $this->cleanAutoArchDB();

        $stmt = $this->db->multiVariableQuery(
            "SELECT `cache_id`, `last_modified`
              FROM `caches`
              WHERE `status` = :1
                AND `last_modified` < NOW() - INTERVAL 4 MONTH",
            GeoCache::STATUS_UNAVAILABLE);

        while ($row = $this->db->dbResultFetch($stmt)) {
            $now = new DateTime();
            $lastModified = new DateTime($row['last_modified']);
            $cache = GeoCache::fromCacheIdFactory($row['cache_id']);
            $step = $this->db->multiVariableQueryValue(
                "SELECT `step` FROM `cache_arch` WHERE `cache_id` = :1",
                self::STEP_0_STAGED,
                $cache->getCacheId()
            );

            if ($step == self::STEP_0_STAGED && $lastModified->diff($now)->days > 4 * 31) {
                $this->proceedFirstStep($cache);
            } elseif ($step == self::STEP_1_FIRST_MAIL_SENT && $lastModified->diff($now)->days > 5 * 31) {
                $this->proceedSecondStep($cache);
            } elseif ($step == self::STEP_2_SECOND_MAIL_SENT && $lastModified->diff($now)->days > 6 * 31) {
                $this->archiveGeocache($cache);
            }

            unset($cache);
        }

        return $this;
    }

    /**
     * Send first notification (after 4 months)
     *
     * @param GeoCache $cache
     * @return AutoArchiveCachesJob
     */
    private function proceedFirstStep(GeoCache $cache)
    {
        $this->updateCacheStepInDb($cache, self::STEP_1_FIRST_MAIL_SENT)
            ->sendEmail($cache, self::STEP_1_FIRST_MAIL_SENT);

        return $this;
    }

    /**
     * Send second notification (after 5 months)
     *
     * @param GeoCache $cache
     * @return AutoArchiveCachesJob
     */
    private function proceedSecondStep(GeoCache $cache)
    {
        $this->updateCacheStepInDb($cache, self::STEP_2_SECOND_MAIL_SENT)
            ->sendEmail($cache, self::STEP_2_SECOND_MAIL_SENT);

        return $this;
    }

    /**
     * Archive Geocache (after 6 months)
     *
     * @param GeoCache $cache
     * @return AutoArchiveCachesJob
     */
    private function archiveGeocache(GeoCache $cache)
    {
        $this->db->beginTransaction();

        $this->updateCacheStepInDb($cache, self::STEP_3_ARCHIVED);
        $cache->updateStatus(GeoCache::STATUS_ARCHIVED);
        $this->db->multiVariableQuery(
            "INSERT INTO `cache_logs`
              (`cache_id`, `uuid`, `user_id`, `type`, `date`, `last_modified`,
               `date_created`, `text`, `owner_notified`, `node`)
              VALUES ( :1, :2, '-1', :3, NOW(), NOW(), NOW(), :4, 1, :5)",
            $cache->getCacheId(),
            Uuid::create(),
            GeoCacheLog::LOGTYPE_ARCHIVED,
            tr('autoArchiveLog'),
            OcConfig::getSiteNodeId());

        if ($this->db->commit()) {
            $this->sendEmail($cache, self::STEP_3_ARCHIVED);
        }

        return $this;
    }

    /**
     * Sends e-mail to cache owner about (planed) auto archiving
     *
     * @param GeoCache $cache
     * @param integer $reason
     */
    private function sendEmail(GeoCache $cache, $reason)
    {
        $email = new Email();
        if (!$email->addToAddr($cache->getOwner()->getEmail())) {
            return;
        }

        $email_template = self::TEMPLATE_DIR;
        $subject = '';
        switch ($reason) {
            case self::STEP_1_FIRST_MAIL_SENT:
                $email_template .= 'step1.email.html';
                $subject = tr('autoArchiveSubject_12');
                break;
            case self::STEP_2_SECOND_MAIL_SENT:
                $email_template .= 'step2.email.html';
                $subject = tr('autoArchiveSubject_12');
                break;
            case self::STEP_3_ARCHIVED:
                $email_template .= 'step3.email.html';
                $subject = tr('autoArchiveSubject_3');
                break;
            case self::ARCHIVE_EVENT:
                $email_template .= 'event.email.html';
                $subject = tr('autoArchiveEventSubject');
                break;
        }
        $subject .= ' '.$cache->getWaypointId().' '.$cache->getCacheName();

        $formattedMessage = new EmailFormatter($email_template, true);
        $formattedMessage->addFooterAndHeader($cache->getOwner()->getUserName(), true)
            ->setVariable('cacheName', $cache->getCacheName())
            ->setVariable('cacheWp', $cache->getGeocacheWaypointId())
            ->setVariable('cacheUrl', $this->ocConfig->getAbsolute_server_URI().$cache->getCacheUrl());

        $email->setFromAddr(OcConfig::getEmailAddrOcTeam());
        $email->setReplyToAddr(OcConfig::getEmailAddrOcTeam());
        $email->setFromAddr(OcConfig::getEmailAddrOcTeam());
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getEmailSubjectPrefix());
        $email->setHtmlBody($formattedMessage->getEmailContent());
        $email->send();
    }

    /**
     * Cleans cache_arch table. Removes:
     * - old data about auto archived caches
     * - caches modified less than 4 months ago
     * - caches which changed status (not temporary unavailable
     *
     * @return AutoArchiveCachesJob
     */
    private function cleanAutoArchDB()
    {
        // Remove old cache arch info
        $this->db->multiVariableQuery(
            'DELETE FROM `cache_arch` WHERE `step` = :1',
            self::STEP_3_ARCHIVED
        );

        // Cancel auto archive procedure if cache was modified or if cache status changed
        $this->db->multiVariableQuery(
            'DELETE FROM `cache_arch` WHERE `cache_arch`.`cache_id` IN (
              SELECT  `tmptable`.`cache_id` AS `cache_id` FROM (
                SELECT `caches`.`cache_id` as `cache_id`
                  FROM `caches`, `cache_arch`
                  WHERE `cache_arch`.`cache_id` = `caches`.`cache_id`
                    AND (`caches`.`last_modified` >= NOW() - INTERVAL 4 MONTH
                      OR `caches`.`status` <> :1)
                  ) `tmptable`
                )',
            GeoCache::STATUS_UNAVAILABLE
        );

        return $this;
    }

    /**
     * Updates stepNo in DB
     *
     * @param GeoCache $cache
     * @param integer $step
     * @return AutoArchiveCachesJob
     */
    private function updateCacheStepInDb(GeoCache $cache, $step)
    {
        $this->db->multiVariableQuery(
            "REPLACE INTO `cache_arch` (`cache_id`, `step`) VALUES (:1, :2 )",
            $cache->getCacheId(),
            $step
        );

        return $this;
    }

    /**
     * Archive all events older than 2 months
     */
    private function processEvents()
    {
        $stmt = $this->db->multiVariableQuery(
            "SELECT `cache_id`
            FROM `caches`
            WHERE `type` = :1
                  AND `status` <> :2
                  AND `date_hidden` < NOW() - INTERVAL 2 MONTH",
            GeoCache::TYPE_EVENT,
            GeoCache::STATUS_ARCHIVED
        );
        while ($row = $this->db->dbResultFetch($stmt)) {
            $cache = GeoCache::fromCacheIdFactory($row['cache_id']);
            $cache->updateStatus(GeoCache::STATUS_ARCHIVED);

            $this->db->multiVariableQuery(
                "INSERT INTO `cache_logs`
              (`cache_id`, `uuid`, `user_id`, `type`, `date`, `last_modified`,
               `date_created`, `text`, `owner_notified`, `node`)
              VALUES ( :1, :2, '-1', :3, NOW(), NOW(), NOW(), :4, 1, :5)",
                $cache->getCacheId(),
                Uuid::create(),
                GeoCacheLog::LOGTYPE_ARCHIVED,
                tr('autoArchiveEventLog'),
                OcConfig::getSiteNodeId());

            $this->sendEmail($cache, self::ARCHIVE_EVENT);
        }
    }
}
