<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;
use lib\Objects\User\User;
use lib\Objects\GeoCache\GeoCache;
use lib\Controllers\Php7Handler;

class AdminNote extends BaseObject
{

    const VERIFY_ALL = "1";
    const NO_VERIFY_ALL = "2";
    const BAN_STATS = "3";
    const UNBAN_STATS = "4";
    const BAN = "5";
    const UNBAN = "6";
    const CACHE_PASS = "7";
    const CACHE_BLOCKED = "8";
    const IGNORE_FOUND_LIMIT = "9";
    const IGNORE_FOUND_LIMIT_RM = "10";
    const NOTIFY_CACHES_ON = "11";
    const NOTIFY_CACHES_OFF = "12";
    const NOTIFY_LOGS_ON = "13";
    const NOTIFY_LOGS_OFF = "14";
    const ACTIVATE = "15";

    /** @var int */
    private $noteId;

    /** @var int */
    private $userId = null;

    /** @var User */
    private $user = null;

    /** @var int */
    private $adminId = null;

    /** @var User */
    private $admin = null;

    /** @var int */
    private $cacheId = null;

    /** @var GeoCache */
    private $cache = null;

    /** @var bool */
    private $automatic;

    /** @var \DateTime */
    private $date = null;

    /** @var string */
    private $content;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getNoteId()
    {
        return $this->noteId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if (is_null($this->user) && ! is_null($this->getUserId())) {
            $this->user = User::fromUserIdFactory($this->getUserId());
        }
        return $this->user;
    }

    /**
     * @return int
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @return User
     */
    public function getAdmin()
    {
        if (is_null($this->admin) && ! is_null($this->getAdminId())) {
            $this->admin = User::fromUserIdFactory($this->getAdminId());
        }
        return $this->admin;
    }

    /**
     * @return int
     */
    public function getCacheId()
    {
        return $this->cacheId;
    }

    /**
     * @return GeoCache
     */
    public function getCache()
    {
        if (is_null($this->cache) && ! is_null($this->getCacheId())) {
            $this->cache = GeoCache::fromCacheIdFactory($this->getCacheId());
        }
        return $this->cache;
    }

    /**
     * @return bool
     */
    public function isAutomatic()
    {
        return $this->automatic;
    }

    /**
     * Returns URL of AdminNote picture (that depends of 'automatic' setting)
     *
     * @return string
     */
    public function getAutomaticPictureUrl()
    {
        switch ($this->isAutomatic()) {
            case true:
                $result = '/tpl/stdstyle/images/misc/gears.svg';
                break;
            case false:
                $result = '/tpl/stdstyle/images/log/octeam.svg';
                break;
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getDateTime()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Return translaction key, which depends of automatically generated content
     *
     * @return string
     */
    public function getContentTranslationKey()
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

    /**
     * @param int $noteId
     */
    public function setNoteId($noteId)
    {
        $this->noteId = $noteId;
    }


    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        $this->user = null;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->setUserId($user->getUserId());
        $this->user = $user;
    }

    /**
     * @param int $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
        $this->admin = null;
    }

    /**
     * @param User $admin
     */
    public function setAdmin(User $admin)
    {
        $this->setAdminId($admin->getUserId());
        $this->admin = $admin;
    }

    /**
     * @param int $cacheId
     */
    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;
        $this->cache = null;
    }

    /**
     * @param GeoCache $cache
     */
    public function setCache(GeoCache $cache)
    {
        $this->setCacheId($cache->getCacheId());
        $this->cache = $cache;
    }

    /**
     * @param bool $automatic
     */
    public function setAutomatic($automatic)
    {
        $this->automatic = $automatic;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param int $noteId
     * @return AdminNote|NULL
     */
    public static function fromNoteIdFactory($noteId)
    {
        $obj = new self();
        try {
            $obj->loadByNoteId($noteId);
            return $obj;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param int $noteId
     * @throws \Exception
     */
    private function loadByNoteId($noteId)
    {
        $s = $this->db->multiVariableQuery(
            "SELECT * FROM `admin_user_notes` WHERE `note_id` = :1 LIMIT 1", $noteId);

        $dbRow = $this->db->dbResultFetchOneRowOnly($s);

        if(is_array($dbRow)) {
            $this->loadFromDbRow($dbRow);
        } else {
            throw new \Exception("No such admin note");
        }
    }

    /**
     * @param array $row
     */
    private function loadFromDbRow($row)
    {
        $this->setNoteId($row['note_id']);
        $this->setUserId($row['user_id']);
        $this->setAdminId($row['admin_id']);
        $this->setCacheId($row['cache_id']);
        $this->setAutomatic(Php7Handler::Boolval($row['automatic']));
        $this->setDate(new \DateTime($row['datetime']));
        $this->setContent($row['content']);
    }


    /**
     * @param int $adminId
     * @param int $userId
     * @param bool $automatic
     * @param string $message
     * @param int $cacheId
     */
    public static function addAdminNote($adminId, $userId, $automatic, $message, $cacheId = null) {
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
        $query = "INSERT INTO `admin_user_notes`(`user_id`, `admin_id`, `cache_id`, `automatic`, `content`) VALUES (:1, :2, :3, :4, :5)";
        $this->db->multiVariableQuery($query, $this->getUserId(), $this->getAdminId(), $this->getCacheId(), $this->isAutomatic(), $this->getContent());
    }

}