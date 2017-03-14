<?php

?>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/misc/qrcode.png" class="icon32" alt="QR-code" title="QR-code" align="middle"/>&nbsp;{{qrcode_01}}</div>

<div class="searchdiv">
    <p style="font-size:12px;">{{qrcode_02}}</p>
    <p>&nbsp;</p>
    <center>{imgqrcode}</center>
    <br/><br/>
    <form action="qrcode.php" method="post">
        <span style="font-weight:bold;font-size:14px;width:120px">{{qrcode_03}}:</span>&nbsp;<br />
        <input name="data" value="{qrcode}" maxlength="77" size="70" class="form-control input400">&nbsp;
        <button type="submit" name="Generuj" value="Generuj" class="btn btn-primary"><b>{{qrcode_04}}</b></button>
    </form><br/><br/>
    <img src="tpl/stdstyle/images/misc/16x16-info.png" class="icon16" alt="Info" /> {{qrcode_05}}
    <p>&nbsp;</p>
</div>
<p>&nbsp;</p>

