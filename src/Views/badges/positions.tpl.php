<?php

use src\Utils\Uri\SimpleRouter;
use src\Models\GeoCache\GeoCacheCommons;

/** @var \src\Utils\View\View $view */

$badge = $view->badge ?? [];
$badgeId = (int) ($view->badgeId ?? 0);
$userId = $view->userId ?? null;
$positions = $view->positions ?? [];
$totalCount = (int) ($view->totalCount ?? 0);
$limit = (int) ($view->limit ?? 50);
$sort = (int) ($view->sort ?? 0);
$foundFilter = (string) ($view->foundFilter ?? 'all');
$foundOptions = $view->foundOptions ?? [];
$sortOptions = $view->sortOptions ?? [];
$badgeImages = $view->badgeImages ?? [];
$paginationModel = $view->paginationModel ?? null;

?>

<div class="content2-pagetitle">
    <img src="/images/blue/merit_badge.png" class="icon32 align-middle" alt="" title="" />&nbsp;
    <a href="<?= htmlspecialchars(SimpleRouter::getLink('Badges.Badges'), ENT_QUOTES, 'UTF-8'); ?>"><?= tr('merit_badges'); ?></a>
    /
    <a href="<?= htmlspecialchars(SimpleRouter::getLink('Badges.Badges', 'show', $badgeId), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($badge['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
    /
    <?= tr('badge_positions_title'); ?>
</div>

<div class="badge-v2-top">
    <div class="badge-v2-ring">
        <svg class="ring_v2 ring_v2--yellow ring_v2--thick" viewBox="0 0 140 140">
            <circle class="ring_v2__progress" style="--percent: 100" cx="70" cy="70" r="67.5" />
        </svg>
        <?php
        if (count($badgeImages) <= 1) {
            echo $view->component('badges/badge_single_img', ['badgeImages' => $badgeImages]);
        } else {
            echo $view->component('badges/badge_gallery_img', ['badgeImages' => $badgeImages]);
        }
        ?>
    </div>
    <div class="badge-v2-title">
        <?= htmlspecialchars($badge['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
    </div>
</div>

<form method="get" action="<?= SimpleRouter::getLink('Badges.BadgePositions', null, $badgeId); ?>/"
      class="w-100 d-flex flex-column mb-15">
    <?php if ($userId !== null) { ?>
        <input type="hidden" name="user_id" value="<?= (int) $userId; ?>">
    <?php } ?>

    <div class="mb-10 d-flex align-items-center">
        <label for="badgePositionsSort" class="w-25 flex-shrink-0 mr-10"><?= tr('sort_by'); ?></label>
        <select id="badgePositionsSort" name="sort" class="form-control flex-grow-1">
            <?php foreach ($sortOptions as $sortValue => $sortLabel) { ?>
                <option value="<?= (int) $sortValue; ?>" <?= $sort === $sortValue ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($sortLabel, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-10 d-flex align-items-center">
        <label for="badgePositionsFound" class="w-25 flex-shrink-0 mr-10"><?= tr('Status'); ?></label>
        <select id="badgePositionsFound" name="found" class="form-control flex-grow-1" <?= $userId === null ? 'disabled' : ''; ?>>
            <?php foreach ($foundOptions as $foundValue => $foundLabel) { ?>
                <option value="<?= htmlspecialchars($foundValue, ENT_QUOTES, 'UTF-8'); ?>" <?= ($foundFilter ?? 'all') === $foundValue ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($foundLabel, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mt-10 d-flex align-items-center flex-wrap">
        <button type="submit" class="blue-button-small mr-10"><?= tr('search'); ?></button>

        <a class="orange-button-small mr-10" href="<?= SimpleRouter::getLink('Badges.BadgePositions', null, $badgeId); ?>/<?= $userId !== null ? '?user_id=' . (int) $userId : ''; ?>">
            <?= tr('clear_search'); ?>
        </a>

        <span class="text-muted">
            <?= tr('powertrail_admin_results_summary', [$totalCount, $limit]); ?>
        </span>
    </div>
</form>

<?php if (empty($positions)) { ?>
    <p><?= tr('badge_positions_none'); ?></p>
<?php } else { ?>
    <table class="table table-striped full-width">
        <thead>
            <tr>
                <th></th>
                <th><?= tr('cache_name'); ?></th>
                <th><?= tr('owner'); ?></th>
                <th><?= tr('date_published'); ?></th>
                <?php if ($userId !== null) { ?>
                    <th><?= tr('Status'); ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($positions as $position) { ?>
                <tr>
                    <td class="align-middle">
                        <img class="icon16" src="<?= htmlspecialchars(GeoCacheCommons::CacheIconByType((int) $position['type'], (int) $position['status']), ENT_QUOTES, 'UTF-8'); ?>" alt="">
                    </td>
                    <td class="align-middle">
                        <a href="/viewcache.php?cacheid=<?= (int) $position['cache_id']; ?>">
                            <?= htmlspecialchars($position['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </td>
                    <td class="align-middle">
                        <a href="/viewprofile.php?userid=<?= (int) $position['owner_id']; ?>">
                            <?= htmlspecialchars($position['owner_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </td>
                    <td class="align-middle">
                        <?= htmlspecialchars($position['date_published'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </td>
                    <?php if ($userId !== null) { ?>
                        <td class="align-middle">
                            <?= ((int) $position['found_by_user'] === 1) ? tr('badge_positions_found') : tr('badge_positions_not_found'); ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php if ($totalCount > 5 && $paginationModel !== null) { ?>
        <?php $view->callChunk('pagination', $paginationModel); ?>
    <?php } ?>
<?php } ?>