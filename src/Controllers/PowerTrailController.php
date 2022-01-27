<?php

namespace src\Controllers;

use DateTime;
use sendEmail;
use src\Models\PowerTrail\Log;
use src\Models\PowerTrail\PowerTrail;
use src\Models\User\User;
use src\Utils\Database\OcDb;

class PowerTrailController
{
    public const MINIMUM_PERCENT_REQUIRED = 67;

    public static function getEntryTypes(): array
    {
        return [
            Log::TYPE_COMMENT => [ //comment
                'translate' => 'pt056',
                'color' => '#000000',
            ],
            Log::TYPE_CONQUESTED => [ // conquested
                'translate' => 'cs_gainedCount',
                'color' => '#00CC00',
            ],
            Log::TYPE_OPENING => [ // geoPath Publishing
                'translate' => 'pt214',
                'color' => '#0000CC',
            ],
            Log::TYPE_DISABLING => [ // geoPath temp. closed
                'translate' => 'pt216',
                'color' => '#CC0000',
            ],
            Log::TYPE_CLOSING => [ // geoPath Closure (permanent)
                'translate' => 'pt213',
                'color' => '#CC0000',
            ],
            Log::TYPE_ADD_WARNING => [ // oc team comment (permanent)
                'translate' => 'pt237',
                'color' => '#CC0000',
            ],
        ];
    }

    /**
     * Adds comment to specified PowerTrail
     */
    public function addComment(PowerTrail $powerTrail, User $user, DateTime $dateTime, $type, $text): bool
    {
        $log = new Log();
        $result = $log->setPowerTrail($powerTrail)
            ->setDateTime($dateTime)
            ->setUser($user)
            ->setType($type)
            ->setText($text)
            ->storeInDb();

        if ($result) {
            sendEmail::emailOwners($powerTrail->getId(), $log->getType(), $dateTime->format('Y-m-d H:i'), $text, 'newComment');
        }

        return $result;
    }

    /**
     * used to set geoPath status to inactive, when has too small amount of caches,
     * etc.
     */
    public function cleanPowerTrailsCronjob()
    {
        $this->archiveAbandonPowerTrails();
        $this->freeCacheCandidates();
    }

    private function archiveAbandonPowerTrails()
    {
        $db = OcDb::instance();
        $archiveAbandonQuery = 'SELECT `id` FROM `PowerTrail` WHERE `id` NOT IN (SELECT PowerTrailId FROM `PowerTrail_owners` WHERE 1 GROUP BY PowerTrailId)';
        $s = $db->simpleQuery($archiveAbandonQuery);

        if ($db->rowCount($s) > 0) { // close all abandon geoPaths
            $ptToClose = $db->dbResultFetchAll($s);
            $updateArr = [];

            foreach ($ptToClose as $pt) {
                $updateArr[] = $pt['id'];
            }
            $updateArr = implode(',', $updateArr);
            $updQuery = 'UPDATE `PowerTrail` SET `status` =3 WHERE `id` IN ( :1 )';
            $db->multiVariableQuery($updQuery, $updateArr);
        }
    }

    private function freeCacheCandidates()
    {
        $db = OcDb::instance();
        $query = 'DELETE FROM `PowerTrail_cacheCandidate` WHERE `date` < DATE_SUB(curdate(), INTERVAL 2 WEEK)';
        $db->simpleQuery($query);
    }

    /**
     * power Trail statuses
     */
    public static function getPowerTrailStatus(): array
    {
        return [
            1 => 'cs_statusPublic',
            2 => 'cs_statusNotYetAvailable', // not yet available
            4 => 'cs_statusInService', // service
            3 => 'cs_statusClosed', // archived
        ];
    }
}
