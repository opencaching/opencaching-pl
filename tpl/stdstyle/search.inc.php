<?php

    $outputformat_notexist = 'Wybrany wyjścowy format nie istnieje!';
    $error_query_not_found = 'This query has expired. Please repeat your search.';
    $safelink = '<a href="query.php?action=save&amp;queryid={queryid}" class="btn btn-primary">'.tr('store_queries').'</a>';

    $caches_newstring = '<b>'.tr('new').'</b>&nbsp;';
    $caches_olddays = 7;

    $next_img = '<img src="/tpl/stdstyle/images/action/16x16-next.png" alt="&gt;" />';
    $prev_img = '<img src="/tpl/stdstyle/images/action/16x16-prev.png" alt="&lt;" />';
    $last_img = '<img src="/tpl/stdstyle/images/action/16x16-last.png" alt="&gt;&gt;" />';
    $first_img = '<img src="/tpl/stdstyle/images/action/16x16-first.png" alt="&lt;&lt;;" />';
    $next_img_inactive = '<img src="/tpl/stdstyle/images/action/16x16-next_inactive.png" alt="&gt;" />';
    $prev_img_inactive = '<img src="/tpl/stdstyle/images/action/16x16-prev_inactive.png" alt="&lt;" />';
    $last_img_inactive = '<img src="/tpl/stdstyle/images/action/16x16-last_inactive.png" alt="&gt;&gt;" />';
    $first_img_inactive = '<img src="/tpl/stdstyle/images/action/16x16-first_inactive.png" alt="&lt;&lt;" />';

    $bgcolor1 = 'bgcolor1';         // even lines
    $bgcolor2 = 'bgcolor2';         // odd lines
    $bgcolor_found = "#66FFCC";     // if cache was found by user
    $bgcolor_owner = "#ffffc5";     // if user is owner
    $bgcolor_inactive = "#fafafa";  // if cache is inactive

    $logdateformat = 'd.m.Y';
    $logdateformatYMD = 'Y.m.d';
    $logdateformat_ymd = 'y.m.d';

    $logpics[1] = '<img alt="Znaleziona" border="0" src="images/ok.gif" />';
    $logpics[2] = '<img alt="Nie znaleziona" border="0" src="images/redcross.gif" />';
    $logpics[3] = '<img alt="Komentarz" border="0" src="images/info.gif" />';

    $rating_text = "Ocena: {rating}%";
    $not_rated = 'Brak oceny';

    $error_plz = '<tr><td><span class="errormsg">Musisz podać nazwę</span></td></tr>';
    $error_ort = '<tr><td><span class="errormsg">'.tr('error_ort').'</span></td></tr>';
    $error_locidnocoords = '<tr><td><span class="errormsg">Für den ausgewählten Ort sind keine Koordinaten vorhanden</span></td></tr>';
    $error_noort = '<tr><td><span class="errormsg">Nie znaleziono współrzędnych dla wskazanej miejscowości</span></td></tr>';
    $error_nofulltext = '<tr><td colspan="3"><span class="errormsg">'.tr('error_nofulltext').'</span></td></tr>';

    $gns_countries['DE'] = 'Niemcy';
    $gns_countries['AU'] = 'Austria';
    $gns_countries['SZ'] = 'Szwajcaria';
    $gns_countries['PL'] = 'Polska';
    $gns_countries['EZ'] = 'Czechy';
    $gns_countries['LO'] = 'Slowenia';
    $gns_countries['LH'] = 'Litwa';
    $gns_countries['UP'] = 'Ukraina';

    $search_all_countries = '<option value="">'.tr('search00').'</option>';
    $search_all_regions = '<option value="">Wszystkie województwa</option>';
    //$search_all_cachetypes = '<option value="" selected="selected">Wszystkie typy skrzynek</option>';

    $cache_attrib_jsarray_line = "new Array('{id}', {state}, '{text_long}', '{icon}', '{icon_no}', '{icon_undef}', '{category}')";
    $cache_attrib_img_line = '<img id="attrimg{id}" src="{icon}" title="{text_long}" alt="{text_long}" onmousedown="switchAttribute({id})" style="cursor: pointer;" /> ';

    $unpublished_cache_style = 'color:red';

function dateDiff($interval, $dateTimeBegin, $dateTimeEnd)
{
  //Parse about any English textual datetime
  //$dateTimeBegin, $dateTimeEnd

  $dateTimeBegin = strtotime($dateTimeBegin);
  if ($dateTimeBegin === -1)
    return("..początkowa data niepoprawna");

  $dateTimeEnd = strtotime($dateTimeEnd);
  if ($dateTimeEnd === -1)
    return("..końcowa data niepoprawna");

  $dif = $dateTimeEnd - $dateTimeBegin;

  switch($interval)
  {
    case "s"://seconds
      return($dif);

    case "n"://minutes
      return(floor($dif/60)); //60s=1m

    case "h"://hours
      return(floor($dif/3600)); //3600s=1h

    case "d"://days
      return(floor($dif/86400)); //86400s=1d

    case "ww"://Week
      return(floor($dif/604800)); //604800s=1week=1semana

    case "m": //similar result "m" dateDiff Microsoft
      $monthBegin = (date("Y",$dateTimeBegin)*12) + date("n",$dateTimeBegin);
      $monthEnd = (date("Y",$dateTimeEnd)*12) + date("n",$dateTimeEnd);
      $monthDiff = $monthEnd - $monthBegin;
      return($monthDiff);

    case "yyyy": //similar result "yyyy" dateDiff Microsoft
      return(date("Y",$dateTimeEnd) - date("Y",$dateTimeBegin));

    default:
      return(floor($dif/86400)); //86400s=1d
  }
}
?>
