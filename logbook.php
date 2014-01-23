<?php
//prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

        //Preprocessing
    if ($error == false)
    {
        //user logged in?
        if ($usr == false)
        {
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target='.$target);
        }
        else
        {
                $tplname = 'logbook';
        }

    }
    tpl_set_var('viewcache_header', '<script type="text/javascript" src="lib/ajax.js" />');
    tpl_BuildTemplate();
?>
