<table class="content">
    <tr>
        <td>
            <img src="tpl/stdstyle/images/cache/traditional.png" class="icon32" alt=""  /><font size="4">  <b>Głosowanie do Rady Rejsu 2014</b></font>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td>
            <form action="glosowanie_do_rr.php" method="POST" name="glosowanie" enctype="application/x-www-form-urlencoded" dir="ltr" style="display:inline;">
                <font size="2">
                <br/>
                <b>{vote_warning}Wybierz od 1 do 5 kandydatów z listy i zagłosuj.</b><br/>
                <br/>
                <table cellpadding="2">
                    <tr><td align="center">#</td><td><b>Kandydat</b></td><td><b>Miejscowość</b></td><td><b>Profil</b></td></tr>
                    {candidate_vote_list}
                </table>
                <input type='hidden' name='glosowanie' value='1' />
                <br/><input type='submit' value='Oddaj głos' class="formbuttons" />
                <br/><br/>
                {vote_info}
                </font>
            </form>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
</table>
