<?php

$submit = tr('new_cache2');
$default_region = '0';
$show_all = tr('show_all');
$default_NS = 'N';
$default_EW = 'E';
$date_time_format_message = tr('newcacheDateFormat');

$error_general = '<div class="warning">' . tr('error_new_cache') . '</div>';
$error_coords_not_ok = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('bad_coordinates') . '</span>';
$time_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('time_incorrect') . '</span>';
$way_length_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('distance_incorrect') . '</span>';
$date_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('date_incorrect') . '</span>';
$name_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('no_cache_name') . '</span>';
$tos_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('new_cache_no_terms') . '</span>';
$desc_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('html_incorrect') . '</span>';
$descwp_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;<span class="errormsg">' . tr('descwp_incorrect') . '</span>';
$type_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('type_incorrect') . '</span>';
$typewp_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('typewp_incorrect') . '</span>';
$stage_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('stage_incorrect') . '</span>';
$size_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('size_incorrect') . '</span>';
$diff_not_ok_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('diff_incorrect') . '</span>';
$sizemismatch_message = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('virtual_cache_size') . '</span>';
$regionNotOkMessage = '<br><img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt="">&nbsp;&nbsp;<span class="errormsg">' . tr('region_not_ok') . '</span>';

$html_desc_errbox = '<br><br><p style="margin-top:0px;margin-left:0px;width:550px;background-color:#e5e5e5;border:1px solid black;text-align:left;padding:3px 8px 3px 8px;"><span class="errormsg">' . tr('html_incorrect') . '</span><br>%text%</p><br>';

$sel_message = tr('choose');

$cache_attrib_js = "new Array({id}, {selected}, '{img_undef}', '{img_large}')";
$cache_attrib_pic = '<img id="attr{attrib_id}" src="{attrib_pic}" alt="{attrib_text}" title="{attrib_text}" onmousedown="toggleAttr({attrib_id})"> ';

$default_lang = $lang;