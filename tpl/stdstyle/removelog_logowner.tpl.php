<?php

?>
<form action="removelog.php" method="post" enctype="application/x-www-form-urlencoded" name="removelog_form" dir="ltr">
    <input type="hidden" name="commit" value="1"/>
    <input type="hidden" name="logid" value="{logid}"/>
    <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" />&nbsp;{{delete_logentry}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></div>
    <div class="buffer"></div>
    <p>{{confirm_remove_log}}?</p>
    <p>{log}</p>
    <p>
        <button type="submit" name="submit" value="{{delete}}" style="font-size:12px;width:140px;"/><b>{{delete}}</b></button>
    </p>
</form>
