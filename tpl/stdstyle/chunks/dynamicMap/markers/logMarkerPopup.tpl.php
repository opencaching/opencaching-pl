<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="iw-container">
  <div class="iw-title">
    <img class="iw-icon dynamicMap_mapPopup-cacheIcon" src="{{icon}}" alt="{{wp}}" title="{{wp}}">
    <a href="{{link}}" class="links" target="_blank">
      <span class="iw-name">{{name}}</span></a>
    {{#if username}}({{username}}){{/if}}
  </div>

  {{#if log_link}}
  <div class="iw-log dynamicMap_mapPopup-cacheLogBlock">
    <a href="{{log_link}}" class="links" target="_blank">
      <img src="{{log_icon}}" title="{{log_typeName}}" alt="{{log_typeName}}">
      <span class="dynamicMap_mapPopup-cacheLogUser">{{log_username}}</span> ({{log_date}})
      {{#if log_text}}
        <div class="iw-logText">{{{log_text}}}</div>
      {{/if}}
    </a>
  </div>
  {{/if}}
  <span class="dynamicMap_mapPopup-closer"></span>
</div>
