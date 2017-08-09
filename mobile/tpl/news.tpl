{$pagename=$news_title1}

{include file="./tpl/header.inc.tpl"}

<div id="pagetitle">{$news_title2}</div>

<div id="content">

  {$page} {$pagenr}/{$pagemax}
  {foreach $newslist as $news}
    <hr/><strong>{$news->getDatePublication(true)}</strong><br/>
    <strong>{$news->getTitle()}</strong><br/>
    {$news->getContent()}
    <br/>
  {/foreach}

    {if $prev_page!=NULL || $next_page!=NULL}<table class="tablefooter" style="width:87%"><tr>{/if}
            {if $prev_page!=NULL}
                <td class="button" style="width: 40%;"><a href="./news.php?page={$prev_page}"><<</a></td>
            {else}
                <td style="width: 40%;">&nbsp;</td>
                    {/if}
                    {if $prev_page!=NULL || $next_page!=NULL}<td style="width: 20%;">&nbsp;</td>{/if}
                    {if $next_page!=NULL}
                <td class="button" style="width: 40%;"><a href="./news.php?page={$next_page}">>></a></td>
            {else}
                <td style="width: 40%;">&nbsp;</td>
            {/if}
            {if $prev_page!=NULL || $next_page!=NULL}</tr></table>{/if}

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}