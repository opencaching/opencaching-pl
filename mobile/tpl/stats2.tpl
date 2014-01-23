{$pagename=$stats_title1}

{include file="./tpl/header.inc.tpl"}

<div id='pagetitle'>{$stats_title2}</div>

<div id="content">

    {if $caches_found}
        <b><i>{$top5_caches_found}</i><br/><br/></b>

        {section name=i loop=$caches_found}
            <a href="./viewcache.php?wp={$caches_found[i].wp_oc}">{$caches_found[i].name}, {$caches_found[i].founds}</a><br/>
        {/section}
    {/if}

    {if $caches_rat}
        <b><i>{$top5_caches_rating}</i><br/><br/></b>

        {section name=i loop=$caches_rat}
            <a href="./viewcache.php?wp={$caches_rat[i].wp_oc}">{$caches_rat[i].name}, {$caches_rat[i].ile}</a><br/>
        {/section}
    {/if}

    {if $user_found}
        <b><i>{$top5_user_found}</i><br/><br/></b>

        {section name=i loop=$user_found}
            <a href='./user.php?id={$user_found[i].user_id}'>{$user_found[i].username}, {$user_found[i].founds_count}</a><br/>
        {/section}
    {/if}

    {if $user_found}
        <b><i>{$top5_user_hidden}</i><br/><br/></b>

        {section name=i loop=$user_found}
            <a href='./user.php?id={$user_hidden[i].user_id}'>{$user_hidden[i].username}, {$user_hidden[i].hidden_count}</a><br/>
        {/section}
    {/if}

        {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}