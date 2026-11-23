<?php
use src\Utils\Uri\SimpleRouter;
$cat = $view->category ?? null;
?>
<div class="content2-pagetitle">
    <img src="/images/blue/merit_badge.png" class="icon32 align-middle" alt="" />&nbsp;
    <?= tr('badge_category_edit'); ?>
</div>
<div class="content2-container">
    <?php if (!empty($view->message)) { ?>
        <div class="callout callout-success mb-15"><?= htmlspecialchars($view->message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php } ?>
    <?php if (!empty($view->error)) { ?>
        <div class="callout callout-danger mb-15"><?= htmlspecialchars($view->error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php } ?>

    <form method="post" action="<?= SimpleRouter::getLink('Admin.BadgeCategoriesAdmin', 'save', (int) ($cat['id'] ?? 0)); ?>" class="w-100 d-flex flex-column mb-15">
        <input type="hidden" name="id" value="<?= (int) ($cat['id'] ?? 0); ?>" />

        <div class="mb-10 d-flex align-items-center">
            <label for="cat_name" class="w-25 flex-shrink-0 mr-10"><?= tr('name_label'); ?> (max 30)</label>
            <input id="cat_name" type="text" name="name" maxlength="30" value="<?= htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control flex-grow-1" />
        </div>

        <div class="mb-10 d-flex align-items-center">
            <label for="cat_sequence" class="w-25 flex-shrink-0 mr-10"><?= tr('sequence'); ?> (0-1000)</label>
            <input id="cat_sequence" type="number" name="sequence" min="0" max="1000" value="<?= (int) ($cat['sequence'] ?? 0); ?>" class="form-control flex-grow-1" />
        </div>

        <div class="mb-10 d-flex align-items-center">
            <label for="cat_active" class="w-25 flex-shrink-0 mr-10"><?= tr('active'); ?></label>
            <input id="cat_active" type="checkbox" name="active" <?= !empty($cat['active']) ? 'checked' : ''; ?> />
        </div>

        <div class="mb-10 d-flex align-items-center">
            <label for="cat_sort_desc" class="w-25 flex-shrink-0 mr-10"><?= tr('sort_descending'); ?></label>
            <input id="cat_sort_desc" type="checkbox" name="sort_desc" <?= !empty($cat['sort_desc']) ? 'checked' : ''; ?> />
        </div>

        <div class="mt-10 d-flex align-items-center flex-wrap">
            <button type="submit" class="blue-button-small mr-10"><?= tr('save'); ?></button>
            <a class="orange-button-small mr-10" href="<?= SimpleRouter::getLink('Admin.BadgeCategoriesAdmin'); ?>"><?= tr('back'); ?></a>
        </div>
    </form>
</div>
