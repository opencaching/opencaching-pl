<table class="content">
    <tr>
        <td class="content2-pagetitle">
            <img src="tpl/stdstyle/images/blue/write.png" class="icon32" alt="" /><font size="4"> {{add_comment_OCTeam_label}} "<a href="viewcache.php?cacheid={cacheid}">{cachename}</a>"</font>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td>
            <form action='viewcache.php' method='post'>
                <input type='hidden' name='cacheid' value='{cacheid}' />
                <textarea name='rr_comment' cols='80' rows='15'></textarea>
                <br />
                <input type='submit' value='Dodaj komentarz' />
                <a href='viewcache.php?cacheid={cacheid}'>Anuluj</a>
            </form>
        </td>
    </tr>
</table>
