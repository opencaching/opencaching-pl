<?php

require_once('./lib/common.inc.php');

$content_table = '

<table class="Badge-div-table" width="770px">
    <tbody>
    <tr class="Badge-category" ><td colspan="5">{category}<br><br></td></tr>
    
    <tr>
    <td>
    {content_badge_img}
    </td>
    </tr>
    </tbody>
</table>

<br>';


$content_element = '
<div class="Badge-div-element">
            <span class="Badge-level-user">'. tr('merit_badge_level') .': {level_name} </span><br>
            <span class="Badge-value-user">'.tr('merit_badge_number').': {progresbar_curr_val} / {next_val}<br></span>
            
            <a href="badge.php?badge_id={badge_id}&user_id={user_id}">
            <div>
            <div class="Badge-pie-progress" role="progressbar" data-goal="{progresbar_curr_val}" data-trackcolor="#d9d9d9" data-barcolor="{progresbar_color}" data-barsize="{progresbar_size}" aria-valuemin="0" aria-valuemax="{progresbar_next_val}">
            <div class="pie_progress__content"><img src="{picture}"/><br>
            </div>
            </div>
            </div>
                    </a>
            <br>
                    
            <span class="Badge-name-user"><a href="badge.php?badge_id={badge_id}&user_id={user_id}">{name}</a></span> <br>
            <span class="Badge-short_desc-user">{short_desc}</span> <br>
	        
</div>';
?>

