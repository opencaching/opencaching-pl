<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{title_text}" title="{title_text}" />&nbsp;{{statistics}}</div>
<div style="line-height: 1.8em; font-size: 13px;">

<?php
//prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    if( $usr['admin'] )
    {
    echo '<br/><img src="graphs/COGstat.php" alt="" title="" align="middle" /><br/><br/>';
    } ?>
</div>
