<?php
use lib\Objects\GeoCache\GeoCacheLogCommons;
use lib\Objects\GeoCache\GeoCacheCommons;
use lib\Objects\Admin\ReportEmailTemplate;
use lib\Objects\Admin\ReportPoll;
use lib\Objects\Admin\ReportCommons;
?>
<?php if ($view->includeGCharts) {?>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
  google.charts.load('current', {'packages':['corechart']});
  <?php foreach ($view->inactivePolls as $inactPoll) {?>
    google.charts.setOnLoadCallback(drawpoll<?=$inactPoll->getId()?>);
    <?=$inactPoll->getJsCode()?>
  <?php }?>
</script>
<?php }?>
<script src="/lib/js/wz_tooltip.js"></script>
<script src="<?=$view->reports_js?>"></script>
<div id="report-confirm-poll"><p><strong>{{admin_reports_poll_confirm}}</strong></p>
  <p style="text-align: left">{{admin_reports_lbl_question}}: <em><span id="confirm-dlg-question"></span></em></p>
  <p style="text-align: left">{{admin_reports_lbl_ans}} 1: <em><span id="confirm-dlg-ans1"></span></em></p>
  <p style="text-align: left">{{admin_reports_lbl_ans}} 2: <em><span id="confirm-dlg-ans2"></span></em></p>
  <p style="text-align: left">{{admin_reports_lbl_ans}} 3: <em><span id="confirm-dlg-ans3"></span></em></p>
  <span class="notice">{{admin_reports_poll_info}}</span><br>
  <button class="btn btn-primary" id="confirm-dlg-yes">{{yes}}</button>
  <button class="btn btn-default" id="confirm-dlg-no">{{no}}</button>
</div>
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
  <?=$view->callChunk('infoBar', $view->cleanUri, $view->infoMsg, $view->errorMsg)?>
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
      <td><a href="<?=$view->report->getUserSubmit()->getProfileUrl()?>" class="links" target="_blank"><?=$view->report->getUserSubmit()->getUserName()?></a> (<?php echo $view->report->getUserSubmit()->getFoundGeocachesCount() + $view->report->getUserSubmit()->getNotFoundGeocachesCount() + $view->report->getUserSubmit()->getHiddenGeocachesCount()?>)</td>
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
        <?php if ($view->report->getDateLastChange() != null) { echo $view->report->getDateLastChange()->format($view->dateFormat); }?>
        <?php if ($view->report->getUserIdLastChange() != null) {?>(<a href="<?=$view->report->getUserLastChange()->getProfileUrl()?>" class="links" target="_blank"><?=$view->report->getUserLastChange()->getUserName()?></a>) <?php }?>
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
        <img src="<?=$view->report->getCache()->getCacheIcon($view->user)?>" height=20 alt="">
        <a href="<?=$view->report->getCache()->getCacheUrl()?>" class="links" target="_blank"><?=$view->report->getCache()->getCacheName()?> (<?=$view->report->getCache()->getWaypointId()?>)</a><br>
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
          <img src="<?=GeoCacheLogCommons::GetIconForType($log->getType())?>" alt="<?=tr(GeoCacheLogCommons::typeTranslationKey($log->getType()))?>" onmouseover="Tip('<b><?=$log->getUser()->getUserName()?></b>&nbsp;(<?=$log->getDate()->format($view->dateFormat)?>)<br><?=GeoCacheLogCommons::cleanLogTextForToolTip($log->getText())?>',OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">
        <?php  }?>
      </td>
    </tr>
    <tr>
      <td class="content-title-noshade" style="text-align: right;">{{owner}}</td>
      <td>
        <a href="<?=$view->report->getCache()->getOwner()->getProfileUrl()?>" class="links" target="_blank"><?=$view->report->getCache()->getOwner()->getUserName()?></a>
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
    <?php foreach ($view->activePolls as $poll) {?>
      <tr>
        <td colspan="2">
          <p class="content-title-noshade-size1">{{admin_reports_lbl_pollactive}}
          (<?=$poll->getDateStart()->format($view->dateFormat)?> - <?=$poll->getDateEnd()->format($view->dateFormat)?>)
          <a href="?action=remindpoll&amp;id=<?=$view->report->getId()?>&amp;pollid=<?=$poll->getId()?>" class="btn btn-default btn-xs">{{admin_reports_lbl_pollremind}}</a>
          <?php if ($poll->getVotesCount() == 0) { ?><a href="?action=cancelpoll&amp;id=<?=$view->report->getId()?>&amp;pollid=<?=$poll->getId()?>" class="btn btn-xs btn-default">{{cancel}}</a><?php }?>
          </p>
        </td>
      </tr>
      <tr>
        <td colspan="2">
        <?php if ($poll->userVoted()) { ?>
          {{admin_reports_lbl_question}}: <?=$poll->getQuestion()?><br>
          <strong>{{admin_reports_info_voted}}.</strong><br>
        <?php } else { ?>
          <form action="?action=savevote" method="post" class="reports-form">
            <fieldset class="reports-fieldset" onchange="showButton(<?=$poll->getId()?>)">
              <legend class="content-title-noshade"><?=$poll->getQuestion()?></legend>
              <input type="radio" value="1" name="vote" id="vote-<?=$poll->getId()?>-1">
              <label for="vote-<?=$poll->getId()?>-1"><?=$poll->getAns1()?></label>
              <input type="radio" value="2" name="vote" id="vote-<?=$poll->getId()?>-2">
              <label for="vote-<?=$poll->getId()?>-2"><?=$poll->getAns2()?></label>
              <?php if ($poll->getAns3() != null) {?>
                <input type="radio" value="3" name="vote" id="vote-<?=$poll->getId()?>-3">
                <label for="vote-<?=$poll->getId()?>-3"><?=$poll->getAns3()?></label>
              <?php } ?>
              &nbsp;&nbsp;<button type="submit" class="btn btn-sm btn-default" id="vote-<?=$poll->getId()?>-btn" style="display: none">{{save}}</button>
            </fieldset>
            <input type="hidden" name="pollid" value="<?=$poll->getId()?>">
            <input type="hidden" name="id" value="<?=$view->report->getId()?>">
          </form>
        <?php } ?>
        <?php if ($poll->getVotesCount() > 0) { ?>
          {{admin_reports_lbl_voters}}: <?=$poll->getVotersList(',')?>
        <?php } ?>
        </td>
      </tr>
    <?php }?>
    <tr>
      <td colspan="2">
      <p class="content-title-noshade-size1">
        {{actions}}&nbsp;&nbsp;
        <span class="btn-group">
          <button type="button" class="btn btn-sm btn-success" id="reports-btn-note" onclick="enableNote()">{{admin_reports_lbl_note}}</button>
          <button type="button" class="btn btn-sm btn-default" id="reports-btn-log" onclick="enableLog()">{{new_log_entry}}</button>
          <button type="button" class="btn btn-sm btn-default" id="reports-btn-email" onclick="enableEmail()">{{admin_reports_lbl_email}}</button>
          <button type="button" class="btn btn-sm btn-default" id="reports-btn-poll" onclick="enablePoll()">{{admin_reports_lbl_poll}}</button>
        </span>
        <img src="/tpl/stdstyle/images/loader/spinning-circles.svg" class="report-watch-img" alt="" id="email-spinning-img" style="display: none;">
        </p>
      </td>
    </tr>
    <tr id="report-note-row">
      <td colspan="2" style="text-align: center">
        <form action="?action=addnote" method="post" class="reports-form">
          <textarea rows="6" name="note" class="report-note form-control" id="form-note-textarea"></textarea><br>
          <input type="submit" class="btn btn-default" value="{{save}}">
          <input type="hidden" name="id" value="<?=$view->report->getId()?>">
        </form>
      </td>
    </tr>
    <tr id="report-log-row" style="display: none;">
      <td colspan="2">
        <form action="?action=newlog" method="post" class="reports-form">
          <?php if ($view->logSelect != '') {?>
          <fieldset id="log-template" class="reports-fieldset">
            <legend class="content-title-noshade">{{admin_reports_lbl_template}}</legend>
            <select class="form-control" id="logTemplateSelect" onchange="getLogTemplate()"><?=$view->logSelect?></select>
          </fieldset>
          <?php }?>
          <fieldset id="log-content" class="reports-fieldset">
            <legend class="content-title-noshade">{{admin_reports_lbl_content}}</legend>
            <textarea class="report-note form-control" name="content" id="form-log-textarea"></textarea>
            <div style="text-align: center"><button class="btn btn-default" type="submit">{{email_submit}}</button></div>
          </fieldset>
          <input type="hidden" name="id" value="<?=$view->report->getId()?>">
        </form>
      </td>
    </tr>
    <tr id="report-email-row" style="display: none;">
      <td colspan="2">
        <form action="?action=sendemail" method="post" class="reports-form">
          <fieldset id="email-recipient" class="reports-fieldset">
            <legend class="content-title-noshade">{{admin_reports_lbl_recipient}}</legend>
            <input type="radio" value="<?=ReportEmailTemplate::RECIPIENT_SUBMITTER?>" name="email-recipient" id="radio-recipient-submitter" onchange="getTemplates(<?=ReportCommons::OBJECT_CACHE?>)">
            <label for="radio-recipient-submitter">{{admin_reports_lbl_submitter}}</label>
            <input type="radio" value="<?=ReportEmailTemplate::RECIPIENT_CACHEOWNER?>" name="email-recipient" id="radio-recipient-cacheowner" onchange="getTemplates(<?=ReportCommons::OBJECT_CACHE?>)">
            <label for="radio-recipient-cacheowner">{{admin_reports_lbl_cacheowner}}</label>
            <input type="radio" value="<?=ReportEmailTemplate::RECIPIENT_ALL?>" name="email-recipient" id="radio-recipient-all" onchange="getTemplates(<?=ReportCommons::OBJECT_CACHE?>)">
            <label for="radio-recipient-all">{{admin_reports_lbl_submitter}} &amp; {{admin_reports_lbl_cacheowner}}</label>
          </fieldset>
          <fieldset id="email-template" class="reports-fieldset" style="display: none;">
            <legend class="content-title-noshade">{{admin_reports_lbl_template}}</legend>
            <select class="form-control" id="templateSelect" onchange="getTemplate()"></select>
          </fieldset>
          <fieldset id="email-content" class="reports-fieldset" style="display: none;">
            <legend class="content-title-noshade">{{admin_reports_lbl_content}}</legend>
            <textarea class="report-note form-control" name="content" id="form-email-textarea"></textarea>
            <div style="text-align: center"><button class="btn btn-default" type="submit">{{email_submit}}</button></div>
          </fieldset>
          <input type="hidden" name="id" value="<?=$view->report->getId()?>" id="reportid">
        </form>
      </td>
    </tr>
    <tr id="report-poll-row" style="display: none;">
      <td colspan="2">
        <form action="?action=addpoll" method="post" class="reports-form" id="reports-form-addpoll">
          <table class="table" id="report-email-table">
            <tr>
              <td colspan="3">
                <div class="content-title-noshade">{{admin_reports_lbl_question}}</div>
                <input type="text" class="form-control" name="question" maxlength="200" onkeypress="return event.keyCode != 13;" id="poll-input-question">
              </td>
            </tr>
            <tr>
              <td>
                <div class="content-title-noshade">{{admin_reports_lbl_ans}} 1</div>
                <input type="text" class="form-control" name="ans1" maxlength="50" onkeypress="return event.keyCode != 13;" id="poll-input-ans1">
              </td>
              <td>
               <div class="content-title-noshade">{{admin_reports_lbl_ans}} 2</div>
               <input type="text" class="form-control" name="ans2" maxlength="50" onkeypress="return event.keyCode != 13;" id="poll-input-ans2">
              </td>
              <td>
               <span class="content-title-noshade">{{admin_reports_lbl_ans}} 3</span>
               <input type="checkbox" name="noans3" id="noans3" onchange="clearAns3()" checked="checked">
               <label for="noans3">{{admin_reports_lbl_none}}</label><br>
               <input type="text" class="form-control" name="ans3" maxlength="50" id="ans3" oninput="clearNoans3()" onkeypress="return event.keyCode != 13;">
              </td>
            </tr>
            <tr>
              <td>
                <div class="content-title-noshade">{{admin_reports_lbl_perdiod}}</div>
                <select name="period" class="form-control input70" id="poll-interval"><?=ReportPoll::generatePollIntervalSelect()?></select> {{admin_reports_lbl_days}}&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-default" onclick="confirmPoll()">{{save}}</button>
              </td>
              <td colspan="2">
                <span class="notice">{{admin_reports_poll_info}}</span>
              </td>
            </tr>
          </table>
          <input type="hidden" name="id" value="<?=$view->report->getId()?>">
        </form>
      </td>
    </tr>
    <?php if (!empty($view->reportLogs)) { ?>
      <tr>
        <td colspan="2"><p class="content-title-noshade-size1">{{admin_reports_lbl_activity}}</p></td>
      </tr>
      <?php foreach ($view->reportLogs as $reportLog) {?>
        <tr>
          <td colspan="2"><p><?=$reportLog->getFormattedLog()?></p></td>
        </tr>
      <?php }?>
    <?php }?>
    <?php if ($view->report->getNote() != '') { ?>
      <tr>
        <td colspan="2"><p class="content-title-noshade-size1">{{admin_reports_lbl_archive}}</p></td>
      </tr>
      <tr>
        <td colspan="2"><p><?=nl2br($view->report->getNote())?></p></td>
      </tr>
    <?php }?>
  </table>
</div>
<link rel="prefetch" href="/tpl/stdstyle/images/loader/spinning-circles.svg">
