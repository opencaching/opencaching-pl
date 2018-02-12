
<?php $view->callChunk('infoBar', $view->reloadUrl, $view->infoMsg, $view->errorMsg ); ?>

<div class="content2-pagetitle">
    <!-- img src="tpl/stdstyle/images/blue/email.png" class="icon32" alt="" align="middle" / -->
    {{email_user}}
    <a href='viewprofile.php?userid=<?=$view->requestedUser->getUserId()?>'>
        <?=$view->requestedUser->getUserName()?>
    </a>
</div>

<form id="sendEmailForm" action="mailto.php?userid=<?=$view->requestedUser->getUserId()?>" method="post" enctype="application/x-www-form-urlencoded" name="mailto_form">

        <div class="form-section">
            <label for="mailSubject">{{titles}}:</label>
            <input type="text" id="mailSubject" name="mailSubject"
                   value="<?=$view->mailSubject?>" <?=($view->formDisabled)?'disabled':''?>/>
        </div>

        <div class="form-section">
            <div>
                <label for="mailText">{{content}}:</label>
            </div>
            <textarea id="mailText" name="mailText" rows="15" <?=($view->formDisabled)?'disabled':''?> ><?=$view->mailText?></textarea>
        </div>

        <div class="form-section">
            <label for="attachEmailAddress">{{my_email_will_send}}</label>
                <input type="checkbox" name="attachEmailAddress" id="attachEmailAddress"
                       class="checkbox" <?=($view->attachEmailAddress)?'checked':''?> <?=($view->formDisabled)?'disabled':''?>/>

            <div class="notice">{{email_publish}}</div>
        </div>

        <?php if(! $view->formDisabled) {?>
            <div class="form-section">
                <input type="submit" name="sendEmailAction" value="{{email_submit}}"
                       class="btn btn-md btn-primary"  />
            </div>
        <?php } //if-formDisabled ?>

</form>
