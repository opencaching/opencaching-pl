<form action="reportcache.php" method="post">
    <input type="hidden" name="cacheid" value="{cacheid}"/>
    <table class="content">
        <colgroup>
            <col width="200">
            <col>
        </colgroup>
        <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/rproblems.png" class="icon32" align="middle" /> <b>   {{report_01}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td colspan="2" class="info">
                {{report_12}}
                <br />
                [<a href="index.php">{{main_page}}</a>]&nbsp;[<a href="viewcache.php?cacheid={cacheid}">{{back_to_the_geocache_listing}}</a>]
            </td></tr>

        <tr><td class="spacer" colspan="2"></td></tr>
    </table>
</form>
