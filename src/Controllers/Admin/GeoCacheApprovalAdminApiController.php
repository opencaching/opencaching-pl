<?php

namespace src\Controllers\Admin;

use src\Controllers\Core\ApiBaseController;
use src\Models\Admin\GeoCacheApproval;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\GeoCache\GeoCacheLogCommons;
use src\Models\OcConfig\OcConfig;
use src\Utils\DateTime\OcDateTime;
use src\Utils\Email\Email;
use src\Utils\Email\EmailFormatter;
use src\Utils\Lock\Lock;
use src\Utils\Text\Formatter;
use src\Utils\Uri\HttpCode;

/**
 * Provides API for operations regarding approval of geocaches being under
 * OC Team supervision.
 */
class GeoCacheApprovalAdminApiController extends ApiBaseController
{
    public const TEMPLATE_PATH = __DIR__ . '/../../../resources/email/admin/';

    public function __construct()
    {
        parent::__construct();

        if (! $this->isUserLogged() || ! $this->loggedUser->hasOcTeamRole()) {
            // this controller is accessible only for OCTeam
            $this->ajaxErrorResponse(
                'Not authorized for this operation',
                HttpCode::STATUS_UNAUTHORIZED
            );
        }
    }

    /**
     * Retrieves list of caches waiting for approval
     */
    public function getWaiting()
    {
        $this->ajaxJsonResponse([
            'updated' => Formatter::date(OcDateTime::now(), true),
            'data' => GeoCacheApproval::getWaitingForApproval(),
        ]);
    }

    /**
     * Assigns current user (OC Team member) as a reviewer of give cache.
     *
     * @param int $cacheId id of the cache to assign user to
     */
    public function assign(int $cacheId)
    {
        $this->wrap($cacheId);
    }

    /**
     * Accepts (approves) given cache. Current user is assigned as a reviewer,
     * then cache status is updated to "not yet available", notification emails
     * are sent to a cache owner and current user and admin note is added to the
     * geocache log.
     *
     * @param int $cacheId id of the cache to accept
     */
    public function accept(int $cacheId)
    {
        $this->wrap(
            $cacheId,
            function (GeoCache $cache): array {
                $cache->updateStatus(GeoCacheCommons::STATUS_NOTYETAVAILABLE);

                // currently empty array, may be not empty in future
                return [];
            },
            function (GeoCache $cache): array {
                $this->sendApprovalActionMessages($cache);
                GeoCacheLog::newLog(
                    $cache->getCacheId(),
                    $this->loggedUser->getUserId(),
                    GeoCacheLogCommons::LOGTYPE_ADMINNOTE,
                    htmlspecialchars(tr('viewPending_03'))
                );

                // currently empty array, may be not empty in future
                return [];
            }
        );
    }

    /**
     * Rejects (declines) given cache. Current user is assigned as a reviewer,
     * then cache status is updated to "blocked", notification emails
     * are sent to a cache owner and current user and admin note is added to the
     * geocache log.
     *
     * @param int $cacheId id of the cache to reject
     */
    public function reject(int $cacheId)
    {
        $this->wrap(
            $cacheId,
            function (GeoCache $cache): array {
                $cache->updateStatus(GeoCacheCommons::STATUS_BLOCKED);

                // currently empty array, may be not empty in future
                return [];
            },
            function (GeoCache $cache): array {
                $this->sendApprovalActionMessages($cache, false);
                GeoCacheLog::newLog(
                    $cache->getCacheId(),
                    $this->loggedUser->getUserId(),
                    GeoCacheLogCommons::LOGTYPE_ADMINNOTE,
                    htmlspecialchars(tr('viewPending_06'))
                );

                // currently empty array, may be not empty in future
                return [];
            }
        );
    }

    /**
     * A common wrapping function for "assign", "accept" and "reject" operations.
     * Assigns current user as a reviewer for given cache, then fills up
     * resulting array with data useful for calling client. Then calls a wrapped
     * function, if provided, merging its result with resulting array. The
     * resulting array is returned in ajax response.
     *
     * @param int $cacheId id of the cache to accept
     * @param callable $wrappedLocked an optional function to call after
     *                                assigning reviewer, but still inside
     *                                the cache lock
     * @param callable $wrappedUnLocked an optional function to call after
     *                                  assigning reviewer and after
     *                                  wrappedLocked, outside of lock
     */
    private function wrap(
        int $cacheId,
        callable $wrappedLocked = null,
        callable $wrappedUnlocked = null
    ) {
        $resultData = [];

        $cache = null;
        $cacheAssigned = false;

        // Prevents concurrent work on the same geocache
        $lockHandle = Lock::tryLock(
            __CLASS__ . '_cache_id_'
            . (! empty($cacheId) ? $cacheId : 'no_cache'),
            Lock::EXCLUSIVE
        );

        if ($lockHandle) {
            $cache = GeoCache::fromCacheIdFactory($cacheId);

            if (
                ! empty($cache)
                && $cache->getStatus() === GeoCacheCommons::STATUS_WAITAPPROVERS
            ) {
                GeoCacheApproval::assignUserToCase(
                    $cache,
                    $this->loggedUser
                );
                $resultData['cache_id'] = $cacheId;
                $resultData['cache_name'] = $cache->getCacheName();
                $resultData['cache_wp'] = $cache->getWaypointId();
                $resultData['cache_owner'] = $cache->getOwner()->getUserName();
                $resultData['assigned_id'] = $this->loggedUser->getUserId();
                $resultData['assigned_username']
                    = $this->loggedUser->getUserName();
                $cacheAssigned = true;

                $resultData = $this->invokeWrapped(
                    $resultData,
                    $cache,
                    $wrappedLocked
                );
            }

            Lock::unlock($lockHandle);
        }

        if ($cacheAssigned) {
            $resultData = $this->invokeWrapped(
                $resultData,
                $cache,
                $wrappedUnlocked
            );
            $resultData['updated'] = Formatter::date(
                OcDateTime::now(),
                true
            );
        } else {
            $this->ajaxErrorResponse(
                tr(
                    'cache_approval_cache_invalid',
                    [
                        ! empty($cache)
                        ? $cache->getCacheName()
                            . ' - ' . $cache->getWaypointId()
                        : '',
                    ]
                ),
                HttpCode::STATUS_OK
            );
        }

        $this->ajaxSuccessResponse(null, $resultData);
    }

    /**
     * Invoked wrapped function if not empty, merging its result with
     * $resultData
     *
     * @return array updated $resultData after invocation
     */
    private function invokeWrapped(
        array $resultData,
        GeoCache $cache,
        callable $wrapped = null
    ): array {
        if (! empty($wrapped)) {
            $wrappedResultData = $wrapped($cache);

            if (! empty($wrappedResultData)) {
                $resultData = array_merge(
                    $resultData,
                    $wrappedResultData
                );
            }
        }

        return $resultData;
    }

    /**
     * Prepares a notification email for given user and operation.
     *
     * @param GeoCacche $cache a geocache to prepare message from
     * @param bool $isAccepted true if operation is "accept", false for "reject"
     */
    private function prepareApprovalActionMessage(
        GeoCache $cache,
        bool $isAccepted = true
    ): EmailFormatter {
        $message = new EmailFormatter(
            self::TEMPLATE_PATH . 'approval_action_cache_'
            . ($isAccepted ? 'activated' : 'archived')
            . '.email.html',
            true
        );
        $message->setVariable(
            'absoluteServerUri',
            OcConfig::getAbsolute_server_URI()
        );
        $message->setVariable('cacheName', $cache->getCacheName());
        $message->setVariable('wp', $cache->getWaypointId());
        $message->setVariable('viewCacheUrl', $cache->getCacheUrl());

        if ($isAccepted) {
            $message->setVariable(
                'editCacheUrl',
                'editcache.php?cacheid=' . $cache->getCacheId()
                // TODO: IMHO editcache.php shouldn't be hardcoded
            );
        }

        $message->addFooterAndHeader(
            $cache->getOwner()->getUserName(),
            false
        );

        return $message;
    }

    /**
     * Sends notification emails: to geocache owner and a copy to current user
     * (reviewer).
     *
     * @param GeoCacche $cache a geocache to send message about
     * @param bool $isAccepted true if operation is "accept", false for "reject"
     */
    private function sendApprovalActionMessages(
        GeoCache $cache,
        bool $isAccepted = true
    ) {
        $message = $this->prepareApprovalActionMessage($cache, $isAccepted);

        foreach ([$cache->getOwner(), $this->loggedUser] as $user) {
            $email = new Email();
            $email->addToAddr($user->getEmail());
            $email->setFromAddr(OcConfig::getEmailAddrOcTeam());
            $email->setReplyToAddr(OcConfig::getEmailAddrOcTeam());
            $email->addSubjectPrefix(OcConfig::getEmailSubjectPrefix());
            $email->setSubject(
                tr($isAccepted ? 'viewPending_01' : 'viewPending_04')
                . ': '
                . $cache->getCacheName()
            );
            $email->setHtmlBody(
                (
                    $user == $this->loggedUser
                    ? (
                        tr($isAccepted ? 'viewPending_02' : 'viewPending_05')
                        . ":<br><br>\n"
                    )
                    : ''
                )
                . $message->getEmailContent()
            );
            $email->send();
        }
    }
}
