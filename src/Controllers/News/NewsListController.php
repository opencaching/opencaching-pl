<?php
namespace src\Controllers\News;

use src\Models\News\News;
use src\Models\ChunkModels\PaginationModel;
use src\Utils\Uri\Uri;
use src\Utils\Uri\SimpleRouter;
use src\Controllers\Core\ViewBaseController;

class NewsListController extends ViewBaseController
{
    /**
     * How many news to display max on mainpage
     */
    const NEWS_ON_MAINPAGE = 3;

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter(string $actionName)
    {
        // all public methods can be called by router
        return true;
    }

    public function index()
    {
        $this->showCat(News::CATEGORY_NEWS);
    }

    /**
     * Method generates news list on News page
     */
    public function showCat(string $category)
    {
        $paginationModel = new PaginationModel();
        $paginationModel->setRecordsCount(News::getAllNewsCount($category, $this->isUserLogged(), false));
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('paginationModel', $paginationModel);

        +$this->showNewsList(News::getAllNews($category, $this->isUserLogged(), false, $offset, $limit));
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
        return News::getAllNews(News::CATEGORY_NEWS, $logged, true, 0, self::NEWS_ON_MAINPAGE);
    }

    private function showNewsList(array $newsList)
    {
        $this->view->setTemplate('news/newsList');
        $this->view->setVar('newsList', $newsList);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/news/news.css'));
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
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/news/news.css'));
        $this->view->setTemplate('news/newsItem');
        $this->view->buildView();
    }
}
