<?php
global $usr;
?>
<div class="content2-pagetitle">
  <?=tr('search_user')?>
</div>
<div class="content2-container">
  {not_found}

  <form name="optionsform" style="display:inline;" action="admin_searchuser.php" method="POST">
    <p><?=tr('username_label')?>:</p>
    <input type="text" name="username" value="{username}" class="form-control input300">
    <button type="submit" name="submit" value="{{search}}" class="btn btn-primary">{{search}}</button>
  </form>
</div>