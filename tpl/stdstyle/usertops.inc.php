<?php

$viewtop5_line = '<tr>
                <td bgcolor="{bgcolor}"><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td>
                <td bgcolor="{bgcolor}">&nbsp</td>
                <td bgcolor="{bgcolor}"><a href=viewprofile.php?userid={owner_id}>{ownername}</a></td>
            </tr>
            ';

$notop5 = '<tr><td colspan="2">{username} nie ma jeszcze rekomendowanych skrzynek.</td></tr>';
$user_notfound = 'Nie znaleziono użytkownika.';

$bgcolor1 = '#eeeeee';
$bgcolor2 = '#ffffff';
//$bgcolor2 = '#e0e0e0';
?>
