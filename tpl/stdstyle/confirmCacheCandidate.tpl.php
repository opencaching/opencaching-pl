<?php
?>
<style>
    #start{
        font-size: 13px;
        font-weight: bold;
    }
</style>

<div id="start" align="center">
    {{pt193}} <br /><br />
</div>
<div style="display: {ptYes}">
    <p>
        {{pt191}} <a href="powerTrail.php?ptAction=showSerie&ptrail={ptId}">{ptName}</a>
    </p>
</div>
<div style="display: {ptNo}">
    <p>
        {{pt192}} {ptName}.<br/><br/> {{pt195}}
    </p>
</div>

<div style="display: {noRecord}">
    <p>
        {{pt196}}
    </p>
</div>

<div style="display: {hack}">
    <p>
        {{pt194}}
    </p>
</div>