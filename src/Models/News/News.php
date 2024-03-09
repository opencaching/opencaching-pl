<?php
namespace src\Models\News;

use src\Utils\Text\Formatter;
use src\Utils\Text\UserInputFilter;
use src\Models\BaseObject;
use src\Models\User\User;
use src\Models\OcConfig\OcConfig;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Debug\Debug;
use src\Utils\Database\OcDb;
use src\Utils\Database\QueryBuilder;

class News extends BaseObject
{
    private $id = 0;
    private $category = '';
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

    // The list of common categories - used by all nodes
    // Each name must be started with "_"
    const CATEGORY_ANY   = '';       // wildcard for categories - pass with every category
    const CATEGORY_NEWS  = '_news';  // articles in "news" section
    const CATEGORY_DRAFT = '_draft'; // articles not ready to be published

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
        return self::db()->multiVariableQuery('
            UPDATE news
            SET title = :1, content = :2, user_id = :3, edited_by = :4,
                hide_author = :5, show_onmainpage = :6, show_notlogged = :7,
                date_publication = :8, date_expiration = :9, date_mainpageexp = :10,
                category = :11
            WHERE id = :12',
            UserInputFilter::purifyHtmlString($this->title),
            UserInputFilter::purifyHtmlString($this->content),
            (is_null($this->author)) ? 0 : $this->author->getUserId(),
            (is_null($this->last_editor)) ? 0 : $this->last_editor->getUserId(),
            (int) $this->hide_author,
            (int) $this->show_onmainpage,
            (int) $this->show_notlogged,
            self::truncateTime($this->date_publication),
            (is_null($this->date_expiration)) ? null : self::truncateTime($this->date_expiration),
            (is_null($this->date_mainpageexp)) ? null : self::truncateTime($this->date_mainpageexp),
            $this->category,
            (int) $this->id);
    }

    private function insertIntoDb()
    {
        return self::db()->multiVariableQuery('
            INSERT INTO news
                (title, content, user_id, edited_by, hide_author, show_onmainpage,
                show_notlogged, date_publication, date_expiration, date_mainpageexp, category)
            VALUES (:1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11)',
            UserInputFilter::purifyHtmlString($this->title),
            UserInputFilter::purifyHtmlString($this->content),
            (is_null($this->author)) ? 0 : $this->author->getUserId(),
            (is_null($this->last_editor)) ? 0 : $this->last_editor->getUserId(),
            (int) $this->hide_author,
            (int) $this->show_onmainpage,
            (int) $this->show_notlogged,
            self::truncateTime($this->date_publication),
            (is_null($this->date_expiration)) ? null : self::truncateTime($this->date_expiration),
            (is_null($this->date_mainpageexp)) ? null : self::truncateTime($this->date_mainpageexp),
            $this->category);
    }

    public function loadFromForm(array $formData)
    {
        $this->hide_author = 0;
        $this->show_onmainpage = 0;
        $this->show_notlogged = 0;

        foreach ($formData as $key => $val) {
            switch ($key) {
                case 'id':
                    $this->id = (int) $val;
                    $this->dataLoaded = true; // mark object as containing data
                    break;
                case 'category':
                    $this->category = $val;
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
                    $this->date_publication = ($val == '') ? null : \DateTime::createFromFormat(OcConfig::instance()->getDateFormat(), $val);
                    break;
                case 'date-expiration':
                    $this->date_expiration = ($val == '') ? null : \DateTime::createFromFormat(OcConfig::instance()->getDateFormat(), $val);
                    break;
                case 'date-mainpageexp':
                    $this->date_mainpageexp = ($val == '') ? null : \DateTime::createFromFormat(OcConfig::instance()->getDateFormat(), $val);
                    break;
                case 'action':
                case 'submit':
                case 'no-date-expiration':
                case 'no-date-mainpageexp':
                    break;
                default:
                    Debug::errorLog("Unknown field: $key");
            }
        }
    }

    /**
     * Factory
     *
     * @param integer $newsId
     * @return News|null
     */
    public static function fromNewsIdFactory($newsId)
    {
        $obj = new self();
        try {
            $obj->loadById($newsId);
            if ($obj->isDataLoaded()) {
                return $obj;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function isThereSuchNews($newsId): bool
    {
        return 1 == self::db()->multiVariableQueryValue(
            "SELECT COUNT(*) FROM news WHERE id = :1 LIMIT 1", 0, $newsId);
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
                case 'category':
                    $this->category = $val;
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
                default:
                    Debug::errorLog("Unknown column: $key");
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

    /**
     * Returns the list of all categories (common + node-specific)
     *
     * @return array
     */
    public static function getAllCategories(): array
    {
        $commonCategories = [ self::CATEGORY_NEWS, self::CATEGORY_DRAFT ];
        $localCategories = OcConfig::getNewsConfig('localCategories');
        return array_merge($commonCategories, $localCategories);
    }

    /**
     * @param boolean $loggeduser
     * @param boolean $mainpage
     * @param integer $offset
     * @param integer $limit
     * @return News[]
     */
    public static function getAllNews(string $category, $loggeduser = false, $mainpage = false, $offset = null, $limit = null)
    {
        $db = self::db();

        $query = 'SELECT * FROM news
            WHERE (date_expiration > NOW()
                OR date_expiration IS NULL)
                AND (date_publication < NOW()
                OR date_publication IS NULL)';
        if ($mainpage) {
            $query .= ' AND show_onmainpage = 1 AND (date_mainpageexp > NOW() OR date_mainpageexp IS NULL)';
        }

        if ($category != self::CATEGORY_ANY && self::checkCategory($category)) {
            $query .= ' AND category = "' . $db->quoteString($category) . '"';
        }

        if (! $loggeduser) {
            $query .= ' AND show_notlogged = 1';
        }
        $query .= ' ORDER BY date_publication DESC';
        if (! is_null($limit)) {
            $query .= ' LIMIT ' . self::db()->quoteLimit($limit);
            if (! is_null($offset)) {
                $query .= ' OFFSET ' . self::db()->quoteOffset($offset);
            }
        }
        $stmt = self::db()->simpleQuery($query);

        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    public static function getAllNewsCount(string $category, $loggeduser = false, $mainpage = false)
    {
        $db = self::db();

        $query = 'SELECT COUNT(*) FROM news
            WHERE (date_expiration > NOW() OR date_expiration IS NULL)
                AND (date_publication < NOW() OR date_publication IS NULL)';
        if ($mainpage) {
            $query .= ' AND show_onmainpage = 1 AND (date_mainpageexp > NOW() OR date_mainpageexp IS NULL)';
        }

        if ($category != self::CATEGORY_ANY && self::checkCategory($category)) {
            $query .= ' AND category = "' . $db->quoteString($category) . '"';
        }

        if (! $loggeduser) {
            $query .= ' AND show_notlogged = 1';
        }
        return $db->simpleQueryValue($query, 0);
    }

    public static function getAdminNews(string $category, $offset = null, $limit = null)
    {
        $db = self::db();

        $query = 'SELECT * FROM news';

        if ($category != self::CATEGORY_ANY && self::checkCategory($category)) {
            $query .= ' WHERE category = "' . $db->quoteString($category) . '"';
        }

        $query .= ' ORDER BY date_publication DESC';

        if (! is_null($limit)) {
            $query .= ' LIMIT ' . $limit;
            if (! is_null($offset)) {
                $query .= ' OFFSET ' . $offset;
            }
        }
        $stmt = $db->simpleQuery($query);

        return $db->dbFetchAllAsObjects($stmt, function ($row) {
            return self::fromDbRowFactory($row);
        });
    }

    public static function getAdminNewsCount(string $category)
    {
        $db = self::db();

        $query = 'SELECT COUNT(*) FROM news';

        if ($category != self::CATEGORY_ANY && self::checkCategory($category)) {
            $query .= ' WHERE category = "' . $db->quoteString($category) . '"';
        }

        return $db->simpleQueryValue($query, 0);
    }

    /**
     * Check if given category is present on the list of common or node-specific categories
     * @return bool
     */
    private static function checkCategory($category): bool
    {
        return in_array($category, self::getAllCategories());
    }

    public function generateDefaultValues()
    {
        $this->date_mainpageexp = new \DateTime('NOW');
        $this->date_mainpageexp->add(new \DateInterval('P1M'));
        $this->category = News::CATEGORY_DRAFT;
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

    public function getCategory(): string
    {
        return $this->category;
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

    /**
     * Returns URI to single news page
     *
     * @return string
     */
    public function getNewsUrl($rawNews=false)
    {
        if ($rawNews) {
            return SimpleRouter::getLink('News.RawNews','show', $this->getId());
        } else {
            return SimpleRouter::getLink('News.NewsList','show', $this->getId());
        }
    }

    /**
     * Check if news can be viewed.
     * Returns true if
     * - publication date is in the past or is not set
     * - expiration date is in the future or is not set
     * - if news is only for logged user - user is logged
     * Returns false otherwise.
     *
     * @param boolean $isUserLogged
     * @return boolean
     */
    public function canBeViewed($isUserLogged)
    {
        $now = new \DateTime();
        if ($this->getDatePublication() != null && $this->getDatePublication() > $now) {
            // not published yet
            return false;
        }

        if ($this->getDateExpiration() != null && $this->getDateExpiration() < $now) {
            // news expired (archived)
            return false;
        }

        $isUserLogged = boolval($isUserLogged);
        if (! $this->getShowNotLogged() && ! $isUserLogged) {
            // news only for logged user, but user is not logged in
            return false;
        }
        return true;
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
            return Formatter::date($obj);
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
        $currentTime = new \DateTime('NOW');
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
