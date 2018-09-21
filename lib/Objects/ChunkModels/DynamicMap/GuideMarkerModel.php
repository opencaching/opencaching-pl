<?php
namespace lib\Objects\ChunkModels\DynamicMap;

use lib\Objects\User\User;

/**
 * This is model of geocache marker
 */
class GuideMarkerModel extends AbstractMarkerModelBase
{
    /** Maxiumum length of guide description passed to marker model */
    const MAX_DSCR_LEN = 200;

    // lat/lon/icon inherited from parent!

    public $userId;
    public $username;
    public $link;
    public $userDesc;
    public $recCount;

    public static function fromGuidesListRowFactory($row)
    {
        $marker = new self();
        $marker->icon = '/images/guide_map_marker.png';
        $marker->link = User::GetUserProfileUrl($row['user_id']);
        $marker->lat = $row['latitude'];
        $marker->lon = $row['longitude'];
        $marker->userId = $row['user_id'];
        $marker->username = $row['username'];
        $text = $row['description'];
        if (mb_strlen($text) > self::MAX_DSCR_LEN) {
            $text = mb_strcut($text, 0, self::MAX_DSCR_LEN);
            $text .= '...';
        }
        $marker->userDesc = nl2br($text);
        $marker->recCount = $row['recomendations'];

        return $marker;
    }
    /**
     * Check if all necessary data is set in this marker class
     * @return boolean
     */
    public function checkMarkerData()
    {
        return parent::checkMarkerData() &&
            isset($this->link) &&
            isset($this->userId) &&
            isset($this->username) &&
            isset($this->userDesc) &&
            isset($this->recCount);
    }
}
