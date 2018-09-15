<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="iw-container">
  <img class="iw-icon dynamicMap_mapPopup-cacheIcon" src="{{icon}}" alt="{{wp}}" title="{{wp}}">
  <a href="{{link}}" class="links" target="_blank">
    <span class="iw-name">{{name}}</span></a>
  {{#if username}}({{username}}){{/if}}
  <span class="dynamicMap_mapPopup-closer"></span>
</div>
