<?php //This is handlebars-js template - see https://handlebarsjs.com/ for format details ?>

<div class="guideMarkerPopup">
  <div class="username">
    <img src="/tpl/stdstyle/images/free_icons/vcard.png" alt="img">
    <a href="{{link}}" class="links" target="_blank">
      <span class="iw-name">{{username}}</span></a>
  </div>
  <div class="userDesc">{{userDesc}}</div>
  <div class="mailTo">
    <img src="/tpl/stdstyle/images/free_icons/email.png" alt="mailTo">
    <a class="links" href="/UserProfile/mailTo/{{user_id}}">Napisz e-mail</a>
  </div>
</div>
