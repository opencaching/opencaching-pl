<?php

namespace src\Models\Admin;

use DateTime;
use Exception;
use src\Models\BaseObject;
use src\Models\GeoCache\GeoCache;
use src\Models\User\User;

class AdminNote extends BaseObject
{
    public const VERIFY_ALL = '1';

    public const NO_VERIFY_ALL = '2';

    public const BAN_STATS = '3';

    public const UNBAN_STATS = '4';

    public const BAN = '5';

    public const UNBAN = '6';

    public const CACHE_PASS = '7';

    public const CACHE_BLOCKED = '8';

    public const IGNORE_FOUND_LIMIT = '9';

    public const IGNORE_FOUND_LIMIT_RM = '10';

    public const NOTIFY_CACHES_ON = '11';

    public const NOTIFY_CACHES_OFF = '12';

    public const NOTIFY_LOGS_ON = '13';

    public const NOTIFY_LOGS_OFF = '14';

    public const ACTIVATE = '15';

    private const AUTOMATIC_PICTURE_URL = '/images/misc/gears.svg';

    private const MANUAL_PICTURE_URL = '/images/log/octeam.svg';

    private int $noteId;

    private ?int $userId = null;

    private ?User $user = null;

    private ?int $adminId = null;

    private ?User $admin = null;

    private ?int $cacheId = null;

    private ?GeoCache $cache = null;

    private bool $automatic;

    private ?DateTime $date = null;

    private string $content;

    public function getNoteId(): int
    {
        return $this->noteId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUser(): ?User
    {
        if (is_null($this->user) && ! is_null($this->getUserId())) {
            $this->user = User::fromUserIdFactory($this->getUserId());
        }

        return $this->user;
    }

    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    public function getAdmin(): ?User
    {
        if (is_null($this->admin) && ! is_null($this->getAdminId())) {
            $this->admin = User::fromUserIdFactory($this->getAdminId());
        }

        return $this->admin;
    }

    public function getCacheId(): ?int
    {
        return $this->cacheId;
    }

    public function getCache(): ?GeoCache
    {
        if (is_null($this->cache) && ! is_null($this->getCacheId())) {
            $this->cache = GeoCache::fromCacheIdFactory($this->getCacheId());
        }

        return $this->cache;
    }

    public function isAutomatic(): bool
    {
        return $this->automatic;
    }

    /**
     * Returns URL of AdminNote picture (that depends on 'automatic' setting)
     */
    public function getAutomaticPictureUrl(): string
    {
        return $this->isAutomatic() ? self::AUTOMATIC_PICTURE_URL : self::MANUAL_PICTURE_URL;
    }

    public function getDateTime(): ?DateTime
    {
        return $this->date;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Return translation key, which depends on automatically generated content
     */
    public function getContentTranslationKey(): string
    {
        if (! $this->isAutomatic()) { // Check for sure
            return 'unknown';
        }

        switch ($this->getContent()) {
            case self::VERIFY_ALL:
                $result = 'admin_notes_1';
                break;
            case self::NO_VERIFY_ALL:
                $result = 'admin_notes_2';
                break;
            case self::BAN_STATS:
                $result = 'admin_notes_3';
                break;
            case self::UNBAN_STATS:
                $result = 'admin_notes_4';
                break;
            case self::BAN:
                $result = 'admin_notes_5';
                break;
            case self::UNBAN:
                $result = 'admin_notes_6';
                break;
            case self::CACHE_PASS:
                $result = 'admin_notes_7';
                break;
            case self::CACHE_BLOCKED:
                $result = 'admin_notes_8';
                break;
            case self::IGNORE_FOUND_LIMIT:
                $result = 'admin_notes_9';
                break;
            case self::IGNORE_FOUND_LIMIT_RM:
                $result = 'admin_notes_10';
                break;
            case self::NOTIFY_CACHES_ON:
                $result = 'admin_notes_11';
                break;
            case self::NOTIFY_CACHES_OFF:
                $result = 'admin_notes_12';
                break;
            case self::NOTIFY_LOGS_ON:
                $result = 'admin_notes_13';
                break;
            case self::NOTIFY_LOGS_OFF:
                $result = 'admin_notes_14';
                break;
            case self::ACTIVATE:
                $result = 'admin_notes_15';
                break;
            default:
                $result = 'unknown';
                break;
        }

        return $result;
    }

    public function setNoteId(int $noteId)
    {
        $this->noteId = $noteId;
    }

    public function setUserId(?int $userId)
    {
        $this->userId = $userId;
        $this->user = null;
    }

    public function setUser(User $user)
    {
        $this->setUserId($user->getUserId());
        $this->user = $user;
    }

    public function setAdminId(?int $adminId)
    {
        $this->adminId = $adminId;
        $this->admin = null;
    }

    public function setAdmin(User $admin)
    {
        $this->setAdminId($admin->getUserId());
        $this->admin = $admin;
    }

    public function setCacheId(?int $cacheId)
    {
        $this->cacheId = $cacheId;
        $this->cache = null;
    }

    public function setCache(GeoCache $cache)
    {
        $this->setCacheId($cache->getCacheId());
        $this->cache = $cache;
    }

    public function setAutomatic(bool $automatic)
    {
        $this->automatic = $automatic;
    }

    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    public function setContent(?string $content)
    {
        $this->content = $content;
    }

    public static function fromNoteIdFactory(int $noteId): ?AdminNote
    {
        $obj = new self();

        try {
            $obj->loadByNoteId($noteId);

            return $obj;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @throws Exception
     */
    private function loadByNoteId(int $noteId)
    {
        $s = $this->db->multiVariableQuery(
            'SELECT * FROM `admin_user_notes` WHERE `note_id` = :1 LIMIT 1',
            $noteId
        );

        $dbRow = $this->db->dbResultFetchOneRowOnly($s);

        if (is_array($dbRow)) {
            $this->loadFromDbRow($dbRow);
        } else {
            throw new Exception('No such admin note');
        }
    }

    /**
     * @throws Exception
     */
    private function loadFromDbRow(array $row)
    {
        $this->setNoteId($row['note_id']);
        $this->setUserId($row['user_id']);
        $this->setAdminId($row['admin_id']);
        $this->setCacheId($row['cache_id']);
        $this->setAutomatic(boolval($row['automatic']));
        $this->setDate(new DateTime($row['datetime']));
        $this->setContent($row['content']);
    }

    public static function addAdminNote(int $adminId, int $userId, bool $automatic, string $message, int $cacheId = null)
    {
        $note = new AdminNote();
        $note->setAdminId($adminId);
        $note->setUserId($userId);
        $note->setAutomatic($automatic);
        $note->setContent($message);
        $note->setCacheId($cacheId);
        $note->insertNoteIntoDb();
    }

    private function insertNoteIntoDb()
    {
        $query = 'INSERT INTO `admin_user_notes`(`user_id`, `admin_id`, `cache_id`, `automatic`, `content`) VALUES (:1, :2, :3, :4, :5)';
        $this->db->multiVariableQuery(
            $query,
            $this->getUserId(),
            $this->getAdminId(),
            $this->getCacheId(),
            (int) $this->isAutomatic(),
            $this->getContent()
        );
    }
}
