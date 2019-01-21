<?php

use Utils\Uri\SimpleRouter;

?>
<div class="content2-container">
    <div class="content2-pagetitle">
        {{admin_dbupdate_title}}
    </div>

<?php if (!empty($view->messages)) { ?>
    <div style="color:brown; padding:0.5em 0 0.5em 0">
        <p><?= nl2br($view->messages) ?></p>
    </div>
<?php } ?>

<?php if (isset($view->viewScript)) { ?>

<p><b><?= $view->scriptFilename ?></b></p>
<pre class="source-code">
<?= htmlspecialchars($view->scriptSource) ?>
</pre>

<?php } elseif (isset($view->askRename)) { ?>

<form action="<?= SimpleRouter::getLink('Admin.DbUpdate', 'rename', $view->askRename) ?>">
<p>
    <!-- English page for developers (only the buttons are translated). -->

    <br />
    New filename:&nbsp;
    <input type="text" maxlength="60" name="newName" value="<?= $view->oldName ?>" class="form-control input300" />
    .php &nbsp; &nbsp; (allowed chars: A-Z a-z 0-9 _)
    <p><br /><em>Rename</em> will do a <b>git mv</b>, if the file is staged or commited in Git.</p>

    <script type="text/javascript">
        // select non-date part of update name

        input = document.getElementsByName('newName')[0];
        input.focus();
        if (input.setSelectionRange) {
            input.setSelectionRange(4, 99);
        } else if (input.createTextRange) {
            var range = input.createTextRange();
            range.moveStart(4);
            range.select();
        }
    </script>

    <input type="submit" class="btn btn-default btn-sm" value="{{rename}}" />
    <a class="btn btn-default btn-sm" href="<?= SimpleRouter::getLink('Admin.DbUpdate') ?>">{{cancel}}</a>

</p>
</form>

<?php } elseif (isset($view->askDelete)) { ?>

    <!-- English page for developers (only the buttons are translated). -->

    <p><br />Do you really want to delete <b><?= $view->fileName ?></b> ?</p>
    <p><br /><i>Delete</i> will do a <b>git rm -f</b>, if the file is staged or commited in Git.</p>

    <a class="btn btn-default btn-sm" href="<?= SimpleRouter::getLink('Admin.DbUpdate', 'delete', $view->askDelete) ?>">{{delete}}</a>
    <a class="btn btn-default btn-sm" href="<?= SimpleRouter::getLink('Admin.DbUpdate') ?>">{{cancel}}</a>

<?php } else { ?>

    <a class="btn btn-default btn-sm" href="<?= SimpleRouter::getLink('Admin.DbUpdate') ?>">{{admin_dbupdate_reload}}</a>
    <?php if ($view->updatesShouldRun) { ?>
        <a class="btn btn-default btn-sm" href="<?= SimpleRouter::getLink('Admin.DbUpdate', 'run') ?>">{{admin_dbupdate_run}}</a>
    <?php }?>
    <?php if ($view->developerMode) { ?>
        <a class="btn btn-default btn-sm" href="<?= SimpleRouter::getLink('Admin.DbUpdate', 'createNew') ?>">{{admin_dbupdate_create}}</a>
    <?php }?>
    <a class="btn btn-default btn-sm" href="https://github.com/opencaching/opencaching-pl/blob/master/docs/DbUpdate.md" target="_blank">{{Help}}</a>
    <br /><br />
    <table class="table">
        <tr>
            <th>{{admin_dbupdate_name}}</th>
            <th>{{admin_dbupdate_runtype}}</th>
            <th>{{admin_dbupdate_time}}</th>
            <th>{{action}}</th>
        </tr>

    <?php foreach ($view->updates as $update) { ?>
        <tr>
            <td <?php if (!$update->isInGitMasterBranch()) { ?>style="font-style:oblique"<?php } ?>><a href="<?= SimpleRouter::getLink('Admin.DbUpdate', 'viewScript', $update->getUuid()) ?>"><?= $update->getName() ?></a></td>
            <td><?= tr('admin_dbupdate_' . $update->getRuntype()) ?></td>
            <td>
                <?= substr($update->wasRunAt(), 0, 10) ?>&nbsp;
                <?= substr($update->wasRunAt(), 11, 5) ?>
            </td>
            <td>
                <?php foreach ($update->adminActions as $action => $title) { ?>
                    [<a href="<?= SimpleRouter::getLink('Admin.DbUpdate', $action, $update->getUuid()) ?>" style="white-space: nowrap"><?= $title ?></a>]
                <?php } ?> 
            </td>
        </tr>
    <?php } ?>

        <tr>
            <td colspan="4" style="background:white;"><br />{{admin_dbupdate_develnote}}</td>
        </tr>
    </table>
<?php } ?>
</div>
