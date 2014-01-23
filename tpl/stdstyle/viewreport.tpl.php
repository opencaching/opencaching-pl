<script language="javascript" type="text/javascript">
function addtext(obj)
{
    var newtext = obj.value;
    document.myform.email_content.value = newtext;
}
</script>

        <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/rproblems.png" alt="" class="icon32" align="middle"/>&nbsp;{{cache_reports_09}}</div>
    <div class="buffer"></div>
    <p>{confirm_resp_change}{confirm_status_change}</p>
    {email_sent}
    <p>{{cache_reports_02}}: [<a href='viewreports.php'>{{cache_reports_10}}</a>] [<a href='viewreports.php?archiwum=1'>{{cache_reports_11}}</a>]</p>
        <input type="hidden" name="cacheid" value="{cacheid}"/>
        <form action='viewreport.php' method='post' name='myform'>
        <table border='1' class="table" width="90%">
            <tr>
                <th >ID</th>
                <th >{{cache_reports_03}}</th>
                <th >Cache</th>
                <th >{{cache_reports_04}}</th>
                <th >{{cache_reports_05}}</th>
                <th >{{cache_reports_06}}</th>
                <th >{{cache_reports_07}}</th>
                <th >Status</th>
                <th >{{cache_reports_08}}</th>
            </tr>
            {content}
        </table>
        <div class="buffer" style="height:50px;"></div>
        <div class="content2-container line-box">
            <p class="content-title-noshade-size1">{report_text_lbl}</p><br/>
            <p>{report_text}</p>
        </div>
        <div class="content2-container line-box">
            <p class="content-title-noshade-size1">{note_lbl}</p><br/>
            <p>{active_form}</p>
        </div>
        <div class="content2-container line-box">
            <p>{note_area}</p>
        </div>
        <div class="buffer"></div>
        <div class="content2-container line-box">
            <p class="content-title-noshade-size1">{perform_action_lbl}</p>
            <ul>
                {mail_actions}
            </ul>
            <ul>
                {actions}
            </ul>
            <br/>
        </div>
            <p>{{cache_reports_02}}: [<a href='viewreports.php'>{{cache_reports_10}}</a>] [<a href='viewreports.php?archiwum=1'>{{cache_reports_11}}</a>]</p>
        </form>
