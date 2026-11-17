<div class="content2-pagetitle"><?= htmlspecialchars($view->title ?? 'MyBadges', ENT_QUOTES, 'UTF-8'); ?></div>
<div class="content2-container">
    <p>badgeId: <?= htmlspecialchars((string) ($view->badgeId ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
    <p>userId: <?= htmlspecialchars((string) ($view->userId ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
</div>
