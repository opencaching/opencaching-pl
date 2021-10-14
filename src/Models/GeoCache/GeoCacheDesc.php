<?php

namespace src\Models\GeoCache;

use src\Utils\Email\EmailSender;
use src\Utils\Text\SmilesInText;
use src\Utils\Text\UserInputFilter;
use src\Models\OcConfig\OcConfig;
use src\Models\User\User;
use src\Models\BaseObject;
use src\Utils\Text\Formatter;
use src\Utils\Generators\Uuid;

class GeoCacheDesc extends BaseObject
{
    const MAX_DESC_SIZE = 300000;
    const HTML_SAFE = 2;

    private $id;
    private $cacheId;
    private $language="";
    private $desc="";
    private $desc_html=0;
    private $hint = "";
    private $short_desc = "";
    private $date_created;
    private $last_modified;
    private $uuid;
    private $node;
    private $rr_comment="";
    private $reactivationRule="";

    public function __construct(){

        parent::__construct();

    }

    public static function fromCacheIdFactory(int $cacheId, string $descLang = null): ?GeoCacheDesc
    {
        if (!$descLang) {
            // lang is not defined so this description can't exists
            return null;
        }
        try {
            $obj = new self();
            $obj->loadByCacheId($cacheId, $descLang);
            return $obj;
        } catch (\Exception $e){
            return null;
        }
    }

    public static function getEmptyDesc(Geocache $cache): GeoCacheDesc
    {
        $obj = new self();
        $obj->cacheId = $cache->getCacheId();
        return $obj;
    }

    private function loadByCacheId(int $cacheId, string $descLang)
    {
        $rs = $this->db->multiVariableQuery(
            "SELECT * FROM cache_desc
            WHERE cache_id = :1 AND language = :2
            LIMIT 1",
            $cacheId, $descLang);

        $descDbRow = $this->db->dbResultFetchOneRowOnly($rs);

        if (is_array($descDbRow)) {
            $this->loadFromRow($descDbRow);
        } else {
            throw new \Exception("Description not found for cacheId=$cacheId");
        }
    }

    public function loadFromRow(array $descDbRow){
        $this->id = $descDbRow['id'];
        $this->cacheId = $descDbRow['cache_id'];
        $this->language = $descDbRow['language'];
        $this->desc = $descDbRow['desc'];
        $this->desc_html = $descDbRow['desc_html'];
        $this->hint = $descDbRow['hint'];
        $this->short_desc = $descDbRow['short_desc'];
        $this->date_created = $descDbRow['date_created'];
        $this->last_modified = $descDbRow['last_modified'];
        $this->uuid = $descDbRow['uuid'];
        $this->node = $descDbRow['node'];
        $this->rr_comment = $descDbRow['rr_comment'];
        $this->reactivationRule = $descDbRow['reactivation_rule'];
    }

    public function getShortDescToDisplay(){
        // plain text, needs escaping
        $short_desc = htmlspecialchars($this->short_desc, ENT_COMPAT, 'UTF-8');

        //replace { and } to prevent replacing
        $short_desc = mb_ereg_replace('{', '&#0123;', $short_desc);
        $short_desc = mb_ereg_replace('}', '&#0125;', $short_desc);

        return $short_desc;
    }

    public function getShortDescRaw(): string
    {
        return $this->short_desc;
    }

    public function getDescriptionRaw(){
        return $this->desc;
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

    public function getLang(): string
    {
        return $this->language;
    }

    public function getAdminComment()
    {
        return $this->rr_comment;
    }

    public function getReactivationRules()
    {
        return $this->reactivationRule;
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

    /**
     * Returns the list of languages from existing descriptions for this geocache
     *
     * @return array
     */
    public function getListOfDescriptionLangs($skipLangOfThisObject=false): array
    {
        $rs = $this->db->multiVariableQuery(
            "SELECT language FROM cache_desc WHERE cache_id = :1", $this->cacheId);
        $result = $this->db->dbFetchOneColumnArray($rs, 'language');

        return array_diff($result, [$this->language]);
    }

    /**
     * Check if language assigned to this object is present in different record in DB
     * @return bool
     */
    public function isLangDuplicated() : bool
    {
        $duplicate = self::fromCacheIdFactory($this->cacheId, $this->language);
        return $duplicate && $duplicate->id != $this->id;
    }

    private function insertToDb (): void
    {
        $this->db->multiVariableQuery(
            "INSERT INTO cache_desc SET
                `cache_id` = :1, `language` = :2, `desc` = :3, `desc_html` = 2,
                `hint` = :4, `short_desc` = :5, `last_modified` = NOW(), `uuid` = :6,
                `node` = :7, `rr_comment` = '', `reactivation_rule` = :8",
            $this->cacheId, $this->language, $this->desc,
            $this->hint, $this->short_desc, Uuid::create(),
            OcConfig::getSiteNodeId(), $this->reactivationRule);
    }

    public function saveToDb (): void
    {
        if (!$this->id) {
            // this is new record - it doesn't have the id - insert instead of updateing
            $this->insertToDb();
            return;
        }
        d($this, 'B');
        $this->db->multiVariableQuery(
            "UPDATE cache_desc SET
                `language` = :1, `desc` = :2, `desc_html` = 2,
                `hint` = :3, `short_desc` = :4, `last_modified` = NOW(),
                `rr_comment` = :5, `reactivation_rule` = :6
            WHERE id = :7 LIMIT 1",
            $this->language, $this->desc,
            $this->hint, $this->short_desc,
            $this->rr_comment, $this->reactivationRule, $this->id);
    }

    /**
     * @param Ambigous <string, unknown> $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @param Ambigous <string, unknown> $desc
     */
    public function setDesc($desc)
    {
        /* Prevent binary data in cache descriptions, e.g. <img src='data:...'> tags. */
        if (strlen($desc) < self::MAX_DESC_SIZE) {
            $this->desc = UserInputFilter::purifyHtmlString($desc);
        }
    }

    /**
     * @param Ambigous <string, unknown> $hint
     */
    public function setHint($hint)
    {
        $this->hint = strip_tags($hint);
    }

    /**
     * @param Ambigous <string, unknown> $short_desc
     */
    public function setShortDesc($short_desc)
    {
        $this->short_desc = strip_tags($short_desc);
    }

    /**
     * @param Ambigous <string, unknown> $rr_comment
     */
    public function setRrComment($rr_comment)
    {
        $this->rr_comment = $rr_comment;
    }

    /**
     * @param Ambigous <string, unknown> $reactivationRule
     */
    public function setReactivationRule($reactivationRule)
    {
        $this->reactivationRule = $reactivationRule;
    }

}

