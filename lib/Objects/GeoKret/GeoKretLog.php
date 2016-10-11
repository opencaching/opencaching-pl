<?php

namespace lib\Objects\GeoKret;

use lib\Objects\User\User;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoKret\GeoKretLogError;

/**
 * Description of GeoKretLog
 *
 * @author Łza
 */
class GeoKretLog
{

    private $id;

    /**
     *  @var \DateTime
     */
    private $logDateTime;

    /**
     *  @var \DateTime
     */
    private $enqueueDatetime;

    /* @var User */
    private $user;

    /**
     * @var GeoCache
     */
    private $geoCache;

    /**
     * @var integer
     */
    private $logType;

    /**
     * @var string
     */
    private $comment;
    private $trackingCode;
    private $geoKretId;
    private $geoKretName;

    /**
     *
     * @var GeoKretLogError
     */
    private $geoKretLogErrors = [];

    const FORMNAME = 'ruchy';
    const APPLICATION_NAME = 'Opencaching';
    const APPLICATION_VERSION = 'PL';

    public function __construct()
    {

    }

    public function getLogDateTime()
    {
        return $this->logDateTime;
    }

    public function getEnqueueDatetime()
    {
        return $this->enqueueDatetime;
    }

    /**
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @return GeoCache
     */
    public function getGeoCache()
    {
        return $this->geoCache;
    }

    public function getLogType()
    {
        return $this->logType;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getTrackingCode()
    {
        return $this->trackingCode;
    }

    public function getGeoKretId()
    {
        return $this->geoKretId;
    }

    public function getGeoKretName()
    {
        return $this->geoKretName;
    }

    public function setLogDateTime(\DateTime $logDateTime)
    {
        $this->logDateTime = $logDateTime;
        return $this;
    }

    public function setEnqueueDatetime(\DateTime $enqueueDatetime)
    {
        $this->enqueueDatetime = $enqueueDatetime;
        return $this;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setGeoCache(GeoCache $geoCache)
    {
        $this->geoCache = $geoCache;
        return $this;
    }

    public function setLogType($logType)
    {
        $this->logType = (int) $logType;
        return $this;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function setTrackingCode($nr)
    {
        $this->trackingCode = $nr;
        return $this;
    }

    public function setGeoKretId($GeoKretId)
    {
        $this->geoKretId = (int) $GeoKretId;
        return $this;
    }

    public function setGeoKretName($GeoKretName)
    {
        $this->geoKretName = $GeoKretName;
        return $this;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getGeoKretLogErrors()
    {
        return $this->geoKretLogErrors;
    }

    public function appendGeoKretLogErrors(GeoKretLogError $geoKretLogError)
    {
        $this->geoKretLogErrors[] = $geoKretLogError;
        return $this;
    }

    public function isLoggingError()
    {
        if(count($this->getGeoKretLogErrors()) > 0){
            return true;
        }
        return false;
    }



}
