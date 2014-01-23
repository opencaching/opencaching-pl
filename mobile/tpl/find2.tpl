{$pagename=$find_title1}

{include file="./tpl/header.inc.tpl"}

<div id='pagetitle'>{$find_title3}</div>

<div id="content">

    {if $error eq '1'}
        <span class="error"><center>{$no_wp}</center></span>
        {else}


    <b>{$result} {$ile}</b><br/>
    {$page} {if $smarty.get.page}{$smarty.get.page}/{$max}{else}1/{$max}{/if}
    <br/>
    {assign var ="gpx_url" value="./file2.php?wp="}

    {section name=i loop=$znalezione}

        <hr/>
        <div class='button'>
            <a href='./{$address}.php?wp={$znalezione[i].wp_oc}'>
                <span class='blue'>
                    {if $znalezione[i].date_hidden}<u><b>{$znalezione[i].date_hidden}</b></u><br/><br/>{/if}

                    {if $znalezione[i].if_found == '1'}
                    <img src="../images/16x16-found.png" alt="{$found}"/>
                    {elseif $znalezione[i].if_found == '2'}
                    <img src="../images/16x16-dnf.png" alt="{$notfound}"/>
                    {/if}

                    {if $znalezione[i].status == '2'}
                    <img src="../images/flag.png" alt=""/>
                    {/if}
                    {if $znalezione[i].status == '3'}
                    <img src="../images/bin.png" alt=""/>
                    {/if}


                    <b>{$znalezione[i].name} ({$znalezione[i].wp_oc})</b><br/>
                    <i>{$znalezione[i].typetext}</i><br/>
                    {if $znalezione[i].distance}
                        {$znalezione[i].kier} {$znalezione[i].distance} km<br/>
                    {/if}

                    {if $znalezione[i].score!=''}
                    {$score} <b>

                    {if $znalezione[i].score=='0'}{$rate0}{/if}
                    {if $znalezione[i].score=='1'}{$rate1}{/if}
                    {if $znalezione[i].score=='2'}{$rate2}{/if}
                    {if $znalezione[i].score=='3'}{$rate3}{/if}
                    {if $znalezione[i].score=='4'}{$rate4}{/if}
                    {if $znalezione[i].score=='5'}N/A{/if}

                    </b> <br/>
                    {/if}

                    {$hidden_by} <b>{$znalezione[i].username}</b><br/><br/><i>
                    N {$znalezione[i].N}<br/>E {$znalezione[i].E}</i>
                </span>
            </a>
        </div>

    {/section}
    {$i=0}
    {section name=i loop=$lista}

        {if $i>0}{$gpx_url=$gpx_url|cat:'|'}{/if}
        {$gpx_url=$gpx_url|cat:$lista[i]}
        {$i=$i+1}
    {/section}

    {if $smarty.get.Nstopien && $smarty.get.Nminuty && $smarty.get.Estopien && $smarty.get.Eminuty}<hr/>
    {$fixed_cords}<br/>{$smarty.get.Nstopien}° {$smarty.get.Nminuty}', {$smarty.get.Estopien}° {$smarty.get.Eminuty}'
    {/if}
    {if $ile>0}<hr/>
    <table class="tablefooter" style="width:87%"><tr>


    {if $prev_page!=NULL}
        <td class="button" style="width:40%"><a href="{$url}&page={$prev_page}"><<</a></td>
    {else}
        <td style="width:40%"><a></a></td>
    {/if}

    <td style="width:2%"><a></a></td>
    <td class="button" style="width:16%"><a href={$gpx_url}><img style="vertical-align: middle;" src="../images/download.png" alt="{$download_file}"/></a></td>
    <td style="width:2%"><a></a></td>


    {if $next_page!=NULL}
        <td class="button" style="width:40%"><a href="{$url}&page={$next_page}">>></a></td>
    {else}
        <td style="width:40%"><a></a></td>
    {/if}

    </tr></table>
    {/if}
    {/if}
    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}