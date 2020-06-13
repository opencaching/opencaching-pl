<link rel="stylesheet" type="text/css" media="screen,projection" href="/js/jquery.1.10.3/css/myCupertino/jquery-ui-1.10.3.custom.css" />
<script src="/js/jquery.1.10.3/js/jquery-1.9.1.js"></script>
<script src="/js/jquery.1.10.3/js/jquery-ui-1.10.3.custom.js"></script>

<link rel="stylesheet" type="text/css" media="screen,projection" href="/css/Badge.css" />
<link rel="stylesheet" href="/js/pieProgress/dist/css/asPieProgress.css">
<script src="/js/pieProgress/js/jquery.js"></script>
<script src="/js/pieProgress/dist/jquery-asPieProgress.js"></script>
<script src="/js/pieProgress/badge.js"></script>

<link rel="stylesheet" type="text/css" media="screen,projection" href="/css/GCT.css" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="/css/GCTStats.css" />
 <!--<script src='https://www.google.com/jsapi'></script>-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="/js/GCT.lang.php"></script>
<script src="/js/GCT.js"></script>
<script src="/js/GCTStats.js"></script>
<script src="/js/wz_tooltip.js"></script>


<div class="content2-pagetitle">
<img src="/images/blue/merit_badge.png" class="icon32" alt="" title="" align="middle" />&nbsp;
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
