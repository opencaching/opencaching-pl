
<div class="content2-pagetitle">{{login}}</div>

<?php $view->callChunk('infoBar', null, null, $view->errorMsg ); ?>

<form action="/login.php?action=login" method="post" enctype="application/x-www-form-urlencoded"
      name="login_form" dir="ltr" style="display: inline;">

    <input type="hidden" name="target" value="<?=$view->target?>">
    <table class="table">
        <colgroup>
            <col style="width:150px;">
            <col>
        </colgroup>
        <tr class="form-group-sm">
            <td class="content-title-noshade">{{loginForm_userOrEmail}}:</td>
            <td>
              <input name="email" maxlength="80" type="text" value="" class="form-control input150">
            </td>
        </tr>
        <tr class="form-group-sm">
            <td class="content-title-noshade">{{password}}:</td>
            <td>
              <input name="password" maxlength="60" type="password" value="" class="form-control input150">
            </td>
        </tr>
    </table>
    <input type="reset" name="reset" value="{{reset}}" class="btn btn-default">
    <input type="submit" value="{{login}}" class="btn btn-primary">
</form>
<p class="content-title-noshade">
    {{loginForm_notRegistered}}&nbsp;&nbsp;<a href="register.php">{{registration}}</a><br>

    {{loginForm_lostPassword}}&nbsp;&nbsp;<a href="newpw.php">Nowe hasłoXXX</a><br>

    <!-- {{loginForm_lostEmail}} -->
</p>
