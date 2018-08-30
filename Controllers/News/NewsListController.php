<?php
namespace Controllers\News;

use Controllers\BaseController;
use lib\Objects\News\News;
use lib\Objects\ChunkModels\PaginationModel;
use Utils\Uri\Uri;
use Utils\Uri\SimpleRouter;

class NewsListController extends BaseController
{

    /**
     * How many news to display max on mainpage
     */
    const NEWS_ON_MAINPAGE = 3;

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return true;
    }

    public function index()
    {
        $this->showNews();
    }

    /**
     * Method generates news list on News page
     */
    private function showNews()
    {
        $paginationModel = new PaginationModel();
        $paginationModel->setRecordsCount(News::getAllNewsCount($this->isUserLogged(), false));
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('paginationModel', $paginationModel);
        $this->showNewsList(News::getAllNews($this->isUserLogged(), false, $offset, $limit));
    }

    /**
     * Method is used by index.php to generate list of news on mainpage
     * (should be removed / refactored after index.php refactoring)
     *
     * @param boolean $logged
     * @return News[]
     */
    public static function listNewsOnMainPage($logged = false)
    {
        return News::getAllNews($logged, true, 0, self::NEWS_ON_MAINPAGE);
    }

    private function showNewsList(array $newsList)
    {
        $this->view->setTemplate('news/newsList');
        $this->view->setVar('newsList', $newsList);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/news/news.css'));
        $this->view->buildView();
    }

    /**
     * Shows page with single news identyfied by $newsId
     *
     * @param integer $newsId
     */
    public function show($newsId)
    {
        $news = News::fromNewsIdFactory($newsId);
        if (is_null($news) || ! $news->canBeViewed($this->isUserLogged())) {
           $this->view->redirect(SimpleRouter::getLink('News.NewsList'));
           exit();
        }
        $this->view->setVar('news', $news);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/news/news.css'));
        $this->view->setTemplate('news/newsItem');
        $this->view->buildView();
    }

}