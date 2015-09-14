<?php

$next_img = '<img src="' . $stylepath . '/images/action/16x16-next.png" alt="&gt;"/>';
$prev_img = '<img src="' . $stylepath . '/images/action/16x16-prev.png" alt="&lt;"/>';
$last_img = '<img src="' . $stylepath . '/images/action/16x16-last.png" alt="&gt;&gt;"/>';
$first_img = '<img src="' . $stylepath . '/images/action/16x16-first.png" alt="&lt;&lt;"/>';
$next_img_inactive = '<img src="' . $stylepath . '/images/action/16x16-next_inactive.png" alt="&gt;"/>';
$prev_img_inactive = '<img src="' . $stylepath . '/images/action/16x16-prev_inactive.png" alt="&lt;"/>';
$last_img_inactive = '<img src="' . $stylepath . '/images/action/16x16-last_inactive.png" alt="&gt;&gt;"/>';
$first_img_inactive = '<img src="' . $stylepath . '/images/action/16x16-first_inactive.png" alt="&lt;&lt;"/>';

$tpl_line = '<tr><td style="width: 70px;">{date}</td><td></td><td width="22">{gkimage}</td><td width="22">{GPicon}</td><td width="22">{log_image}</td><td width="22"><img src="{imglink}" class="icon16" alt="Cache" title="{cachetype}" style="margin-top:4px;" /></td><td><b><a class="links" href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td><td><span class="txt-blue10">{region}</span></td><td><b><a class="links" href="viewprofile.php?userid={userid}">{username}</a></b></td></tr>';
?>
