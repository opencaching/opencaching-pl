{$pagename=$stats_title1}

{include file="./tpl/header.inc.tpl"}

<div id='pagetitle'>{$stats_title2}</div>

<div id="content">

    <i>{$caches_count}</i> <b>{$ile}</b><br/>

    <i>{$caches_active}</i> <b>{$ile_akt}</b><br/><br/>

    <i>{$caches_found_text}</i> <b>{$founds}</b><br/><br/>

    <i>{$user_count}</i> <b>{$user}</b><br/><br/><hr/><br/>

    <b><i>{$top5_caches_found}</i><br/><br/></b>

    {section name=i loop=$caches_found}
        <a href="./viewcache.php?wp={$caches_found[i].wp_oc}">{$caches_found[i].name}, {$caches_found[i].founds}</a><br/>
    {/section}<br/>
    <div class='button'><a href="./stats.php?more=1">{$more}</a></div><br/><hr/><br/>

    <b><i>{$top5_caches_rating}</i><br/><br/></b>

    {section name=i loop=$caches_rat}
        <a href="./viewcache.php?wp={$caches_rat[i].wp_oc}">{$caches_rat[i].name}, {$caches_rat[i].ile}</a><br/>
    {/section}<br/>
    <div class='button'><a href="./stats.php?more=2">{$more}</a></div><br/><hr/><br/>

    <b><i>{$top5_user_found}</i><br/><br/></b>

    {section name=i loop=$user_found}

        {if $smarty.session.user_id}
            <a href='./user.php?id={$user_found[i].user_id}'>{$user_found[i].username}, {$user_found[i].founds_count}</a>
        {else}
            {$user_found[i].username}, {$user_found[i].founds_count}
        {/if}



        <br/>
    {/section}<br/>
    <div class='button'><a href="./stats.php?more=3">{$more}</a></div><br/><hr/><br/>

    <b><i>{$top5_user_hidden}</i><br/><br/></b>

    {section name=i loop=$user_hidden}

        {if $smarty.session.user_id}
            <a href='./user.php?id={$user_hidden[i].user_id}'>{$user_hidden[i].username}, {$user_hidden[i].hidden_count}</a>
        {else}
            {$user_hidden[i].username}, {$user_hidden[i].hidden_count}
        {/if}


        <br/>
    {/section}<br/>
    <div class='button'><a href="./stats.php?more=4">{$more}</a></div><br/>

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}