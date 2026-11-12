<?php
$badgeImages = $view->badgeImages ?? [];
?>
<?php if (count($badgeImages) === 1) { ?>
    <img class="badge-v2-ring-image" src="/<?= htmlspecialchars($badgeImages[0], ENT_QUOTES, 'UTF-8'); ?>" alt="" />
<?php } ?>
