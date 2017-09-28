<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;
use lib\Objects\User\User;

class ReportEmailTemplate extends BaseObject
{

    /**
     * Recipients of emails
     */
    // RECIPIENT_CACHEOWNER & RECIPIENT_SUBMITTER
    const RECIPIENT_ALL = 0;

    // Owner of cache
    const RECIPIENT_CACHEOWNER = 1;

    // Report submitter
    const RECIPIENT_SUBMITTER = 2;

    // Direct to cachelog
    const RECIPIENT_CACHELOG = 3;

    /**
     * Unique ID of report email template
     *
     * @var int
     */
    private $id = null;

    /**
     * Short, internal description of template.
     * Used to group templates
     *
     * @var string
     */
    private $name;

    /**
     * Version of template.
     * Every edition of template is saved as new version
     *
     * @var int
     */
    private $version;

    /**
     * Type of object which template concerns to
     * 1 - cache, 2 -geopath.
     * See consts ReportCommons::OBJECT_*
     *
     * @var int
     */
    private $objectType;

    /**
     * Short description - is shown i.e.
     * in <select> elements
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
     * Who is recipient of email.
     * See consts ReportEmailTemplate::RECIPIENT_*
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

    /**
     * Returns email template $templateId with processed template fields
     *
     * @param int $templateId
     * @param Report $report
     * @return string
     */
    public static function getProcessedTemplate($templateId, Report $report)
    {
        $content = self::getContentByTemplateId($templateId);
        $content = self::processTemplate($content, $report);
        return $content;
    }

    /**
     * Processes $content and replace template fields:
     * {user} - username of user who makes last changes in report (sends mail, changes status etc.)
     * {authorname} - username of current logged user
     * {reportid} - ID of the report
     * {cachename} - full name of cache
     * {cachewp} - Waypoint of cache (i.e. OP12345)
     * {cacheowner} - Username of cacheowner
     * {submitter} - Username of user submits report
     * {leader} - Username of user who is the leader of the report
     *
     * @param string $content
     * @param Report $report
     * @return string
     */
    public static function processTemplate($content, Report $report)
    {
        $content = mb_ereg_replace('{reportid}', $report->getId(), $content);
        $content = mb_ereg_replace('{cacheowner}', $report->getCache()->getOwner()->getUserName(), $content);
        $content = mb_ereg_replace('{cachename}', $report->getCache()->getCacheName(), $content);
        $content = mb_ereg_replace('{cachewp}', $report->getCache()->getWaypointId(), $content);
        $content = mb_ereg_replace('{authorname}', self::getCurrentUser()->getUserName(), $content);
        $content = mb_ereg_replace('{user}', ($report->getUserLastChange() !== null) ? $report->getUserLastChange()->getUserName() : '', $content);
        $content = mb_ereg_replace('{submitter}', $report->getUserSubmit()->getUserName(), $content);
        $content = mb_ereg_replace('{leader}', ($report->getUserLeader() !== null) ? $report->getUserLeader()->getUserName() : '', $content);
        $content = mb_ereg_replace('%cachename%', $report->getCache()->getCacheName(), $content);  // Backward compatibility
        $content = mb_ereg_replace('%rr_member_name%', self::getCurrentUser()->getUserName(), $content);  // Backward compatibility
        return $content;
    }

    /**
     * Returns array of templates with fields id, shortdesc and version
     *
     * @param int $recipient
     * @param int $objectType
     * @return array
     */
    public static function generateTemplateArray($recipient, $objectType = ReportCommons::OBJECT_CACHE)
    {
        $query = '
            SELECT `a`.`id` AS id, `a`.`shortdesc` AS shortdesc, `a`.`version` AS `version`
            FROM `email_schemas` a
            INNER JOIN (
                SELECT `name`, MAX(`version`) AS ver
                FROM `email_schemas`
                GROUP BY `name`
            ) b ON a.name = b.name AND a.version = b.ver
            WHERE `object_type` = :object_type AND `receiver` = :receiver AND `deleted` = 0
            ORDER BY `shortdesc`';
        $params = [];
        $params['object_type']['value'] = $objectType;
        $params['object_type']['data_type'] = 'integer';
        $params['receiver']['value'] = $recipient;
        $params['receiver']['data_type'] = 'integer';
        $stmt = self::db()->paramQuery($query, $params);
        return self::db()->dbResultFetchAll($stmt);
    }

    /**
     * Creates <option></option> select code for all templates for
     * given $recipient and $objectType
     *
     * @param int $recipient
     * @param int $objectType
     * @return string
     */
    public static function generateTemplateSelect($recipient, $objectType = ReportCommons::OBJECT_CACHE)
    {
        $content = '';
        $templates = self::generateTemplateArray($recipient, $objectType);
        foreach ($templates as $template) {
            $content .= '<option value="' . $template['id'] . '">' . $template['shortdesc']. '</option>';
        }
        return $content;
    }

    private static function getContentByTemplateId($templateId)
    {
        $query = 'SELECT `text` FROM `email_schemas` WHERE `id` = :id LIMIT 1';
        $params = [];
        $params['id']['value'] = $templateId;
        $params['id']['data_type'] = 'integer';
        return self::db()->paramQueryValue($query, null, $params);
    }
}
