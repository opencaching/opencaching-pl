<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="dmp-guide">
    <div class="dmp-table">
        <div class="dmp-row">
            <div class="dmp-cell">
                <img src="/tpl/stdstyle/images/free_icons/vcard.png" alt="img">
            </div>
            <div class="dmp-cell">
                <a href="{{link}}" class="links" target="_blank">{{username}}</a>
            </div>
        </div>
        <div class="dmp-row">
            <div class="dmp-cell"></div>
            <div class="dmp-cell">
                <div class="dmp-desc">
                    {{{userDesc}}}
                </div>
            </div>
        </div>
        <div class="dmp-row">
            <div class="dmp-cell">
                <img src="/images/rating-star.png" alt="*" title="*">
            </div>
            <div class="dmp-cell">
                {{recCount}} <?=tr('guides_recommendations')?>
            </div>
        </div>
        <div class="dmp-row">
            <div class="dmp-cell">
                <img src="/tpl/stdstyle/images/free_icons/email.png" alt="mailTo">
            </div>
            <div class="dmp-cell">
                <a class="links" href="/UserProfile/mailTo/{{user_id}}" target="_blank"><?=tr('guides_sendemail')?></a>
            </div>
        </div>
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
