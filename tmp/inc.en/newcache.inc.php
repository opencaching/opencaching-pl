<?php
/***************************************************************************
												  ./tpl/stdstyle/newcache.inc.php
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

	 set template specific language variables

 ****************************************************************************/

 $submit = $language[$lang]['new_cache'];
 $default_country = 'SV';
 $default_lang = 'SV';
 $show_all = $language[$lang]['show_all'];
 $default_NS = 'N';
 $default_EW = 'E';
 $date_time_format_message = '&nbsp;Format:&nbsp;DD-MM-YYYY';

 $error_general = '<div class="warning">'.$language[$lang]['error_new_cache'].'</div>';
 $error_coords_not_ok = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['bad_coordinates'].'</span>';
 $time_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['time_incorrect'].'</span>';
 $way_length_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['distance_incorrect'].'</span>';
 $date_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['date_incorrect'].'</span>';
 $name_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['no_cache_name'].'</span>';
 $tos_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['new_cache_no_terms'].'</span>';
 $desc_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['html_incorrect'].'</span>';
 $type_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;&nbsp;<span class="errormsg">'.$language[$lang]['type_incorrect'].'</span>';
 $size_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;&nbsp;<span class="errormsg">'.$language[$lang]['size_incorrect'].'</span>';
 $diff_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;&nbsp;<span class="errormsg">'.$language[$lang]['diff_incorrect'].'</span>';
 $sizemismatch_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;&nbsp;<span class="errormsg">'.$language[$lang]['virtual_cache_size'].'</span>';

 $html_desc_errbox = '<br /><br /><p style="margin-top:0px;margin-left:0px;width:550px;background-color:#e5e5e5;border:1px solid black;text-align:left;padding:3px 8px 3px 8px;"><span class="errormsg">'.$language[$lang]['html_incorrect'].'</span><br />%text%</p><br />';

 $cache_submitted = $language[$lang]['cache_submitted'];

 $sel_message = 'Select';
 $cache_size[] = array('id' => '-1', 'pl' => $language['pl']['select_one'], 'en' => $language['en']['select_one']);
 $cache_types[] = array('id' => '-1', 'short' => 'n/a', 'pl' => $language['pl']['select_one'], 'en' => $language['en']['select_one']);

 $cache_attrib_js = "new Array({id}, {selected}, '{img_undef}', '{img_large}')";
 $cache_attrib_pic = '<img id="attr{attrib_id}" src="{attrib_pic}" border="0" alt="{attrib_text}" title="{attrib_text}" onmousedown="toggleAttr({attrib_id})" />&nbsp;';

 $default_lang = 'SV';
 ?>
