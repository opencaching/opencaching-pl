<?php
namespace src\Models\ChunkModels\DynamicMap;

/**
 * This is model of geocache marker
 */
class GuideMarkerModel extends AbstractMarkerModelBase
{
    // lat/lon/icon inherited from parent!

    public $userId;
    public $username;
    public $link;
    public $userDesc;
    public $recCount;

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
