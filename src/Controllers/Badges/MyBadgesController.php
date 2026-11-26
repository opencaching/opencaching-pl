<?php

declare(strict_types=1);

namespace src\Controllers\Badges;

use src\Controllers\Core\ViewBaseController;
use src\Models\User\User;
use src\Services\Badges\BadgesService;
use src\Utils\Uri\SimpleRouter;

/**
 * Badges in a user context (progress).
 */
class MyBadgesController extends ViewBaseController
{
    private readonly BadgesService $badgeService;

    public function __construct()
    {
        parent::__construct();
        $this->badgeService = new BadgesService();
    }

    public function index($userId = null): void
    {
        $userId = (int) ($userId ?? ($this->loggedUser ? $this->loggedUser->getUserId() : 0));

        $user = $userId > 0 ? User::fromUserIdFactory($userId) : null;
        if ($user === null) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges'));
            return;
        }

        $progressMap = $this->badgeService->getUserBadgesProgress($userId);
        $categories = $this->badgeService->findAllActiveBadgesGroupedByCategory();

        $result = [];
        foreach ($categories as $category) {
            $badges = [];
            foreach (($category['badges'] ?? []) as $badge) {
                $badgeId = (int) ($badge['id'] ?? 0);
                $progress = $progressMap[$badgeId] ?? null;
                $levelId = $progress !== null ? (int) ($progress['level_id'] ?? 0) : 0;

                $levels = $this->badgeService->findLevels($badgeId);
                $levelsCount = count($levels);

                $levelPicture = null;
                if ($levelId > 0) {
                    foreach ($levels as $level) {
                        if ((int) ($level['level'] ?? 0) === $levelId && ! empty($level['picture'])) {
                            $levelPicture = (string) $level['picture'];
                            break;
                        }
                    }
                }

                $ringStyle = $this->badgeService->getBadgeRingStyle($levelId, $levelsCount);

                $currVal = $progress !== null ? (int) ($progress['curr_val'] ?? 0) : 0;
                $prevVal = $progress !== null ? (int) ($progress['prev_val'] ?? 0) : 0;
                $shortDesc = $this->badgeService->getShortDescription($badgeId, $currVal, $levelId);

                $levelName = '';
                $nextVal = 0;
                $foundCurrent = false;
                foreach ($levels as $level) {
                    if ((int) ($level['level'] ?? 0) === $levelId) {
                        $levelName = (string) ($level['name'] ?? '');
                    }
                    if ($foundCurrent) {
                        $nextVal = (int) ($level['threshold'] ?? 0);
                        break;
                    }
                    if ((int) ($level['level'] ?? 0) === $levelId) {
                        $foundCurrent = true;
                    }
                }
                if ($nextVal === 0) {
                    $nextVal = 999999;
                }

                $levelRoman = $levelId >= 0 ? $this->badgeService->toRoman($levelId + 1) : '';

                $badges[] = [
                    'id' => $badgeId,
                    'name' => (string) ($badge['name'] ?? ''),
                    'picture' => $levelPicture !== null ? $levelPicture : (string) ($badge['picture'] ?? ''),
                    'levelId' => $levelId,
                    'levelsCount' => $levelsCount,
                    'ringColorClass' => $ringStyle['colorClass'],
                    'ringThickness' => $ringStyle['thickness'],
                    'currVal' => $currVal,
                    'nextVal' => $nextVal,
                    'ringPercent' => $this->badgeService->getBadgeProgressPercent($currVal, $prevVal, $nextVal),
                    'levelName' => $levelName,
                    'levelRoman' => $levelRoman,
                    'levelDate' => $progress !== null ? (string) ($progress['level_date'] ?? '') : '',
                    'shortDesc' => $shortDesc ?? '',
                ];
            }

            $result[] = [
                'id' => (int) ($category['id'] ?? 0),
                'name' => (string) ($category['name'] ?? ''),
                'badges' => $badges,
            ];
        }

        $this->view->setVar('title', 'My Badges');
        $this->view->setVar('userId', $userId);
        $this->view->setVar('username', $user->getUserName());
        $this->view->setVar('categories', $result);
        $this->view->setTemplate('badges/indexUserBadge');
        $this->view->buildView();
    }

    public function badge($badgeId, $userId = null): void
    {
        $badgeId = (int) $badgeId;
        $userId = (int) ($userId ?? ($this->loggedUser ? $this->loggedUser->getUserId() : 0));

        $badge = $this->badgeService->findBadgeById($badgeId);
        if ($badge === null) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.MyBadges'));
            return;
        }

        $user = $userId > 0 ? User::fromUserIdFactory($userId) : null;
        if ($user === null) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges'));
            return;
        }

        $isOcteam = $this->loggedUser !== null && $this->loggedUser->hasOcTeamRole();
        if (empty($badge['active']) && ! $isOcteam) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.MyBadges'));
            return;
        }

        $progress = $this->badgeService->getUserBadgeProgress($badgeId, $userId);
        $levelId = $progress !== null ? (int) ($progress['level_id'] ?? 0) : 0;
        $prevVal = $progress !== null ? (int) ($progress['prev_val'] ?? 0) : 0;

        $levels = $this->badgeService->findLevels($badgeId);
        $levelsCount = count($levels);
        $colorClass = 'badge-level-1';

        $nextVal = 0;
        $foundCurrent = false;
        foreach ($levels as $level) {
            if ($foundCurrent) {
                $nextVal = (int) ($level['threshold'] ?? 0);
                break;
            }
            if ((int) ($level['level'] ?? 0) === $levelId) {
                $foundCurrent = true;
            }
        }
        if ($nextVal === 0) {
            $nextVal = 999999;
        }
        $currVal = $progress !== null ? (int) ($progress['curr_val'] ?? 0) : 0;
        $ringPercent = $this->badgeService->getBadgeProgressPercent($currVal, $prevVal, $nextVal);
        if ($levelId > 0) {
            $idx = $levelsCount > 0 ? (int) round(($levelId + 1) * 10 / $levelsCount) : 1;
            if ($idx > 10) { $idx = 10; }
            if ($idx <= 0) { $idx = 1; }
            $colorClass = 'badge-level-' . $idx;
        }

        $ringStyle = $this->badgeService->getBadgeRingStyle($levelId, $levelsCount);

        $levelPicture = null;
        foreach ($levels as $level) {
            if ((int) ($level['level'] ?? 0) === $levelId && ! empty($level['picture'])) {
                $levelPicture = (string) $level['picture'];
                break;
            }
        }
        if ($levelPicture === null && ! empty($badge['picture'])) {
            $levelPicture = (string) $badge['picture'];
        }

        $requestedLang = isset($_GET['desclang']) ? (string) $_GET['desclang'] : null;
        $badgeDescription = $this->badgeService->getDescription($badgeId, $requestedLang);

        $this->view->loadJQuery();
        $this->view->loadJQueryUI();

        $this->view->setVar('title', 'My Badges - badge');
        $this->view->setVar('badge', $badge);
        $this->view->setVar('badgeId', $badgeId);
        $this->view->setVar('userId', $userId);
        $this->view->setVar('username', $user->getUserName());
        $this->view->setVar('badgePicture', $levelPicture);
        $this->view->setVar('colorClass', $colorClass);
        $this->view->setVar('ringColorClass', $ringStyle['colorClass']);
        $this->view->setVar('ringThickness', $ringStyle['thickness']);
        $this->view->setVar('ringPercent', $ringPercent);
        $this->view->setVar('progress', $progress);
        $this->view->setVar('shortDesc', $this->badgeService->getShortDescription($badgeId, $progress !== null ? (int) ($progress['curr_val'] ?? 0) : 0, $levelId));

        $popupBadgeId = isset($_POST['badgesPopupFor']) ? (int) $_POST['badgesPopupFor'] : 0;
        $this->view->setVar('showBadgePopup', $popupBadgeId === $badgeId);
        $this->view->setVar('activeCount', $this->badgeService->getBadgeStats($badgeId, $userId)['activeCount']);
        $this->view->setVar('badgeImages', $this->badgeService->findBadgeImages($badgeId));
        $this->view->setVar('levels', $levels);
        $this->view->setVar('recommendedCaches', $this->badgeService->findRecommendedCaches($badgeId));
        $this->view->setVar('titledCaches', $this->badgeService->findTitledCaches($badgeId));
        $this->view->setVar('badgeDescription', $badgeDescription);
        $this->view->setVar('badgeOwners', $this->badgeService->getBadgeOwners($badgeId));
        $this->view->setTemplate('badges/showUserBadge');
        $this->view->buildView();
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index', 'badge'], true);
    }
}
