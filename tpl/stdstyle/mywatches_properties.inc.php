<?php

$commit = '<div class="notice">' . tr('commit_watch') . '</div>';
$commiterr = '<div class="warning">' . tr('commit_watch_error') . '</div>';

$weekday[1] = tr('monday');
$weekday[2] = tr('tuesday');
$weekday[3] = tr('wednesday');
$weekday[4] = tr('thursday');
$weekday[5] = tr('friday');
$weekday[6] = tr('saturday');
$weekday[7] = tr('sunday');

$intervalls[0] = tr('hourly');    // table indices are misplaced accordingly to
$intervalls[1] = tr('once_day');  // ones used in runwatch.php script that performs the real check
$intervalls[2] = tr('once_week'); // there: hourly=1, daily=0, and weekly=2
                                  // thus mywatches.php required a change
