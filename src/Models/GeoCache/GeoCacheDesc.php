<?php

namespace src\Models\GeoCache;

use src\Utils\Email\EmailSender;
use src\Utils\Database\XDb;
use src\Utils\Text\SmilesInText;
use src\Utils\Text\UserInputFilter;
use src\Models\User\User;
use src\Models\BaseObject;
use src\Utils\Text\Formatter;

class GeoCacheDesc extends BaseObject
{

    const HTML_SAFE = 2;

    private $id;
    private $cacheId;
    private $language="";
    private $desc="";
    private $desc_html=0;
    private $desc_htmledit;
    private $hint = "";
    private $short_desc = "";
    private $date_created;
    private $last_modified;
    private $uuid;
    private $node;
    private $rr_comment="";


    public function __construct(){

        parent::__construct();

    }

    public static function fromCacheIdFactory($cacheId, $descLang)
    {
        try{
            $obj = new self();
            $obj->loadByCacheId($cacheId, $descLang);
            return $obj;
        }catch (\Exception $e){
            return null;
        }
    }

    public static function getEmptyDesc($cacheId=null)
    {
        $obj = new self();
        $obj->cacheId = $cacheId;
        return $obj;
    }

    private function loadByCacheId($cacheId, $descLang)
    {
        $rs = $this->db->multiVariableQuery(
            "SELECT * FROM cache_desc
            WHERE cache_id = :1
                AND language = :2
            LIMIT 1",
            $cacheId, $descLang);

        $descDbRow = $this->db->dbResultFetchOneRowOnly($rs);

        if(is_array($descDbRow)){
            $this->loadFromRow($descDbRow);
        }else{
            throw new \Exception("Description not found for cacheId=$cacheId");
        }
    }

    public function loadFromRow(array $descDbRow){
        $this->id = $descDbRow['id'];
        $this->cacheId = $descDbRow['cache_id'];
        $this->language = $descDbRow['language'];
        $this->desc = $descDbRow['desc'];
        $this->desc_html = $descDbRow['desc_html'];
        $this->desc_htmledit = $descDbRow['desc_htmledit'];
        $this->hint = $descDbRow['hint'];
        $this->short_desc = $descDbRow['short_desc'];
        $this->date_created = $descDbRow['date_created'];
        $this->last_modified = $descDbRow['last_modified'];
        $this->uuid = $descDbRow['uuid'];
        $this->node = $descDbRow['node'];
        $this->rr_comment = $descDbRow['rr_comment'];
    }

    public function getShortDescToDisplay(){
        // plain text, needs escaping
        $short_desc = htmlspecialchars($this->short_desc, ENT_COMPAT, 'UTF-8');

        //replace { and } to prevent replacing
        $short_desc = mb_ereg_replace('{', '&#0123;', $short_desc);
        $short_desc = mb_ereg_replace('}', '&#0125;', $short_desc);

        return $short_desc;
    }

    public function getDescToDisplay()
    {
        $desc = $this->desc;

        if ($this->desc_html != self::HTML_SAFE) {

            // unsafe HTML, needs purifying
            $desc = htmlspecialchars_decode($desc);
            $desc = UserInputFilter::purifyHtmlString($desc);

        } else {
            // safe HTML - pass as is
        }

        //replace { and } to prevent replacing
        $desc = mb_ereg_replace('{', '&#0123;', $desc);
        $desc = mb_ereg_replace('}', '&#0125;', $desc);

        // process smiles in description
        $desc = SmilesInText::process($desc);

        return $desc;
    }

    public function getHint()
    {
        return $this->hint;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAdminComment(){
        return $this->rr_comment;
    }

    public static function UpdateAdminComment(GeoCache $geoCache, $comment, User $author){

        $userName = $author->getUserName();
        $comment = UserInputFilter::purifyHtmlString(nl2br($comment));
        $date = Formatter::dateTime(); //current formated date+time

        $formattedComment = '<span class="ocTeamCommentHeader">'.tr('date').": $date, ".tr('add_by')." $userName:</span>" .
                            '<span class="ocTeamComment">'.$comment.'</span>';

        self::db()->multiVariableQuery(
            "UPDATE cache_desc SET
                rr_comment = CONCAT(:1, rr_comment),
                last_modified = NOW()
            WHERE cache_id= :2 ", $formattedComment, $geoCache->getCacheId());


        EmailSender::sendNotifyOfOcTeamCommentToCache($geoCache, $author, $comment);

    }

    public static function RemoveAdminComment(GeoCache $geoCache){
        self::db()->multiVariableQuery("UPDATE cache_desc SET rr_comment='' WHERE cache_id=:1 ", $geoCache->getCacheId());
    }
}
