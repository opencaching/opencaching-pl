<?php

declare(strict_types=1);

namespace src\Controllers\Badges;

use src\Controllers\Core\ViewBaseController;
use src\Services\Badges\BadgesService;

/**
 * Badges in a user context (progress). User is optional (session when omitted).
 */
class MyBadgesController extends ViewBaseController
{
    private readonly BadgesService $badgeService;

    public function __construct()
    {
        parent::__construct();
        $this->badgeService = new BadgesService();
    }

    public function index(): void
    {
        $this->view->setVar('title', 'MyBadges');
        $this->view->setTemplate('badges/my');
        $this->view->buildView();
    }

    public function user($userId = null): void
    {
        $this->view->setVar('title', 'MyBadges - user');
        $this->view->setVar('userId', $userId);
        $this->view->setTemplate('badges/my');
        $this->view->buildView();
    }

    public function badge($badgeId, $userId = null): void
    {
        $this->view->setVar('title', 'MyBadges - badge');
        $this->view->setVar('badgeId', $badgeId);
        $this->view->setVar('userId', $userId);
        $this->view->setTemplate('badges/myBadge');
        $this->view->buildView();
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index', 'user', 'badge'], true);
    }
}
