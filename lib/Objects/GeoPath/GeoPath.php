<?php

namespace lib\Objects\GeoPath;


use lib\Objects\Coordinates\Coordinates;
use Utils\Database\OcDb;
use lib\Objects\OcConfig\OcConfig;

class GeoPath extends GeoPathCommon
{

    private $id;
    private $name;
    private $image;
    private $type;
    private $centerCoordinates;
    private $status;
    private $dateCreated;
    private $cacheCount;
    private $description;
    private $perccentRequired;
    private $conquestedCount;

    public function __construct()
    {
        parent::__construct();
    }

    private function loadDataFromDb($fields = null)
    {
        if (is_null($fields)) {
            $fields = "*"; // default select all fields
        }

        $s = $this->db->multiVariableQuery(
            "SELECT $fields FROM PowerTrail WHERE id=:1 LIMIT 1", $this->id);

        if($row = $db->dbResultFetch($s)){
            $this->loadFromDbRow($row);
            return true;
        }
        return false;
    }

    private function loadFromDbRow(array $dbRow)
    {
        foreach ($dbRow as $key => $val) {
            switch ($key) {
                case 'id':
                    $this->id = (int) $val;
                    break;
                case 'name':
                    $this->name = $val;
                    break;
                case 'image':
                    if($val === ''){
                        /* no image was loaded by user, set default image */
                        $val = 'tpl/stdstyle/images/blue/powerTrailGenericLogo.png';
                    }
                    $this->image = $val;
                    break;
                case 'type':
                    $this->type = (int) $val;
                    break;
                case 'status':
                    $this->status = (int) $val;
                    break;
                case 'dateCreated':
                    $this->dateCreated = new \DateTime($val);
                    break;
                case 'cacheCount':
                    $this->cacheCount = (int) $val;
                    break;
                case 'description':
                    $this->description = $val;
                    break;
                case 'perccentRequired':
                    $this->perccentRequired = $val;
                    break;
                case 'conquestedCount':
                    $this->conquestedCount = $val;
                    break;
                case 'points':
                    $this->points = $val;
                    break;

                case 'centerLatitude':
                case 'centerLongitude':
                    // cords are handled below...
                    break;
                default:
                    error_log(__METHOD__ . ": Unknown column: $key");
            }
        }

        // and the coordinates..
        if (isset($dbRow['centerLatitude'], $dbRow['centerLongitude'])) {
            $this->centerCoordinates = Coordinates::FromCoordsFactory(
                $dbRow['centerLatitude'], $dbRow['centerLongitude']);
        }
    }

    private static function FromDbRowFactory(array $dbRow)
    {
        $gp = new self();
        $gp->loadFromDbRow($dbRow);
        return $gp;
    }

    /**
     * Returns list of all geopaths
     * @return array
     */
    public static function GetAllGeoPaths()
    {
        $stmt = self::Db()->multiVariableQuery(
                    "SELECT * FROM PowerTrail
                    WHERE 1=1");

        $result = array();
        while($row = self::Db()->dbResultFetch($stmt, OcDb::FETCH_ASSOC)){
            $result[] = self::FromDbRowFactory($row);
        }
        return $result;
    }



    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIcon()
    {
        return self::GetTypeIcon($this->type);
    }

    public function getTypeTranslation()
    {
        return tr(self::GetTypeTranslationKey($this->type));
    }

    public function getStatusTranslation()
    {
        return tr(self::GetStatusTranslationKey($this->status));
    }

    public function getCreationDate()
    {
        return $this->dateCreated;
    }

    public function getCreationDateString()
    {
        global $dateFormat;
        return $this->dateCreated->format($dateFormat);
    }

    public function getCacheCount()
    {
        return $this->cacheCount;
    }

    public function getGainedCount()
    {
        return $this->conquestedCount;
    }


}

