<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>

<div class="content2-container">

    <div class="content2-pagetitle">
      <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="{{latest_logs}}">
      &nbsp;{{latest_logs}}
    </div>

    <div class="align-right">
      <a class="btn btn-default btn-sm" href="/logmap.php">{{logmap_04}}</a>
    </div>

    <!-- Text container -->
    <p>
        {pages}
    </p>
    <div class="searchdiv">
        <table style="border-collapse: separate; border-spacing: 2px; margin-left: 10px; line-height: 1.4em; font-size: 13px; width: 97%">
            <tr>
                <td><strong>{{date}}</strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><strong>{{geocache}}</strong></td>
                <td><strong>{{logged_by}}</strong></td>
            </tr>
            <tr>
                <td colspan="8"><hr></td>
            </tr>
            {file_content}
            <tr>
                <td colspan="8"><hr></td>
            </tr>
        </table>
    </div>
    <p>
        {pages}
    </p>
</div>
