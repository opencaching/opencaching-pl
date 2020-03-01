<?php
namespace src\Models\GeoCache;

/**
 * This class contains rules of which decide about allowed actions like:
 * - switching the geocache status
 * - log adding depends on log type
 * etc.
 *
 * https://docs.google.com/spreadsheets/d/1BpkpmusS98HQKmyJJpJhCQtKHOltdY1b7QE6f8poW8E/edit#gid=1336776289
 */
class GeoCacheRules extends GeoCacheCommons {

    /**
     * Return array of geocache statuses which can be the next status based on given current status
     *
     * @param int $currentStatus
     * @param boolean $isOwner
     * @param boolean $isOcTeamMember
     * @param boolean $isUserUnderVerification
     *
     * @return array of all possible next statuses (if empty no edit is allowed)
     */
    public static function getAllowedNextStatus ($currentStatus, $isOwner, $isOcTeamMember=false,
        $isUserUnderVerification=false)
    {
        if (!$isOwner && !$isOcTeamMember) {
            // Only owner or OcTea can switch status
            return [];
        }

        $next = [];
        switch ($currentStatus) {

            case self::STATUS_READY:
            case self::STATUS_UNAVAILABLE:

                $next[] = self::STATUS_READY;
                $next[] = self::STATUS_UNAVAILABLE;
                $next[] = self::STATUS_ARCHIVED;
                if ($isOcTeamMember) {
                    $next[] = self::STATUS_BLOCKED;
                }
                return $next;

            case self::STATUS_ARCHIVED:
                $next[] = self::STATUS_ARCHIVED;
                if ($isOcTeamMember) {
                    $next[] = self::STATUS_BLOCKED;
                    $next[] = self::STATUS_READY;
                }
                return $next;

            case self::STATUS_WAITAPPROVERS:
                $next[] = self::STATUS_WAITAPPROVERS;
                if ($isOcTeamMember) {
                    $next[] = self::STATUS_READY;
                    $next[] = self::STATUS_BLOCKED;
                }
                return $next;

            case self::STATUS_NOTYETAVAILABLE:
                $next[] = self::STATUS_UNAVAILABLE;
                $next[] = self::STATUS_ARCHIVED;
                $next[] = self::STATUS_NOTYETAVAILABLE;

                if (!$isUserUnderVerification) {
                    $next[] = self::STATUS_READY;
                } else {
                    $next[] = self::STATUS_WAITAPPROVERS;
                }

                if ($isOcTeamMember) {
                    $next[] = self::STATUS_READY;
                    $next[] = self::STATUS_BLOCKED;
                }
                return $next;

            case self::STATUS_BLOCKED:
                if ($isOcTeamMember) {
                    $next[] = self::STATUS_READY;
                    $next[] = self::STATUS_UNAVAILABLE;
                    $next[] = self::STATUS_ARCHIVED;
                    $next[] = self::STATUS_NOTYETAVAILABLE;
                    $next[] = self::STATUS_BLOCKED;
                }
                return $next;
        }

        // never should be here...`
        return [];
    }

}
