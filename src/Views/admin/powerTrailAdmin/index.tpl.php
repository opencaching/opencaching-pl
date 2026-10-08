<?php

use src\Utils\Uri\SimpleRouter;

?>
<div class="content2-container">
    <div class="content2-pagetitle">
        <?= htmlspecialchars(tr('menu_octeam_power_trail'), ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <form method="get" action="<?= SimpleRouter::getLink('Admin.PowerTrailAdmin'); ?>" class="form-inline w-100">
        <label for="powerTrailId">ID</label>
        <input id="powerTrailId" name="id" type="number"
               value="<?= $view->filters['id'] ? (int) $view->filters['id'] : ''; ?>">

        <label for="powerTrailName"><?= tr('name_label'); ?></label>
        <input id="powerTrailName" name="name" type="search"
               value="<?= htmlspecialchars($view->filters['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <label for="powerTrailSort"><?= tr('sort_by'); ?></label>
        <select id="powerTrailSort" name="sort">
            <?php foreach ($view->sortOptions as $sortValue => $sortLabel) { ?>
                <option value="<?= (int) $sortValue; ?>" <?= $view->filters['sort'] === $sortValue ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($sortLabel, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit" class="btn btn-primary btn-sm"><?= tr('search'); ?></button>
        <span><?= tr('powertrail_admin_results_summary', [(int) $view->totalCount, (int) $view->limit]); ?></span>
    </form>

    <?php if (empty($view->powerTrails)) { ?>
        <p><?= tr('powertrail_admin_no_geopaths'); ?></p>
    <?php } else { ?>
        <table class="table w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?= tr('pt008'); ?></th>
                    <th><?= tr('pt023'); ?></th>
                    <th><?= tr('status_label'); ?></th>
                    <th><?= tr('pt020'); ?></th>
                    <th><?= tr('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($view->powerTrails as $powerTrail) {
                    $type = $view->types[(int) $powerTrail['type']] ?? null;
                    $statusKey = $view->statuses[(int) $powerTrail['status']] ?? null;
                    ?>
                    <tr>
                        <td>
                            <a href="/powerTrail.php?ptAction=showSerie&amp;ptrail=<?= (int) $powerTrail['id']; ?>">
                                <?= (int) $powerTrail['id']; ?>
                            </a>
                        </td>
                        <td>
                            <a href="/powerTrail.php?ptAction=showSerie&amp;ptrail=<?= (int) $powerTrail['id']; ?>">
                                <?= htmlspecialchars($powerTrail['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td><?= $type ? tr($type['translationKey']) : (int) $powerTrail['type']; ?></td>
                        <td><?= $statusKey ? tr($statusKey) : (int) $powerTrail['status']; ?></td>
                        <td><?= (int) $powerTrail['cacheCount']; ?></td>
                        <td>
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
