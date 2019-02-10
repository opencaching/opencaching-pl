<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="dynamicMap_mapPopup-guide">
    <div class="dynamicMap_mapPopup-table">
        <div class="dynamicMap_mapPopup-tableRow">
            <div class="dynamicMap_mapPopup-tableCell">
                <img src="/tpl/stdstyle/images/free_icons/vcard.png" alt="img">
            </div>
            <div class="dynamicMap_mapPopup-tableCell">
                <a href="{{link}}" class="links" target="_blank">{{username}}</a>
            </div>
        </div>
        <div class="dynamicMap_mapPopup-tableRow">
            <div class="dynamicMap_mapPopup-tableCell"></div>
            <div class="dynamicMap_mapPopup-tableCell">
                {{userDesc}}
            </div>
        </div>
        <div class="dynamicMap_mapPopup-tableRow">
            <div class="dynamicMap_mapPopup-tableCell">
                <img src="/images/rating-star.png" alt="rekomendacje" title="rekomendacje">
            </div>
            <div class="dynamicMap_mapPopup-tableCell">
                {{recCount}} <?=tr('guides_recommendations')?>
            </div>
        </div>
        <div class="dynamicMap_mapPopup-tableRow">
            <div class="dynamicMap_mapPopup-tableCell">
                <img src="/tpl/stdstyle/images/free_icons/email.png" alt="mailTo">
            </div>
            <div class="dynamicMap_mapPopup-tableCell">
                <a class="links" href="/UserProfile/mailTo/{{userId}}" target="_blank"><?=tr('guides_sendemail')?></a>
            </div>
        </div>
    </div>
    <span class="dynamicMap_mapPopup-closer"></span>
</div>
