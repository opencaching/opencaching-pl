{$pagename=$mainpage_title}

{include file="./tpl/header.inc.tpl"}

<div id="content">

    <div class='menu'>
        <div class="button"><a href="./find.php" >{$seek}</a></div>
        <div class="button"><a href="./near.php" >{$seek_near}</a></div>

        {if $smarty.session.user_id}
            <div class="button"><a href="./logentryfind.php" >{$entry}</a></div>
            <div class="button"><a href="./menu.php" >{$my_menu}</a></div>
            {/if}

        <div class="button"><a href="./moar.php" >{$more}</a></div>
    </div>

</div>

{include file="./tpl/footer.inc.tpl"}