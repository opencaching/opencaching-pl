{$pagename=$news_title1}

{include file="./tpl/header.inc.tpl"}

<div id='pagetitle'>{$news_title2}</div>

<div id="content">

    {$page} {if $smarty.get.page}{$smarty.get.page}/{$max}{else}1/{$max}{/if}<hr/>
    {$j=0}
    {section name=i loop=$news}
        {$j=$j+1}
        <b>{$news[i].date_posted}</b><br/><br/> {$news[i].content}

        {if $prev_page!=NULL || $next_page!=NULL}<br/><br/><hr/>{else}<br/><br/>{/if}

    {/section}


    {if $prev_page!=NULL || $next_page!=NULL}<table class="tablefooter" style="width:87%"><tr>{/if}


            {if $prev_page!=NULL}
                <td class="button" style="width:40%"><a href="./news.php?page={$prev_page}"><<</a></td>
            {else}
                <td style="width:40%"><a></a></td>
                    {/if}
                    {if $prev_page!=NULL || $next_page!=NULL}<td style="width:20%"><a></a></td> {/if}
                    {if $next_page!=NULL}
                <td class="button" style="width:40%"><a href="./news.php?page={$next_page}">>></a></td>
            {else}
                <td style="width:40%"><a></a></td>
                    {/if}

            {if $prev_page!=NULL || $next_page!=NULL}</tr></table>{/if}


    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}