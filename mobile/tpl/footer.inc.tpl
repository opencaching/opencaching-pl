<hr/>
<div id="footer">
    <div style="padding: 0 5px">
        <div class='menu'>
            <table class="tablefooter" style="width: 100%">
                <tr>
                    <td class="button" style="width:50%"><a href="./about.php" >{$about}</a></td>
                    <td class="button" style="width:50%"><a href="./contact.php" >{$contact}</a> </td></tr>
                <tr><td colspan="2" class="button"><a href="./index.php" >{$main_page}</a> </td></tr>
                <tr><td colspan="2" class="button"><a href="{$absolute_server_url}/index.php?mobile=false" >{$pc_ver}</a></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footertitle">m.{$site_name}</div>

    <table class="tablefooter">
        <tr>
            {foreach $languages as $language}
                <td class="button" style="width:25%"><a href="./?lang={$language}" >{$language|upper}</a></td>
            {/foreach}
        </tr>
    </table>

</div>

</body>

</html>
