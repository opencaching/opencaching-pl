
<script src="lib/js/wz_tooltip.js"></script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/event.png" class="icon32" alt="Cache" title="Cache">&nbsp;{{incomming_events}}</div>
<!-- Text container -->
<p>&nbsp;</p>
<div class="searchdiv">
    <table style="border-collapse: separate; border-spacing: 2px; margin-left: 10px; line-height: 1.4em; font-size: 13px; width:97%">
        <tr>
            <td colspan="2"><strong>{{date}}</strong></td>
            <td><strong>{{event}}</strong></td>
            <td><strong>{{owner}}</strong>&nbsp;&nbsp;&nbsp;</td>
            <td colspan="3"><strong>{{latest_logs}}</strong></td>
        </tr>
        <tr>
            <td colspan="7"><hr></td>
        </tr>
        {file_content}
        <tr>
            <td colspan="7"><hr></td>
        </tr>
    </table>
</div>

<!-- End Text Container -->
