{$pagename=$more_title}

{include file="./tpl/header.inc.tpl"}

<div id='pagetitle'>{$more_title2}</div>

<div id="content">

    <div class='menu'>
        <div class="button"><a href="./news.php" >{$news_title1}</a></div>
        <div class="button"><a href="./newest.php" >{$newest_caches}</a></div>
        <!--<div class="button"><a href="#" >nadchodzące wydarzenia</a></div>-->
        <!--<div class="button"><a href="#" >rekomendowane</a></div>-->
        <div class="button"><a href="./stats.php" >{$stats2}</a></div>
        <div class="button"><a href="http://wiki.opencaching.pl/index.php/Regulamin_OC_PL" >{$reg}</a></div>
    </div>

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}