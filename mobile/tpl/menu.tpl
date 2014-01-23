{$pagename=$menu_title}

{include file="./tpl/header.inc.tpl"}

<div id='pagetitle'>{$menu_title2}</div>

<div id="content">

    <div class='menu'>
        <div class="button"><a href="./find.php?owner={$smarty.session.username}" >{$my_caches}</a></div>
        <div class="button"><a href="./user.php?id={$smarty.session.user_id}" >{$my_stats}</a></div>
        <div class="button"><a href="./mywatches.php" >{$observed_caches}</a></div>
        <div class="button"><a href="./find.php?finder={$smarty.session.username}" >{$found_caches}</a></div>
        <div class="button"><a href="./user.php" >{$find_user}</a></div>
    </div>

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}