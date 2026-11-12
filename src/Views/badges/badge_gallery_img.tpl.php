<?php
$badgeImages = $view->badgeImages ?? [];
$n = count($badgeImages);
?>
<?php if ($n >= 2) { ?>
<div class="badge-v2-ring-gallery n-<?= (int) $n; ?>">
    <?php foreach ($badgeImages as $img) { ?>
        <img class="badge-v2-ring-image" src="/<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt="" />
    <?php } ?>
</div>
<?php } ?>
