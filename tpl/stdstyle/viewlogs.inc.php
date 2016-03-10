<?php

$functions_start = '<br/><img src="images/trans.gif" alt="" title="" class="icon16" />&nbsp;';
$functions_middle = '&nbsp;';
$functions_end = '';
$decrypt_log = '<img src="tpl/stdstyle/images/free_icons/lock_open.png" class="icon16" alt="" title=""/>&nbsp;<a class="links" href="viewcache.php?cacheid={cacheid}&amp;nocryptlog=1#{decrypt_log_id}" onclick="var hint=document.getElementById(\'{decrypt_log_id}\');hint.innerHTML=convertROTStringWithBrackets(hint.innerHTML);void(0); return false;">' . tr("decrypt") . '</a>';
$nodecrypt_log = '&nbsp;<img src="tpl/stdstyle/images/free_icons/lock.png" class="icon16" alt="" title=""/>&nbsp;<span style="font-weight;">' . tr("encrypt_log") . '</span>';
$edit_log = '<img src="tpl/stdstyle/images/free_icons/pencil.png" class="icon16" alt="" title=""/>&nbsp;<a class="links" href="editlog.php?logid={logid}">' . tr("edit") . '</a>&nbsp;';
$remove_log = '<span id="rmLogLoader-{logid}" style="display: none"><img src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />'.tr('removingLog').'..</span><span id="rmLogHrefSection-{logid}"><img src="tpl/stdstyle/images/free_icons/cross.png" class="icon16" alt="" title=""/>&nbsp;<a class="linksRmLog" href="#" onclick="rmLog({logid});">' . tr("delete") . '</a></span>&nbsp;';
$revertLog = '<img src="tpl/stdstyle/images/free_icons/comment_edit.png" class="icon16"> <a class="links" href="revertlog.php?logid={logid}">' . tr("revert") . '</a> ';
$upload_picture = '<img src="tpl/stdstyle/images/action/16x16-addimage.png" class="icon16" alt="" title=""/>&nbsp;<a class="links" href="newpic.php?objectid={logid}&amp;type=1">' . tr("add_picture") . '</a>&nbsp;';
$remove_picture = ' <span class="removepic"><img src="tpl/stdstyle/images/log/16x16-trash.png" class="icon16" alt="" title=""/><a class="links" href="removepic.php?uuid={uuid}">' . tr("delete") . '</a></span>';
$rating_picture = '<img src="images/rating-star.png" alt="Rekomendacja" /> '
?>
