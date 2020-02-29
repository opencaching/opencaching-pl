<?php
namespace src\Models\Pictures;

use src\Models\BaseObject;

/**
 * Generic representation of user statistic banner
 *
 */

class StatPic extends BaseObject
{

    private $id;
    private $tplPath;
    private $previewPath;
    private $description;
    private $maxtextwidth;




    public static function getAll ()
    {
        $db = self::db();
        return $db->dbFetchAllAsObjects(
            $db->simpleQuery('SELECT * FROM statpics'),
            function ($row) {
                $obj = new self();
                $obj->id = $row['id'];
                $obj->tplPath = $row ['tplpath'];
                $obj->previewPath = $row['previewpath'];
                $obj->description = $row['description'];
                $obj->maxtextwidth = $row['maxtextwidth'];
                return $obj;
            });
    }

    public function getId ()
    {
        return $this->id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPreviewPath ()
    {
        return $this->previewPath;
    }

}