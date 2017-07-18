
<div class="content2-pagetitle">
    {{qrcode_title}}
</div>

<div id="qrCodeDesc">
  <p>{{qrcode_desc}}</p>
</div>

<div id="qrCodeimage">
    <img src="{ocLabelImgUrl}" border="0" alt="" width="171" height="284" />
</div>


<div id="qrCodeForm">
    <div>
    <form action="qrcode.php" method="post">
        <label for="qrCodeText">{{qrcode_textLabel}}:</label><br>

        <input name="qrCodeText" value="{qrCodeText}" maxlength="77"
                class="form-control input400"><br>

        <button type="submit" name="Generate" value="Generate"
                class="btn btn-primary">{{qrcode_button}}</button>
    </form>
    </div>
</div>

<div>
    <a href="{ocLabelImgUrl}" title="qrCode_image" download="QRcodeLabel.png">
        <button class="btn btn-default">{{qrcode_downloadLabeled}}</button>
    </a>
    <a href="{qrCodeImgUrl}" title="qrCode_image" download="QRcode.png">
        <button class="btn btn-default">{{qrcode_downloadQRCodeOnly}}</button>
    </a>
</div>

<div>
    <img src="tpl/stdstyle/images/misc/16x16-info.png" class="icon16" alt="Info" />
    {{qrcode_appInfo}}
</div>


