<?php
/* * *************************************************************************
  ./tpl/stdstyle/voting.tpl.php
  -------------------
  begin                : October 28 2008
  copyright            : (C) 2008 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder ??

  view another players profile

 * ************************************************************************** */
?>
<table class="content" border="0">
    <colgroup>
        <col width="260">
        <col>
    </colgroup>
    <tr><td class="content2-pagetitle" colspan="2">
            <table border="0" class="null">
                <tr>
                    <td><img src="tpl/stdstyle/images/blue/home.png" class="icon32" alt="Głosowanie" title="Głosowanie" align="middle" /><font size="4">  <b>Głosowanie - problem {id}</b></font>
                    </td>
                    <td width="250" align="right"><a href="mailto.php?uuid='{uuid}'"><img src="/tpl/stdstyle/images/blue/email.png" class="icon32" alt="Email" title="Email" align="middle" /></a>&nbsp;[<a href="mailto.php?userid={userid}">Email do użytkownika</a>]
                    </td>
                </tr>
            </table>
        </td></tr>

    <tr>
        <td align="left" class="header-small" colspan="2">
            [<a href="cachemap2.php?userid={userid}">Pokaż mapę użytkownika</a>]
        </td>
    </tr>
    {stat_ban}

    <tr><td class="spacer" colspan="2"></td></tr>

    <tr><td class="spacer" colspan="2"></td></tr>
    {opis_start}
    <tr>
        <td class="header" colspan="2">
            <img src="tpl/stdstyle/images/profile/32x32-profile.png" class="icon32" alt="Dane profilu" title="Logs" />&nbsp;<b>Opis użytkownika</b></td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td colspan="2" valign="top">{{description}}</td>
    </tr>

    {opis_end}
    <tr>
        <td class="header" colspan="2">
            <img src="tpl/stdstyle/images/profile/32x32-profile.png" class="icon32" alt="Dane profilu" title="Logs" />&nbsp;<b>Dane Profilu</b></td>
    </tr>
    <tr>
        <td class="header"><b>Kraj:</b></td>
        <td>{country}</td>
    </tr>
    <!--<tr>
        <td class="header"><b>Opcje:</b></td>
        <td><ul>{options}</ul></td>
    </tr>-->
    <tr>
        <td class="header"><b>Data rejestracji:</b></td>
        <td>{registered}</td>
    </tr>
    <tr>
        <td class="header"><b>Obrazek statystyki:</b></td>
        <td><img src="statpics/{userid}.jpg" align="middle" /></td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td class="header-small">
            <img src="tpl/stdstyle/images/cache/22x22-traditional.png" width="22" height="22" align="middle" border="0" alt="Logs" title="Logs" />&nbsp;Ukryte skrzynki:</td>
        <td class="header-small">{hidden}
            [<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;ownerid={userid}&amp;searchbyowner=">pokaż wszystkie</a>]

        </td>
    </tr>
    {type_hidden}

    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td class="header-small">
            <img src="tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt="Znalezione" title="Znalezione" />&nbsp;Znalezienia:</td>
        <td class="header-small" >{{founds}}
            [<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={userid}&amp;searchbyfinder=&logtype=1">pokaż wszystkie</a>]
        </td>
    </tr>
    {type_found}

    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td class="header-small">
            <img src="tpl/stdstyle/images/log/16x16-dnf.png" class="icon16" alt="Nienalezione" title="Nienalezione" />&nbsp;Nieznalezienia:</td>
        <td class="header-small" >{not_founds}
            [<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={userid}&amp;searchbyfinder=&logtype=2">pokaż wszystkie</a>]
        </td>
    </tr>
    {type_notfound}

    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td class="header-small">
            <img src="tpl/stdstyle/images/cache/22x22-traditional.png" width="22" height="22" align="middle" border="0" alt="Rekomendowane" title="Rekomendowane" />&nbsp;Rekomendowane skrzynki:</td>
        <td class="header-small" >{recommended} z możliwych {maxrecommended} [<a href="usertops.php?userid={userid}">pokaż wszystkie</a>]
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
</table>
