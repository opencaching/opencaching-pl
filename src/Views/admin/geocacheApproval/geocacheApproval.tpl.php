<?php

use src\Controllers\ViewCacheController;
use src\Utils\Uri\SimpleRouter;

?>
<script>
var currentUserId = "<?= $view->currentUserId; ?>";
var cacheNonamedTrans = "{{cache_approval_nonamed}}";
var acceptButtonText = "{{viewPending_16}}";
var cancelButtonText = "{{viewPending_17}}";
var blockButtonText = "{{viewPending_20}}";
var viewCacheLink = "<?= SimpleRouter::getLink(ViewCacheController::class); ?>";
</script>

<div class="content2-pagetitle"><img src="/images/blue/aprove-cache.png" class="icon32" alt="" />&nbsp;{{pendings}}</div>
<div id="geocacheApproval_info"></div>
<div class="buffer"></div>
<div class="buffer geocacheApproval-refreshBlock">
    <button id="geocacheApproval_refresh" class="geocacheApproval-refreshButton">{{cache_approval_refresh}}</button>
    <span class="geocacheApproval-refreshInfo">{{cache_approval_refresh_time}}: <span id="geocacheApproval_refreshDatetime"></span></span>
</div>
<table id="geocacheApproval_waitingTable" border='1' class="table" width="97%">
    <tr>
        <th width="240px" >Cache</th>
        <th width="100px">{{date_created}}</th>
        <th width="130px">{{pendings_last_log}}</th>
        <th >{{actions}}</th>
        <th >{{assigned_to}}</th>
    </tr>
</table>
<br/><br/>

<div id="geocacheApproval_rowTemplate" class="geocacheApproval-hidden">
    <table>
        <tr class="geocacheApproval-row">
            <td class="">
                <a class="geocacheApproval-cacheName" href=""></a><br/>
                <a class="links geocacheApproval-userName" href=""></a><br/>
                <span class="geocacheApproval-region"></span>
            </td>
            <td class="alertable"></td>
            <td class=""><span class="geocacheApproval-lastLogDate"></span><br/>
                <a class="links geocacheApproval-truncated geocacheApproval-lastLogUserName" href=""></a><br/>
                <a class="geocacheApproval-truncated geocacheApproval-lastLogText" href="" title=""></a>
            </td>
            <td class="">
                <img src="/images/blue/arrow.png" alt="" />&nbsp;<a class="links geocacheApproval-actionAccept" href="">{{accept}}</a><br/>
                <img src="/images/blue/arrow.png" alt="" />&nbsp;<a class="links geocacheApproval-actionBlock" href="">{{block}}</a><br/>
                <img src="/images/blue/arrow.png" alt="" />&nbsp;<a class="links geocacheApproval-actionAssign" href="">{{assign_yourself}}</a>
            </td>
            <td class="">
                <a class="links geocacheApproval-assignedUser" href=""></a><br/>
            </td>
        </tr>
    </table>
</div>

<div id="geocacheApproval_rowEmptyTemplate" class="geocacheApproval-hidden">
    <table>
        <tr class="geocacheApproval-row">
            <td class="geocacheApproval-emptyList" colspan="5"> -- {{cache_approval_no_caches}} -- </td>
        </tr>
    </table>
</div>

<?= $view->callSubTpl('/admin/geocacheApproval/geocacheApprovalMessages'); ?>

<div id="geocacheApproval_confirmDialogTemplate" class="geocacheApproval-hidden">
</div>
