<?php
/***************************************************************************
                                                  ./tpl/stdstyle/mywatches_properties.inc.php
                                                            -------------------
        begin                : July 17 2004
        copyright            : (C) 2004 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder ??

     set template specific variables

 ****************************************************************************/

    $commit = '<div class="notice">'.tr('commit_watch').'</div>';
    $commiterr = '<div class="warning">Błąd podczas zapisu!</div>';

    $weekday[1] = tr('monday');
    $weekday[2] = tr('tuesday');
    $weekday[3] = tr('wednesday');
    $weekday[4] = tr('thursday');
    $weekday[5] = tr('friday');
    $weekday[6] = tr('saturday');
    $weekday[7] = tr('sunday');

    $intervalls[0] = tr('immediately');      // table indices are misplaced accordingly to
    $intervalls[1] = tr('once_day');     // ones used in runwatch.php script that performs the real check
    $intervalls[2] = tr('once_week');  // there: immediately=1, daily=0, and weekly=2
                                             // thus mywatches.php required a change
?>
