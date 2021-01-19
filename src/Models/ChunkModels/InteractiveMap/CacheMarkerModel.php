<?php
namespace src\Models\ChunkModels\InteractiveMap;

use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheLogCommons;
use src\Models\User\User;
use src\Utils\Text\Formatter;

/**
 * This is model of geocache marker
 */

class CacheMarkerModel extends AbstractMarkerModelBase
{
    // id/lat/lon/icon inherited from parent!

    public $wp;
    public $link;
    public $name;
    public $username;
    public $userProfile;

    public $isEvent;
    public $eventStartDate;
    public $size;
    public $rating;
    public $ratingId;
    public $founds;
    public $notFounds;
    public $ratingVotes;
    public $recommendations;
    public $titledDesc;
    public $isStandingOut;
    public $powerTrailName;
    public $powerTrailIcon;
    public $powerTrailUrl;

    public $cacheType;
    public $cacheStatus;
    public $logStatus;
    public $isOwner;

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
        $this->id = $c->getCacheId();
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
        $this->ratingId = $c->getRatingId();
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

        $this->cacheType = $c->getCacheType();
        $this->cacheStatus = $c->getStatus();
        $this->logStatus = $c->getLogStatus($user);
        $this->isOwner =
            ($user != null && $user->getUserId() == $c->getOwner()->getUserId());
    }

    /**
     * Check if all necessary data is set in this marker class
     * @return boolean
     */
    public function checkMarkerData(): bool
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
        && isset($this->ratingId)
        && isset($this->recommendations)
        && isset($this->cacheType)
        && isset($this->cacheStatus)
        && isset($this->isOwner)
        ;
    }

}
