<?php

?>
<form name="descform" action="removedesc.php" method="get" enctype="application/x-www-form-urlencoded" id="removedesc_form" dir="ltr">
    <input type="hidden" name="cacheid" value="{cacheid}"/>
    <input type="hidden" name="desclang" value="{desclang}"/>
    <input type="hidden" name="commit" value="1"/>

    <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/trash.png" class="icon32" alt="" title="" align="middle" />&nbsp;{{remove_description}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></div>
    <p>&nbsp;</p>
    <p class="warningmsg">{{remove_desc_01}} &quot;<b><i><span class="errormsg">{desclang_name}</span></i></b>&quot; 
    {{remove_desc_02}} &quot;<b>{cachename}</b>&quot;
    {{remove_desc_03}}
    </p>

<p>&nbsp;</p>
    <div>
        <input type="submit" name="submitform" value="{{remove_desc_04}}" class="formbuttons"/>&nbsp;&nbsp;&nbsp;
    </div>
    <p>&nbsp;</p>
    <p><a href="javascript:;" onclick="history.go(-1); return true;">{{back}}</a></p>
</form>