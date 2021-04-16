<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="imp-cache">
    {{#if sectionName}}
    <div class="imp-table ipm-section">
        <div class="imp-row">
            <div class="imp-cell ipm-section">{{sectionName}}</div>
        </div>
    </div>
    {{/if}}
    <div class="imp-table">
        <div class="imp-row">
            <div class="imp-cell imp-icon">
                <img src="{{icon}}" alt="{{wp}}" title="{{wp}}">
            </div>
            <div class="imp-cell">
                <a href="{{link}}" class="links" target="_blank">
                    <div class="imp-wp">{{wp}}</div>
                    {{name}}
                </a>
            </div>
        </div>
    </div>
    <div class="imp-table imp-details">
        <div class="imp-row">
            <div class="imp-cell">
                {{#if isEvent}}
                <span class="imp-label"><?= tr('beginning'); ?>:</span>&nbsp;{{eventStartDate}}
                {{else}}
                <span class="imp-label"><?= tr('size'); ?>:</span>&nbsp;{{size}}
                {{/if}}
            </div>
            <div class="imp-cell imp-counter">
                {{#if isEvent}}
                <img src="/images/log/attend.svg" alt="<?= tr('attendends'); ?>"> x {{founds}} <?= tr('attendends'); ?>
                {{else}}
                <img src="/images/log/found.svg" alt="<?= tr('found'); ?>"> x {{founds}} <?= tr('found'); ?>
                {{/if}}
            </div>
        </div>
        <div class="imp-row">
            <div class="imp-cell">
                {{#if rating}}<span class="imp-label"><?= tr('score'); ?>:</span>&nbsp;{{rating}}</a>{{/if}}
            </div>
            <div class="imp-cell imp-counter">
                {{#if isEvent}}
                <img src="/images/log/will_attend.svg" alt="<?= tr('will_attend'); ?>"> x {{notFounds}} <?= tr('will_attend'); ?>
                {{else}}
                <img src="/images/log/dnf.svg" alt="<?= tr('not_found'); ?>"> x {{notFounds}} <?= tr('not_found'); ?>
                {{/if}}
            </div>
        </div>
        <div class="imp-row">
            <div class="imp-cell">
                {{#if username}}<a href="{{userProfile}}" class="links" alt="{{username}}" title="{{username}}"><span class="imp-label"><?= tr('owner'); ?>:</span>&nbsp;{{username}}</a>{{/if}}
            </div>
            <div class="imp-cell imp-counter">
                <img src="/images/free_icons/thumb_up.png" alt="<?= tr('scored'); ?>"> {{ratingVotes}} x <?= tr('scored'); ?>
            </div>
        </div>
        {{#if isStandingOut}}
        <div class="imp-row">
            <div class="imp-cell">
                {{#if titledDesc}}
                <img src="/images/free_icons/award_star_gold_1.png" alt="{{titledDesc}}"> <span class="imp-label">{{titledDesc}}</span>
                {{/if}}
            </div>
            <div class="imp-cell imp-counter">
                {{#if recommendations}}
                <img src="/images/rating-star.png" alt="<?= tr('recommended'); ?>"> {{recommendations}} x <?= tr('recommended'); ?>
                {{/if}}
            </div>
        </div>
        {{/if}}
    </div>
    {{#if powerTrailName}}
    <div class="imp-table imp-details">
        <div class="imp-row">
            <div class="imp-narrow-cell">
                <span class="imp-label"><?= tr('pt000'); ?>:</span>
            </div>
            <div class="imp-cell">
                <a href="{{powerTrailUrl}}" title="{{powerTrailName}}" target="_blank" class="links">
                    <img src="{{powerTrailIcon}}" alt="<?= tr('pt000'); ?>" title="{{powerTrailName}}"> {{powerTrailName}}
                </a>
            </div>
        </div>
    </div>
    {{/if}}
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
