<?php

namespace src\Controllers\Admin;

use src\Controllers\Core\ViewBaseController;
use src\Enums\BadgeSortEnum;
use src\Models\Badges\BadgeImageUploadModel;
use src\Models\Badges\BadgeLevelImageUploadModel;
use src\Models\ChunkModels\PaginationModel;
use src\Services\Badges\BadgesAdminService;
use src\Utils\FileSystem\FileUploadMgr;
use src\Utils\Img\OcImage;
use src\Utils\Uri\HttpCode;
use src\Utils\Uri\SimpleRouter;

/**
 * Admin controller for badges (OC Team only).
 */
class BadgesAdminController extends ViewBaseController
{
    use \src\Services\RequiresOcteamPostTrait;

    private const PER_PAGE = 50;

    private BadgesAdminService $service;

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

        $this->service = new BadgesAdminService();
    }

    public function deleteLevelImage($badgeId = null, $level = null)
    {
        $badgeId = (int) $badgeId;
        $level = (int) $level;

        try {
            $this->requireOcteamPost();
        } catch (\RuntimeException $e) {
            $this->view->redirect(SimpleRouter::getLink('Admin.BadgesAdmin', 'edit', $badgeId) . '?msg=error&err=octeam_access_required');
            return;
        }

        $badge = $this->service->getBadge($badgeId);
        if (! is_array($badge) || empty($badge['can_edit'])) {
            $this->view->redirect(SimpleRouter::getLink('Admin.BadgesAdmin'));
            return;
        }

        $this->service->deleteLevelImage($badgeId, $level);

        $this->view->redirect(SimpleRouter::getLink('Admin.BadgesAdmin', 'edit', $badgeId) . '?msg=saved');
    }

    public function uploadImageAjax($badgeId): void
    {
        $badgeId = (int) $badgeId;

        if (! $this->isUserLogged()) {
            http_response_code(HttpCode::STATUS_UNAUTHORIZED);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        $badge = $this->service->getBadge($badgeId);
        if (! is_array($badge) || empty($badge['can_edit'])) {
            http_response_code(HttpCode::STATUS_FORBIDDEN);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Badge not editable']);
            exit;
        }

        $uploadModel = BadgeImageUploadModel::forBadge($badgeId);

        try {
            $tmpFiles = FileUploadMgr::processFileUpload($uploadModel);
            $tmpFile = array_shift($tmpFiles);
        } catch (\RuntimeException $e) {
            http_response_code(HttpCode::STATUS_BAD_REQUEST);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
            exit;
        }

        $newPath = \src\Models\OcConfig\OcConfig::getDynFilesPath(true) . '/images/uploads/badges/main/' . \src\Utils\Generators\Uuid::create();
        $dir = dirname($newPath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $fullTmpPath = $uploadModel->getDirAtServer() . '/' . $tmpFile;
        $savedPath = \src\Utils\Img\OcImage::createThumbnail($fullTmpPath, $newPath, [120, 120]);

        $relativeUrl = 'images/uploads/badges/main/' . basename($savedPath);
        $this->service->updateBadgePicture($badgeId, $relativeUrl);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'OK', 'url' => $relativeUrl], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function uploadLevelImageAjax($badgeId, $level): void
    {
        $badgeId = (int) $badgeId;
        $level = (int) $level;

        if (! $this->isUserLogged()) {
            http_response_code(HttpCode::STATUS_UNAUTHORIZED);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        $badge = $this->service->getBadge($badgeId);
        if (! is_array($badge) || empty($badge['can_edit'])) {
            http_response_code(HttpCode::STATUS_FORBIDDEN);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Badge not editable']);
            exit;
        }

        $uploadModel = BadgeLevelImageUploadModel::forLevel($badgeId, $level);

        try {
            $tmpFiles = FileUploadMgr::processFileUpload($uploadModel);
            $tmpFile = array_shift($tmpFiles);
        } catch (\RuntimeException $e) {
            http_response_code(HttpCode::STATUS_BAD_REQUEST);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
            exit;
        }

        $dir = \src\Models\OcConfig\OcConfig::getDynFilesPath(true) . '/images/uploads/badges/levels';
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $newPath = \src\Models\OcConfig\OcConfig::getDynFilesPath(true) . '/images/uploads/badges/levels/' . \src\Utils\Generators\Uuid::create();
        $fullTmpPath = $uploadModel->getDirAtServer() . '/' . $tmpFile;
        $savedPath = \src\Utils\Img\OcImage::createThumbnail($fullTmpPath, $newPath, [120, 120]);

        $relativeUrl = 'images/uploads/badges/levels/' . basename($savedPath);
        $this->service->updateLevelPicture($badgeId, $level, $relativeUrl);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'OK', 'url' => $relativeUrl], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function deleteImageAjax($badgeId): void
    {
        $badgeId = (int) $badgeId;

        if (! $this->isUserLogged()) {
            http_response_code(HttpCode::STATUS_UNAUTHORIZED);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        $badge = $this->service->getBadge($badgeId);
        if (! is_array($badge) || empty($badge['can_edit'])) {
            http_response_code(HttpCode::STATUS_FORBIDDEN);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Badge not editable']);
            exit;
        }

        $this->service->deleteBadgeImage($badgeId);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'OK'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function deleteLevelImageAjax($badgeId, $level): void
    {
        $badgeId = (int) $badgeId;
        $level = (int) $level;

        if (! $this->isUserLogged()) {
            http_response_code(HttpCode::STATUS_UNAUTHORIZED);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        $badge = $this->service->getBadge($badgeId);
        if (! is_array($badge) || empty($badge['can_edit'])) {
            http_response_code(HttpCode::STATUS_FORBIDDEN);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Badge not editable']);
            exit;
        }

        $this->service->deleteLevelImage($badgeId, $level);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'OK'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index', 'edit', 'save', 'deleteLevelImage', 'uploadImageAjax', 'uploadLevelImageAjax', 'deleteImageAjax', 'deleteLevelImageAjax'], true);
    }

    public function index()
    {
        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
        $sort = BadgeSortEnum::fromValue($_GET['sort'] ?? null);
        $page = isset($_GET['_page']) ? max(1, (int) $_GET['_page']) : 1;
        $perPage = self::PER_PAGE;

        $paginationModel = new PaginationModel($perPage);
        $result = $this->service->getBadges($search, $sort, $page, $perPage);
        $paginationModel->setRecordsCount($result['total']);

        $this->view->setVar('badges', $result['items']);
        $this->view->setVar('search', $search);
        $this->view->setVar('sort', $sort->value);
        $this->view->setVar('sortOptions', BadgeSortEnum::options());
        $this->view->setVar('paginationModel', $paginationModel);
        $this->view->setVar('totalCount', $result['total']);
        $this->view->setVar('limit', $perPage);
        $this->view->setTemplate('admin/badges/badges');
        $this->view->buildView();
    }

    public function edit($id = null)
    {
        $id = (int) $id;

        $badge = $this->service->getBadge($id);
        if (is_array($badge) && empty($badge['can_edit'])) {
            $this->view->redirect(SimpleRouter::getLink('Admin.BadgesAdmin'));
            return;
        }

        $levels = $this->service->getBadgeLevels($id);
        $categories = $this->service->getCategories();

        if ($id > 0 && ! empty($badge['can_edit'])) {
            $this->view->loadJQuery();
            $this->view->loadJQueryUI();
            $this->view->addHeaderChunk('upload/upload');
            $this->view->addHeaderChunk('handlebarsJs');
            $levelUploadModel = \src\Models\Badges\BadgeLevelImageUploadModel::forLevel($id, 0);
            $this->view->setVar('badgeLevelImageUploadModelJson', $levelUploadModel->getJsonParams());
            $badgeUploadModel = \src\Models\Badges\BadgeImageUploadModel::forBadge($id);
            $this->view->setVar('badgeImageUploadModelJson', $badgeUploadModel->getJsonParams());
        }



        $message = null;
        $error = null;
        if (isset($_GET['msg']) && $_GET['msg'] === 'saved') {
            $message = tr('changes_saved');
        }
        if (isset($_GET['err'])) {
            $errKey = (string) $_GET['err'];
            if ($errKey === 'badge_level_name_too_long') {
                $error = tr('badge_level_name_too_long') . ' ' . tr('max_n_characters', [30]);
            } else {
                $error = tr($errKey);
            }
        }

        $this->view->setVar('badge', $badge);
        $this->view->setVar('levels', $levels);
        $this->view->setVar('categories', $categories);
        $this->view->setVar('message', $message);
        $this->view->setVar('error', $error);
        $this->view->setTemplate('admin/badges/badgeEdit');
        $this->view->buildView();
    }

    public function save($id = null)
    {
        $id = (int) $id;

        try {
            $this->requireOcteamPost();
        } catch (\RuntimeException $e) {
            $this->view->redirect(SimpleRouter::getLink('Admin.BadgesAdmin', 'edit', $id) . '?msg=error&err=octeam_access_required');
            return;
        }

        $badge = $this->service->getBadge($id);
        if (! is_array($badge) || empty($badge['can_edit'])) {
            $this->view->redirect(SimpleRouter::getLink('Admin.BadgesAdmin'));
            return;
        }

        $postId = isset($_POST['id']) ? (int) $_POST['id'] : $id;
        $name = isset($_POST['name']) ? trim((string) $_POST['name']) : '';
        $categoryId = isset($_POST['category_id']) ? (int) $_POST['category_id'] : (int) ($badge['category_id'] ?? 0);
        $sequence = isset($_POST['sequence']) ? (int) $_POST['sequence'] : (int) ($badge['sequence'] ?? 0);

        $levelNames = [];
        if (isset($_POST['level_name']) && is_array($_POST['level_name'])) {
            $levelNames = $_POST['level_name'];
        }

        try {
            $this->service->saveBadge($postId > 0 ? $postId : $id, [
                'name' => $name !== '' ? $name : ($badge['name'] ?? ''),
                'category_id' => $categoryId,
                'sequence' => $sequence,
            ]);
            if (! empty($levelNames)) {
                $this->service->saveLevelNames($postId > 0 ? $postId : $id, $levelNames);
            }
            $targetId = $postId > 0 ? $postId : $id;
            $this->view->redirect(SimpleRouter::getLink('Admin.BadgesAdmin', 'edit', $targetId) . '?msg=saved');
        } catch (\RuntimeException $e) {
            $targetId = $postId > 0 ? $postId : $id;
            $this->view->redirect(SimpleRouter::getLink('Admin.BadgesAdmin', 'edit', $targetId) . '?msg=error&err=' . urlencode($e->getMessage()));
        }
    }
}
