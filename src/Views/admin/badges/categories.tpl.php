<?php
use src\Utils\Uri\SimpleRouter;
?>
<div class="content2-container">
    <div class="content2-pagetitle">
        <?= tr('mnu_badgeCategories'); ?>
    </div>

    <form method="get" action="<?= SimpleRouter::getLink('Admin.BadgeCategoriesAdmin'); ?>" class="w-100 d-flex flex-column mb-15">
        <div class="mb-10 d-flex align-items-center">
            <label for="cat_id" class="w-25 flex-shrink-0 mr-10">ID</label>
            <input id="cat_id" type="text" name="id" value="<?= htmlspecialchars((string) ($view->searchId ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="form-control flex-grow-1" />
        </div>
        <div class="mb-10 d-flex align-items-center">
            <label for="cat_name" class="w-25 flex-shrink-0 mr-10"><?= tr('name_label'); ?></label>
            <input id="cat_name" type="text" name="name" value="<?= htmlspecialchars($view->searchName ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control flex-grow-1" />
        </div>
        <div class="mb-10 d-flex align-items-center">
            <label for="cat_sort" class="w-25 flex-shrink-0 mr-10"><?= tr('sort_by'); ?></label>
            <select id="cat_sort" name="sort" class="form-control flex-grow-1">
                <?php foreach (($view->sortOptions ?? []) as $value => $label) { ?>
                    <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?= ($view->sort ?? '') === $value ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="mt-10 d-flex align-items-center flex-wrap">
            <button type="submit" class="blue-button-small mr-10"><?= tr('search'); ?></button>

            <a class="orange-button-small mr-10" href="<?= SimpleRouter::getLink('Admin.BadgeCategoriesAdmin'); ?>">
                <?= tr('clear_search'); ?>
            </a>

            <span class="text-muted">
                <?= tr('role_manager_results_summary', [(int) ($view->totalCount ?? 0), (int) ($view->limit ?? 0)]); ?>
            </span>
        </div>
    </form>

    <table class="table table-striped full-width">
        <thead>
            <tr>
                <th>ID</th>
                <th><?= tr('name_label'); ?></th>
                <th><?= tr('sequence'); ?></th>
                <th><?= tr('active'); ?></th>
                <th><?= tr('options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($view->categories ?? []) as $cat) { ?>
            <tr>
                <td><?= (int) ($cat['id'] ?? 0); ?></td>
                <td><?= htmlspecialchars($cat['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= (int) ($cat['sequence'] ?? 0); ?></td>
                <td><?= !empty($cat['active']) ? tr('yes') : tr('no'); ?></td>
                <td>
                    <a href="<?= SimpleRouter::getLink('Admin.BadgeCategoriesAdmin', 'edit', (int) ($cat['id'] ?? 0)); ?>" class="blue-button-small">
                        <?= tr('edit'); ?>
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php if (!empty($view->paginationModel)) { ?>
        <?php $view->callChunk('pagination', $view->paginationModel); ?>
    <?php } ?>
</div>
