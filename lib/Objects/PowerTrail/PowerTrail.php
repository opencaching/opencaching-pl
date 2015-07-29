<?php
namespace lib\Objects\PowerTrail;

use \lib\Database\DataBaseSingleton;

class PowerTrail
{

    const TYPE_GEODRAW = 1;
    const TYPE_TOURING = 2;
    const TYPE_NATURE = 3;
    const TYPE_TEMATIC = 4;

    private $id;
    private $name;
    private $image;
    private $typeId;

    public function __construct(array $params)
    {
        if (isset($params['id'])) {
            $this->id = (int) $params['id'];
            $this->loadDataFromDb();
        } else
            if (isset($params['dbRow'])) {
                $this->setFieldsByUsedDbRow($params['dbRow']);
            }
    }

    private function loadDataFromDb()
    {
        // TODO: needs future implementation
        // take a look at /powerTrail/powerTrailBase
    }

    private function setFieldsByUsedDbRow(array $dbRow)
    {
        foreach ($dbRow as $key => $value) {
            switch ($key) {
                case 'id':
                    $this->id = $value;
                    break;
                case 'name':
                    $this->name = $value;
                    break;
                case 'image':
                    $this->image = $value;
                    break;
                case 'type':
                    $this->typeId = $value;
                    break;
                default:
                    error_log(__METHOD__ . ": Unknown column: $key");
            }
        }
    }

    public static function CheckForPowerTrailByCache($cacheId)
    {
        $queryPt = 'SELECT `id`, `name`, `image`, `type` FROM `PowerTrail` WHERE `id` IN ( SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` =:1 ) AND `status` = 1 ';
        $db = DataBaseSingleton::Instance();
        $db->multiVariableQuery($queryPt, $cacheId);

        return $db->dbResultFetchAll();
    }

    public static function GetPowerTrailIconsByType($typeId)
    {
        $imgPath = '/tpl/stdstyle/images/blue/';
        $icon = '';
        switch ($typeId) {
            case self::TYPE_GEODRAW:
                $icon = 'footprintRed.png';
                break;
            case self::TYPE_TOURING:
                $icon = 'footprintBlue.png';
                break;
            case self::TYPE_NATURE:
                $icon = 'footprintGreen.png';
                break;
            case self::TYPE_TEMATIC:
                $icon = 'footprintYellow.png';
                break;
        }
        return $imgPath . $icon;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getFootIcon()
    {
        return self::GetPowerTrailIconsByType($this->typeId);
    }

    public function getPowerTrailUrl()
    {
        $url = '/powerTrail.php?ptAction=showSerie&ptrail=';
        return $url . $this->id;
    }
}