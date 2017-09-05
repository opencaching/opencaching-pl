<?php ?>
<div class="content2-container">
  <div class="content2-pagetitle">
    <div style="float: right;"><button type="button" class="btn btn-primary" onclick="window.location.href = '/admin_reports.php'">{{admin_reports_title_reportslist}}</button></div>
    <img src="tpl/stdstyle/images/blue/rproblems.png" class="icon32" alt=""> {{admin_reports_title_reportshow}}
  </div>
  <table class="table full-width">
    <tr>
      <td colspan="2">
        <p class="content-title-noshade-size1">{{admin_reports_lbl_report}}</p>
      </td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{admin_reports_lbl_id}}</td>
      <td><strong><?=$view->report->getId()?> - <?=tr($view->report->getReportTypeTranslationKey())?></strong></td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{date}}</td>
      <td><?=$view->report->getDateSubmit()->format($view->dateFormat)?></td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{admin_reports_lbl_submiter}}</td>
      <td><a href="/viewprofile.php?userid=<?=$view->report->getUserIdSubmit()?>" class="links" target="_blank"><?=$view->report->getUserSubmit()->getUserName()?></a> (<?php echo $view->report->getUserSubmit()->getFoundGeocachesCount() + $view->report->getUserSubmit()->getNotFoundGeocachesCount() + $view->report->getUserSubmit()->getHiddenGeocachesCount()?>)</td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{admin_reports_lbl_leader}}</td>
      <td><?php if ($view->report->getUserIdLeader() != null) { ?><a href="/viewprofile.php?userid=<?=$view->report->getUserIdLeader()?>" class="links" target="_blank"><?=$view->report->getUserLeader()->getUserName()?></a><?php }?></td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{status_label}}</td>
      <td>
        <strong><?=tr($view->report->getReportStatusTranslationKey())?></strong>
        <?php if ($view->report->getDateChangeStatus() != null) { ?><br>{{last_modified_label}} <?php echo $view->report->getDateChangeStatus()->format($view->dateFormat); }?>
        <?php if ($view->report->getUserIdChangeStatus() != null) {?>(<a href="/viewprofile.php?userid=<?=$view->report->getUserIdChangeStatus()?>" class="links" target="_blank"><?=$view->report->getUserChangeStatus()->getUserName()?></a>) <?php }?>
      </td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{content}}</td>
      <td><?=$view->report->getContent()?></td>
    </tr>
    <tr>
      <td colspan="2"><p class="content-title-noshade-size1">{{cache}}</p></td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{cache}}</td>
      <td>
        <img src="/<?=$view->report->getCache()->getCacheIcon($view->user)?>" height=20 alt="">
        <a href="/viewcache.php?wp=<?=$view->report->getCache()->getWaypointId()?>" class="links" target="_blank"><?=$view->report->getCache()->getCacheName()?> (<?=$view->report->getCache()->getWaypointId()?>)</a><br>
        <?=$view->report->getCache()->getCacheLocationObj()->getLocationDesc(' &gt; ')?>
      </td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{owner}}</td>
      <td>
        <a href="/viewprofile.php?userid=<?=$view->report->getCache()->getOwner()->getUserId()?>" class="links" target="_blank"><?=$view->report->getCache()->getOwner()->getUserName()?></a>
        (<?php echo $view->report->getCache()->getOwner()->getFoundGeocachesCount() + $view->report->getCache()->getOwner()->getNotFoundGeocachesCount() + $view->report->getCache()->getOwner()->getHiddenGeocachesCount()?>)</td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{status_label}}</td>
      <td><?=tr($view->report->getCache()->getStatusTranslationKey())?></td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{type}}</td>
      <td><?=tr($view->report->getCache()->getCacheTypeTranslationKey())?></td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{size}}</td>
      <td><?=tr($view->report->getCache()->getSizeTranslationKey())?></td>
    </tr>
<!-- 
    <tr>
      <td colspan="2"><p class="content-title-noshade-size1">{{actions}}</p></td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;"></td>
      <td></td>
    </tr>
     -->
    <tr>
      <td colspan="2"><p class="content-title-noshade-size1">{{admin_reports_lbl_archive}}</p></td>
    </tr>
    <tr>
      <td colspan="2"><p><?=nl2br($view->report->getNote())?></p></td>
    </tr>
  </table>


</div>
