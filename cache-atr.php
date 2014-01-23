<?php
/***************************************************************************
                                                                ./cache-atr.php
                                                            -------------------
        begin                : Mon June 14 2004
        copyright            : (C) 2004 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        $tplname = 'cache-atr';
    }
    //make the template and send it out

    tpl_BuildTemplate();
?>
