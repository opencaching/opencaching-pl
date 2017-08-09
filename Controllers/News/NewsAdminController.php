<?php
namespace Controllers\News;

use Controllers\BaseController;
use lib\Objects\ChunkModels\PaginationModel;
use lib\Objects\News\News;
use Utils\Uri\Uri;
use Utils\DateTime\Converter;

class NewsAdminController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (is_null($this->loggedUser) || ! $this->loggedUser->isAdmin()) {
            $this->view->redirect('/');
            exit();
        }
        if (News::compatibileMode()) { // TODO: REMOVE THIS!
            $this->adminDisabled();
            exit();
        }

        if (isset($_REQUEST['action'])) {
            switch ($_REQUEST['action']) {
                case 'edit':
                    $this->editNews();
                    break;
                case 'create':
                    $this->createNews();
                    break;
                case 'save':
                    $this->saveNews();
                    break;
            }
        }

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

    private function saveNews()
    {
        $formResult = $_REQUEST;
        if (! is_array($formResult)) {
            return false;
        }
        header("X-XSS-Protection: 0");
        if ($formResult['id'] != 0) {
            $news = new News([
                'newsId' => $formResult['id']
            ]);
        } else {
            $news = new News();
            $news->setAuthor($this->loggedUser);
        }
        $news->loadFromForm($formResult);
        $news->setEditor($this->loggedUser);
        $news->saveNews();
    }

    private function editNews()
    {
        if (! isset($_REQUEST['id'])) {
            return;
        }
        $news = new News([
            'newsId' => (int) $_REQUEST['id']
        ]);
        if (! $news->dataReady()) {
            return;
        }
        $this->showEditForm($news);
    }

    private function createNews()
    {
        $news = new News();
        $news->generateDefaultValues();
        $news->setAuthor($this->loggedUser);
        $this->showEditForm($news);
    }

    private function showEditForm($news)
    {
        global $dateFormat;
        $this->view->setVar('dateformat_jQuery', Converter::dateformat_PHP_to_jQueryUI($dateFormat));
        $this->view->setVar('news', $news);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/news/news.css'));
        $this->view->loadJquery();

        tpl_set_tplname('news/newsAdminEdit');
        tpl_BuildTemplate();
        exit();
    }

    private function showAdminNewsList(array $newsList)
    {
        tpl_set_tplname('news/newsAdmin');
        $this->view->setVar('newsList', $newsList);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/news/news.css'));
        tpl_BuildTemplate();
        exit();
    }

    private function adminDisabled() // TODO: REMOVE THIS
    {
        tpl_set_tplname('news/newsDisabled');
        tpl_BuildTemplate();
        exit();
    }
}
