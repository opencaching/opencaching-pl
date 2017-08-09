<?php
namespace lib\Objects\News;

use lib\Objects\BaseObject;
use lib\Objects\User\User;

class News extends BaseObject
{

    private $id = 0;

    private $title = null;

    private $content = '';

    private $author = null;

    private $last_editor = null;

    private $hide_author = 0;

    private $show_onmainpage = 1;

    private $show_notlogged = 0;

    private $date_publication;

    private $date_expiration = null;

    private $date_mainpageexp;

    private $date_lastmod;

    const USER_NOT_SET = 0;

    const STATUS_OTHER = 0;

    const STATUS_FUTURE = 1;

    const STATUS_ON_MAINPAGE = 2;

    const STATUS_ONLY_NEWSPAGE = 3;

    const STATUS_ARCHIVED = 4;

    public function __construct(array $params = array())
    {
        parent::__construct();
        if (isset($params['newsId'])) {
            $this->loadById($params['newsId']);
        }
    }

    public function saveNews()
    {
        if (! $this->dataLoaded) {
            return false;
        }
        if (is_null($this->date_publication)) {
            $this->date_publication = new \DateTime('now');
        }
        if ($this->id == 0) {
            return $this->insertIntoDb();
        } else {
            return $this->saveToDb();
        }
    }

    private function saveToDb()
    {
        return self::db()->multiVariableQuery('UPDATE news SET title = :1, content = :2, user_id = :3, edited_by = :4, hide_author = :5, show_onmainpage = :6,
                show_notlogged = :7, date_publication = :8, date_expiration = :9, date_mainpageexp = :10
                WHERE id = :11', \userInputFilter::purifyHtmlString($this->title), \userInputFilter::purifyHtmlString($this->content), (is_null($this->author)) ? 0 : $this->author->getUserId(), (is_null($this->last_editor)) ? 0 : $this->last_editor->getUserId(), (int) $this->hide_author, (int) $this->show_onmainpage, (int) $this->show_notlogged, self::truncateTime($this->date_publication), (is_null($this->date_expiration)) ? null : self::truncateTime($this->date_expiration), (is_null($this->date_mainpageexp)) ? null : self::truncateTime($this->date_mainpageexp), (int) $this->id);
    }

    private function insertIntoDb()
    {
        return self::db()->multiVariableQuery('INSERT INTO news (title, content, user_id, edited_by, hide_author, show_onmainpage, show_notlogged, date_publication, date_expiration, date_mainpageexp)
                VALUES (:1, :2, :3, :4, :5, :6, :7, :8, :9, :10)', \userInputFilter::purifyHtmlString($this->title), \userInputFilter::purifyHtmlString($this->content), (is_null($this->author)) ? 0 : $this->author->getUserId(), (is_null($this->last_editor)) ? 0 : $this->last_editor->getUserId(), (int) $this->hide_author, (int) $this->show_onmainpage, (int) $this->show_notlogged, self::truncateTime($this->date_publication), (is_null($this->date_expiration)) ? null : self::truncateTime($this->date_expiration), (is_null($this->date_mainpageexp)) ? null : self::truncateTime($this->date_mainpageexp));
    }

    public function loadFromForm(array $formData)
    {
        global $dateFormat;
        $this->hide_author = 0;
        $this->show_onmainpage = 0;
        $this->show_notlogged = 0;
        
        foreach ($formData as $key => $val) {
            switch ($key) {
                case 'id':
                    $this->id = (int) $val;
                    $this->dataLoaded = true; // mark object as containing data
                    break;
                case 'title':
                    $this->title = $val;
                    break;
                case 'content':
                    $this->content = $val;
                    break;
                case 'hide-author':
                    $this->hide_author = ($val == 'on') ? 1 : 0;
                    break;
                case 'show-onmainpage':
                    $this->show_onmainpage = ($val == 'on') ? 1 : 0;
                    break;
                case 'show-notlogged':
                    $this->show_notlogged = ($val == 'on') ? 1 : 0;
                    break;
                case 'date-publication':
                    $this->date_publication = ($val == '') ? null : \DateTime::createFromFormat($dateFormat, $val);
                    break;
                case 'date-expiration':
                    $this->date_expiration = ($val == '') ? null : \DateTime::createFromFormat($dateFormat, $val);
                    break;
                case 'date-mainpageexp':
                    $this->date_mainpageexp = ($val == '') ? null : \DateTime::createFromFormat($dateFormat, $val);
                    break;
                case 'action':
                case 'submit':
                case 'no-date-expiration':
                case 'no-date-mainpageexp':
                    break;
                default:
                    error_log(__METHOD__ . ": Unknown field: $key");
            }
        }
    }

    private function loadById($newsId)
    {
        $query = 'SELECT * FROM news WHERE id = :1 LIMIT 1';
        $stmt = self::db()->multiVariableQuery($query, $newsId);
        $dbRow = self::db()->dbResultFetch($stmt);
        
        if (is_array($dbRow)) {
            $this->loadFromDbRow($dbRow);
        } else {
            $this->dataLoaded = false;
        }
    }

    private function loadFromDbRow(array $dbRow)
    {
        foreach ($dbRow as $key => $val) {
            switch ($key) {
                case 'id':
                    $this->id = (int) $val;
                    break;
                case 'title':
                    $this->title = htmlspecialchars($val, ENT_COMPAT, 'UTF-8');
                    break;
                case 'content':
                    $this->content = $val;
                    break;
                case 'user_id':
                    if ($val == $this::USER_NOT_SET) {
                        $this->author = null;
                    } else {
                        $this->author = new User(array(
                            'userId' => (int) $val
                        ));
                    }
                    break;
                case 'edited_by':
                    if ($val == $this::USER_NOT_SET) {
                        $this->last_editor = null;
                    } else {
                        $this->last_editor = new User(array(
                            'userId' => (int) $val
                        ));
                    }
                    break;
                case 'hide_author':
                    $this->hide_author = (bool) $val;
                    break;
                case 'show_onmainpage':
                    $this->show_onmainpage = (bool) $val;
                    break;
                case 'show_notlogged':
                    $this->show_notlogged = (bool) $val;
                    break;
                case 'date_publication':
                    $this->date_publication = (is_null($val)) ? null : new \DateTime($val);
                    break;
                case 'date_expiration':
                    $this->date_expiration = (is_null($val)) ? null : new \DateTime($val);
                    break;
                case 'date_mainpageexp':
                    $this->date_mainpageexp = (is_null($val)) ? null : new \DateTime($val);
                    break;
                case 'date_lastmod':
                    $this->date_lastmod = new \DateTime($val);
                    break;
                
                // TODO: Will be removed after all OC nodes do sqlAlter
                // Compatibility block
                case 'date_posted':
                    $this->date_publication = new \DateTime($val);
                    $this->date_mainpageexp = new \DateTime($val);
                    $this->date_mainpageexp->add(new \DateInterval('P31D'));
                    break;
                case 'topic':
                    $this->show_onmainpage = ($val == 2) ? true : false;
                    break;
                case 'display':
                    if ($val == 0) {
                        $this->date_expiration = DateTime::createFromFormat('d/m/Y', '6/08/2017');
                    }
                    break;
                // End of compatibility block
                
                default:
                    error_log(__METHOD__ . ": Unknown column: $key");
            }
        }
        $this->dataLoaded = true;
    }

    private static function fromDbRowFactory(array $dbRow)
    {
        $n = new self();
        $n->loadFromDbRow($dbRow);
        return $n;
    }

    public static function compatibileMode() // TODO: Remove it!
    {
        $query = "SHOW COLUMNS FROM `news` LIKE 'display'";
        $stmt = self::db()->simpleQuery($query);
        return self::db()->rowCount($stmt);
    }

    public static function getAllNews($loggeduser = false, $mainpage = false, $offset = null, $limit = null)
    {
        if (self::compatibileMode()) { // TODO: Remove it!
            return self::getAllNewsCompat($mainpage, $offset, $limit);
        }
        $query = 'SELECT * FROM news WHERE (date_expiration > NOW() OR date_expiration IS NULL) AND (date_publication < NOW() OR date_publication IS NULL)';
        if ($mainpage) {
            $query .= ' AND show_onmainpage = 1 AND (date_mainpageexp > NOW() OR date_mainpageexp IS NULL)';
        }
        if (! $loggeduser) {
            $query .= ' AND show_notlogged = 1';
        }
        $query .= ' ORDER BY date_publication DESC';
        if (! is_null($limit)) {
            $query .= ' LIMIT ' . $limit;
            if (! is_null($offset)) {
                $query .= ' OFFSET ' . $offset;
            }
        }
        $stmt = self::db()->simpleQuery($query);
        
        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    public static function getAllNewsCount($loggeduser = false, $mainpage = false)
    {
        if (self::compatibileMode()) { // TODO: Remove it!
            return self::getAllNewsCountCompat($mainpage);
        }
        $query = 'SELECT COUNT(*) FROM news WHERE (date_expiration > NOW() OR date_expiration IS NULL) AND (date_publication < NOW() OR date_publication IS NULL)';
        if ($mainpage) {
            $query .= ' AND show_onmainpage = 1 AND (date_mainpageexp > NOW() OR date_mainpageexp IS NULL)';
        }
        if (! $loggeduser) {
            $query .= ' AND show_notlogged = 1';
        }
        return self::db()->simpleQueryValue($query, 0);
    }

    public static function getAdminNews($offset = null, $limit = null)
    {
        $query = 'SELECT * FROM news ORDER BY date_publication DESC';
        if (! is_null($limit)) {
            $query .= ' LIMIT ' . $limit;
            if (! is_null($offset)) {
                $query .= ' OFFSET ' . $offset;
            }
        }
        $stmt = self::db()->simpleQuery($query);
        
        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    public static function getAdminNewsCount()
    {
        return self::db()->simpleQueryValue('SELECT COUNT(*) FROM news', 0);
    }

    private static function getAllNewsCompat($mainpage = false, $offset = null, $limit = null) // TODO: Remove it!
    {
        $query = 'SELECT * FROM news WHERE display = 1';
        if ($mainpage) {
            $query .= ' AND topic = 2 AND date_posted > NOW() - INTERVAL 31 DAY';
        }
        $query .= ' ORDER BY date_posted DESC';
        if (! is_null($limit)) {
            $query .= ' LIMIT ' . $limit;
            if (! is_null($offset)) {
                $query .= ' OFFSET ' . $offset;
            }
        }
        $stmt = self::db()->simpleQuery($query);
        
        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    private static function getAllNewsCountCompat($mainpage = false) // TODO: Remove it!
    {
        $query = 'SELECT COUNT(*) FROM news WHERE display = 1';
        if ($mainpage) {
            $query .= ' AND topic = 2 AND date_posted > NOW() - INTERVAL 31 DAY';
        }
        return self::db()->simpleQueryValue($query, 0);
    }

    public function generateDefaultValues()
    {
        $this->date_mainpageexp = new \DateTime('NOW');
        $this->date_mainpageexp->add(new \DateInterval('P1M'));
        $this->dataLoaded = true;
    }

    public function setAuthor(User $newAuthor)
    {
        $this->author = $newAuthor;
    }

    public function setEditor(User $editor)
    {
        $this->last_editor = $editor;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getLastEditor()
    {
        return $this->last_editor;
    }

    public function getHideAuthor()
    {
        return $this->hide_author;
    }

    public function isAuthorHidden()
    {
        return ($this->hide_author || $this->author == null);
    }

    public function getShowOnMainpage()
    {
        return $this->show_onmainpage;
    }

    public function getShowNotLogged()
    {
        return $this->show_notlogged;
    }

    public function getDatePublication($asString = false)
    {
        return self::datePrepare($this->date_publication, $asString);
    }

    public function getDateExpiration($asString = false)
    {
        return self::datePrepare($this->date_expiration, $asString);
    }

    public function getDateMainPageExpiration($asString = false)
    {
        return self::datePrepare($this->date_mainpageexp, $asString);
    }

    public function getDateLastModified($asString = false)
    {
        return self::datePrepare($this->date_lastmod, $asString);
    }

    public function dataReady()
    {
        return $this->dataLoaded;
    }

    private static function datePrepare($obj, $asString = false)
    {
        if (is_null($obj)) { // Date is not set
            return null;
        }
        if ($asString) { // Date should be formated as human readable string
            global $dateFormat;
            return $obj->format($dateFormat);
        } else { // Date should be returned as an object
            return $obj;
        }
    }

    private static function truncateTime(\DateTime $date)
    {
        return $date->format('Y-m-d') . ' 00:00:00';
    }

    public function getStatus()
    {
        $currentTime = $objDateTime = new \DateTime('NOW');
        if ($this->date_publication > $currentTime) {
            return self::STATUS_FUTURE;
        } elseif (! is_null($this->date_expiration) && $this->date_expiration < $currentTime) {
            return self::STATUS_ARCHIVED;
        } elseif ($this->show_onmainpage && (is_null($this->date_mainpageexp) || $this->date_mainpageexp > $currentTime)) {
            return self::STATUS_ON_MAINPAGE;
        } else {
            return self::STATUS_ONLY_NEWSPAGE;
        }
    }

    public function getStatusBootstrapName()
    {
        switch ($this->getStatus()) {
            case self::STATUS_FUTURE:
                return 'warning';
                break;
            case self::STATUS_ARCHIVED:
                return 'danger';
                break;
            case self::STATUS_ON_MAINPAGE:
                return 'primary';
                break;
            case self::STATUS_ONLY_NEWSPAGE:
                return 'default';
                break;
        }
    }
}