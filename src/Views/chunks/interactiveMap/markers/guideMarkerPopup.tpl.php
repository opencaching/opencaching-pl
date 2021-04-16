<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="imp-guide">
    <div class="imp-table">
        <div class="imp-row">
            <div class="imp-cell">
                <img src="/images/free_icons/vcard.png" alt="img">
            </div>
            <div class="imp-cell">
                <a href="{{link}}" class="links" target="_blank">{{username}}</a>
            </div>
        </div>
        <div class="imp-row">
            <div class="imp-cell"></div>
            <div class="imp-cell">
                <div class="imp-desc">
                    {{{userDesc}}}
                </div>
            </div>
        </div>
        <div class="imp-row">
            <div class="imp-cell">
                <img src="/images/rating-star.png" alt="*" title="*">
            </div>
            <div class="imp-cell">
                {{recCount}} <?= tr('guides_recommendations'); ?>
            </div>
        </div>
        <div class="imp-row">
            <div class="imp-cell">
                <img src="/images/free_icons/email.png" alt="mailTo">
            </div>
            <div class="imp-cell">
                <a class="links" href="/UserProfile/mailTo/{{user_id}}" target="_blank"><?= tr('guides_sendemail'); ?></a>
            </div>
        </div>
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
