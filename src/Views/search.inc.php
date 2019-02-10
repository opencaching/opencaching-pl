<?php

    $outputformat_notexist = tr('search_outputformat');
    $error_query_not_found = tr('search_oldquery');
    $safelink = '<a href="query.php?action=save&amp;queryid={queryid}" class="btn btn-primary">'.tr('store_queries').'</a>';

    $caches_newstring = '<b>'.tr('new').'</b>&nbsp;';
    $caches_olddays = 7;

    $next_img = '<img src="/images/action/16x16-next.png" alt="&gt;" />';
    $prev_img = '<img src="/images/action/16x16-prev.png" alt="&lt;" />';
    $last_img = '<img src="/images/action/16x16-last.png" alt="&gt;&gt;" />';
    $first_img = '<img src="/images/action/16x16-first.png" alt="&lt;&lt;;" />';
    $next_img_inactive = '<img src="/images/action/16x16-next_inactive.png" alt="&gt;" />';
    $prev_img_inactive = '<img src="/images/action/16x16-prev_inactive.png" alt="&lt;" />';
    $last_img_inactive = '<img src="/images/action/16x16-last_inactive.png" alt="&gt;&gt;" />';
    $first_img_inactive = '<img src="/images/action/16x16-first_inactive.png" alt="&lt;&lt;" />';

    $bgcolor1 = 'bgcolor1';         // even lines
    $bgcolor2 = 'bgcolor2';         // odd lines
    $bgcolor_found = "#66FFCC";     // if cache was found by user
    $bgcolor_owner = "#ffffc5";     // if user is owner
    $bgcolor_inactive = "#fafafa";  // if cache is inactive

    $logdateformat = 'd.m.Y';
    $logdateformatYMD = 'Y.m.d';
    $logdateformat_ymd = 'y.m.d';

    $error_plz = '<tr><td><span class="errormsg">Musisz podać nazwę</span></td></tr>';
    $error_ort = '<tr><td><span class="errormsg">'.tr('error_ort').'</span></td></tr>';
    $error_locidnocoords = '<tr><td><span class="errormsg">'.tr('search_locnocoord').'</span></td></tr>';
    $error_noort = '<tr><td><span class="errormsg">'.tr('search_citynotfound').'</span></td></tr>';
    $error_nofulltext = '<tr><td colspan="3"><span class="errormsg">'.tr('error_nofulltext').'</span></td></tr>';

    $search_all_countries = '<option value="">'.tr('search00').'</option>';
    $search_all_regions = '<option value="">Wszystkie województwa</option>';
    //$search_all_cachetypes = '<option value="" selected="selected">Wszystkie typy skrzynek</option>';

    $cache_attrib_jsarray_line = "new Array('{id}', {state}, '{text_long}', '{icon}', '{icon_no}', '{icon_undef}', '{category}')";
    $cache_attrib_img_line = '<img id="attrimg{id}" src="{icon}" title="{text_long}" alt="{text_long}" onmousedown="switchAttribute({id})" style="cursor: pointer;" /> ';

    $unpublished_cache_style = 'color:red';
