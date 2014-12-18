<?php
/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  UTF-8 ąść
 * ************************************************************************* */
?>
<!--    CONTENT -->
<div class="content2-container">
    <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{title_text}" title="{title_text}" />&nbsp;{{statistics_users}}: {username} </div>
    <div class="nav4">
        <?php
        // statlisting
        $statidx = mnu_MainMenuIndexFromPageId($menu, "statlisting");
        if ($menu[$statidx]['title'] != '') {
            echo '<ul id="statmenu">';
            $menu[$statidx]['visible'] = false;
            echo '<li class="title" ';
            echo '>' . $menu[$statidx]["title"] . '</li>';
            mnu_EchoSubMenu($menu[$statidx]['submenu'], $tplname, 1, false);
            echo '</ul>';
        }
        //end statlisting
        ?>
    </div>

    {content}
</div>

