<?php

declare(strict_types=1);

namespace src\Controllers\Badges;

use src\Controllers\Core\ViewBaseController;
use src\Models\Badges\BadgeImageUploadModel;
use src\Services\Badges\BadgesService;
use src\Utils\FileSystem\FileUploadMgr;
use src\Utils\Img\OcImage;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;

/**
 * Badges (without a user context): list and description.
 */
class BadgesController extends ViewBaseController
{
    private readonly BadgesService $badgeService;

    public function __construct()
    {
        parent::__construct();
        $this->badgeService = new BadgesService();
    }

    public function index(): void
    {
        $this->view->setVar('title', 'Badges');
        $this->view->setVar(
            'categories',
            $this->badgeService->findAllActiveBadgesGroupedByCategory()
        );
        $this->view->setTemplate('badges/indexBadge');
        $this->view->buildView();
    }

    public function show($badgeId): void
    {
        $badge = $this->badgeService->findBadgeById((int) $badgeId);

        if ($badge === null) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges'));
            return;
        }

        $user = $this->loggedUser;
        $isOcteam = $user !== null && $user->hasOcTeamRole();

        if (empty($badge['active']) && ! $isOcteam) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges'));
            return;
        }

        // Restricted badge: visible only to its owners and OC Team members.
        if (! empty($badge['display_restricted'])) {
            $isOwner = $user !== null && $this->badgeService->isOwner((int) $badge['id'], $user->getUserId());

            if (! $isOcteam && ! $isOwner) {
                SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges'));
                return;
            }
        }

        $userId = $user !== null ? $user->getUserId() : 0;
        $stats = $this->badgeService->getBadgeStats((int) $badge['id'], $userId);

        $requestedLang = isset($_GET['desclang']) ? (string) $_GET['desclang'] : null;
        $badgeDescription = $this->badgeService->getDescription((int) $badge['id'], $requestedLang);

        $this->view->setVar('title', 'Badges - show');
        $this->view->setVar('badge', $badge);
        $this->view->setVar('badgeId', $badgeId);
        $this->view->setVar('activeCount', $stats['activeCount']);
        $this->view->setVar('badgeImages', $this->badgeService->findBadgeImages((int) $badgeId));
        $this->view->setVar('levels', $this->badgeService->findLevels((int) $badgeId));
        $this->view->setVar('recommendedCaches', $this->badgeService->findRecommendedCaches((int) $badgeId));
        $this->view->setVar('titledCaches', $this->badgeService->findTitledCaches((int) $badgeId));
        $this->view->setVar('badgeDescription', $badgeDescription);
        $this->view->setVar('badgeOwners', $this->badgeService->getBadgeOwners((int) $badgeId));
        $this->view->setTemplate('badges/showBadge');
        $this->view->buildView();
    }

    public function gainers(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Method not allowed']);
            exit;
        }

        if (! $this->isUserLogged()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        $badgeId = isset($_POST['badge_id']) ? (int) $_POST['badge_id'] : 0;
        $level = isset($_POST['level']) ? (int) $_POST['level'] : 0;
        $page = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;

        if ($badgeId <= 0) {
            http_response_code(400);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'badge_id required']);
            exit;
        }

        $data = $this->badgeService->findBadgeLevelGainers($badgeId, $level, $page, 20);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'status' => 'OK',
            'badgeId' => $data['badgeId'],
            'level' => $data['level'],
            'page' => $data['page'],
            'perPage' => $data['perPage'],
            'total' => $data['total'],
            'users' => $data['users'],
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function uploadImageAjax($badgeId): void
    {
        $badgeId = (int) $badgeId;

        if (! $this->isUserLogged()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        if (! $this->loggedUser->hasOcTeamRole()) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Not authorized']);
            exit;
        }

        $badge = $this->badgeService->findBadgeById($badgeId);
        if ($badge === null || empty($badge['can_edit'])) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Badge not editable']);
            exit;
        }

        $uploadModel = BadgeImageUploadModel::forBadge($badgeId);

        try {
            $tmpFiles = FileUploadMgr::processFileUpload($uploadModel);
            $tmpFile = array_shift($tmpFiles);
        } catch (\RuntimeException $e) {
            http_response_code(400);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
            exit;
        }

        $newPath = \src\Models\OcConfig\OcConfig::getDynFilesPath(true) . '/images/uploads/badges/main/' . \src\Utils\Generators\Uuid::create();
        // ensure dir exists
        $dir = dirname($newPath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $fullTmpPath = $uploadModel->getDirAtServer() . '/' . $tmpFile;
        $savedPath = \src\Utils\Img\OcImage::createThumbnail($fullTmpPath, $newPath, [120, 120]);

        $relativeUrl = 'images/uploads/badges/main/' . basename($savedPath);
        $this->badgeService->updateBadgePicture($badgeId, $relativeUrl);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'OK', 'url' => $relativeUrl], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function uploadLevelImageAjax($badgeId, $level): void
    {
        $badgeId = (int) $badgeId;
        $level = (int) $level;

        if (! $this->isUserLogged()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        if (! $this->loggedUser->hasOcTeamRole()) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Not authorized']);
            exit;
        }

        $badge = $this->badgeService->findBadgeById($badgeId);
        if ($badge === null || empty($badge['can_edit'])) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Badge not editable']);
            exit;
        }

        $uploadModel = \src\Models\Badges\BadgeLevelImageUploadModel::forLevel($badgeId, $level);

        try {
            $tmpFiles = \src\Utils\FileSystem\FileUploadMgr::processFileUpload($uploadModel);
            $tmpFile = array_shift($tmpFiles);
        } catch (\RuntimeException $e) {
            http_response_code(400);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
            exit;
        }

        $dir = \src\Models\OcConfig\OcConfig::getDynFilesPath(true) . '/images/uploads/badges';
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $newPath = \src\Models\OcConfig\OcConfig::getDynFilesPath(true) . '/images/uploads/badges/levels/' . \src\Utils\Generators\Uuid::create();
        $fullTmpPath = $uploadModel->getDirAtServer() . '/' . $tmpFile;
        $savedPath = \src\Utils\Img\OcImage::createThumbnail($fullTmpPath, $newPath, [120, 120]);

        $relativeUrl = 'images/uploads/badges/levels/' . basename($savedPath);
        $this->badgeService->updateLevelPicture($badgeId, $level, $relativeUrl);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'OK', 'url' => $relativeUrl], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function deleteImageAjax($badgeId): void
    {
        $badgeId = (int) $badgeId;

        if (! $this->isUserLogged()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        if (! $this->loggedUser->hasOcTeamRole()) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Not authorized']);
            exit;
        }

        $badge = $this->badgeService->findBadgeById($badgeId);
        if ($badge === null || empty($badge['can_edit'])) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Badge not editable']);
            exit;
        }

        $adminService = new \src\Services\Badges\BadgesAdminService();
        $adminService->deleteBadgeImage($badgeId);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'OK'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function deleteLevelImageAjax($badgeId, $level): void
    {
        $badgeId = (int) $badgeId;
        $level = (int) $level;

        if (! $this->isUserLogged()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        if (! $this->loggedUser->hasOcTeamRole()) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Not authorized']);
            exit;
        }

        $badge = $this->badgeService->findBadgeById($badgeId);
        if ($badge === null || empty($badge['can_edit'])) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Badge not editable']);
            exit;
        }

        $adminService = new \src\Services\Badges\BadgesAdminService();
        $adminService->deleteLevelImage($badgeId, $level);

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'OK'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index', 'show', 'gainers', 'uploadImageAjax', 'uploadLevelImageAjax', 'deleteImageAjax', 'deleteLevelImageAjax'], true);
    }
}
