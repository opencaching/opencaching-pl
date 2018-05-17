{$pagename=$user_find_title1}

{include file="./tpl/header.inc.tpl"}

{if !$user_id}<div id='pagetitle'>{$user_find_title2}</div>{/if}

<div id="content">

    {if $error eq '1'}
        <center><span class="error">{$no_user}</span></center><br/>
        {/if}

    {if $user_id}
        <b>{$username}</b><hr/><br/>
        <i>{$registered_user}</i> <b>{$date_created}</b><br/><br/>
        <i>{$hidden_count_user}</i> <b>{$hidden_count}</b><br/><br/>
        <i>{$founds_count_user}</i> <span style="color:green"><b>{$founds_count}</b></span><br/>
        <i>{$notfounds_count_user}</i> <span style="color:red"><b>{$notfounds_count}</b></span><br/>
        <i>{$notes_count_user}</i> <b>{$log_notes_count}</b><br/>
        {if $hidden_count>0}
            <br/><div class='menu'><div class='button'><a href='./find.php?owner={$username}'>{$show_user_caches}</a></div></div>
                {/if}
        <br/>
    {else}
        <form action="#" method="post" name="form1">
            {$user_name}<br/>
            <input type="text" name="username_find"/><br/><br/>
            <div class='menu'>
                <div class='button'>
                    <a href='javascript: document.form1.submit()'>{$seek_button}</a>
                </div>
            </div><br/>
        </form>
    {/if}

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}