{$pagename=$remove_title}

{include file="./tpl/header.inc.tpl"}

<div id="content">

    {if $error eq '1'}
        <center><span class="error">{$no_entry}</span></center>
        {elseif $error eq '2'}
        <center><span class="error">{$not_your_entry}</span></center>
        {else}
            {$del_question}
        <div class="menu">
            <div class="button">
                <form name='form2' action='#' method='post'>
                    <input type="hidden" name="confirm" value="true">
                    <a href='javascript:document.form2.submit()'><span class="blue">{$yes}</span></a>
                </form>
            </div>
        </div>

        <br/>

    {/if}

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}