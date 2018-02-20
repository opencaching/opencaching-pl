<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="iw-container">
  <div class="iw-title">
    <a href="{{link}}" target="_blank">
      <img class="iw-icon" src="{{icon}}">
      <span class="iw-wp">{{wp}}:</span>
      <span class="iw-name">{{name}}</span>
    </a>
  </div>

  {{#if llog}}
  <div class="iw-log">
      {{#if llog_text}}
        <img src="{{llog_icon}}" title="{{llog_type_name}}" alt="{{llog_type_name}}">
        <a class="iw-logUsername" target="_blank" href="{{llog_user_id}}">
          {{llog_username}}
        </a>:
        <span class="iw-logText">{{llog_text}}</span>
      {{else}}
        <?=tr('usrWatch_noLogs')?>
      {{/if}}
  </div>
  {{/if}}
</div>
