<?php

?>
<div class="content2-pagetitle"><img src="/images/blue/save.png" class="icon32" alt="" />&nbsp;{{save_queries}}</div>

<form action="query.php" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="action" value="save" />
    <input type="hidden" name="queryid" value="{queryid}" />
    <input type="hidden" name="submit" value="1" />
    <table class="table">
        <colgroup>
            <col width="180">
            <col>
        </colgroup>
        {nameerror}
        <tr class="form-group-sm">
            <td>{{name_queries}}</td>
            <td>
                <input type="text" name="queryname" class="form-control input200" maxlength="60" value="{queryname}" />
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button type="submit" value="Zapamietaj" class="btn btn-primary btn-sm"/>{{store}}</button>

            </td>
        </tr>
    </table>
</form>
<br /><br />
<form action="query.php" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="action" value="saveas" />
    <input type="hidden" name="queryid" value="{queryid}" />
    <input type="hidden" name="submit" value="1" />
    <table class="table">
        <colgroup>
            <col width="180">
            <col>
        </colgroup>
        <tr>
            <td class="header-small" colspan="2"><b>{{old_options}}</b></td>
        </tr>
        <tr class="form-group-sm">
            <td>{{name_queries}}</td>
            <td>
                <select name="oldqueryid" class="form-control input300">
                    <option value="0">{selecttext}</option>
                    {oldqueries}
                </select>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button type="submit" value="Zapamietaj" class="btn btn-primary btn-sm"/>{{store}}</button>

            </td>
        </tr>
    </table>
</form>
