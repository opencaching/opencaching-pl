<?php
namespace Controllers\News;

use Controllers\BaseController;
use Utils\DateTime\Converter;
use Utils\Uri\SimpleRouter;
use Utils\Uri\Uri;
use lib\Objects\ChunkModels\PaginationModel;
use lib\Objects\News\News;

class NewsAdminController extends BaseController
{

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
        $this->checkUserIsAdmin();

        $this->showAdminNews();
    }

    private function showAdminNews()
    {
        $paginationModel = new PaginationModel(10);
        $paginationModel->setRecordsCount(News::getAdminNewsCount());
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('paginationModel', $paginationModel);
        $this->showAdminNewsList(News::getAdminNews($offset, $limit));
    }

    public function saveNews()
    {
        $this->checkUserIsAdmin();

        $formResult = $_POST;
        if (! is_array($formResult)) {
            return false;
        }
        header("X-XSS-Protection: 0");
        if ($formResult['id'] != 0) {
            $news = News::fromNewsIdFactory($formResult['id']);
        } else {
            $news = new News();
            $news->setAuthor($this->loggedUser);
        }
        $news->loadFromForm($formResult);
        $news->setEditor($this->loggedUser);
        $news->saveNews();

        unset($news);
        $this->view->redirect(SimpleRouter::getLink('News.NewsAdmin'));
    }

    public function editNews($newsId = null)
    {
        $this->checkUserIsAdmin();
        $news = News::fromNewsIdFactory($newsId);
        if (is_null($news)) {
            $this->view->redirect(SimpleRouter::getLink('News.NewsAdmin'));
            exit();
        }
        $this->showEditForm($news);
    }

    public function createNews()
    {
        $this->checkUserIsAdmin();

        $news = new News();
        $news->generateDefaultValues();
        $news->setAuthor($this->loggedUser);
        $this->showEditForm($news);
    }

    private function showEditForm($news)
    {
        $this->view->setVar('dateformat_jQuery', Converter::dateformat_PHP_to_jQueryUI($this->ocConfig->getDateFormat()));
        $this->view->setVar('news', $news);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/news/news.css'));
        $this->view->loadJQueryUI();

        $this->view->setTemplate('news/newsAdminEdit');
        $this->view->buildView();
        exit();
    }

    private function showAdminNewsList(array $newsList)
    {
        $this->view->setVar('newsList', $newsList);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/news/news.css'));

        $this->view->setTemplate('news/newsAdmin');
        $this->view->buildView();
        exit();
    }

    private function checkUserIsAdmin()
    {
        if (! $this->isUserLogged() || ! $this->loggedUser->isAdmin()) {
            $this->view->redirect('/');
            exit();
        }
    }
}