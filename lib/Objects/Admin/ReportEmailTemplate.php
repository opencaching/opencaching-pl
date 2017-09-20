<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;
use lib\Objects\User\User;

class ReportEmailTemplate extends BaseObject
{

    // Recipients
    const RECIPIENT_ALL = 0;
    const RECIPIENT_CACHEOWNER = 1;
    const RECIPIENT_SUBMITTER = 2;


    /**
     * Unique ID of report email template
     *
     * @var int
     */
    private $id = null;

    /**
     * Short, internal description of template. Used to group templates
     *
     * @var string
     */
    private $name;

    /**
     * Version of template. Every edition of template is saved as new version
     *
     * @var int
     */
    private $version;

    /**
     * Type of object which template concerns to
     * 1 - cache, 2 -geopath. See consts Report::OBJECT_*
     *
     * @var int
     */
    private $objectType;

    /**
     * Short description - is shown i.e. in <select> elements
     *
     * @var string
     */
    private $shortDesc;
    
    /**
     * Content of template
     *
     * @var string
     */
    private $text;
    
    /**
     * Who is recipient of email. See consts ReportEmailTemplate::RECIPIENT_*
     *
     * @var int
     */
    private $recipient;

    /**
     * UserId of current version's author 
     *
     * @var int
     */
    private $autorId = null;

    /**
     * User object build from authorId
     *
     * @var User
     */
    private $author = null;

    /**
     * DateTime object - when current version was saved
     *
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * Is template deleted?
     * 
     * @var bool
     */
     private $deleted;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getContentByTemplateId($templateId)
    {
        $query = "SELECT `text` FROM `email_schemas` WHERE `id` = :id";
        $params = [];
        $params['id']['value'] = $templateId;
        $params['id']['data_type'] = 'integer';
        return self::db()->paramQueryValue($query, null, $params);
    }

    public static function generateTemplateArray($recipient, $objectType = Report::OBJECT_CACHE)
    {
        $query = "
            SELECT `id`, `shortdesc`, MAX(`version`) AS version
            FROM `email_schemas`
            WHERE `object_type` = :object_type AND `receiver` = :receiver
            GROUP BY `name`
            ORDER BY `shortdesc`";
        $params = [];
        $params['object_type']['value'] = $objectType;
        $params['object_type']['data_type'] = 'integer';
        $params['receiver']['value'] = $recipient;
        $params['receiver']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);
        return self::db()->dbResultFetchAll($stmt);
    }
}
