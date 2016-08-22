<style>
.truncated {
    display: inline-block;
    width: 130px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

</style>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/aprove-cache.png" class="icon32" alt="" />&nbsp;{{pendings}}</div>
<div class="buffer"></div>
{confirm}
<div class="buffer"></div>
<table border='1' class="table" width="97%">
    <tr>
        <th width="240px" >Cache</th>
        <th width="100px">{{date_created}}</th>
        <th width="130px">{{pendings_last_log}}</th>
        <th >{{actions}}</th>
        <th >{{assigned_to}}</th>
    </tr>
    {content}
</table>
<br/><br/>
