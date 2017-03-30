<?php

$content_table_badge = '

<table width="770px">
    <tbody>
    {content_badge_rows}
    </tbody>
</table>

<br>';

$content_row_pattern_badge = "<tr class='Badge-table-view'><td><span class='Badge-category-small'>{category_table}</span><br>{content_badge}</tr></td>";


$content_tip_badge = "<div style =\'width:500px;\'><img src=\'{picture}\' style= \'float: left;margin-right:20px;\' /> \\
<p style=\'font-size:20px; font-weight:bold;\'> {name} <br>\\
<span style=\'font-size:13px; font-weight:normal; font-style:italic;\'> {short_desc} </span></p> \\
<p style=\'font-size:13px;font-weight:normal;\'>\\"
.tr('merit_badge_level_name').": <b>{level_name}</b><br>\\"
.tr('merit_badge_number').": <b>{progresbar_curr_val}</b><br>\\"
.tr('merit_badge_next_level_threshold').": <b>{next_val}</b><br>\\
</p></div>";


$content_element_badge = '<div class="Badge-div-element-small">
        <a href="badge.php?badge_id={badge_id}&user_id={user_id}" onmouseover="Tip(\'{content_tip}\', PADDING,10)" onmouseout="UnTip()">
            <div class="Badge-pie-progress-small" role="progressbar" data-goal="{progresbar_curr_val}" data-trackcolor="#d9d9d9" data-barcolor="{progresbar_color}" data-barsize="{progresbar_size}" aria-valuemin="0" aria-valuemax="{progresbar_next_val}">
            <div class="pie_progress__content"><img src="{picture}" class="Badge-pic-small" /><br>
            </div>
            </div>
        </a>
</div>';


?>