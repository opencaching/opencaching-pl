<?php
use lib\Objects\GeoCache\GeoCacheLogCommons;
use lib\Objects\GeoCache\GeoCacheCommons;
use lib\Objects\Admin\ReportEmailTemplate;
use lib\Objects\Admin\Report;
?>
<script type="text/javascript" src="/lib/js/wz_tooltip.js"></script>
<script src="<?=$view->reports_js?>"></script>
<link rel="prefetch" href="/tpl/stdstyle/images/loader/spinning-circles.svg">
<div class="content2-container">
  <div class="content2-pagetitle">
    <div style="float: right;">
      <button type="button" class="btn btn-default" onclick="watchOff(<?=$view->report->getId()?>)" id="report-btn-on" <?php if (!$view->report->isReportWatched($view->user->getUserId())) {?>style="display: none;"<?php }?>>
        <img src="/tpl/stdstyle/images/misc/eye.svg" class="report-watch-img" alt="{{admin_reports_watch_on}}" id="report-img-on"> {{admin_reports_watch_on}}
      </button>
      <button type="button" class="btn btn-default" onclick="watchOn(<?=$view->report->getId()?>)" id="report-btn-off" <?php if ($view->report->isReportWatched($view->user->getUserId())) {?>style="display: none;"<?php }?>>
        <img src="/tpl/stdstyle/images/misc/eye-off.svg" class="report-watch-img" alt="{{admin_reports_watch_off}}" id="report-img-off"> {{admin_reports_watch_off}}
      </button>
      <button type="button" class="btn btn-primary" onclick="window.location.href='/admin_reports.php'">{{admin_reports_title_reportslist}}</button>
    </div>
    <img src="tpl/stdstyle/images/blue/rproblems.png" class="icon32" alt=""> {{admin_reports_title_reportshow}}
  </div>
  <?php if (isset($view->infoMsg)) { $view->callChunk('infoBar', null, $view->infoMsg, null); }?>
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
      <td>
        <select name="reportLeader" class="form-control input200" id="leaderSelectCtrl"><?=$view->leaderSelect?></select>
        <button type="button" class="btn btn-default" onclick="changeLeader(<?=$view->report->getId()?>)">{{admin_reports_btn_change}}</button>
        (<span class="report-strong" id="report-leader"><?php if ($view->report->getUserIdLeader() != null) { echo $view->report->getUserLeader()->getUserName(); } ?></span>)
      </td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{status_label}}</td>
      <td>
        <select name="reportStatus" class="form-control input200" id="statusSelectCtrl"><?=$view->statusSelect?></select>
        <button type="button" class="btn btn-default" onclick="changeStatus(<?=$view->report->getId()?>)">{{admin_reports_btn_change}}</button>
        (<span class="report-strong" id="report-status"><?=tr($view->report->getReportStatusTranslationKey())?></span>)
      </td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{last_modified_label}}</td>
      <td>
        <?php if ($view->report->getDateChangeStatus() != null) { echo $view->report->getDateChangeStatus()->format($view->dateFormat); }?>
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
        <?=$view->report->getCache()->getCacheLocationObj()->getLocationDesc(' &gt; ')?><br>
        <?php if ($view->report->getCache()->getCacheType() == GeoCacheCommons::TYPE_EVENT) { ?>
            <img src="/tpl/stdstyle/images/log/16x16-attend.png" class="icon16" alt="{{attendends}}"> x<?=$view->report->getCache()->getFounds()?> 
            <img src="/tpl/stdstyle/images/log/16x16-will_attend.png" class="icon16" alt="{{will_attend}}"> x<?=$view->report->getCache()->getNotFounds()?> 
            <img src="/tpl/stdstyle/images/log/16x16-note.png" class="icon16" alt="{{note}}"> x<?=$view->report->getCache()->getNotesCount()?>
            <br>
        <?php  } else { ?>
            <img src="/tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt="{{found}}"> x<?=$view->report->getCache()->getFounds()?> 
            <img src="/tpl/stdstyle/images/log/16x16-dnf.png" class="icon16" alt="{{not_found}}"> x<?=$view->report->getCache()->getNotFounds()?> 
            <img src="/tpl/stdstyle/images/log/16x16-note.png" class="icon16" alt="{{note}}"> x<?=$view->report->getCache()->getNotesCount()?>
            <img src="/images/rating-star.png" class="icon16" alt="{{recommendations}}"> x<?=$view->report->getCache()->getRecommendations()?>
        <?php }?>
      </td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{new_logs}}</td>
      <td>
        <?php foreach ($view->lastLogs as $log) { ?>
          <img src="/<?=GeoCacheLogCommons::GetIconForType($log->getType())?>" alt="<?=tr(GeoCacheLogCommons::typeTranslationKey($log->getType()))?>" onmouseover="Tip('<b><?=$log->getUser()->getUserName()?></b>&nbsp;(<?=$log->getDate()->format($view->dateFormat)?>)<br><?=GeoCacheLogCommons::cleanLogTextForToolTip($log->getText())?>',OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"> 
        <?php  }?>
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
    <tr id="report-note-row">
      <td colspan="2">
        <p class="content-title-noshade-size1">
          {{admin_reports_lbl_note}}&nbsp;&nbsp;
          <button type="button" class="btn btn-sm btn-default" onclick="enableEmail()">{{admin_reports_lbl_email}}</button>
        </p>
      </td>
    </tr>
    <tr id="report-note-row2">
      <td colspan="2" style="text-align: center">
        <form action="?action=addnote" method="post" class="reports-form">
          <textarea rows="6" name="note" class="report-note form-control" id="form-note-textarea"></textarea><br>
          <input type="submit" class="btn btn-default" value="{{save}}">
          <input type="hidden" name="id" value="<?=$view->report->getId()?>">
        </form>
      </td>
    </tr>
    <tr id="report-email-row" style="display: none;">
      <td colspan="2">
        <p class="content-title-noshade-size1">
          {{admin_reports_lbl_email}}&nbsp;&nbsp;
          <button type="button" class="btn btn-sm btn-default" onclick="enableNote()">{{admin_reports_lbl_note}}</button> &nbsp;
          <img src="/tpl/stdstyle/images/loader/spinning-circles.svg" class="report-watch-img" alt="" id="email-spinning-img" style="display: none;">
        </p>
      </td>
    </tr>
    <tr id="report-email-row2" style="display: none;">
      <td colspan="2">
        <form action="?action=sendemail" method="post" class="reports-form">
          <fieldset id="email-recipient" class="reports-fieldset">
            <legend class="content-title-noshade">{{admin_reports_lbl_recipient}}</legend>
            <input type="radio" value="<?=ReportEmailTemplate::RECIPIENT_SUBMITTER?>" name="email-recipient" id="radio-recipient-submitter" onchange="getTemplates(<?=Report::OBJECT_CACHE?>)">
            <label for="radio-recipient-submitter">{{admin_reports_lbl_submitter}}</label>
            <input type="radio" value="<?=ReportEmailTemplate::RECIPIENT_CACHEOWNER?>" name="email-recipient" id="radio-recipient-cacheowner" onchange="getTemplates(<?=Report::OBJECT_CACHE?>)">
            <label for="radio-recipient-cacheowner">{{admin_reports_lbl_cacheowner}}</label>
            <input type="radio" value="<?=ReportEmailTemplate::RECIPIENT_ALL?>" name="email-recipient" id="radio-recipient-all" onchange="getTemplates(<?=Report::OBJECT_CACHE?>)">
            <label for="radio-recipient-all">{{admin_reports_lbl_submitter}} &amp; {{admin_reports_lbl_cacheowner}}</label>
          </fieldset>
          <fieldset id="email-template" class="reports-fieldset" style="display: none;">
            <legend class="content-title-noshade">{{admin_reports_lbl_template}}</legend>
            <select class="form-control" id="templateSelect" onchange="getTemplate()"></select>
          </fieldset>
          <fieldset id="email-content" class="reports-fieldset" style="display: none;">
            <legend class="content-title-noshade">{{admin_reports_lbl_content}}</legend>
            <textarea class="report-note form-control" name="content" id="form-email-textarea"></textarea>
            <button class="btn btn-default" type="button">{{email_submit}}</button>
          </fieldset>
          <input type="hidden" name="id" value="<?=$view->report->getId()?>">
        </form>
      </td>
    </tr>
    <tr>
      <td colspan="2"><p class="content-title-noshade-size1">{{admin_reports_lbl_archive}}</p></td>
    </tr>
    <tr>
      <td colspan="2"><p><?=nl2br($view->report->getNote())?></p></td>
    </tr>
  </table>


</div>
