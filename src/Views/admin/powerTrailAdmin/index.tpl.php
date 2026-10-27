<?php

use src\Utils\Uri\SimpleRouter;

?>
<div class="content2-container">
    <div class="content2-pagetitle">
        <?= htmlspecialchars(tr('menu_octeam_power_trail'), ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <form method="get" action="<?= SimpleRouter::getLink('Admin.PowerTrailAdmin'); ?>"
          class="w-100 d-flex flex-column mb-15">
        <div class="mb-10 d-flex align-items-center">
            <label for="powerTrailId" class="w-25 flex-shrink-0 mr-10">ID</label>
            <input id="powerTrailId" name="id" type="number"
                   value="<?= $view->filters['id'] ? (int) $view->filters['id'] : ''; ?>"
                   class="form-control flex-grow-1">
        </div>

        <div class="mb-10 d-flex align-items-center">
            <label for="powerTrailName" class="w-25 flex-shrink-0 mr-10"><?= tr('name_label'); ?></label>
            <input id="powerTrailName" name="name" type="search"
                   value="<?= htmlspecialchars($view->filters['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-control flex-grow-1">
        </div>

        <div class="mb-10 d-flex align-items-center">
            <label for="powerTrailSort" class="w-25 flex-shrink-0 mr-10"><?= tr('sort_by'); ?></label>
            <select id="powerTrailSort" name="sort" class="form-control flex-grow-1">
                <?php foreach ($view->sortOptions as $sortValue => $sortLabel) { ?>
                    <option value="<?= (int) $sortValue; ?>" <?= $view->filters['sort'] === $sortValue ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($sortLabel, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mt-10 d-flex align-items-center flex-wrap">
            <button type="submit" class="blue-button-small mr-10"><?= tr('search'); ?></button>

            <a class="orange-button-small mr-10" href="<?= SimpleRouter::getLink('Admin.PowerTrailAdmin'); ?>">
                <?= tr('clear_search'); ?>
            </a>

            <span class="text-muted">
                <?= tr('powertrail_admin_results_summary', [(int) $view->totalCount, (int) $view->limit]); ?>
            </span>
        </div>
    </form>

    <?php if (empty($view->powerTrails)) { ?>
        <p><?= tr('powertrail_admin_no_geopaths'); ?></p>
    <?php } else { ?>
        <table class="table table-striped full-width">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?= tr('pt008'); ?></th>
                    <th><?= tr('pt023'); ?></th>
                    <th><?= tr('status_label'); ?></th>
                    <th><?= tr('pt020'); ?></th>
                    <th class="text-center"><?= tr('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($view->powerTrails as $powerTrail) {
                    $type = $view->types[(int) $powerTrail['type']] ?? null;
                    $statusKey = $view->statuses[(int) $powerTrail['status']] ?? null;
                    ?>
                    <tr>
                        <td class="align-middle">
                            <a href="/powerTrail.php?ptAction=showSerie&amp;ptrail=<?= (int) $powerTrail['id']; ?>">
                                <?= (int) $powerTrail['id']; ?>
                            </a>
                        </td>
                        <td class="align-middle">
                            <a href="/powerTrail.php?ptAction=showSerie&amp;ptrail=<?= (int) $powerTrail['id']; ?>">
                                <?= htmlspecialchars($powerTrail['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td class="align-middle"><?= $type ? tr($type['translationKey']) : (int) $powerTrail['type']; ?></td>
                        <td class="align-middle"><?= $statusKey ? tr($statusKey) : (int) $powerTrail['status']; ?></td>
                        <td class="align-middle"><?= (int) $powerTrail['cacheCount']; ?></td>
                        <td class="text-center align-middle">
                            <a href="<?= SimpleRouter::getLink(
                                'Admin.PowerTrailAdmin',
                                'editPowerTrail',
                                (int) $powerTrail['id']
                            ); ?>" class="blue-button-small">
                                <?= tr('edit'); ?>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php $view->callChunk('pagination', $view->paginationModel); ?>
    <?php } ?>
</div>
