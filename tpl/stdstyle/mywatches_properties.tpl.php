<?php

?>
<script type="text/javascript">
    function intervalChanged()
    {
        var interval = document.getElementById('interval');
        var hour = document.getElementById('hour');
        var weekday = document.getElementById('weekday');

        switch (interval.selectedIndex)
        {
            case 0: // sofort
                hour.options[0].selected = true;
                weekday.options[0].selected = true;
                weekday.disabled = true;
                hour.disabled = true;
                break;
            case 1: // taeglich
                weekday.disabled = true;
                hour.disabled = false;
                break;
            case 2: // woechentlich
                weekday.disabled = false;
                hour.disabled = false;
                break;
        }
    }
</script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/clock.png" class="icon32" alt=""  />&nbsp;{{settings_notifications}}</div>
<div class="searchdiv">
    {commit}
    <form action="mywatches.php" method="post" enctype="application/x-www-form-urlencoded" name="forgot_pw_form" dir="ltr" style="display: inline;">
        <input type="hidden" name="rq" value="properties">
        <table class="table">
            <colgroup>
                <col width="150">
                <col>
            </colgroup>
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade">{{send}}:</td>
                <td>
                    <select id="interval" name="interval" onChange="intervalChanged();" class="input120">
                        {intervalls}
                    </select>
                </td>
            </tr>
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade">{{hour}}</td>
                <td>
                    <select id="hour" name="hour">
                        {houroptions}
                    </select>
                </td>
            </tr>
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td class="content-title-noshade">{{weekday}}</td>
                <td>
                    <select id="weekday" name="weekday" class="input100">
                        {weekdays}
                    </select>
                </td>
            </tr>
        </table>

        <div class="buffer"></div>

        <button type="submit" name="submit" id="submit" value="{{submit}}" style="font-size:12px;width:140px;"/><b>{{store}}</b></button>

    </form>
</div>
<script type="text/javascript">
    intervalChanged();
</script>

