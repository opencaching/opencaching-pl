<?php

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

     german popup template

     template replacement(s):

       title          HTML page title
       lang           language
       style          style
       htmlheaders    additional HTML headers
       template       template to display

 ****************************************************************************/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo isset($tpl_subtitle) ? $tpl_subtitle : ''; ?>{title}</title>
        <meta http-equiv="content-type" content="text/xhtml; charset=UTF-8" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
        <meta http-equiv="Content-Language" content="{lang}" />
        <meta http-equiv="gallerimg" content="no" />
        <meta http-equiv="pragma" content="no-cache" />
        <meta http-equiv="cache-control" content="no-cache" />
        <link rel="SHORTCUT ICON" href="favicon.ico">
        <link rel="stylesheet" type="text/css" href="tpl/stdstyle/css/main.css" />
        {htmlheaders}
    </head>
    <body{bodyMod}>
        <div id="content">
{template}
        </div>
    </body>
</html>
