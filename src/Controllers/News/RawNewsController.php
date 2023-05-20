<?php

namespace src\Controllers\News;

use src\Controllers\Core\ViewBaseController;
use src\Models\News\News;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;

class RawNewsController extends ViewBaseController
{

    public function isCallableFromRouter(string $actionName): bool
    {
        // all public methods can be called by router
        return true;
    }

    public function index(): void
    {
        $this->view->redirect(SimpleRouter::getLink('StartPage'));
    }

    /**
     * Shows page with single news identified by $newsId
     *
     * @param int $newsId
     */
    public function show($newsId): void
    {
        $news = News::fromNewsIdFactory($newsId);

        if (is_null($news) || ! $news->canBeViewed($this->isUserLogged())) {
            $this->view->redirect(SimpleRouter::getLink('News.NewsList'));
            exit();
        }
        $this->view->setVar('news', $news);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/news/news.css'));
        $this->view->setTemplate('news/newsRawItem');
        $this->view->buildView();
    }
}
