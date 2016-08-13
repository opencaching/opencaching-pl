    <script src="{reportcache_js}"></script>
<form action="reportcache.php" method="post">
    <input type="hidden" name="cacheid" value="{cacheid}"/>
    <table class="content">
        <colgroup>
            <col width="200" />
            <col/>
        </colgroup>
        <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/rproblems.png" class="icon32" align="middle" alt="" /> <b>    {{report_01}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td colspan="2" class="info">
            </td></tr>
        <tr><td colspan="2">
                <b>{{report_02}}</b><br />
                <input onclick="hiddeCheck()" type="radio" name="adresat" id="adresat1" value="owner" checked="checked" /><label for="adresat1">{{report_03}}</label><br />
                <input onclick="showCheck()" type="radio" name="adresat" id="adresat2" value="rr" /><label for="adresat2">{{report_04}}</label><br />
                <br />
                <font color="#ff0000">{{report_05}}
                <br /><br />
                </font>

            </td></tr>
        <tr>
            <td colspan="2">{{report_06}}
                <select name="reason">
                    <option value="0" selected="selected">====={{select}}=====</option>
                    <option value="1" >{{report_07}}</option>
                    <option value="2" >{{report_08}}</option>
                    <option value="3" >{{report_09}}</option>
                    <option value="4" >{{report_10}}</option>
                </select>{noreason_error}
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td colspan="2">{{report_11}}</td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea class="logs" name="text" cols="68" rows="15"></textarea>
            </td>
        </tr>

        <tr><td class="spacer" colspan="2"></td></tr>

        <tr><td class="spacer" colspan="2"></td></tr>


        <tr>
            <td class="header-small" colspan="2">
                <input style="visibility:hidden;" onclick="statementChange()" type="checkbox" id="statement">
                <label style="visibility:hidden;" for="statement" id="statement_label">{{report_13}}</label><br /><br />
                <input type="reset" name="cancel" value={{reset}} class="formbuttons"/>&nbsp;&nbsp;
                <input id="sender" type="submit" name="ok" value={{submit}} zgłoszenie" class="formbuttons"/>
            </td>
        </tr>

        <tr><td class="spacer" colspan="2"></td></tr>
    </table>
</form>
