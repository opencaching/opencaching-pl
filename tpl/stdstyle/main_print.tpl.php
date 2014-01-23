<?php
/***************************************************************************
                                            ./tpl/stdstyle/main_print.tpl.php
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

/****************************************************************************

   Unicode Reminder ??

     german main template for printing

     template replacement(s):

       title          HTML page title
       lang           language
       style          style
       htmlheaders    additional HTML headers
       loginbox       login status (login form or username)
       functionsbox   available function on this site
       template       template to display
       runtime        computing time

 ****************************************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $tpl_subtitle; ?>{title}</title>
        <meta http-equiv="content-type" content="text/xhtml; charset=UTF-8" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
        <meta http-equiv="Content-Language" content="{lang}" />
        <meta http-equiv="gallerimg" content="no" />
        <meta http-equiv="pragma" content="no-cache" />
        <meta http-equiv="cache-control" content="no-cache" />
        <!-- Favicon noch nicht vorhanden <link rel="shortcut icon" href="favicon.ico" />-->
        <link rel="stylesheet" type="text/css" href="tpl/{style}/css/style_print.css" />
        {htmlheaders}
        {cachemap_header}
        {viewcache_header}
    </head>
    <body onload="load()" onunload="GNuload()">
        {template}
    </body>
</html>
