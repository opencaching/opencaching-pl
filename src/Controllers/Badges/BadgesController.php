<?php

declare(strict_types=1);

namespace src\Controllers\Badges;

use src\Controllers\Core\ViewBaseController;
use src\Services\Badges\BadgesService;
use src\Utils\Uri\HttpCode;
use src\Utils\Uri\SimpleRouter;

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
        $this->view->setVar('userId', $userId);
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
            http_response_code(HttpCode::STATUS_METHOD_NOT_ALLOWED);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Method not allowed']);
            exit;
        }

        if (! $this->isUserLogged()) {
            http_response_code(HttpCode::STATUS_UNAUTHORIZED);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        $badgeId = isset($_POST['badge_id']) ? (int) $_POST['badge_id'] : 0;
        $level = isset($_POST['level']) ? (int) $_POST['level'] : 0;
        $page = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;

        if ($badgeId <= 0) {
            http_response_code(HttpCode::STATUS_BAD_REQUEST);
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

    public function recalculateAjax($badgeId, $userId): void
    {
        $badgeId = (int) $badgeId;
        $userId = (int) $userId;

        if (! $this->isUserLogged()) {
            http_response_code(HttpCode::STATUS_UNAUTHORIZED);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'User not logged']);
            exit;
        }

        if ($userId !== $this->loggedUser->getUserId() && ! $this->loggedUser->hasOcTeamRole()) {
            http_response_code(HttpCode::STATUS_FORBIDDEN);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Not authorized']);
            exit;
        }

        $badge = $this->badgeService->findBadgeById($badgeId);
        if ($badge === null || empty($badge['active'])) {
            http_response_code(HttpCode::STATUS_NOT_FOUND);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Badge not found']);
            exit;
        }

        if (empty($badge['is_obtainable'])) {
            http_response_code(HttpCode::STATUS_FORBIDDEN);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'ERROR', 'message' => 'Badge not obtainable']);
            exit;
        }

        $result = $this->badgeService->recalculateUserProgress($badgeId, $userId);
        $result['status'] = 'OK';

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index', 'show', 'gainers', 'recalculateAjax'], true);
    }
}
