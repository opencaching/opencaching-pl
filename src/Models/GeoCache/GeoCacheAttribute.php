<?php
namespace src\Models\GeoCache;

use src\Models\BaseObject;
use src\Utils\I18n\I18n;

/**
  *
  */
class GeoCacheAttribute extends BaseObject {

    private $id;
    private $textLong;      // long attribute description
    private $textShort;     // short attribute description
    private $iconLarge;     // icon for selected attribute
    private $iconNo;        // icon for negative attaching
    private $iconUndef;     // greyouted icon when not selected and not negated
    private $category;      // ???


    public function __construct()
    {
        parent::__construct();
    }

    public static function getAll()
    {
        $db = self::db();
        return $db->dbFetchAllAsObjects(
            $db->multiVariableQuery (
                "SELECT id, text_long, icon_undef, icon_large FROM cache_attrib
                 WHERE language = :1 ORDER BY category, id", I18n::getCurrentLang()),
            function ($row) {
                $obj = new self();
                $obj->loadFromDbRow($row);
                return $obj;
            });
    }



    protected function loadFromDbRow($row)
    {
        $this->id = $row['id'];
        $this->textLong = $row['text_long'];
        $this->iconUndef = $row['icon_undef'];
        $this->iconLarge = $row['icon_large'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLongText ()
    {
        return $this->textLong;
    }

    public function getIconUndef ()
    {
        return $this->iconUndef;
    }

    public function getIconLarge()
    {
        return $this->iconLarge;
    }
}