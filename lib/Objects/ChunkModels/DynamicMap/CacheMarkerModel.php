<?php
namespace lib\Objects\ChunkModels\DynamicMap;

use lib\Objects\GeoCache\GeoCache;
use lib\Objects\User\User;
use Utils\Text\Formatter;

/**
 * This is model of geocache marker
 */

class CacheMarkerModel extends AbstractMarkerModelBase
{
    // lat/lon/icon inherited from parent!

    public $wp;
    public $link;
    public $name;
    public $username;
    public $userProfile;

    public $isEvent;
    public $eventStartDate;
    public $size;
    public $rating;
    public $founds;
    public $notFounds;
    public $ratingVotes;
    public $recommendations;
    public $titledDesc;
    public $isStandingOut;
    public $powerTrailName;
    public $powerTrailIcon;
    public $powerTrailUrl;
    
    /**
     * Creates marker model from Geocache model
     * @param GeoCache $c
     * @param User $user
     * @return CacheMarkerModel
     */
    public static function fromGeocacheFactory(GeoCache $c, User $user=null)
    {
        $marker = new self();
        $marker->importDataFromGeoCache( $c, $user);
        return $marker;
    }

    protected function importDataFromGeoCache(GeoCache $c, User $user=null)
    {
        // Abstract-Marker data
        $this->icon = $c->getCacheIcon($user);
        $this->lat = $c->getCoordinates()->getLatitude();
        $this->lon = $c->getCoordinates()->getLongitude();

        $this->wp = $c->getGeocacheWaypointId();
        $this->link = $c->getCacheUrl();
        $this->name = $c->getCacheName();
        $this->username = $c->getOwner()->getUserName();
        $this->userProfile = $c->getOwner()->getProfileUrl();

        $this->isEvent= $c->isEvent();
        if ($this->isEvent) {
            $this->eventStartDate = Formatter::date($c->getDatePlaced());
        }
        $this->size = tr($c->getSizeTranslationKey());
        $this->ratingVotes = $c->getRatingVotes();
        $this->rating = (
            $this->ratingVotes < 3
            ? tr('not_available')
            : $c->getRatingDesc()
        );
        $this->founds = $c->getFounds();
        $this->notFounds = $c->getNotFounds();
        $this->recommendations = $c->getRecommendations();
        $this->isTitled = $c->isTitled();
        if ($c->isTitled() ) {
            global $titled_cache_period_prefix; //TODO: move it to the ocConfig
            $this->titledDesc = tr($titled_cache_period_prefix.'_titled_cache');
        }
        $this->isStandingOut = ($this->titledDesc || $this->recommendations);
        if ($c->isPowerTrailPart()) {
            $this->powerTrailName = $c->getPowerTrail()->getName();
            $this->powerTrailIcon = $c->getPowerTrail()->getFootIcon();
            $this->powerTrailUrl = $c->getPowerTrail()->getPowerTrailUrl();
        }
    }

    /**
     * Check if all necessary data is set in this marker class
     * @return boolean
     */
    public function checkMarkerData()
    {
        return parent::checkMarkerData()
        && isset($this->wp)
        && isset($this->link)
        && isset($this->name)
        && isset($this->username)
        && isset($this->userProfile)
        && isset($this->isEvent)
        && isset($this->size)
        && isset($this->founds)
        && isset($this->notFounds)
        && isset($this->ratingVotes)
        && isset($this->recommendations)
        ;
    }

}
