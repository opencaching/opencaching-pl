        <input type="hidden" name="cacheid" value="{cacheid}"/>
        <font size="1">
        <table class="content" style="font-size: 120%;" width="97%">
            <colgroup>
                <col width="200">
                <col>
            </colgroup>
            {start_przejmij}
            <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/email.png" class="icon32" align="middle" /> <b>{{adopt_10}}</b></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td>
                    <table border='0' width='97%'>
                    <tr>
                        <td colspan="2">
                            {{adopt_11}}<br /><br />
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor='#D5D9FF'>{{adopt_02}}</td>
                        <td bgcolor='#D5D9FF'>{{adopt_03}}</td>
                    </tr>
                    {acceptList}
                    <tr><td colspan='2' height="30"></td></tr>
                    </table>
                </td>
            </tr>
            {end_przejmij}
            <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/email.png" border="0" align="middle" /> <b>{{adopt_00}}</b></td></tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td>
                    <table border='0' width='97%'>
                    <tr>
                        <td colspan="2">
                            <font color="#ff0000">{error_msg}</font>
                            <font color="green">{info_msg}</font>
                            {{adopt_01}}<br /><br />
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor='#D5D9FF'>{{adopt_02}}</td>
                        <td bgcolor='#D5D9FF'>{{adopt_03}}</td>
                    </tr>
                    {cacheList}
                    <tr><td colspan='2' bgcolor='#D5D9FF'></td></tr>
                    </table>
                </td>
            </tr>
            <br />
        </table>
        </font>
