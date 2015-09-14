<?php

?>
<form action="query.php" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="action" value="save" />
    <input type="hidden" name="queryid" value="{queryid}" />
    <input type="hidden" name="submit" value="1" />
    <table class="content">
        <colgroup>
            <col width="150">
            <col>
        </colgroup>
        <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/save.png" class="icon32" alt="Zapamiętaj szukanie" title="Zapamiętaj szukanie" align="middle" /> <b>{{save_queries}}</b></td></tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        {nameerror}
        <tr>
            <td>{{name_queries}}</td>
            <td>
                <input type="text" name="queryname" class="input200" maxlength="60" value="{queryname}" />
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button type="submit" value="Zapamietaj" style="font-size:12px;width:140px;"/><b>{{store}}</b></button>

            </td>
        </tr>
    </table>
</form>
<form action="query.php" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="action" value="saveas" />
    <input type="hidden" name="queryid" value="{queryid}" />
    <input type="hidden" name="submit" value="1" />
    <table class="content">
        <colgroup>
            <col width="150">
            <col>
        </colgroup>
        <tr>
            <td class="header-small" colspan="2">{{old_options}}</td>
        </tr>
        <tr>
            <td>{{name_queries}}</td>
            <td>
                <select name="oldqueryid" class="input300">
                    <option value="0">-- {selecttext} --</option>
                    {oldqueries}
                </select>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button type="submit" value="Zapamietaj" style="font-size:12px;width:140px;"/><b>{{store}}</b></button>

            </td>
        </tr>
    </table>
</form>
