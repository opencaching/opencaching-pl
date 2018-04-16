<?php

namespace lib\Objects\GeoCache;

use Utils\Email\EmailSender;
use Utils\Database\XDb;
use Utils\Text\SmilesInText;
use Utils\Text\UserInputFilter;
use lib\Objects\User\User;
use lib\Objects\BaseObject;

class GeoCacheDesc extends BaseObject
{

    const HTML_SAFE = 2;

    private $id;
    private $cacheId;
    private $language;
    private $desc;
    private $desc_html;
    private $desc_htmledit;
    private $hint;
    private $short_desc;
    private $date_created;
    private $last_modified;
    private $uuid;
    private $node;
    private $rr_comment;


    public function __construct($cacheId, $descLang){

        parent::__construct();

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


    public static function fromCacheIdFactory($cacheId, $descLang)
    {
        try{
            return new self( $cacheId, $descLang );
        }catch (\Exception $e){
            return null;
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

    public function getAdminComment(){
        return $this->rr_comment;
    }

    public static function UpdateAdminComment(GeoCache $geoCache, $comment, User $author){

        $sender_name = $author->getUserName();
        $comment = nl2br($comment);
        $date = date("d-m-Y H:i:s");

        $octeam_comment = '<b><span class="content-title-noshade txt-blue08">' .
                            tr('date') . ': ' . $date . ', ' .
                            tr('add_by') . ' ' . $sender_name . '</span></b><br/>' . $comment . '<br/><br/>';

        XDb::xSql(
            "UPDATE cache_desc SET rr_comment = CONCAT('" . XDb::xEscape($octeam_comment) . "', rr_comment),
                last_modified = NOW()
            WHERE cache_id= ? ", $geoCache->getCacheId());


        EmailSender::sendNotifyOfOcTeamCommentToCache($geoCache,$author, nl2br($_POST['rr_comment']));

    }

    public static function RemoveAdminComment(GeoCache $geoCache){
        XDb::xSql("UPDATE cache_desc SET rr_comment='' WHERE cache_id= ? ",$geoCache->getCacheId() );
    }
}

