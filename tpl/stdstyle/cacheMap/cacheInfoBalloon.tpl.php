<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div id="balloonHeader">
  <div id="cacheName">
    <a title="{{cacheName}}" href="{{cacheUrl}}" target="_blank">
      <img src="{{cacheIcon}}" alt="cache-icon">
      {{cacheName}}
    </a>
  </div>

  <div id="cacheCode">
    <a href="/viewcache.php?wp={{cacheCode}}&print_list=y" target="_blank">
      <img src="/images/actions/list-add-16.png" title="<?=tr("add_to_list")?>" alt="<?=tr("add_to_list")?>">
    </a>
    <strong>{{cacheCode}}</strong>
  </div>
</div>

<div>
  <div id="cacheParams">
  {{#unless isEvent}}
    <div>
      <strong><?=tr("size")?>:</strong> {{cacheSizeDesc}}
    </div>
  {{/unless}}

  {{#if ratingDesc}}
    <div>
      <strong><?=tr("score")?>:</strong> {{ratingDesc}}
    </div>
  {{/if}}

    <div>
      <strong><?=tr("owner")?>:</strong>
      <a href="{{ownerProfileUrl}}" target="_blank">{{ownerName}}</a>
    </div>

  {{#if isEvent}}
    <div>
      <strong><?=tr("beginning")?>:</strong> {{eventStartDate}}
    </div>
  {{/if}}
  </div>

  <div id="cacheCounters">
    <div>
    {{#if isEvent}}
      <img src="/tpl/stdstyle/images/log/16x16-attend.png" alt="<?=tr("attendends")?>">
      {{cacheFounds}} x <?=tr("attendends")?>
    {{else}}
      <img src="/tpl/stdstyle/images/log/16x16-found.png" alt="<?=tr("found")?>">
      {{cacheFounds}} x <?=tr("found")?>
    {{/if}}
    </div>

    <div>
    {{#if isEvent}}
      <img src="/tpl/stdstyle/images/log/16x16-will_attend.png" alt="<?=tr("will_attend")?>">
      {{cacheNotFounds}} x <?=tr("will_attend")?>
    {{else}}
      <img src="/tpl/stdstyle/images/log/16x16-dnf.png" alt="<?=tr("not_found")?>">
      {{cacheNotFounds}} x <?=tr("not_found")?>
    {{/if}}
    </div>

    <div>
      <img src="/tpl/stdstyle/images/free_icons/thumb_up.png" alt="<?=tr("scored")?>">
        {{cacheRatingVotes}} x <?=tr("scored")?>
      </div>

  {{#if cacheRecosNumber}}
    <div>
      <img src="/images/rating-star.png" alt="<?=tr("recommended")?>">
      {{cacheRecosNumber}} x <?=tr("recommended")?>
    </div>
  {{/if}}

  {{#if titledDesc}}
    <div>
      <img src="/tpl/stdstyle/images/free_icons/award_star_gold_1.png" alt="{{titledDesc}}">
      {{titledDesc}}
    </div>
  {{/if}}
  </div>
</div>

{{#if powerTrailName}}
<div>
  <div id="cachePtLabel">
    <div>
      <strong><?=tr("pt000")?>:</strong>
    </div>
  </div>

  <div id="cachePT">
    <a href="{{powerTrailUrl}}" title="{{powerTrailName}}" target="_blank">
    <img src="{{powerTrailIcon}}" alt="<?=tr("pt000")?>" title="{{powerTrailName}}">
    {{powerTrailName}}</a>
  </div>
</div>
{{/if}}
