<?php
use lib\Objects\Admin\ReportCommons;
?>
<script src="<?=$view->reports_js?>"></script>
<div class="content2-container">
  <div class="content2-pagetitle">
    <div class="content-title-noshade-size0" style="float: right;">
      <button type="button" class="btn btn-primary" onclick="location.href='/admin_reports.php';">{{admin_reports_title_reportslist}}</button>
    </div>
    <img src="tpl/stdstyle/images/blue/rproblems.png" class="icon32" alt=""> {{reports}}: {{admin_reports_watch_on}}
  </div>
  <table class="table table-striped full-width">
    <tr>
      <th>{{admin_reports_lbl_id}}</th>
      <th>{{cache}}</th>
      <th>{{admin_reports_lbl_report}}</th>
      <th>{{status_label}}</th>
      <th>{{admin_reports_lbl_leader}}</th>
    </tr>
<?php foreach ($view->reports as $report) { ?>
    <tr>
      <td style="text-align: center;">
        <a href="/admin_reports.php?action=showreport&amp;id=<?=$report->getId()?>" class="links">
        <?php switch ($report->getPollStatus()) {
            case ReportCommons::POLLS_ACTIVE :  ?>
                <img src="/tpl/stdstyle/images/misc/poll-vote.svg" class="report-vote-img" alt="{{admin_reports_poll_active}}" title="{{admin_reports_poll_active}}"><br>
                <?php
                break;
            case ReportCommons::POLLS_ACTIVE_VOTED : ?>
                <img src="/tpl/stdstyle/images/misc/poll-voted.svg" class="report-vote-img" alt="{{admin_reports_poll_voted}}" title="{{admin_reports_poll_voted}}"><br>
                <?php
                break; }?>
        <?=$report->getId()?></a><br>
        <img src="/tpl/stdstyle/images/misc/eye.svg" class="report-watch-img" alt="{{admin_reports_watch_on}}" title="{{admin_reports_watch_on}} | {{admin_reports_watch_info}}" onclick="watchOff(<?=$report->getId()?>)" id="img-on-<?=$report->getId()?>" <?php if (!$report->isReportWatched($view->user->getUserId())) {?>style="display: none;"<?php }?>>
        <img src="/tpl/stdstyle/images/misc/eye-off.svg" class="report-watch-img" alt="{{admin_reports_watch_off}}" title="{{admin_reports_watch_off}} | {{admin_reports_watch_info}}" onclick="watchOn(<?=$report->getId()?>)" id="img-off-<?=$report->getId()?>" <?php if ($report->isReportWatched($view->user->getUserId())) {?>style="display: none;"<?php }?>>
      </td>
      <td>
        <a href="<?=$report->getCache()->getCacheUrl()?>" class="links" target="_blank"><img src="<?=$report->getCache()->getCacheIcon($view->user)?>" height=20 title="<?=tr($report->getCache()->getStatusTranslationKey())?>" alt="<?=tr($report->getCache()->getStatusTranslationKey())?>"> <?=$report->getCache()->getCacheName()?> (<?=$report->getCache()->getWaypointId()?>)</a><br>
        <?=$report->getCache()->getCacheLocationObj()->getLocationDesc(' &gt; ')?><br>
        <a href="<?=$report->getCache()->getOwner()->getProfileUrl()?>" class="links" target="_blank"><?=$report->getCache()->getOwner()->getUserName()?></a>
      </td>
      <td class="<?=$report->getReportStyle()?>">
        <a href="/admin_reports.php?action=showreport&amp;id=<?=$report->getId()?>" class="links"><?=tr($report->getReportTypeTranslationKey())?></a><br>
        <?=$report->getDateSubmit()->format($view->dateFormat)?><br>
        <a href="<?=$report->getUserSubmit()->getProfileUrl()?>" class="links" target="_blank"><?=$report->getUserSubmit()->getUserName()?></a>
      </td>
      <td class="<?=$report->getStatusClass()?>">
        <a href="/admin_reports.php?action=showreport&amp;id=<?=$report->getId()?>" class="links"><?=tr($report->getReportStatusTranslationKey())?></a><br>
        <?php if ($report->getDateLastChange() != null) { echo $report->getDateLastChange()->format($view->dateFormat);}?>
        <?php if ($report->getUserIdLastChange() != null) {?><br><a href="<?=$report->getUserLastChange()->getProfileUrl()?>" class="links" target="_blank"><?=$report->getUserLastChange()->getUserName()?></a><?php }?>
      </td>
      <td><?php if ($report->getUserIdLeader() != null) { ?><a href="<?=$report->getUserLeader()->getProfileUrl()?>" class="links" target="_blank"><?=$report->getUserLeader()->getUserName()?></a><?php }?></td>
    </tr>
<?php } ?>
  </table>
  <?php $view->callChunk('pagination', $view->paginationModel); ?>
</div>
<link rel="prefetch" href="/tpl/stdstyle/images/loader/spinning-circles.svg">
<link rel="prefetch" href="https://www.gstatic.com/charts/loader.js">
