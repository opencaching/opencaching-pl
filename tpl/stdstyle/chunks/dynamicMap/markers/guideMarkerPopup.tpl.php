<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="guideMarkerPopup">
    <div class="guideMarkerPopup-table">
        <div class="guideMarkerPopup-tableRow">
            <div class="guideMarkerPopup-tableCell">
                <img src="/tpl/stdstyle/images/free_icons/vcard.png" alt="img">
            </div>
            <div class="guideMarkerPopup-tableCell">
                <a href="{{link}}" class="links" target="_blank">{{username}}</a>
            </div>
        </div>
        <div class="guideMarkerPopup-tableRow">
            <div class="guideMarkerPopup-tableCell"></div>
            <div class="guideMarkerPopup-tableCell">
                {{userDesc}}
            </div>
        </div>
        <div class="guideMarkerPopup-tableRow">
            <div class="guideMarkerPopup-tableCell">
                <img src="/images/rating-star.png" alt="rekomendacje" title="rekomendacje">
            </div>
            <div class="guideMarkerPopup-tableCell">
                {{recCount}} {{_tr_guides_recommendations}}
            </div>
        </div>
        <div class="guideMarkerPopup-tableRow">
            <div class="guideMarkerPopup-tableCell">
                <img src="/tpl/stdstyle/images/free_icons/email.png" alt="mailTo">
            </div>
            <div class="guideMarkerPopup-tableCell">
                <a class="links" href="/UserProfile/mailTo/{{user_id}}" target="_blank">{{_tr_guides_sendemail}}</a>
            </div>
        </div>
    </div>
    <span class="dynamicMap_mapPopup-closer"></span>
</div>
