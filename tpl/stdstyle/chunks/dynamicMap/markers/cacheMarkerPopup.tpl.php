<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="dmp-cache">
    <div class="dmp-table">
        <div class="dmp-row">
            <div class="dmp-cell dmp-icon">
                <img src="{{icon}}" alt="{{wp}}" title="{{wp}}">
            </div>
            <div class="dmp-cell">
                <a href="{{link}}" class="links" target="_blank">
                    <div class="dmp-wp">{{wp}}</div>
                    {{name}}
                </a>
            </div>
        </div>
    </div>
    <div class="dmp-table dmp-details">
        <div class="dmp-row">
            <div class="dmp-cell">
                {{#if isEvent}}
                <span class="dmp-label"><?=tr("beginning")?>:</span>&nbsp;{{eventStartDate}}
                {{else}}
                <span class="dmp-label"><?=tr("size")?>:</span>&nbsp;{{size}}
                {{/if}}
            </div>
            <div class="dmp-cell dmp-counter">
                {{#if isEvent}}
                <img src="/tpl/stdstyle/images/log/attend.svg" alt="<?=tr("attendends")?>"> x {{founds}} <?=tr("attendends")?>
                {{else}}
                <img src="/tpl/stdstyle/images/log/found.svg" alt="<?=tr('found')?>"> x {{founds}} <?=tr('found')?>
                {{/if}}
            </div>
        </div>
        <div class="dmp-row">
            <div class="dmp-cell">
                {{#if rating}}<span class="dmp-label"><?=tr('score')?>:</span>&nbsp;{{rating}}</a>{{/if}}
            </div>
            <div class="dmp-cell dmp-counter">
                {{#if isEvent}}
                <img src="/tpl/stdstyle/images/log/will_attend.svg" alt="<?=tr("will_attend")?>"> x {{notFounds}} <?=tr("will_attend")?>
                {{else}}
                <img src="/tpl/stdstyle/images/log/dnf.svg" alt="<?=tr('not_found')?>"> x {{notFounds}} <?=tr('not_found')?>
                {{/if}}
            </div>
        </div>
        <div class="dmp-row">
            <div class="dmp-cell">
                {{#if username}}<a href="{{userProfile}}" class="links" alt="{{username}}" title="{{username}}"><span class="dmp-label"><?=tr('owner')?>:</span>&nbsp;{{username}}</a>{{/if}}
            </div>
            <div class="dmp-cell dmp-counter">
                <img src="/tpl/stdstyle/images/free_icons/thumb_up.png" alt="<?=tr("scored")?>"> {{ratingVotes}} x <?=tr("scored")?>
            </div>
        </div>
        {{#if isStandingOut}}
        <div class="dmp-row">
            <div class="dmp-cell">
                {{#if titledDesc}}
                <img src="/tpl/stdstyle/images/free_icons/award_star_gold_1.png" alt="{{titledDesc}}"> <span class="dmp-label">{{titledDesc}}</span>
                {{/if}}
            </div>
            <div class="dmp-cell dmp-counter">
                {{#if recommendations}}
                <img src="/images/rating-star.png" alt="<?=tr("recommended")?>"> {{recommendations}} x <?=tr("recommended")?>
                {{/if}}
            </div>
        </div>
        {{/if}}
    </div>
    {{#if powerTrailName}}
    <div class="dmp-table dmp-details">
        <div class="dmp-row">
            <div class="dmp-narrow-cell">
                <span class="dmp-label"><?=tr("pt000")?>:</span>
            </div>
            <div class="dmp-cell">
                <a href="{{powerTrailUrl}}" title="{{powerTrailName}}" target="_blank" class="links">
                    <img src="{{powerTrailIcon}}" alt="<?=tr("pt000")?>" title="{{powerTrailName}}"> {{powerTrailName}}
                </a>
            </div>
        </div>
    </div>
    {{/if}}
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
