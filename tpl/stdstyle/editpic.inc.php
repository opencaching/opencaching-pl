<?php

$submit = tr('submit');

$pictypedesc_cache = tr('cache_pictures');
$pictypedesc_log = tr('log_pictures');

$errnotitledesc = '<span class="errormsg">' . tr('image_err_no_title') . '</span>';
$errnopicgivendesc = '<span class="errormsg">' . tr('image_err_no_desc') . '</span>';

$message_title_internal = tr('file_err_internal_title');
$message_internal = tr('file_err_internal_file');

tpl_set_var('maxpicsizeMB', $config['limits']['image']['filesize']);
tpl_set_var('maxpicresolution', $config['limits']['image']['pixels_text']);
tpl_set_var('picallowedformats', $config['limits']['image']['extension_text']);

$message_title_toobig = tr('image_err_too_big');
$message_toobig = tr('image_max_size');
$message_title_wrongext = tr('image_bad_format');
$message_wrongext = tr('image_bad_format_info');

$message_picture_not_found = tr('no_picture');

