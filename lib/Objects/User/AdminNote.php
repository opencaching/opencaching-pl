<?php
/**
 * Created by PhpStorm.
 * User: mzylowski
 * Date: 25.02.16
 * Time: 20:19
 */

namespace lib\Objects\User;

use Utils\Database\OcDb;

class AdminNote
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

    private $note_id;
    private $user_id;
    private $admin_id;
    private $cache_id;
    private $automatic;
    private $datetime;
    private $content;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getNoteId()
    {
        return $this->note_id;
    }

    /**
     * @param int $note_id
     */
    public function setNoteId($note_id)
    {
        $this->note_id = $note_id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getAdminId()
    {
        return $this->admin_id;
    }

    /**
     * @param int $admin_id
     */
    public function setAdminId($admin_id)
    {
        $this->admin_id = $admin_id;
    }

    /**
     * @return bool
     */
    public function getAutomatic()
    {
        return $this->automatic;
    }

    /**
     * @param bool $automatic
     */
    public function setAutomatic($automatic)
    {
        $this->automatic = $automatic;
    }

    /**
     * @return string
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @return int
     */
    public function getCacheId()
    {
        return $this->cache_id;
    }

    /**
     * @param int $cache_id
     */
    public function setCacheId($cache_id)
    {
        $this->cache_id = $cache_id;
    }


    private function addNoteIntoDb()
    {
        $db = OcDb::instance();
        $query = "INSERT INTO `admin_user_notes`(`user_id`, `admin_id`, `automatic`, `content`) VALUES (:1, :2, :3, :4)";
        $db->multiVariableQuery($query, $this->getUserId(), $this->getAdminId(), $this->getAutomatic(), $this->getContent());
    }

    private function addNoteIntoDbWithCacheId()
    {
        $db = OcDb::instance();
        $query = "INSERT INTO `admin_user_notes`(`user_id`, `admin_id`, `cache_id`, `automatic`, `content`) VALUES (:1, :2, :3, :4, :5)";
        $db->multiVariableQuery($query, $this->getUserId(), $this->getAdminId(), $this->getCacheId(), $this->getAutomatic(), $this->getContent());
    }

    public static function addAdminNote($admin_id, $user_id, $automatic, $message, $cache_id = -1) {
        $note = new AdminNote();
        $note->setAdminId($admin_id);
        $note->setUserId($user_id);
        $note->setAutomatic($automatic);
        $note->setContent($message);
        if($cache_id == -1) {
            $note->addNoteIntoDb();
        }
        else {
            $note->setCacheId($cache_id);
            $note->addNoteIntoDbWithCacheId();
        }
    }

    public static function getAllUserNotes($user_id)
    {
        $results = array();
        $i = 0;
        $db = OcDb::instance();
        $query = "SELECT `admin_id`, `cache_id`, `automatic`, `datetime`, `content` FROM `admin_user_notes` WHERE `user_id`=:1 ORDER BY `datetime` DESC";
        $s = $db->multiVariableQuery($query, $user_id);
        while (true)
        {
            $cacheDbRow = $db->dbResultFetch($s);
            if(is_array($cacheDbRow)) {
                $results[$i] = $cacheDbRow;
                $i++;
            }
            else {
                for ($j=0; $j < $i; $j++) {
                    $query_for_username = "SELECT `username` FROM `user` WHERE `user_id`=:1 LIMIT 1";
                    $stmt = $db->multiVariableQuery($query_for_username, $results[$j]["admin_id"]);
                    $admin_name = $db->dbResultFetchOneRowOnly($stmt);
                    $results[$j]["admin_username"] = $admin_name["username"];
                }
                return $results;
            }
        }
    }
}

