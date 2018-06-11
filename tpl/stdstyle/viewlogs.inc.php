<?php

$functions_start = '<br>&nbsp;';
$functions_middle = '&nbsp;';
$functions_end = '';
$edit_log = '<img src="/tpl/stdstyle/images/free_icons/pencil.png" class="icon16" alt="Pencil icon">&nbsp;<a class="links" href="/editlog.php?logid={logid}">' . tr("edit") . '</a> ';
$remove_log = '<span id="rmLogLoader-{logid}" style="display: none"><img src="/tpl/stdstyle/images/misc/ptPreloader.gif" alt="Arrows">'.tr('removingLog').'..</span><span id="rmLogHrefSection-{logid}"><img src="/tpl/stdstyle/images/free_icons/cross.png" class="icon16" alt="Cross icon">&nbsp;<a class="links" href="#" onclick="return rmLog(event, {logid});">' . tr("delete") . '</a></span> ';
$revertLog = '<span id="revertLogLoader-{logid}" style="display: none"><img src="/tpl/stdstyle/images/misc/ptPreloader.gif" alt="Arrows"></span><span id="revertLogHrefSection-{logid}"><img src="/tpl/stdstyle/images/free_icons/comment_edit.png" class="icon16" alt="Notepad icon">&nbsp;<a class="links" href="#" onclick="return revertLog(event, {logid});">' . tr("revert") . '</a></span> ';
$upload_picture = '<img src="/tpl/stdstyle/images/action/16x16-addimage.png" class="icon16" alt="Picture icon">&nbsp;<a class="links" href="/newpic.php?objectid={logid}&amp;type=1">' . tr("add_picture") . '</a> ';
$remove_picture = '<span class="removepic"><img src="/tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="Trash icon">&nbsp;<a class="links" href="/removepic.php?uuid={uuid}">' . tr("delete") . '</a></span> ';
$rating_picture = '<img src="/images/rating-star.png" alt="Star icon"> ';