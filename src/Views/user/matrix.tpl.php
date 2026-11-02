<div class="content2-container">
    <div class="content2-pagetitle">
        <img src="/images/blue/stat1.png" class="icon32" alt="<?= tr('matrix_title'); ?>" title="<?= tr('matrix_title'); ?>">
        &nbsp;<?= tr('statistics_users'); ?> <?= htmlspecialchars($view->matrixUsername, ENT_QUOTES, 'UTF-8'); ?>
    </div>

    <div class="nav4">
        <ul id="statmenu">
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

    <div class="matrix-table-wrapper">
        <table class="matrix-table">
            <thead>
                <tr>
                    <th><?= tr('matrix_difficulty_terrain'); ?></th>
                    <?php foreach ($view->matrix['columnLabels'] as $label) { ?>
                        <th><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($view->matrix['rows'] as $row) { ?>
                    <tr>
                        <th><?= htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?></th>
                        <?php foreach ($row['counts'] as $count) { ?>
                            <td><?= (int) $count; ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
