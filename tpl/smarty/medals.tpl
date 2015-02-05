<head>
    <link href="/tpl/smarty/medals.css" rel="stylesheet" type="text/css"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
</head>
<div class="mainContainer">
    <div class="headerDiv"> MEDALE </div>
    <div class="medalWrapper">
        {foreach $smartyMedals.medals as $medal}
            <div class="medal">
                <a class="tooltips" href="#">
                <img src="{$medal.imgSrc}" class="medalImage" />
                <span class="tt">Tooltip lorem ypsum dolor</span></a><br>
                <div class="medalName">
                    {$medal.name}
                </div>
                <div class="medalLevel">
                    {$smartyMedals.tr.level}: {$medal.level} {$medal.levelName}
                </div>
            </div>
        {/foreach}
    </div>

</div>