<?php
/**
 * Contains \lib\Objects\Watchlist\WatchlistWatcher class definition
 */
namespace lib\Objects\Watchlist;

use lib\Objects\BaseObject;
use lib\Controllers\Php7Handler;

/**
 * Represents single row data from watchlist watchers query result, extended by
 * formatted new logs items for owner and watch parts and mail sending status
 */
class WatchlistWatcher extends BaseObject
{
    /** @var integer {@see WatchlistWatcher::setUserId()} */
    private $userId;
    /** @var string {@see WatchlistWatcher::setUsername()} */
    private $username;
    /** @var string {@see WatchlistWatcher::setEmail()} */
    private $email;
    /** @var integer {@see WatchlistWatcher::setWatchmailMode()} */
    private $watchmailMode;
    /** @var integer {@see WatchlistWatcher::setWatchmailHour()} */
    private $watchmailHour;
    /** @var integer {@see WatchlistWatcher::setWatchmailDay()} */
    private $watchmailDay;
    /** @var \DateTime {@see WatchlistWatcher::setWatchmailNext()} */
    private $watchmailNext;
    /** @var string[] {@see WatchlistWatcher::getOwnerLogs()} */
    private $ownerLogs;
    /** @var string[] {@see WatchlistWatcher::getWatchLogs()} */
    private $watchLogs;
    /** @var boolean {@see WatchlistWatcher::setSendStatus()} */
    private $sendStatus;

    /**
     * It is assumed the class will be instantiated using the query result row.
     * 
     * @param integer $userId  {@see WatchlistWatcher::setUserId()}
     * @param string $username {@see WatchlistWatcher::setUsername()}
     * @param string $email {@see WatchlistWatcher::setEmail()}
     * @param integer $watchmailMode {@see WatchlistWatcher::setWatchmailMode()}
     * @param integer $watchmailHour {@see WatchlistWatcher::setWatchmailHour()}
     * @param integer $watchmailDay {@see WatchlistWatcher::setWatchmailDay()}
     * @param \DateTime $watchmailNext {@see WatchlistWatcher::setWatchmailNext()}
     */
    public function __construct(
        $userId,
        $username,
        $email,
        $watchmailMode,
        $watchmailHour,
        $watchmailDay,
        $watchmailNext
    ) {
        $this->setUserId($userId);
        $this->setUsername($username);
        $this->setEmail($email);
        $this->setWatchmailMode($watchmailMode);
        $this->setWatchmailHour($watchmailHour);
        $this->setWatchmailDay($watchmailDay);
        $this->setWatchmailNext($watchmailNext);
        $this->ownerLogs = array();
        $this->watchLogs = array();
    }

    /**
     * Gives the watcher id
     *
     * @return integer the watcher id
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Gives the watcher username
     *
     * @return string the watcher username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Gives the watcher email
     *
     * @return string the watcher email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Gives the watcher mail sending mode. Possible values:
     * {@see \lib\Objects\User\UserWatchedCache} SEND_ constants
     *
     * @return int the watcher mail sending mode
     */
    public function getWatchmailMode()
    {
        return $this->watchmailMode;
    }

    /**
     * Gives the watcher mail sending hour
     *
     * @return int the watcher mail sending hour
     */
    public function getWatchmailHour()
    {
        return $this->watchmailHour;
    }

    /**
     * Gives the watcher mail sending day, from 1 - Monday to 7 - Sunday
     *
     * @return int the watcher mail sending day
     */
    public function getWatchmailDay()
    {
        return $this->watchmailDay;
    }

    /**
     * Gives the watcher next mail sending date and time
     *
     * @return \DateTime the watcher next mail sending date and time
     */
    public function getWatchmailNext()
    {
        return $this->watchmailNext;
    }

    /**
     * Gives the watcher own caches logs waiting to include in email
     *
     * @return string[] the watcher own caches logs
     */
    public function getOwnerLogs()
    {
        return $this->ownerLogs;
    }

    /**
     * Gives the watcher watched caches logs waiting to include in email
     *
     * @return string[] the watcher watched caches logs
     */
    public function getWatchLogs()
    {
        return $this->watchLogs;
    }

    /**
     * Gives the status of sending email with watchlist report
     *
     * @return boolean true if sending email was successful
     */
    public function getSendStatus()
    {
        return $this->sendStatus;
    }

    /**
     * Sets the watcher id
     *
     * @param integer $userId the watcher id to set
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Sets the watcher username
     *
     * @param string $username the watcher username to set
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Sets the watcher email
     *
     * @param string $email the watcher email to set
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Sets the watcher mail sending mode
     *
     * @param integer $watchmailMode mode to set. Possible values:
     *     {@see \lib\Objects\User\UserWatchedCache} SEND_ constants
     */
    public function setWatchmailMode($watchmailMode)
    {
        $this->watchmailMode = $watchmailMode;
    }

    /**
     * Sets the watcher mail sending hour
     *
     * @param integer $watchmailHour the watcher mail sending hour to set
     */
    public function setWatchmailHour($watchmailHour)
    {
        $this->watchmailHour = $watchmailHour;
    }

    /**
     * Sets the watcher mail sending day
     *
     * @param integer $watchmailDay the watcher mail sending day to set.
     *     Possible values: from 1 - Monday to 7 - Sunday
     */
    public function setWatchmailDay($watchmailDay)
    {
        $this->watchmailDay = $watchmailDay;
    }

    /**
     * Sets the watcher next mail sending date and time
     *
     * @param \DateTime $watchmailNext next mail sending date and time to set
     */
    public function setWatchmailNext(\DateTime $watchmailNext = null)
    {
        $this->watchmailNext = $watchmailNext;
    }

    /**
     * Adds new log item to the watcher owner logs
     *
     * @param string $ownerLog formatted log item to add
     */
    public function addOwnerLog($ownerLog)
    {
        $this->ownerLogs[] = $ownerLog;
    }

    /**
     * Adds new log item to the watcher watch logs
     *
     * @param string $watchLog formatted log item to add
     */
    public function addWatchLog($watchLog)
    {
        $this->watchLogs[] = $watchLog;
    }

    /**
     * Sets status of sending mail with report
     *
     * @param boolean $sendStatus true if mail sending was successful, false
     *     otherwise
     */
    public function setSendStatus($sendStatus)
    {
        $this->sendStatus = $sendStatus;
    }
}
