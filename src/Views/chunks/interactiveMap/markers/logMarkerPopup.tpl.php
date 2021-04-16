<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="imp-cache-log">
    {{#if sectionName}}
    <div class="imp-table ipm-section">
        <div class="imp-row">
            <div class="imp-cell ipm-section">{{sectionName}}</div>
        </div>
    </div>
    {{/if}}
    <div class="imp-table">
        <div class="imp-row">
            <div class="imp-cell imp-cache-icon">
                <img src="{{icon}}" alt="{{wp}}" title="{{wp}}">
            </div>
            <div class="imp-cell">
                <a href="{{link}}" class="links" target="_blank">{{name}}</a>
                {{#if username}}({{username}}){{/if}}
            </div>
        </div>
        {{#if logLink}}
        <div class="imp-row imp-block">
            <div class="imp-cell imp-log-icon">
                <img src="{{logIcon}}" title="{{logTypeName}}" alt="{{logTypeName}}">
            </div>
            <div class="imp-cell">
                <a href="{{logLink}}" class="links" target="_blank">
                    <span class="imp-user">{{logUsername}}</span> ({{logDate}})
                    {{#if logText}}
                    <div class="imp-content">{{{logText}}}</div>
                    {{/if}}
                </a>
            </div>
        </div>
        {{/if}}
    </div>
  <span class="imp-closer"></span>
</div>
{{#if showNavi}}
<div class="imp-navi">
    <div class="imp-backward">
        <img src="/images/blue/arrow2.png" alt="&lt;" title="<?= tr('map_popup_previous'); ?>">
    </div>
    <div class="imp-forward">
        <img src="/images/blue/arrow2.png" alt="&gt;" title="<?= tr('map_popup_next'); ?>">
    </div>
</div>
{{/if}}
