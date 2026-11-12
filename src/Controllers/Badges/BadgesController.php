<?php

declare(strict_types=1);

namespace src\Controllers\Badges;

use src\Controllers\Core\ViewBaseController;
use src\Services\Badges\BadgesService;
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
        $this->view->setTemplate('badges/index');
        $this->view->buildView();
    }

    public function show($badgeId): void
    {
        $badge = $this->badgeService->findBadgeById((int) $badgeId);

        if ($badge === null) {
            $this->displayCommonErrorPageAndExit('Badge not found', 404);
            return;
        }

        // Restricted badge: visible only to its owners and OC Team members.
        if (! empty($badge['display_restricted'])) {
            $user = $this->loggedUser;
            $isOcteam = $user !== null && $user->hasOcTeamRole();
            $isOwner = $user !== null && $this->badgeService->isOwner((int) $badge['id'], $user->getUserId());

            if (! $isOcteam && ! $isOwner) {
                SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges'));
                return;
            }
        }

        $userId = $this->loggedUser !== null ? $this->loggedUser->getUserId() : 0;
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
        $this->view->setTemplate('badges/show');
        $this->view->buildView();
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index', 'show'], true);
    }
}
