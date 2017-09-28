<?php
use lib\Objects\Admin\ReportCommons;
?>
<script src="<?=$view->report_js?>"></script>
<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/rproblems.png" class="icon32" alt=""> {{reports_user_title}}
  </div>
  <form action="?action=save" method="post" class="reports-form">
    <input type="hidden" name="cacheid" value="<?=$view->cacheid?>">
    <fieldset class="reports-fieldset" onchange="recipientChange()">
      <legend class="content-title-noshade">{{reports_user_lbl_recipient}}</legend>
      <input type="radio" value="<?=ReportCommons::RECIPIENT_OWNER?>" name="type" id="report-type-owner" checked>
      <label for="report-type-owner">{{reports_user_lbl_owner}}</label><br>
      <input type="radio" value="<?=ReportCommons::RECIPIENT_OCTEAM?>" name="type" id="report-type-octeam">
      <label for="report-type-octeam">{{reports_user_lbl_octeam}}</label>
    </fieldset>
    <fieldset class="reports-fieldset">
      <legend class="content-title-noshade">{{reports_user_lbl_type}}</legend>
      <select class="form-control" name="reason" onchange="getLogTemplate()"><?=$view->reasonSelect?></select>
    </fieldset>
    <fieldset class="reports-fieldset">
      <legend class="content-title-noshade">{{message}}</legend>
      <textarea class="report-note form-control" name="content" id="form-reason-textarea"></textarea>
    </fieldset>
    <div id="report-warning" style="display: none"><span class="notice">{{reports_user_msg_ocwarn}}</span><br>
      <input onchange="ocdeclCheck()" type="checkbox" id="report-ocdecl">
      <label for="report-ocdecl">{{reports_user_msg_ocdecl}}</label>
    </div>
    <div id="report-mail-pub">
      <input type="checkbox" name="report-pubemail" id="report-pubemail" checked>
      <label for="report-pubemail">{{my_email_will_send}}</label><br>
      <span class="notice">{{email_publish}}</span>
    </div>
    <div style="text-align: center"><button class="btn btn-primary" type="submit" id="report-submit-btn">{{email_submit}}</button></div>
  </form>
</div>