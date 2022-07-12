<?php

namespace src\Models\Admin;

use DateTime;
use src\Models\BaseObject;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\User\User;

class GeoCacheApproval extends BaseObject
{
    public const STATUS_ASSIGNED = 2;

    /**
     * ID of the related geocache
     *
     * @var int
     */
    private $cacheId = null;

    /**
     * ID of the assigned user
     *
     * @var int
     */
    private $userId = null;

    /**
     * Approval status
     *
     * @var int
     */
    private $status = null;

    /**
     * Approval status update datetime
     *
     * @var DateTime
     */
    private $dateApproval = null;

    /**
     * @param int $cacheId geocache database identifier (optional)
     * @throws Exception
     */
    public function __construct(?int $cacheId)
    {
        parent::__construct();

        if (isset($cacheId)) {
            $this->loadByCacheId($cacheId);
        }
    }

    /**
     * Factory
     *
     * @return GeoCacheApproval|null (null if geocache with given id is not found)
     */
    public static function fromCacheIdFactory(int $cacheId)
    {
        try {
            return new self($cacheId);
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * @throws Exception
     */
    private function loadByCacheId(int $cacheId)
    {
        $s = $this->db->multiVariableQuery(
            'SELECT * FROM approval_status WHERE cache_id = :1 LIMIT 1',
            $cacheId
        );

        $cacheApprovalDbRow = $this->db->dbResultFetch($s);

        if (is_array($cacheApprovalDbRow)) {
            $this->loadFromRow($cacheApprovalDbRow);
        } else {
            throw new Exception('Cache approval status not found');
        }
    }

    /**
     * Load object data based on DB data-row
     *
     * @throws Exception
     */
    private function loadFromRow(array $geocacheApprovalDbRow)
    {
        $this->cacheId = $geocacheApprovalDbRow['cache_id'];
        $this->userId = $geocacheApprovalDbRow['user_id'];
        $this->status = $geocacheApprovalDbRow['status'];

        if (! empty($geocacheApprovalDbRow['date_approval'])) {
            $this->dateApproval = new DateTime(
                $geocacheApprovalDbRow['date_approval']
            );
        }
    }

    public static function getWaitingForApprovalCount()
    {
        return self::db()->multiVariableQueryValue(
            'SELECT COUNT(status) FROM caches WHERE status = :1',
            0,
            GeoCacheCommons::STATUS_WAITAPPROVERS
        );
    }

    /**
     * Retrieves all caches waiting for approval, including additional data
     * needed to display in approval view table rows.
     * @throws Exception
     */
    public static function getWaitingForApproval(): array
    {
        $stmt = self::db()->multiVariableQuery(
            "SELECT
                cache_owner.username AS username,
                cache_owner.user_id AS user_id,
                caches.cache_id AS cache_id,
                caches.name AS cachename,
                IFNULL(`cache_location`.`adm3`, '') AS `adm3`,
                caches.date_created AS date_created,
                last_log.id AS last_log_id,
                last_log.date AS last_log_date,
                last_log.user_id AS last_log_author,
                log_author.username AS last_log_username,
                last_log.text AS last_log_text,
                assigned_user.user_id AS assigned_user_id,
                assigned_user.username AS assigned_user_name
            FROM
                `caches`
                LEFT JOIN `cache_location`
                    ON `caches`.`cache_id` = `cache_location`.`cache_id`
                LEFT JOIN (
                    SELECT
                        id,
                        cache_id,
                        text,
                        user_id,
                        date
                    FROM
                        cache_logs logs
                    WHERE
                        date = (
                            SELECT
                                MAX(date)
                            FROM
                                cache_logs
                            WHERE
                                cache_id = logs.cache_id
                        )
                ) AS last_log
                    ON caches.cache_id = last_log.cache_id
                LEFT JOIN user AS cache_owner
                    ON caches.user_id = cache_owner.user_id
                LEFT JOIN user AS log_author
                    ON last_log.user_id = log_author.user_id
                LEFT JOIN approval_status
                    ON caches.cache_id = approval_status.cache_id
                LEFT JOIN user AS assigned_user
                    ON approval_status.user_id = assigned_user.user_id
            WHERE
                caches.status = :1
            GROUP BY
                caches.cache_id
            ORDER BY
                caches.date_created DESC",
            GeoCacheCommons::STATUS_WAITAPPROVERS
        );

        return self::db()->dbResultFetchAll($stmt);
    }

    public static function getInReviewCount()
    {
        return self::db()->multiVariableQueryValue(
            'SELECT COUNT(*)
             FROM caches
                JOIN approval_status USING(cache_id)
             WHERE caches.status = :1',
            0,
            GeoCacheCommons::STATUS_WAITAPPROVERS
        );
    }

    /**
     * Factory
     * @param int $cacheId
     * @return GeoCacheApproval|null (null if no $returnInstance)
     * @throws Exception
     */
    public static function assignUserToCase(
        GeoCache $cache,
        User $user,
        bool $returnInstance = false
    ) {
        $result = null;

        if (! empty($cache->getCacheId()) && ! empty($user->getUserId())) {
            self::db()->multiVariableQuery(
                'INSERT INTO approval_status
                (cache_id, user_id, status, date_approval)
                VALUES (:1, :2, :3, NOW())
                ON DUPLICATE KEY UPDATE user_id = :2',
                $cache->getCacheId(),
                $user->getUserId(),
                self::STATUS_ASSIGNED
            );

            if ($returnInstance) {
                $result = new self($cache->getCacheId());
            }
        }

        return $result;
    }
}
