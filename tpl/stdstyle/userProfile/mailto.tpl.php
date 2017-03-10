<link rel="stylesheet" href="<?=$view->mailto_css?>">

<form action="mailto.php" method="post" enctype="application/x-www-form-urlencoded" name="mailto_form">
    <input type="hidden" name="userid" value="<?=$view->requestedUser->getUserId()?>" />

    <div class="content2-pagetitle">
        <!-- img src="tpl/stdstyle/images/blue/email.png" class="icon32" alt="" align="middle" / -->
        {{email_user}}
        <a href='viewprofile.php?userid=<?=$view->requestedUser->getUserId()?>'>
            <?=$view->requestedUser->getUserName()?>
        </a>
    </div>

    <?php if($view->messagePresent){ ?>
        <div>
            <span id="message"><?=$view->message?></span>
        </div>
    <?php } else { // message-present ?>

        <div>
            <label>{{titles}}:</label>
            <input type="text" name="subject" value="{subject}" class="input400" />
            {errnosubject}
        </div>

        <div>
            <div>
                <label>{{content}}:</label>
                {errnotext}
            </div>
            <textarea class="logs" name="text" cols="68" rows="15">{text}</textarea>
        </div>

        <div>
            <label for="l_send_emailaddress">{{my_email_will_send}}</label>
            <input type="checkbox" name="send_emailaddress" value="1"{send_emailaddress_sel} id="l_send_emailaddress" class="checkbox" />
            <div class="notice" style="width:500px;height:44px;">
                    {{email_publish}}<br />
            </div>
        </div>

        <div>
            <input type="reset" name="reset" value="{{email_reset}}" class="formbuttons" />
            <input type="submit" name="submit" value="{{email_submit}}" class="formbuttons" />
        </div>

    <?php } // message-present ?>
</form>
