<?php

require_once('./lib/common.inc.php');
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Cache" title="Cache" align="middle"/>{{log01}}</div>
<p>{{log00}}
    <br />
</p>
<br />
<form enctype="multipart/form-data" method="POST" action="log_cache_multi.php">
    <input type="hidden" name="MAX_FILE_SIZE" value="51200" />
    <b>{{file_name}}</b>:<br/><input name="userfile" type="file" size="60" accept="Text/plain" /><br/><br/>
    <button type="submit" value="Wyślij"  style="font-size:14px;width:160px"><b>{{submit}}</b></button>
</form>

