<?php
    global $watchlistMailfrom;

    $mailfrom = $watchlistMailfrom;
    $debug = false;
    $debug_mailto = 'rt@opencaching.pl';

    $logwatch_text = $logowner_text = '
      <tr>
        <td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 10px; font-family: Verdana; width: 10%;" align="center">{date}</td>
        <td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 10px; font-family: Verdana; " align="center"><b>{user}</b></td>
        <td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 10px; font-family: Verdana; " align="center"><span style="color: {logtypeColor}"><b>{logtype}</b></span></td>
        <td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 10px; font-family: Verdana; width: 20%;" align="center"><a href="{absolute_server_URI}{wp}">{wp}<br/> {cachename}</a><br/></td>
        <td valign="top" style="padding-right:4px; padding-left:4px; font-size: 10px; font-family: Verdana;">
            {text}
        </td>
      </tr>
      <tr>
        <td colspan="5" valign="top" style="border-top: 1px solid gray; height: 3px"></td>
      </tr>
    ';
?>
