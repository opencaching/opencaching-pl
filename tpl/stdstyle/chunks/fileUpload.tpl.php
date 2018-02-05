<?php
/**
 * This is file upload chunk.
 * Note: for now this chunk does not support multiple file inputs in single view
 *
 * TODO support for multiple file inputs
 *
 */

return function ($inputName, $accepted, $maxFileSize=0) {

// begining of chunk
?>
<!-- MAX= <?=$maxFileSize?> -->

    <?php if($maxFileSize != 0) {?>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?=$maxFileSize?>" />
    <?php } //if($maxFileSize != 0) ?>

    <div class="form-group">
        <div class="input-group input-group-sm">
            <label class="input-group-addon btn btn-primary">
                <span ><?= tr('newcache_choose_file') ?></span>
                <input id="<?=$inputName?>" name="<?=$inputName?>" class="form-upload" type="file" size="30" accept="<?= $accepted?>"/>
            </label>
            <input id="<?=$inputName?>_fileName" class="form-control" placeholder="<?= tr('newcache_no_file') ?>" disabled="disabled" />
        </div>
    </div>
    <script>
            console.log("helo");
        document.getElementById("<?=$inputName?>").onchange = function () {
            var str = this.value;
            var output = str.split("\\").pop();
            document.getElementById("<?=$inputName?>_fileName").value = output;
            console.log(output);
        };
    </script>

<?php
};

// end of chunk - nothing should be added below
