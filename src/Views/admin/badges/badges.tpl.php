<?php
use src\Utils\Uri\SimpleRouter;
?>
<div class="content2-container">
    <div class="content2-pagetitle">
        <?= tr('mnu_badgesAdmin'); ?>
    </div>

    <form method="get" action="<?= SimpleRouter::getLink('Admin.BadgesAdmin'); ?>" class="w-100 d-flex flex-column mb-15">
        <div class="mb-10 d-flex align-items-center">
            <label class="w-25 flex-shrink-0 mr-10">Szukaj</label>
            <input type="text" name="search" value="<?= htmlspecialchars($view->search ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control flex-grow-1" />
        </div>
        <div class="mb-10 d-flex align-items-center">
            <label class="w-25 flex-shrink-0 mr-10">Sortowanie</label>
            <select name="sort" class="form-control flex-grow-1">
                <?php foreach (($view->sortOptions ?? []) as $value => $label) { ?>
                    <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?= ($view->sort ?? '') === $value ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="mt-10 d-flex align-items-center flex-wrap">
            <button type="submit" class="blue-button-small mr-10">Szukaj</button>

            <a class="orange-button-small mr-10" href="<?= SimpleRouter::getLink('Admin.BadgesAdmin'); ?>">
                Wyczyść wyszukiwanie
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
                <th>Nazwa</th>
                <th>Kategoria</th>
                <th>Kolejność</th>
                <th>Aktywna</th>
                <th>Opcje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($view->badges ?? []) as $badge) { ?>
            <tr>
                <td><?= (int) ($badge['id'] ?? 0); ?></td>
                <td><?= htmlspecialchars($badge['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= !empty($badge['category_name']) ? htmlspecialchars($badge['category_name'], ENT_QUOTES, 'UTF-8') : 'BRAK KATEGORII'; ?></td>
                <td><?= (int) ($badge['sequence'] ?? 0); ?></td>
                <td><?= !empty($badge['active']) ? 'Tak' : 'Nie'; ?></td>
                <td>
                    <?php if (!empty($badge['can_edit'])) { ?>
                    <a href="<?= SimpleRouter::getLink('Admin.BadgesAdmin', 'edit', (int) ($badge['id'] ?? 0)); ?>" class="blue-button-small">
                        <?= tr('edit'); ?>
                    </a>
                    <?php } else { ?>
                    <button type="button" class="blue-button-small" disabled><?= tr('edit'); ?></button>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php if (!empty($view->paginationModel)) { ?>
        <?php $view->callChunk('pagination', $view->paginationModel); ?>
    <?php } ?>
</div>
