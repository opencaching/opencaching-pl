<?php
require_once('./lib/common.inc.php');
$tplname = 'foundmap';
tpl_set_var('cachemap_js', 'onload="load()" onunload="GUnload()"');

tpl_set_var('cachemap_header', '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$googlemap_key.'" type="text/javascript"></script>');

tpl_BuildTemplate();
?>
