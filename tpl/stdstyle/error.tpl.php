<?php
/* * *************************************************************************
  ./tpl/stdstyle/error.tpl.php
  -------------------
  begin                : Mon June 14 2004
  copyright            : (C) 2004 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */
/* * **************************************************************************
  Critical error messages for the user

  template replacement(s):

  tplname       Name of the template in which the error occurs
  error_msg     message to display the user
 * ************************************************************************** */
?>
<h1>{{errtpl01}}</h1>
<p>{{errtpl02}}.</p>
<p style="font-size:x-small;margin-bottom:0px;margin-left:15px;"></p>
<p style="margin-top:0px;margin-left:15px;margin-right:20px;background-color:#e5e5e5;border:1px solid black;text-align:left;padding:3px 8px 3px 8px;">
    {{errtpl03}}: {tplname}<br/>
    {{errtpl04}}: {error_msg}
</p>
