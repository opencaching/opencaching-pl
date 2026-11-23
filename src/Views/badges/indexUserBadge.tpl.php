<?php /** @var \src\Utils\View\View $view */ ?>

<?php use src\Utils\Uri\SimpleRouter; ?>

<div class="content2-pagetitle">
    <img src="/images/blue/merit_badge.png" class="icon32 align-middle" alt="" title="" />&nbsp;
    <?= tr('my_badges'); ?><?= !empty($view->username) ? ' - ' . htmlspecialchars($view->username, ENT_QUOTES, 'UTF-8') : ''; ?>
</div>
<div class="content2-container">
    <?php foreach (($view->categories ?? []) as $category) { ?>
        <div class="badge-v2-table mb-20">
        <div class="badge-v2-category-header mb-10">
            <h3 class="m-0 text-left"><?= htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
        </div>
        <div class="badge-v2-list flex">
            <?php foreach (($category['badges'] ?? []) as $badge) { ?>
                    <div class="badge-v2-item mb-10">
                    <div class="badge-v2-ring-wrap">
                        <svg class="ring_v2 <?= htmlspecialchars($badge['ringThickness'] ?? 'ring_v2--thick', ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($badge['ringColorClass'] ?? 'badge-level-1', ENT_QUOTES, 'UTF-8'); ?>" viewBox="0 0 140 140">
                            <circle class="ring_v2__progress" style="--percent: 100" cx="70" cy="70" r="67.5" />
                        </svg>
                        <?php if (!empty($badge['picture'])) { ?>
                            <a href="<?= htmlspecialchars(SimpleRouter::getLink('Badges.MyBadges', 'badge', [(int) ($badge['id'] ?? 0), (int) ($view->userId ?? 0)]), ENT_QUOTES, 'UTF-8'); ?>">
                                <img class="badge-v2-ring-image" src="<?= htmlspecialchars($badge['picture'], ENT_QUOTES, 'UTF-8'); ?>" alt="" />
                            </a>
                        <?php } ?>
                    </div>
                    <div class="badge-v2-name">
                    <a href="<?= htmlspecialchars(SimpleRouter::getLink('Badges.MyBadges', 'badge', [(int) ($badge['id'] ?? 0), (int) ($view->userId ?? 0)]), ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($badge['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </div>

                </div>
            <?php } ?>
        </div>
        </div>
    <?php } ?>
</div>
