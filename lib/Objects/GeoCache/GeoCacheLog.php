<?php

namespace lib\Objects\GeoCache;

use lib\Objects\User\User;

use lib\Controllers\Php7Handler;

/**
 * Description of GeoCacheLog
 * 1    Found it    log/16x16-found.png
 * 2    Didn't find it  log/16x16-dnf.png
 * 3    Comment     log/16x16-note.png
 * 4    Moved   log/16x16-moved.png
 * 5    Potrzebny serwis    Needs maintenance   log/16x16-need-maintenance.png
 * 7    Attended    log/16x16-attend.png
 * 8    Zamierza uczestniczyć  Will attend     log/16x16-will_attend.png
 * 10   Gotowa do szukania  Ready to search     log/16x16-published.png
 * 11   Niedostępna czasowo    Temporarily unavailable     log/16x16-temporary.png
 * 12   Komentarz COG   OC Team comment     log/16x16-octeam.png
 * 9    Zarchiwizowana  Archived    log/16x16-trash.png
 * @author Łza
 */
class GeoCacheLog
{

    const LOGTYPE_FOUNDIT = 1;
    const LOGTYPE_DIDNOTFIND = 2;
    const LOGTYPE_COMMENT = 3;
    const LOGTYPE_MOVED = 4;
    const LOGTYPE_NEEDMAINTENANCE = 5;
    const LOGTYPE_ATTENDED = 7;

    private $id;
    private $geoCache;
    private $user;
    private $type;
    private $date;
    private $text;
    private $textHtml;
    private $textHtmlEdit;
    private $lastModified;
    private $okapiSyncbase;
    private $uuid;
    private $picturesCount;
    private $mp3count;
    private $dateCreated;
    private $ownerNotified;
    private $node;
    private $deleted;
    private $delByUserId;
    private $editByUserId;
    private $editCount;
    private $lastDeleted;

    public function __construct()
    {

    }

    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return GeoCache
     */
    public function getGeoCache()
    {
        if(!($this->geoCache instanceof GeoCache)){
            $this->geoCache = new GeoCache(array('cacheId' => $this->geoCache));
        }

        return $this->geoCache;
    }

    /**
     *
     * @return User
     */
    public function getUser()
    {
        if(!($this->user instanceof User)){
            $this->user = new User(array('userId' => $this->user));
        }
        return $this->user;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getTextHtml()
    {
        return $this->textHtml;
    }

    public function getTextHtmlEdit()
    {
        return $this->textHtmlEdit;
    }

    public function getLastModified()
    {
        return $this->lastModified;
    }

    public function getOkapiSyncbase()
    {
        return $this->okapiSyncbase;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getPicturesCount()
    {
        return $this->picturesCount;
    }

    public function getMp3count()
    {
        return $this->mp3count;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getOwnerNotified()
    {
        return $this->ownerNotified;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function getDelByUserId()
    {
        return $this->delByUserId;
    }

    public function getEditByUserId()
    {
        return $this->editByUserId;
    }

    public function getEditCount()
    {
        return $this->editCount;
    }

    public function getLastDeleted()
    {
        return $this->lastDeleted;
    }

    public function setId($logId)
    {
        $this->id = $logId;
        return $this;
    }

    public function setGeoCache($geoCache)
    {
        $this->geoCache = $geoCache;
        return $this;
    }

    public function setUser($userId)
    {
        $this->user = $userId;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function setTextHtml($textHtml)
    {
        $this->textHtml = $textHtml;
        return $this;
    }

    public function setTextHtmlEdit($textHtmlEdit)
    {
        $this->textHtmlEdit = $textHtmlEdit;
        return $this;
    }

    public function setLastModified(\DateTime $lastModified)
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    public function setOkapiSyncbase(\DateTime $okapiSyncbase)
    {
        $this->okapiSyncbase = $okapiSyncbase;
        return $this;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setPicturesCount($picturesCount)
    {
        $this->picturesCount = $picturesCount;
        return $this;
    }

    public function setMp3count($mp3count)
    {
        $this->mp3count = $mp3count;
        return $this;
    }

    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function setOwnerNotified($ownerNotified)
    {
        $this->ownerNotified = $ownerNotified;
        return $this;
    }

    public function setNode($node)
    {
        $this->node = $node;
        return $this;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = Php7Handler::Boolval($deleted);
        return $this;
    }

    public function setDelByUserId($delByUserId)
    {
        $this->delByUserId = $delByUserId;
        return $this;
    }

    public function setEditByUserId($editByUserId)
    {
        $this->editByUserId = $editByUserId;
        return $this;
    }

    public function setEditCount($editCount)
    {
        $this->editCount = (int) $editCount;
        return $this;
    }

    public function setLastDeleted($lastDeleted)
    {
        $this->lastDeleted = $lastDeleted;
        return $this;
    }


    /**
     * There are many places where log text is displayed as a tooltip
     * It is needed to remove many chars which can break the tooltip display operation
     *
     * @param String $text - original log text
     * @return String - clean log text
     */
    public static function cleanLogTextForToolTip( $text ){

        //strip all tags but not <li>
        $text = strip_tags($text, "<li>");

        $replace = array(
            //'<p>&nbsp;</p>' => '', //duplicated ? by strip_tags above
            '&nbsp;' => ' ',
            //'<p>' => '', //duplicated ? by strip_tags above
            "\n" => ' ',
            "\r" => '',
            //'</p>' => "", //duplicated ? by strip_tags above
            //'<br>' => "", //duplicated ? by strip_tags above
            //'<br />' => "", //duplicated ? by strip_tags above
            //'<br/>' => "", //duplicated ? by strip_tags above
            '<li>' => " - ",
            '</li>' => "",
            '&oacute;' => 'o',
            '&quot;' => '-',
            //'&[^;]*;' => '', ???
            '&' => '',
            "'" => '',
            '"' => '',
            '<' => '',
            '>' => '',
            '(' => ' -',
            ')' => '- ',
            ']]>' => ']] >',
            '' => ''
        );

        $text = str_ireplace( array_keys($replace), array_values($replace), $text);
        return preg_replace('/[\x00-\x08\x0E-\x1F\x7F\x0A\x0C]+/', '', $text);

    }

}
