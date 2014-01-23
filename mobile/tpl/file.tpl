{$pagename=$file_title1}

{include file="./tpl/header.inc.tpl"}

<div id='pagetitle'>{$file_title2}</div>

<div id="content">

    <div class="button"><a href="./geo.php?wp={$smarty.get.wp}&output=gpx" >GPX</a></div>
    <div class="button"><a href="./geo.php?wp={$smarty.get.wp}&output=gpxgc" >GPX GC</a></div>
    <div class="button"><a href="./geo.php?wp={$smarty.get.wp}&output=loc" >LOC</a></div>
    <div class="button"><a href="./geo.php?wp={$smarty.get.wp}&output=wpt" >WPT</a></div>
    <!--<div class="button"><a href="./geo.php?wp={$smarty.get.wp}&output=uam" >UAM</a></div>-->

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}