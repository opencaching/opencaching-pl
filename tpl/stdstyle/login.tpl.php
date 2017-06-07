
<div class="content2-pagetitle">{{login}}</div>

<?php $view->callChunk('infoBar', null, null, $view->errorMsg ); ?>

<form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login_form" dir="ltr"
      style="display: inline;">

    <input type="hidden" name="target" value="<?=$view->target?>">
    <input type="hidden" name="action" value="login">
    <table class="table">
        <colgroup>
            <col style="width:150px;">
            <col>
        </colgroup>
        <tr class="form-group-sm">
            <td class="content-title-noshade">{{user_or_email}}:</td>
            <td><input name="email" maxlength="80" type="text" value="" class="form-control input150"></td>
        </tr>
        <tr class="form-group-sm">
            <td class="content-title-noshade">{{password}}:</td>
            <td><input name="password" maxlength="60" type="password" value="" class="form-control input150"></td>
        </tr>
    </table>
    <input type="reset" name="reset" value="{{reset}}" class="btn btn-default">&nbsp;&nbsp;
    <input type="submit" name="LogMeIn" value="{{login}}" class="btn btn-primary">
</form>
<p class="content-title-noshade">
    {{not_registered}}<br>
    {{forgotten_your_password}}<br>
    {{forGottenEmailAddress}}
</p>
