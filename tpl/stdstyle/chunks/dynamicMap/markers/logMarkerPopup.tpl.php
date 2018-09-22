<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="dmp-cache-log">
    <div class="dmp-table">
        <div class="dmp-row">
            <div class="dmp-cell dmp-cache-icon">
                <img src="{{icon}}" alt="{{wp}}" title="{{wp}}">
            </div>
            <div class="dmp-cell">
                <a href="{{link}}" class="links" target="_blank">{{name}}</a>
                {{#if username}}({{username}}){{/if}}
            </div>
        </div>
        {{#if log_link}}
        <div class="dmp-row dmp-block">
            <div class="dmp-cell dmp-log-icon">
                <img src="{{log_icon}}" title="{{log_typeName}}" alt="{{log_typeName}}">
            </div>
            <div class="dmp-cell">
                <a href="{{log_link}}" class="links" target="_blank">
                    <span class="dmp-user">{{log_username}}</span> ({{log_date}})
                    {{#if log_text}}
                    <div class="dmp-content">{{{log_text}}}</div>
                    {{/if}}
                </a>
            </div>
        </div>
        {{/if}}
    </div>
  <span class="dmp-closer"></span>
</div>
{{#if showNavi}}
<div class="dmp-navi">
    <div class="dmp-backward">
        <img src="/tpl/stdstyle/images/blue/arrow2.png" alt="&lt;" title="<?=tr('map_popup_next')?>">
    </div>
    <div class="dmp-forward">
        <img src="/tpl/stdstyle/images/blue/arrow2.png" alt="&gt;" title="<?=tr('map_popup_next')?>">
    </div>
</div>
{{/if}}
