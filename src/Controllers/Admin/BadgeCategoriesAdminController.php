<?php

namespace src\Controllers\Admin;

use src\Controllers\Core\ViewBaseController;
use src\Enums\BadgeCategorySortEnum;
use src\Models\ChunkModels\PaginationModel;
use src\Services\Badges\BadgeCategoriesAdminService;
use src\Utils\Uri\HttpCode;
use src\Utils\Uri\SimpleRouter;

/**
 * Admin controller for badge categories (OC Team only).
 */
class BadgeCategoriesAdminController extends ViewBaseController
{
    use \src\Services\RequiresOcteamPostTrait;

    private BadgeCategoriesAdminService $service;

    public function __construct()
    {
        parent::__construct();

        $this->redirectNotLoggedUsers();

        if (! $this->loggedUser || ! $this->loggedUser->hasOcTeamRole()) {
            $this->displayCommonErrorPageAndExit(
                'Not authorized for this operation',
                HttpCode::STATUS_FORBIDDEN
            );
        }

        $this->service = new BadgeCategoriesAdminService();
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index', 'edit', 'save'], true);
    }

    public function index()
    {
        $name = isset($_GET['name']) ? trim((string) $_GET['name']) : '';
        $id = isset($_GET['id']) && ctype_digit((string) $_GET['id']) ? (int) $_GET['id'] : null;
        $sort = BadgeCategorySortEnum::fromValue($_GET['sort'] ?? null);
        $page = isset($_GET['_page']) ? max(1, (int) $_GET['_page']) : 1;
        $perPage = 50;

        $paginationModel = new PaginationModel($perPage);
        $result = $this->service->getCategories($name !== '' ? $name : null, $id, $sort, $page, $perPage);
        $paginationModel->setRecordsCount($result['total']);

        $this->view->setVar('categories', $result['items']);
        $this->view->setVar('searchName', $name);
        $this->view->setVar('searchId', $id);
        $this->view->setVar('sort', $sort->value);
        $this->view->setVar('sortOptions', BadgeCategorySortEnum::options());
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('totalCount', $result['total']);
        $this->view->setVar('limit', $perPage);
        $this->view->setTemplate('admin/badges/categories');
        $this->view->buildView();
    }

    public function edit($id = null)
    {
        $id = (int) $id;

        $category = $this->service->getCategory($id);

        $message = null;
        $error = null;
        if (isset($_GET['msg']) && $_GET['msg'] === 'saved') {
            $message = tr('changes_saved');
        }
        if (isset($_GET['err'])) {
            $error = tr((string) $_GET['err']);
        }

        $this->view->setVar('category', $category);
        $this->view->setVar('message', $message);
        $this->view->setVar('error', $error);
        $this->view->setTemplate('admin/badges/categoryEdit');
        $this->view->buildView();
    }

    public function save($id = null)
    {
        $id = (int) $id;

        try {
            $this->requireOcteamPost();
        } catch (\RuntimeException $e) {
            $this->view->redirect(SimpleRouter::getLink('Admin.BadgeCategoriesAdmin', 'edit', $id) . '?msg=error&err=octeam_access_required');
            return;
        }

        $postId = isset($_POST['id']) ? (int) $_POST['id'] : $id;
        $name = isset($_POST['name']) ? trim((string) $_POST['name']) : '';
        $sequence = isset($_POST['sequence']) ? (int) $_POST['sequence'] : 0;
        $active = isset($_POST['active']) ? 1 : 0;
        $sortDesc = isset($_POST['sort_desc']) ? 1 : 0;

        try {
            $this->service->saveCategory($postId > 0 ? $postId : $id, [
                'name' => $name,
                'sequence' => $sequence,
                'active' => $active,
                'sort_desc' => $sortDesc,
            ]);
            $targetId = $postId > 0 ? $postId : $id;
            $this->view->redirect(SimpleRouter::getLink('Admin.BadgeCategoriesAdmin', 'edit', $targetId) . '?msg=saved');
        } catch (\RuntimeException $e) {
            $targetId = $postId > 0 ? $postId : $id;
            $this->view->redirect(SimpleRouter::getLink('Admin.BadgeCategoriesAdmin', 'edit', $targetId) . '?msg=error&err=' . urlencode($e->getMessage()));
        }
    }
}
