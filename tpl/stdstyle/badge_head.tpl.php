<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/js/jquery.1.10.3/css/myCupertino/jquery-ui-1.10.3.custom.css" />
<script src="tpl/stdstyle/js/jquery.1.10.3/js/jquery-1.9.1.js"></script>
<script src="tpl/stdstyle/js/jquery.1.10.3/js/jquery-ui-1.10.3.custom.js"></script>

<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/Badge.css" />
<link rel="stylesheet" href="tpl/stdstyle/js/PieProgress/dist/css/asPieProgress.css">
<script src="tpl/stdstyle/js/PieProgress/js/jquery.js"></script>
<script src="tpl/stdstyle/js/PieProgress/dist/jquery-asPieProgress.js"></script>
<script src="tpl/stdstyle/js/PieProgress/badge.js"></script>

<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCT.css" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCTStats.css" />
<script src='https://www.google.com/jsapi'></script>
<script src="lib/js/GCT.lang.php"></script>
<script src="lib/js/GCT.js"></script>
<script src="lib/js/GCTStats.js"></script>
<script src="lib/js/wz_tooltip.js"></script>


<div class="content2-pagetitle">
<img src="tpl/stdstyle/images/blue/merit_badge.png" class="icon32" alt="" title="" align="middle" />&nbsp;
{{merit_badge}}
</div>

<br>
<br>
<div >

    <div class="Badge-pie-progress" role="progressbar" data-goal="{progresbar_curr_val}" data-trackcolor="#d9d9d9" data-barcolor="{progresbar_color}" data-barsize="{progresbar_size}" aria-valuemin="0" aria-valuemax="{progresbar_next_val}">
        <div class="pie_progress__content"><img src="{picture}" /><br></div>
    </div>

    <span class="Badge-name">{badge_name}</span><br>
    <span class="Badge-short_desc">{badge_short_desc}</span>

<p class="Badge-other">
<br><br>
{{merit_badge_level_name}}: <b>{userLevelName}</b><br>
{{merit_badge_number}}: <b>{userCurrValue}</b><br>
{{merit_badge_next_level_threshold}}: <b>{userThreshold}</b><br>
<br><br>
</p>

</div>



