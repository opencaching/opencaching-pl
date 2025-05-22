<?php

$content_table = '

<div class="Badge-div-table">
    <div class="Badge-category" >{category}<br><br></div>
    <div class="c-flex flex-stretch">
      {content_badge_img}
    </div>
</div>
<br>';


$content_element = '
<div class="Badge-div-element c-flex ac-space-betwen">
  <div class="w100">
    <div class="Badge-level-user">' . tr('merit_badge_level') . ': {level_name} </div>
    <div class="Badge-value-user">' . tr('merit_badge_number') . ': {curr_val} / {next_val}</div>
  </div>
  <div class="w100 as-flex-end">
    <a href="badge.php?badge_id={badge_id}&user_id={user_id}">
        <div>
          <div class="Badge-pie-progress" role="progressbar" data-goal="{progresbar_curr_val}" data-trackcolor="#d9d9d9" data-barcolor="{progresbar_color}" data-barsize="{progresbar_size}" aria-valuemin="0" aria-valuemax="{progresbar_next_val}">
            <div class="pie_progress__content"><img src="{picture}"/><br> </div>
          </div>
        </div>
    </a>
    <br>
    <div class="Badge-name-desc">
        <span class="Badge-name-user"><a href="badge.php?badge_id={badge_id}&user_id={user_id}">{name}</a></span> <br>
        <span class="Badge-short_desc-user">{short_desc}</span> <br>
    </div>
  </div>
</div>';
