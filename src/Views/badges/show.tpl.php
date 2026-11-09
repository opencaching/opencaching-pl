<?php

use src\Utils\Uri\SimpleRouter;

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
        <?php if (!empty($view->badge['picture'])) { ?>
            <img class="badge-v2-ring-image" src="/<?= htmlspecialchars($view->badge['picture'], ENT_QUOTES, 'UTF-8'); ?>" alt="" />
        <?php } ?>
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
    <div class="badge-v2-frame-light">
        <?= tr('badge_recommended_caches'); ?>
    </div>
    <div class="badge-v2-frame-light">
        <?= tr('badge_cache_of_week'); ?>
    </div>
</div>

<div class="badge-v2-frame mb-20">
    <div class="badge-v2-frame-light">
        <?= tr('badge_levels_table'); ?>
    </div>
    <div class="badge-v2-frame-light">
        <?= tr('badge_threshold_gainers'); ?>
    </div>
</div>

<div class="badge-v2-frame mb-20 text-center">
    <button type="button"><?= tr('merit_badge_list'); ?></button>
    <button type="button"><?= tr('badge_export_gpx'); ?></button>
    <button type="button"><?= tr('badge_check_my_level'); ?></button>
    <button type="button"><?= tr('merit_badge_map'); ?></button>
</div>

<div class="badge-v2-frame mb-20">
    <?= tr('badge_authors'); ?>
</div>
