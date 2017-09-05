<?php
?>
<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/rproblems.png" class="icon32" alt=""> {{admin_reports_title_reportslist}}
  </div>
  <form action="/admin_reports.php" method="post">
  <table class="table table-striped full-width">
    <tr>
      <th>{{admin_reports_lbl_id}}</th>
      <th>{{cache}}</th>
      <th>{{admin_reports_lbl_report}}</th>
      <th>{{status_label}}</th>
      <th>{{admin_reports_lbl_leader}}</th>
    </tr>
    <tr>
      <td style="text-align: center;">
        <button type="submit" class="btn btn-primary btn-sm">{{filter}}</button>
        <button type="submit" name="reset" class="btn btn-default btn-xs">{{reset}}</button>
      </td>
      <td>
        <input type="text" placeholder="{{waypointname_label}}" name="reportWp" value="<?=$_SESSION['reportWp']?>" class="form-control">
        
      </td>
      <td><select name="reportType" class="form-control"><?=$view->typeSelect?></select></td>
      <td><select name="reportStatus" class="form-control"><?=$view->statusSelect?></select></td>
      <td><select name="reportUser" class="form-control"><?=$view->userSelect?></select></td>
    </tr>
<?php foreach ($view->reports as $report) { ?>
    <tr>
      <td><a href="/admin_reports.php?action=showreport&id=<?=$report->getId()?>" class="links"><?=$report->getId()?></a></td>
      <td>
        <a href="/viewcache.php?wp=<?=$report->getCache()->getWaypointId()?>" class="links" target="_blank"><img src="/<?=$report->getCache()->getCacheIcon($view->user)?>" height=20 title="<?=tr($report->getCache()->getStatusTranslationKey())?>" alt="<?=tr($report->getCache()->getStatusTranslationKey())?>"> <?=$report->getCache()->getCacheName()?> (<?=$report->getCache()->getWaypointId()?>)</a><br>
        <?=$report->getCache()->getCacheLocationObj()->getLocationDesc(' &gt; ')?><br>
        <a href="/viewprofile.php?userid=<?=$report->getCache()->getOwnerId()?>" class="links" target="_blank"><?=$report->getCache()->getOwner()->getUserName()?></a>
      </td>
      <td>
        <a href="/admin_reports.php?action=showreport&id=<?=$report->getId()?>" class="links"><?=tr($report->getReportTypeTranslationKey())?></a><br>
        <?=$report->getDateSubmit()->format($view->dateFormat)?><br>
        <a href="/viewprofile.php?userid=<?=$report->getUserIdSubmit()?>" class="links" target="_blank"><?=$report->getUserSubmit()->getUserName()?></a>
      </td>
      <td>
        <a href="/admin_reports.php?action=showreport&id=<?=$report->getId()?>" class="links"><?=tr($report->getReportStatusTranslationKey())?></a><br>
        <?php if ($report->getDateChangeStatus() != null) { echo $report->getDateChangeStatus()->format($view->dateFormat);}?>
        <?php if ($report->getUserIdChangeStatus() != null) {?><br><a href="/viewprofile.php?userid=<?=$report->getUserIdChangeStatus()?>" class="links" target="_blank"><?=$report->getUserChangeStatus()->getUserName()?></a><?php }?>
      </td>
      <td><?php if ($report->getUserIdLeader() != null) { ?><a href="/viewprofile.php?userid=<?=$report->getUserIdLeader()?>" class="links" target="_blank"><?=$report->getUserLeader()->getUserName()?></a><?php }?></td>
    </tr>
<?php } ?>
  </table>
    </form>
  
  <?php $view->callChunk('pagination', $view->paginationModel); ?>
</div>