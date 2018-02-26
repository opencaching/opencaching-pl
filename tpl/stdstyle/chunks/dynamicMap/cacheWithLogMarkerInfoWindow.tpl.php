<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="iw-container">
  <div class="iw-title">
    <a href="{{link}}" target="_blank">
      <img class="iw-icon" src="{{icon}}">
      <span class="iw-wp">{{wp}}:</span>
      <span class="iw-name">{{name}}</span>
    </a>
  </div>

  {{#if log_link}}
  <div class="iw-log">
      {{#if log_text}}
        <img src="{{log_icon}}" title="{{log_typeName}}" alt="{{log_typeName}}">
        <a class="iw-logUsername" target="_blank" href="{{log_userLink}}">
          {{log_username}}
        </a>:
        <span class="iw-logText">{{log_text}}</span>
      {{else}}
        <?=tr('usrWatch_noLogs')?>
      {{/if}}
  </div>
  {{/if}}
</div>
