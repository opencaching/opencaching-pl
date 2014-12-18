{$pagename=$login_title}

{include file="./tpl/header.inc.tpl"}

<div id='pagetitle'>{$login_title2}</div>

<div id='content'>

    {if $error eq '2'}
        <center><span class="error">{$no_cookie}</span></center>
        {else}
            {if $error eq '1'}
            <center><span class="error">{$wrong_login_pass}<br/><br/></span></center>
                {/if}
        <form name='form1' action='#' method='post'>
            {$user}<br/>
            <input type='text' name='username'/><br/><br/>
            {$passw}<br/>
            <input type='password' name='pass'/><br/><br/>
            <input type='checkbox' name='remember' /> <span onClick="document.form1.remember.checked = (!document.form1.remember.checked);">{$remember_me}</span><br/><br/>
            <div class='menu'>
                <div class='button'><a href='javascript: document.form1.submit()'>{$login_button}</a></div>
            </div>
        </form><br/>
    {/if}

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}