<?php

use src\Utils\Uri\SimpleRouter;

/** @var \src\Utils\View\View $view */

?>

<div class="content2-pagetitle">
    <img src="/images/blue/merit_badge.png" class="icon32" alt="" title="" align="middle" />&nbsp;
    <a href="<?= htmlspecialchars(SimpleRouter::getLink('Badges.Badges'), ENT_QUOTES, 'UTF-8'); ?>"><?= tr('merit_badges'); ?></a>
    /
    <?= htmlspecialchars($view->badge['name'] ?? 'show', ENT_QUOTES, 'UTF-8'); ?>
</div>

<div class="badge-v2-top">
    <div class="badge-v2-ring">
        <svg class="ring_v2 ring_v2--yellow ring_v2--thick" viewBox="0 0 140 140">
            <circle class="ring_v2__progress" style="--percent: 100" cx="70" cy="70" r="67.5" />
        </svg>
        <?php
        $badgeImages = $view->badgeImages ?? [];
        $view->badgeImages = $badgeImages;
        if (count($badgeImages) <= 1) {
            echo $view->component('badges/badge_single_img', ['badgeImages' => $badgeImages]);
        } else {
            echo $view->component('badges/badge_gallery_img', ['badgeImages' => $badgeImages]);
        }
        ?>
    </div>
    <div class="badge-v2-title">
        <?= htmlspecialchars($view->badge['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
    </div>
</div>

<div class="badge-v2-frame mb-20">
    <?php if (!empty($view->badge['long_name'])) { ?>
        <h3 class="mb-10 text-center"><?= htmlspecialchars($view->badge['long_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
    <?php } ?>
    <?php if (!empty($view->badgeDescription)) { ?>
        <div><?= $view->badgeDescription; ?></div>
    <?php } ?>
</div>

<div class="badge-v2-frame mb-20">
    <?php if (!empty($view->activeCount)) { ?>
    <div class="badge-v2-frame-full">
        <p class="m-0"><?= tr('badge_active_caches_count'); ?> <?= (int) $view->activeCount; ?></p>
    </div>
    <?php } ?>
    <?php if (!empty($view->recommendedCaches)) { ?>
    <div class="badge-v2-frame-light">
        <?= tr('badge_recommended_caches'); ?>
        <table class="badge-v2-table">
            <thead>
                <tr>
                    <th>LP</th>
                    <th>Nazwa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_values($view->recommendedCaches) as $i => $cache) { ?>
                <tr>
                    <td><?= $i + 1; ?></td>
                    <td>
                        <a href="/viewcache.php?cacheid=<?= (int) ($cache['cache_id'] ?? 0); ?>">
                            <?= htmlspecialchars($cache['cache_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
    <?php if (!empty($view->titledCaches)) { ?>
    <div class="badge-v2-frame-light">
        <?= tr('badge_cache_of_week'); ?>
        <table class="badge-v2-table">
            <thead>
                <tr>
                    <th>LP</th>
                    <th>Nazwa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_values($view->titledCaches) as $i => $cache) { ?>
                <tr>
                    <td><?= $i + 1; ?></td>
                    <td>
                        <a href="/viewcache.php?cacheid=<?= (int) ($cache['cache_id'] ?? 0); ?>">
                            <?= htmlspecialchars($cache['cache_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</div>

<div class="badge-v2-frame mb-20">
    <div class="badge-v2-levels-gainers">
        <div class="badge-v2-levels">
            <table class="badge-v2-table badge-v2-table--counter w-100">
                <thead>
                    <tr>
                        <th></th>
                        <th><?= tr('merit_badge_level'); ?></th>
                        <th><?= tr('merit_badge_level_name'); ?></th>
                        <th><?= tr('merit_badge_number_threshold'); ?></th>
                        <th><?= tr('merit_badge_gain_count'); ?></th>
                        <th><?= tr('merit_badge_gain_last_date'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $levels = $view->levels ?? [];
                    $levelsCount = count($levels);
                    foreach ($levels as $level) {
                        $lvl = (int) ($level['level'] ?? 0);
                        $idx = $levelsCount > 0 ? (int) round(($lvl + 1) * 10 / $levelsCount) : 1;
                        if ($idx > 10) { $idx = 10; }
                        if ($idx <= 0) { $idx = 1; }
                        $colorClass = 'badge-level-' . $idx;
                        $gain = $level['gain_counter'] ?? 0;
                        $lastDate = !empty($level['gain_last_date']) ? date('d-m-Y', strtotime((string) $level['gain_last_date'])) : '';
                    ?>
                    <tr>
                        <td></td>
                        <td class="<?= $colorClass; ?>"></td>
                        <td class="<?= $colorClass; ?>"><?= htmlspecialchars($level['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= (int) ($level['threshold'] ?? 0); ?></td>
                        <td><?= (int) $gain; ?></td>
                        <td><?= htmlspecialchars($lastDate, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="badge-v2-gainers">
            <?= tr('badge_threshold_gainers'); ?>
        </div>
    </div>
</div>

<div class="badge-v2-frame mb-20 text-center">
    <button type="button">Lista pozycji</button>
    <button type="button">Eksport do GPX</button>
    <button type="button">Sprawdź mój poziom sprawności</button>
    <button type="button">Pokaż mapę</button>
</div>

<div class="badge-v2-frame mb-20">
    <?= tr('badge_authors'); ?>
</div>
