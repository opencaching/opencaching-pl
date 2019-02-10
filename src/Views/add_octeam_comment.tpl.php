<table class="content">
    <tr>
        <td class="content2-pagetitle">
            <img src="tpl/stdstyle/images/blue/write.png" class="icon32" alt="" /><font size="4"> {{add_rr_comment}} "<a href="viewcache.php?cacheid={cacheid}">{cachename}</a>"</font>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td>
            <form action='/viewcache.php?cacheid={cacheid}' method='post'>
                <input type='hidden' name='cacheid' value='{cacheid}' />
                <textarea name='rr_comment' cols='80' rows='15'></textarea>
                <br/><br/>
                <button type="submit" value="Dodaj komentarz" class="btn btn-primary">{{send}}</button>&nbsp;&nbsp;
                <button type="button" class="btn btn-default" onclick="window.location.href = '/viewcache.php?cacheid={cacheid}'">{{cancel}}</button>
            </form>
        </td>
    </tr>
</table>
