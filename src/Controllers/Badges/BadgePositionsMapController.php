<?php

declare(strict_types=1);

namespace src\Controllers\Badges;

use src\Controllers\Core\ViewBaseController;
use src\Controllers\MainMapController;
use src\Enums\CacheStatusEnum;
use src\Models\OcConfig\OcConfig;
use src\Services\Badges\BadgePositionsService;
use src\Utils\Database\OcDb;
use src\Utils\Uri\SimpleRouter;

/**
 * Map of badge positions.
 */
class BadgePositionsMapController extends ViewBaseController
{
    private readonly BadgePositionsService $service;

    public function __construct()
    {
        parent::__construct();
        $this->redirectNotLoggedUsers();
        $this->service = new BadgePositionsService();
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return in_array($actionName, ['index'], true);
    }

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

        if (empty($badge['show_positions_map'])) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges', 'show', $badgeId));
            return;
        }

        $belongingQuery = trim((string) ($badge['belonging_query'] ?? ''));
        if ($belongingQuery === '' || strtolower($belongingQuery) === 'all') {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges', 'show', $badgeId));
            return;
        }

        $blockedStatus = CacheStatusEnum::BLOCKED_BY_OC_TEAM->value;
        $db = OcDb::instance();

        $borderQuery = 'SELECT MAX(c.longitude) AS maxlongitude, MAX(c.latitude) AS maxlatitude,
                               MIN(c.longitude) AS minlongitude, MIN(c.latitude) AS minlatitude
                        FROM (' . $belongingQuery . ') AS bp
                        JOIN caches AS c ON c.cache_id = bp.badge_position_id
                        WHERE c.status <> ' . $blockedStatus;

        $stmt = $db->simpleQuery($borderQuery);
        $r = $db->dbResultFetchOneRowOnly($stmt);

        if (!is_array($r) || $r['minlatitude'] === null) {
            SimpleRouter::redirect(SimpleRouter::getLink('Badges.Badges', 'show', $badgeId));
            return;
        }

        $minlat = $r['minlatitude'];
        $minlon = $r['minlongitude'];
        $maxlat = $r['maxlatitude'];
        $maxlon = $r['maxlongitude'];

        $cacheQuery = 'SELECT c.cache_id
                       FROM (' . $belongingQuery . ') AS bp
                       JOIN caches AS c ON c.cache_id = bp.badge_position_id
                       WHERE c.status <> ' . $blockedStatus;

        $stmt = $db->simpleQuery($cacheQuery);

        $hash = uniqid();
        $dir = OcConfig::getDynFilesPath() . '
        /';
        // Ensure the searchdata directory exists (not present in legacy code; added for safety).
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = $dir . $hash;
        $f = fopen($path, 'w');
        if ($f) {
            while ($row = $db->dbResultFetch($stmt)) {
                fprintf($f, "%s\n", $row['cache_id']);
            }
            fclose($f);
        }

        SimpleRouter::redirect(
            SimpleRouter::getLink(MainMapController::class, 'fullscreen')
            . '?searchdata=' . urlencode($hash)
            . '&bbox=' . urlencode($minlon . '|' . $minlat . '|' . $maxlon . '|' . $maxlat)
        );
    }

}