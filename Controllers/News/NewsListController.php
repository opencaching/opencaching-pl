<?php
namespace Controllers\News;

use Controllers\BaseController;
use lib\Objects\News\News;
use lib\Objects\ChunkModels\PaginationModel;
use Utils\Uri\Uri;

class NewsListController extends BaseController
{

    /**
     * How many news to display max on mainpage
     */
    const NEWS_ON_MAINPAGE = 3;

    /**
     * How many news to display max in RSS feed
     */
    const NEWS_IN_RSS = 20;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Default
     */
    public function index()
    {
        $this->showNews();
    }

    /**
     * Method is used to generate news RSS feed
     */
    public function showRss()
    {
        global $short_sitename, $site_name, $absolute_server_URI;
        
        header('Content-type: application/xml; charset="utf-8"');
        $newsList = News::getAllNews(false, false, 0, self::NEWS_IN_RSS);
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\">\n<channel>\n<title>" . $short_sitename . " - " . tr('rss_04') . "</title>\n";
        $content .= "<ttl>60</ttl><link>" . $absolute_server_URI . "news.php</link>\n <description><![CDATA[" . tr('rss_12') . " " . $site_name . "]]></description>";
        $content .= "<image><title>" . $short_sitename . " - " . tr('rss_04') . "</title><url>" . $absolute_server_URI . "images/oc.png</url>";
        $content .= "<link>" . $absolute_server_URI . "news.php</link><width>100</width><height>28</height></image>\n";
        foreach ($newsList as $news) {
            $content .= "<item>\n<title>" . $news->getDatePublication(true) . " - " . $news->getTitle() . "</title>\n<description><![CDATA[" . $news->getContent() . "]]></description>\n<link>" . $absolute_server_URI . "news.php</link>\n<guid isPermaLink=\"false\">article " . $news->getId() . "</guid>\n</item>\n";
        }
        $content .= "</channel>\n</rss>\n";
        echo $content;
    }

    /**
     * Method generates news list on News page
     */
    private function showNews()
    {
        $logged = ($this->loggedUser) ? true : false;
        $paginationModel = new PaginationModel();
        $paginationModel->setRecordsCount(News::getAllNewsCount($logged, false));
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('paginationModel', $paginationModel);
        $this->showNewsList(News::getAllNews($logged, false, $offset, $limit));
    }

    /**
     * Method is used by index.php to generate list of news on mainpage
     * (should be removed / refactored after index.php refactoring)
     *
     * @param string $logged            
     * @return array of News objects
     */
    public static function listNewsOnMainPage($logged = false)
    {
        return News::getAllNews($logged, true, 0, self::NEWS_ON_MAINPAGE);
    }

    private function showNewsList(array $newsList)
    {
        tpl_set_tplname('news/newsList');
        $this->view->setVar('newsList', $newsList);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/news/news.css'));
        tpl_BuildTemplate();
    }
}
