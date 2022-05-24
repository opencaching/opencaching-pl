<script>
var currentUserId = "<?= $view->currentUserId; ?>";
var cacheNonamedTrans = "{{cache_approval_nonamed}}";
var acceptButtonText = "{{viewPending_16}}";
var cancelButtonText = "{{viewPending_17}}";
var blockButtonText = "{{viewPending_20}}";
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

<script id="geocacheApproval_acceptTemplate" type="text/x-jsrender">
<div class="geocacheApproval-confirmDialog-content">
    <div class="geocacheApproval-confirmDialog-content2">
        {{viewPending_13}} "<a href='viewcache.php?cacheid=<%:cache_id%>'><%:cache_name%></a>" {{viewPending_14}} <%:cache_owner%>.<br/>
        {{viewPending_15}}.
    </div>
</div>
</script>

<script id="geocacheApproval_blockTemplate" type="text/x-jsrender">
<div class="geocacheApproval-confirmDialog-content">
    <div class="geocacheApproval-confirmDialog-content2">
        {{viewPending_18}} "<a href='viewcache.php?cacheid=<%:cache_id%>'><%:cache_name%></a>" {{viewPending_14}} <%:cache_owner%>.<br/>
        {{viewPending_19}}.
    </div>
</div>
</script>

<script id="geocacheApproval_generalErrorTemplate" type="text/x-jsrender">
<p class="geocacheApproval-errorInfo">
<img src="images/free_icons/error.png" alt="error" class="geocacheApproval-icon"><%:error_thrown%>: <%:text_status%>
</p>
</script>

<script id="geocacheApproval_assignedTemplate" type="text/x-jsrender">
<p>{{viewPending_07}} <%:assigned_username%> {{viewPending_08}}.</p>
</script>

<script id="geocacheApproval_assignErrorTemplate" type="text/x-jsrender">
<p class="geocacheApproval-errorInfo">
<%if message && message.length%><img src="images/free_icons/error.png" alt="error" class="geocacheApproval-icon"><%:message%><%/if%>
</p>
</script>

<script id="geocacheApproval_acceptedTemplate" type="text/x-jsrender">
<p class="geocacheApproval-acceptedInfo">
<img src="images/log/16x16-published.png" alt="accepted" class="geocacheApproval-icon">{{viewPending_09}}<br>
[ {{cache}}: <%:cache_name%> - <%:cache_wp%>; {{owner_label}}: <%:cache_owner%>; {{cache_approval_changed_time}}: <%:updated%> ]
</p>
</script>

<script id="geocacheApproval_acceptErrorTemplate" type="text/x-jsrender">
<p class="geocacheApproval-errorInfo">
<img src="images/free_icons/error.png" alt="error" class="geocacheApproval-icon">{{viewPending_10}}
<%if message && message.length%><br>[ <%:message%> ]<%/if%>
</p>
</script>

<script id="geocacheApproval_rejectedTemplate" type="text/x-jsrender">
<p class="geocacheApproval-rejectedInfo">
<img src="images/log/16x16-trash.png" alt="rejected" class="geocacheApproval-icon">{{viewPending_11}}<br>
[ {{cache}}: <%:cache_name%> - <%:cache_wp%>; {{owner_label}}: <%:cache_owner%>; {{cache_approval_changed_time}}: <%:updated%> ]
</p>
</script>

<script id="geocacheApproval_rejectErrorTemplate" type="text/x-jsrender">
<p class="geocacheApproval-errorInfo">
<img src="images/free_icons/error.png" alt="error" class="geocacheApproval-icon">{{viewPending_12}}
<%if message && message.length%><br>[ <%:message%> ]<%/if%>
</p>
</script>

<div id="geocacheApproval_confirmDialogTemplate" class="geocacheApproval-hidden">
</div>
