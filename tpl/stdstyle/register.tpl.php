<?php
use Utils\Uri\SimpleRouter;

?>
<div class="content2-pagetitle">
  {{register_pageTitle}}
</div>

<div class="content2-container">
  <div class="notice">
    {{register_msg1}}
  </div>
  <form name="register" action="register.php" method="post" id="register">
    <div class="form-group-sm">
      <label for="username">{{username_label}}</label>
      <input type="text" name="username" id="username" maxlength="60" value="{username}" class="form-control input200" placeholder="{{register00}}" required="required" autocomplete="username">
      <span style="font-size: 15px; color: red;">*</span>
      {username_message}
    </div>
    <div class="form-group-sm">
      <label for="email">{{email_address}}</label>
      <input type="email" name="email" maxlength="80" id="email" value="{email}" class="form-control input200" placeholder="{{register01}}" required="required" autocomplete="email">
      <span style="font-size: 15px; color: red;">*</span>
      {email_message}
    </div>
    <div class="form-group-sm">
      <label for="password1">{{password}}</label>
      <input type="password" name="password1" maxlength="80" id="password1" class="form-control input200" placeholder="{{register02}}" required="required" autocomplete="new-password">
      <span style="font-size: 15px; color: red;">*</span>
      {password_message}
    </div>
    <div class="form-group-sm">
      <label for="password2">{{password_confirm}}</label>
      <input type="password" name="password2" maxlength="80" id="password2" class="form-control input200" placeholder="{{register03}}" required="required" autocomplete="new-password">
      <span style="font-size: 15px; color: red;">*</span>
    </div>
    <div>
      <input type="checkbox" name="TOS" value="ON">
      <span style="font-size: 15px; color: red;">*</span>
      {{register_msg4}}
      <br>{tos_message}
    </div>
    <div>
      {{register_msg2}}
    </div>
    <div class="notice">
      {{register_msg3}}
    </div>
    <div class="notice">
      {{register_msg7}}
    </div>
    <div class="align-center">
      <input type="submit" name="submit" value="{{registration}}" class="btn btn-md btn-primary">
      <a href="<?=SimpleRouter::getLink('UserAuthorization', 'newPassword')?>" class="btn btn-md btn-default"><?=tr('loginForm_resetPassword')?></a>
    </div>
  </form>
</div>