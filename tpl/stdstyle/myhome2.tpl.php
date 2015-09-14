<?php

?>
<div class="searchdiv">
    <table class="content" >

        <tr><td class="content2-pagetitle" colspan="2"><font size="4">  <b>Logi {username}</b></font></td></tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr>
            <td class="header-small" colspan="2">
                <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="Logs" title="Logs"/>&nbsp;
                <span class="content-title-noshade-size1"> Znalazłeś {founds} skrzynek.
                    {events}
                    [<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=byname&amp;finderid={userid}&amp;searchbyfinder=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">Pokaż wszystkie</a>]</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                {reports}<br /><br />
                <span class="content-title-noshade-size2">
                    <b>{{your_newest_log_entries}}:</b><br />
                </span>
                <table class="table">
                    {lastlogs}
                </table>
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>



    </table>
</div>
