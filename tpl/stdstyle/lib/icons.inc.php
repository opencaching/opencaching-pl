<?php


function icon_log_type($icon_small, $text)
{
    return "<img src='/tpl/stdstyle/images/$icon_small' class='icon16' alt='$text' title='$text'>";
}

function icon_cache_status($status, $text)
{
    switch ($status) {
        case 1: $icon = "log/16x16-go.png";
            break;
        case 2: $icon = "log/16x16-stop.png";
            break;
        case 3: $icon = "log/16x16-trash.png";
            break;
        case 4: $icon = "log/16x16-wattend.png";
            break;
        case 5: $icon = "log/16x16-wattend.png";
            break;
        case 6: $icon = "log/16x16-stop.png";
            break;

        default: $icon = "log/16x16-go.png";
            break;
    }
    return "<img src='/tpl/stdstyle/images/$icon' class='icon16' alt='$text' title='$text'>";
}


function icon_rating($founds, $topratings)
{
    global $rating_text;
    global $not_rated;

    if ($topratings == 0)
        return '';

    $sAltText = $topratings . ' Rekomendacji';

    if ($topratings > 3)
        $nIconsCount = 2;
    else
        $nIconsCount = $topratings;

    $sRetval = '';
    $sRetval .= str_repeat('<img src="images/rating-star.png" alt="' . $sAltText . '" title="' . $sAltText . '" width="17px" height="16px" />', $nIconsCount);

    if ($topratings > 3)
        $sRetval .= '<img src="images/rating-plus.gif" alt="' . $sAltText . '" title="' . $sAltText . '" width="17px" height="16px" />';

    return '<nobr>' . $sRetval . '</nobr>&nbsp;';
}

function icon_geopath_small($ptID, $ptImg, $ptName, $ptType, $pt_cache_intro_tr, $pt_icon_title_tr)
{
    /*
      attributes:
      $ptID   = GeoPatch Name
      $ptImg  = GeoPath Image (link)
      $ptName = GeoPath name
      $ptTyp  = GeoPath Type (atr for $poweTrailMarkers below)
      $pt_cache_intro_tr =  translated tooltip into ("This cache belongs to..")
      $pt_icon_title_tr = translate attr. for icon ALT and NAME
     */
    $poweTrailMarkers = powerTrailBase::getPowerTrailTypes();

    if ($ptImg == '')
        $ptImg = '/tpl/stdstyle/images/blue/powerTrailGenericLogo.png';
    // for testing use: $ptImg = 'ocpl-dynamic-files/images/uploads/powerTrailLogoId13.png';
    $PT_tip = $pt_cache_intro_tr . '<BR>';
    $PT_tip.='<table width=\'99%\'>';
    $PT_tip.='  <tr>';
    $PT_tip.='      <td width=\'51\'><img border=\'0\' width=\'50\' src=\'' . $ptImg . '\' /></td>';
    $PT_tip.='      <td align=\'center\'><span style=\'font-size:13px;\'><B>' . $ptName . '</B></span></td>';
    $PT_tip.='  </tr>';
    $PT_tip.='</table>';
    $PT_tip = str_replace('\\', '\\\\', $PT_tip);
    $PT_tip = str_replace('\'', '\\\'', $PT_tip);
    $PT_tip = htmlspecialchars($PT_tip, ENT_QUOTES, 'UTF-8');
    // no tabled version: $PT_tip= $pt_cache_intro_tr.'<BR><span align=center><B>'.$ptName.'</B><BR>    <img border=0 width=50 src='.$ptImg.' /></span>';
    $PT_icon = '<a href="powerTrail.php?ptAction=showSerie&ptrail=' . $ptID . '" onmouseover="Tip(\'' . $PT_tip . '\', OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,220,SHADOW,true)" onmouseout="UnTip()" class="links">';
    $PT_icon.='<img src="' . $poweTrailMarkers[$ptType]['icon'] . '" class="icon16" alt="' . $pt_icon_title_tr . '" title="' . $pt_icon_title_tr . '" /></a>';

    return $PT_icon;
}

/**
 * Just return PT icon by PT-type
 *
 * @param unknown $ptType
 * @return string|NULL|unknown
 */
function getPtIconByType($ptType){

    $poweTrailMarkers = powerTrailBase::getPowerTrailTypes();

    if(!empty($ptType)){
        return $poweTrailMarkers[$ptType]['icon'];
    }
}
