
<script src="lib/js/wz_tooltip.js"></script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="New Log entry" align="middle"/>&nbsp;{{latest_logs_in_caches}}: {username}</div>
<!-- Text container -->
<p>
    {pages}
</p>
<div class="searchdiv">
    <table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="97%">
        <tr>
            <td><strong>{{date}}</strong></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><strong>{{cache}}</strong></td>
            <td><strong>{{logged_by}}</strong></td>
        </tr>
        <tr>
            <td colspan="7"><hr></hr></td>
        </tr>
        {file_content}
        <tr>
            <td colspan="7"><hr></hr></td>
        </tr>
    </table>
</div>
<p>
    {pages}
</p>
