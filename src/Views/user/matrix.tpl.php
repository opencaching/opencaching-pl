<div class="content2-container">
    <div class="content2-pagetitle">
        <img src="/images/blue/stat1.png" class="icon32" alt="<?= tr('matrix_title'); ?>" title="<?= tr('matrix_title'); ?>">
        &nbsp;<?= tr('statistics_users'); ?> <?= htmlspecialchars($view->matrixUsername, ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <div class="nav4 clearfix mb-20">
        <ul id="statmenu" class="clearfix">
            <li class="group">
                <a style="background-image: url(/images/actions/stat-18.png);background-repeat:no-repeat;"
                   href="/viewprofile.php?userid=<?= (int) $view->matrixUserId; ?>">
                    <?= tr('general_stat'); ?>
                </a>
            </li>
            <li class="group">
                <a style="background-image: url(/images/actions/stat-18.png);background-repeat:no-repeat;"
                   href="/ustatsg2.php?userid=<?= (int) $view->matrixUserId; ?>">
                    <?= tr('graph_find'); ?>
                </a>
            </li>
            <li class="group">
                <a style="background-image: url(/images/actions/stat-18.png);background-repeat:no-repeat;"
                   href="/ustatsg1.php?userid=<?= (int) $view->matrixUserId; ?>">
                    <?= tr('graph_created'); ?>
                </a>
            </li>
        </ul>
    </div>

    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">&nbsp;<img src="/images/blue/cache.png" class="icon32" alt="Caches created" title="Caches created">&nbsp;&nbsp;&nbsp;<?= tr('matrix_difficulty_terrain_title'); ?></p>
    </div>

    <div class="matrix-table-wrapper mb-10">
        <table class="matrix-table">
            <thead>
                <tr>
                    <th><?= tr('matrix_difficulty_terrain'); ?></th>
                    <?php foreach ($view->matrix['columnLabels'] as $label) { ?>
                        <th><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></th>
                    <?php } ?>
                    <th><?= tr('pt085'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($view->matrix['rows'] as $row) { ?>
                    <tr>
                        <th><?= htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></th>
                        <?php foreach ($row['counts'] as $count) { ?>
                            <td><?= (int) $count; ?></td>
                        <?php } ?>
                        <td><?= (int) $row['total']; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <th><?= tr('pt085'); ?></th>
                    <?php foreach ($view->matrix['columnTotals'] as $total) { ?>
                        <td><?= (int) $total; ?></td>
                    <?php } ?>
                    <td><?= (int) $view->matrix['grandTotal']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <p class="mb-10"><span class="content-title-noshade txt-blue08"><?= tr('matrix_completion_level'); ?></span> <strong><?= (int) $view->matrix['minimum']; ?></strong></p>

    <div class="content2-container bg-blue02">
        <p class="content-title-noshade-size1">&nbsp;<img src="/images/blue/cache.png" class="icon32" alt="Caches created" title="Caches created">&nbsp;&nbsp;&nbsp;<?= tr('matrix_month_day'); ?></p>
    </div>

    <div class="matrix-table-wrapper mb-10">
        <table class="matrix-table">
            <thead>
                <tr>
                    <th><?= tr('matrix_day_month_header'); ?></th>
                    <?php foreach ($view->monthDayMatrix['columnLabels'] as $label) { ?>
                        <th><?= htmlspecialchars(tr($label), ENT_QUOTES, 'UTF-8'); ?></th>
                    <?php } ?>
                    <th><?= tr('pt085'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($view->monthDayMatrix['rows'] as $row) { ?>
                    <tr>
                        <th><?= htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></th>
                        <?php foreach ($row['values'] as $value) { ?>
                            <td><?= $value === 'X' ? 'X' : (int) $value; ?></td>
                        <?php } ?>
                        <td><?= (int) $row['total']; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <th><?= tr('pt085'); ?></th>
                    <?php foreach ($view->monthDayMatrix['columnTotals'] as $total) { ?>
                        <td><?= (int) $total; ?></td>
                    <?php } ?>
                    <td><?= (int) $view->monthDayMatrix['grandTotal']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
