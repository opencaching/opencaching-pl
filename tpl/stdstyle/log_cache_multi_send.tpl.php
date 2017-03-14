<?php

require_once('./lib/common.inc.php');
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Cache" title="Cache" align="middle"/>{{log01}}</div>
<p>{{log00}}
    <br />
</p>
<br />
<form enctype="multipart/form-data" method="POST" action="log_cache_multi.php">

    <b>{{file_name}}</b>:<br/>
    <div class="form-inline">
    <?php $view->callChunk('fileUpload','userfile', 'Text/plain', '51200'); ?>
    </div>

    <button type="submit" value="Wyślij"  class='btn btn-primary'>{{submit}}</button>
</form>

