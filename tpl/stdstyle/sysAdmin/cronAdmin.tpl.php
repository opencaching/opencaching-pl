<?php

use Utils\Uri\SimpleRouter;

?>
<div class="content2-container">
    <div class="content2-pagetitle">
        {{admin_cron_title}}
    </div>
    <p>
        <?= $view->message ?>
    </p>
    <table class="table table-striped full-width">
        <tr>
            <th>{{admin_cron_lbl_job}}</th>
            <th>{{admin_cron_lbl_schedule}}</th>
            <th>{{admin_cron_lbl_lastrun}}</th>
            <?php if ($view->allowRun) { ?><th>{{action}}</th><?php } ?>
        </tr>
<?php foreach ($view->jobs as $jobName => $jobData) { ?>
        <tr>
            <td><?= $jobData['shortName'] ?></td>
            <td><?= $jobData['schedule'] ?></td>
            <td>
                <?php if ($jobData['jobFileMissing']) { ?>
                    <span class="errormsg">{{admin_cron_file_missing}}</span>
                <?php } else { ?>
                    <?= substr($jobData['lastRun'], 0, 10) ?>&nbsp;
                    <?= substr($jobData['lastRun'], 11, 8) ?>
                <?php } ?>
            </td>
            <?php if ($view->allowRun) { ?>
                <td>
                    <?php if ($jobData['mayRunNow']) { ?>
                        <a href="<?=SimpleRouter::getLink('Cron.CronAdmin', 'run', $jobName) ?>">
                            {{admin_cron_run_now}}
                        </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>
<?php } ?>
    </table>
</div>
