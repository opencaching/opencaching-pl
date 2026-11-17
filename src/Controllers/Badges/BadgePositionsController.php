<?php

declare(strict_types=1);

namespace src\Controllers\Badges;

use src\Controllers\Core\ViewBaseController;
use src\Enums\Badges\BadgePositionsEnum;
use src\Models\ChunkModels\PaginationModel;
use src\Services\Badges\BadgePositionsService;
use src\Utils\Uri\SimpleRouter;

/**
 * Badge positions list.
 */
class BadgePositionsController extends ViewBaseController
{
    private readonly BadgePositionsService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new BadgePositionsService();
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index'], true);
    }

    /**
     * Route: Badges.BadgePositions
     */
    public function index($badgeId = null)
    {
        if ($badgeId === null) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges'));
            return;
        }

        $badgeId = (int) $badgeId;
        if ($badgeId <= 0) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges'));
            return;
        }

        $badge = $this->service->findBadge($badgeId);
        if ($badge === null) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges'));
            return;
        }

        if (empty($badge['show_positions_list'])) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges', 'show', $badgeId));
            return;
        }

        $userId = $this->getIntegerQueryParameter('user_id', 0);
        $userId = $userId > 0 ? $userId : null;

        if ($userId !== null && ! $this->service->userExists($userId)) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges'));
            return;
        }

        $foundFilter = isset($_GET['found']) && is_string($_GET['found']) ? trim($_GET['found']) : BadgePositionsEnum::FOUND_ALL;
        if (!in_array($foundFilter, [BadgePositionsEnum::FOUND_ALL, BadgePositionsEnum::FOUND_NOT_FOUND, BadgePositionsEnum::FOUND_FOUND], true)) {
            $foundFilter = BadgePositionsEnum::FOUND_ALL;
        }
        if ($userId === null) {
            $foundFilter = BadgePositionsEnum::FOUND_ALL;
        }

        $sort = $this->getIntegerQueryParameter('sort', BadgePositionsEnum::SORT_ID_DESC);
        if (!array_key_exists($sort, BadgePositionsEnum::sortOptions())) {
            $sort = BadgePositionsEnum::SORT_ID_DESC;
        }

        $belongingQuery = trim((string) ($badge['belonging_query'] ?? ''));

        $totalCount = $this->service->countPositions($badgeId, $belongingQuery, $userId, $foundFilter);

        $paginationModel = new PaginationModel(BadgePositionsEnum::LIMIT);
        $paginationModel->setRecordsCount($totalCount);
        [$limit, $offset] = $paginationModel->getQueryLimitAndOffset();

        $positions = $this->service->findPositions(
            $badgeId,
            $belongingQuery,
            $limit,
            $offset,
            BadgePositionsEnum::sortMapper($sort),
            $userId,
            $foundFilter
        );

        $this->view->setVar('title', 'Badge positions');
        $this->view->setVar('badge', $badge);
        $this->view->setVar('badgeId', $badgeId);
        $this->view->setVar('userId', $userId);
        $this->view->setVar('foundFilter', $foundFilter);
        $this->view->setVar('badgeImages', $this->service->findBadgeImages($badgeId));
        $this->view->setVar('positions', $positions);
        $this->view->setVar('totalCount', $totalCount);
        $this->view->setVar('sort', $sort);
        $this->view->setVar('sortOptions', BadgePositionsEnum::sortOptions());
        $foundOptions = [];
        foreach (BadgePositionsEnum::FOUND_OPTIONS as $value => $key) {
            $foundOptions[$value] = tr($key);
        }
        $this->view->setVar('foundOptions', $foundOptions);
        $this->view->setVar('limit', BadgePositionsEnum::LIMIT);
        $this->view->setVar('paginationModel', $paginationModel);

        $this->view->setTemplate('badges/positions');
        $this->view->buildView();
    }

}