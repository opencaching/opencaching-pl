<?php
/**
 * Contains \lib\Objects\Watchlist\WatchlistGeoCacheLog class definition
 */
namespace lib\Objects\Watchlist;

use lib\Objects\BaseObject;
use lib\Controllers\Php7Handler;

/**
 * Represents single row data from watchlist new logs query result
 */
class WatchlistGeoCacheLog extends BaseObject
{
    /** @var integer {@see WatchlistGeoCacheLog::setLogId()} */
    private $logId;
    /** @var string {@see WatchlistGeoCacheLog::setLogger()} */
    private $logger;
    /** @var integer {@see WatchlistGeoCacheLog::setLogType()} */
    private $logType;
    /** @var string {@see WatchlistGeoCacheLog::setLogText()} */
    private $logText;
    /** @var \DateTime {@see WatchlistGeoCacheLog::setLogDate()} */
    private $logDate;
    /** @var integer {@see WatchlistGeoCacheLog::setCacheId()} */
    private $cacheId;
    /** @var integer {@see WatchlistGeoCacheLog::setCacheOwnerId()} */
    private $cacheOwnerId;
    /** @var string {@see WatchlistGeoCacheLog::setCacheWaypoint()} */
    private $cacheWaypoint;
    /** @var string {@see WatchlistGeoCacheLog::setCacheName()} */
    private $cacheName;
    /** @var boolean {@see WatchlistGeoCacheLog::setRecommended()} */
    private $recommended;

    /**
     * It is assumed the class will be instantiated using the query result row.
     * 
     * @param integer $logId {@see WatchlistGeoCacheLog::setLogId()}
     * @param string $logger {@see WatchlistGeoCacheLog::setLogger()}
     * @param integer $logType {@see WatchlistGeoCacheLog::setLogType()}
     * @param string $logText {@see WatchlistGeoCacheLog::setLogText()}
     * @param \DateTime $logDate {@see WatchlistGeoCacheLog::setLogDate()}
     * @param integer $cacheId {@see WatchlistGeoCacheLog::setCacheId()}
     * @param integer $cacheOwnerId {@see WatchlistGeoCacheLog::setCacheOwnerId()}
     * @param string $cacheWaypoint {@see WatchlistGeoCacheLog::setCacheWaypoint()}
     * @param string $cacheName {@see WatchlistGeoCacheLog::setCacheName()}
     * @param boolean $recommended {@see WatchlistGeoCacheLog::setRecommended()}
     */
    public function __construct(
        $logId,
        $logger,
        $logType,
        $logText,
        $logDate,
        $cacheId,
        $cacheOwnerId,
        $cacheWaypoint,
        $cacheName,
        $recommended
    ) {
        $this->setLogId($logId);
        $this->setLogger($logger);
        $this->setLogType($logType);
        $this->setLogText($logText);
        $this->setLogDate($logDate);
        $this->setCacheId($cacheId);
        $this->setCacheOwnerId($cacheOwnerId);
        $this->setCacheWaypoint($cacheWaypoint);
        $this->setCacheName($cacheName);
        $this->setRecommended($recommended);
    }

    /**
     * Gives the cache log id
     *
     * @return integer the cache log id
     */
    public function getLogId()
    {
        return $this->logId;
    }

    /**
     * Gives username who made the cache log
     *
     * @return string username who made the cache log
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Gives type of the cache log made. Possible values:
     * {@see \lib\Objects\GeoCache\GeoCacheLogCommons} LOGTYPE_ constants
     * 
     * @return integer type of the cache log made
     */
    public function getLogType()
    {
        return $this->logType;
    }

    /**
     * Gives the cache log text entered by user
     *
     * @return string the cache log text entered by user
     */
    public function getLogText()
    {
        return $this->logText;
    }

    /**
     * Gives date and time related to the cache log, entered by user
     *
     * @return \DateTime date and time related to the cache log
     */
    public function getLogDate()
    {
        return $this->logDate;
    }

    /**
     * Gives id of the cache the log was made for
     *
     * @return integer id of the cache the log was made for
     */
    public function getCacheId()
    {
        return $this->cacheId;
    }

    /**
     * Gives owner id of the cache the log was made for
     *
     * @return integer owner id of the cache the log was made for
     */
    public function getCacheOwnerId()
    {
        return $this->cacheOwnerId;
    }

    /**
     * Gives waypoint of the cache the log was made for
     *
     * @return string waypoint of the cache the log was made for
     */
    public function getCacheWaypoint()
    {
        return $this->cacheWaypoint;
    }

    /**
     * Gives name of the cache the log was made for
     *
     * @return string name of the cache the log was made for
     */
    public function getCacheName()
    {
        return $this->cacheName;
    }

    /**
     * Gives true if user who made the cache log recommended the cache
     *
     * @return boolean true if the cache was recommended with log
     */
    public function isRecommended()
    {
        return $this->getRecommended();
    }

    /**
     * Gives true if user who made the cache log recommended the cache
     *
     * @return boolean true if the cache was recommended with log
     */
    public function getRecommended()
    {
        return $this->recommended;
    }

    /**
     * Sets the cache log id
     *
     * @param integer $logId the cache log id to set
     */
    public function setLogId($logId)
    {
        $this->logId = $logId;
    }

    /**
     * Sets username who made the cache log
     *
     * @param string $logger username who made the cache log
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Sets type of the cache log made.
     * 
     * @param integer $logType type of the cache log made. Possible values:
     *     {@see \lib\Objects\GeoCache\GeoCacheLogCommons} LOGTYPE_ constants
     */
    public function setLogType($logType)
    {
        $this->logType = $logType;
    }

    /**
     * Sets the cache log text entered by user
     *
     * @param string $logText the cache log text entered by user
     */
    public function setLogText($logText)
    {
        $this->logText = $logText;
    }

    /**
     * Sets date and time related to the cache log, entered by user
     *
     * @param \DateTime $logDate date and time related to the cache log
     */
    public function setLogDate(\DateTime $logDate)
    {
        $this->logDate = $logDate;
    }

    /**
     * Sets id of the cache the log was made for
     *
     * @param integer $cacheId id of the cache the log was made for
     */
    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;
    }

    /**
     * Sets owner id of the cache the log was made for
     *
     * @param integer $cacheOwnerId owner id of the cache the log was made for
     */
    public function setCacheOwnerId($cacheOwnerId)
    {
        $this->cacheOwnerId = $cacheOwnerId;
    }

    /**
     * Sets waypoint of the cache the log was made for
     *
     * @param string $cacheWaypoint waypoint of the cache the log was made for
     */
    public function setCacheWaypoint($cacheWaypoint)
    {
        $this->cacheWaypoint = $cacheWaypoint;
    }

    /**
     * Sets name of the cache the log was made for
     *
     * @param string $cacheName name of the cache the log was made for
     */
    public function setCacheName($cacheName)
    {
        $this->cacheName = $cacheName;
    }

    /**
     * Sets flag indicating whether the cache was recommended with log
     *
     * @param boolean $recommended true if the cache was recommended with log,
     * false otherwise 
     */
    public function setRecommended($recommended)
    {
        $this->recommended = Php7Handler::Boolval($recommended);
    }
}
