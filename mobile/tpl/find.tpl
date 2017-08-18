{$pagename=$find_title1}

{include file="./tpl/header.inc.tpl"}

<div id='pagetitle'>{$find_title3}</div>

<div id="content">

    {if $error eq '1'}
        <center><span class="error">{$no_wp}</span></center><br/>
        {/if}
        {if $error eq '2'}
        <center><span class="error">{$too_short_find}</span></center><br/>
        {/if}

    <form action=".{$action}" method="get" name="form1">
        {$name}<br/>
        <input type="text" name="nazwa"/><br/><br/>
        <div class='menu'>
            <div class='button'>
                <a href='javascript: document.form1.submit()'>{$seek_button}</a>
            </div>
        </div><br/><hr/>
    </form>

    <form action=".{$action}" method="get" name="form2">
        {$wpt}<br/>
        <input type="text" name="wp" value="{$oc_waypoint}"/><br/><br/>
        <div class='menu'>
            <div class='button'>
                <a href='javascript: document.form2.submit()'>{$seek_button}</a>
            </div>
        </div><br/><hr/>
    </form>

    <form action=".{$action}" method="get" name="form3">
        {$owner}<br/>
        <input type="text" name="owner"/><br/><br/>
        <div class='menu'>
            <div class='button'>
                <a href='javascript: document.form3.submit()'>{$seek_button}</a>
            </div>
        </div><br/>
    </form>

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}