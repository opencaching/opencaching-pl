<?php

tpl_set_var('title', htmlspecialchars($pagetitle, ENT_COMPAT, 'UTF-8'));
tpl_set_var('htmlheaders', '');
tpl_set_var('lang', $lang);
tpl_set_var('style', $style);
tpl_set_var('bodyMod', '');
tpl_set_var('cachemap_header', '');

$tpl_subtitle = '';


