<table class="content">
    <tr>
        <td class="content2-pagetitle">
            <img src="tpl/stdstyle/images/blue/write.png" class="icon32" alt=""  /><font size="4">  <b>{{send_bulletin}}</b></font>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td>
            <form action='admin_bulletin.php' method='POST'>
                <textarea name='bulletin' cols='80' rows='15'></textarea>
                <br />
                <button type="submit"  value="Wyślij biuletyn" class="btn btn-primary"/>{{send}}</button>

            </form>
        </td>
    </tr>
</table>
