<?php

use Utils\Uri\Uri;

/**
 * This chunk is purpose is to display info/error bar at the top of the page
 */

return function ($reloadUrl=null, $infoMsg=null, $errorMsg=null) {

    $chunkCSS = Uri::getLinkWithModificationTime('/tpl/stdstyle/chunks/infoBar.css');

// begining of chunk
?>
    <script type='text/javascript'>
        // load chunk css
        var linkElement = document.createElement("link");
        linkElement.rel = "stylesheet";
        linkElement.href = "<?=$chunkCSS?>";
        linkElement.type = "text/css";
        document.head.appendChild(linkElement);
    </script>

    <script type='text/javascript'>
        function infoBarReload(){
          window.location = "<?=$reloadUrl?>";
        }

        function infoBarHide(){
          $('.infoBar-message').hide();
        }
    </script>

    <?php if(!empty($infoMsg)) { ?>
        <div class="infoBar-message">
          <h5>
            <?=$infoMsg?>

            <?php if($reloadUrl) { ?>
                <span class="infoBar-close-but" onclick="infoBarReload()">[X]</span>
            <?php }else{ //if-reloadUrl ?>
                <span class="infoBar-close-but" onclick="infoBarHide()">[X]</span>
            <?php } //if-reloadUrl ?>
          </h5>
        </div>
    <?php } ?>

    <?php if(!empty($errorMsg)) { ?>
        <div class="infoBar-message infoBar-message-err">
          <h5>
            <?=$errorMsg?>

            <?php if($reloadUrl) { ?>
                <span class="infoBar-close-but" onclick="infoBarReload()">[X]</span>
            <?php }else{ //if-reloadUrl ?>
                <span class="infoBar-close-but" onclick="infoBarHide()">[X]</span>
            <?php } //if-reloadUrl ?>

          </h5>
        </div>
    <?php } ?>

<?php
};

// end of chunk - nothing should be added below
