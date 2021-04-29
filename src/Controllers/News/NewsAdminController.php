<?php
namespace src\Controllers\News;

use src\Utils\DateTime\Converter;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;
use src\Models\ChunkModels\PaginationModel;
use src\Models\ChunkModels\UploadModel;
use src\Models\News\News;
use src\Models\Pictures\OcPicture;
use src\Controllers\Core\ViewBaseController;

class NewsAdminController extends ViewBaseController
{

    public function __construct()
    {
        parent::__construct();

        // check if user has rights to edit news
        if (!$this->isUserLogged()) {
            $this->redirectNotLoggedUsers();
            exit;
        }

        if(!$this->loggedUser->hasNewsPublisherRole()) {
            $this->displayCommonErrorPageAndExit("You do not have rights to edit news");
            exit();
        }
    }

    public function isCallableFromRouter($actionName): bool
    {
        // all public methods can be called by router
        return true;
    }

    public function index(): void
    {
        $this->showCategory(News::CATEGORY_ANY);
    }

    public function showCategory($category = News::CATEGORY_ANY): void
    {
        $paginationModel = new PaginationModel(10);
        $paginationModel->setRecordsCount(News::getAdminNewsCount($category));
        list ($limit, $offset) = $paginationModel->getQueryLimitAndOffset();
        $this->view->setVar('paginationModel', $paginationModel);

        $this->view->setVar('selectedCategory', $category);
        $this->showAdminNewsList(News::getAdminNews($category, $offset, $limit));
    }

    public function saveNews(): void
    {
        $formResult = $_POST;
        if (! is_array($formResult)) {
            return;
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

        $this->view->redirect(SimpleRouter::getLink('News.NewsAdmin'));
    }

    public function editNews($newsId = null): void
    {
        $news = News::fromNewsIdFactory($newsId);
        if (is_null($news)) {
            $this->view->redirect(SimpleRouter::getLink('News.NewsAdmin'));
            exit();
        }

        $this->view->addHeaderChunk('upload/upload');
        $this->view->addHeaderChunk('handlebarsJs');

        $uploadModel = UploadModel::NewsPicUploadFactory($newsId);
        $this->view->setVar('picsUploadModelJson', $uploadModel->getJsonParams());

        // prepare the list of pictures for this news
        $picList = OcPicture::getListForParent(OcPicture::TYPE_NEWS, $newsId);
        $this->view->setVar('picList', $picList);

        $this->showEditForm($news);
    }

    public function createNews(): void
    {
        $this->view->setVar('picsUploadModelJson', '{}');
        $news = new News();
        $news->generateDefaultValues();
        $news->setAuthor($this->loggedUser);

        // set empty list of pics
        $this->view->setVar('picList', []);

        $this->showEditForm($news);
    }

    private function showEditForm($news): void
    {
        $this->view->setVar('allCategories', News::getAllCategories());
        $this->view->setVar('dateformat_jQuery', Converter::dateformat_PHP_to_jQueryUI($this->ocConfig->getDateFormat()));
        $this->view->setVar('news', $news);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/news/news.css'));
        $this->view->loadJQueryUI();

        $this->view->setTemplate('news/newsAdminEdit');
        $this->view->buildView();
        exit();
    }

    private function showAdminNewsList(array $newsList): void
    {
        $this->view->loadJQuery();

        $this->view->setVar('newsList', $newsList);
        $this->view->setVar('allCategories', News::getAllCategories());
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/news/news.css'));

        $this->view->setTemplate('news/newsAdmin');
        $this->view->buildView();
        exit();
    }


}
