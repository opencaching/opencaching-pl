
<div class="content2-pagetitle">
    <?=tr('qrcode_title')?>
</div>

<div id="qrCodeDesc">
  <p><?=tr('qrcode_desc')?></p>
</div>

<div id="qrCodeimage">
    <img src="<?=$view->ocLabelImgUrl?>" alt="" width="171" height="284" />
</div>


<div id="qrCodeForm">
    <div>
    <form method="post">
        <label for="qrCodeText"><?=tr('qrcode_textLabel')?>:</label><br>

        <input id="qrCodeText" name="qrCodeText" value="<?=$view->qrCodeText?>" maxlength="77"
                class="form-control input400"><br>

        <button type="submit" name="Generate" value="Generate"
                class="btn btn-primary"><?=tr('qrcode_button')?></button>
    </form>
    </div>
</div>

<div>
    <a class="btn btn-default" href="<?=$view->ocLabelImgUrl?>" title="qrCode_image" download="QRcodeLabel.png">
       <?=tr('qrcode_downloadLabeled')?>
    </a>
    <a class="btn btn-default" href="<?=$view->qrCodeImgUrl?>" title="qrCode_image" download="QRcode.png">
        <?=tr('qrcode_downloadQRCodeOnly')?>
    </a>
</div>

<div>
    <img src="/tpl/stdstyle/images/misc/16x16-info.png" class="icon16" alt="Info" />
    <?=tr('qrcode_appInfo')?>
</div>


