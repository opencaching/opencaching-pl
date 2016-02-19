{$pagename=$logs_title}

{include file="./tpl/header.inc.tpl"}

<div id="content">



    <div class="big infotitle"><b>{$name}</b></div>
    {$page} {if $smarty.get.page}{$smarty.get.page}/{$max}{else}1/{$max}{/if}

    {section name=i loop=$logs}

        <hr/>


        <br/>

        <b>{$logs[i].newdate} #

            {if $smarty.session.user_id}
                <a href='./user.php?id={$logs[i].user_id}'>{$logs[i].username}</a>
            {else}
                {$logs[i].username}
            {/if}

            <br/>

            {if $logs[i].newtype eq '1'}
                <span style='color: green'>
                    {$logtype = $found}
                {/if}
                {if $logs[i].newtype eq '2'}
                    <span style='color: red'>
                        {$logtype = $notfound}
                    {/if}
                    {if $logs[i].newtype eq '3'}
                        <span style='color: gray'>
                            {$logtype = $notes}
                        {/if}
                        {if $logs[i].newtype eq '4'}
                            <span style='color: black'>
                                {$logtype = $moved}
                            {/if}
                            {if $logs[i].newtype eq '5'}
                                <span style='color: red'>
                                    {$logtype = $service}
                                {/if}
                                {if $logs[i].newtype eq '6'}
                                    <span style='color: orange'>
                                        {$logtype = $made_service}
                                    {/if}
                                    {if $logs[i].newtype eq '7'}
                                        <span style='color: green'>
                                            {$logtype = $attented}
                                        {/if}
                                        {if $logs[i].newtype eq '8'}
                                            <span style='color: black'>
                                                {$logtype = $will_attend}
                                            {/if}
                                            {if $logs[i].newtype eq '9'}
                                                <span style='color: red'>
                                                    {$logtype = $archived}
                                                {/if}
                                                {if $logs[i].newtype eq '10'}
                                                    <span style='color: black'>
                                                        {$logtype = $ready}
                                                    {/if}
                                                    {if $logs[i].newtype eq '11'}
                                                        <span style='color: red'>
                                                            {$logtype = $temp_unavailable}
                                                        {/if}
                                                        {if $logs[i].newtype eq '12'}
                                                            <span style='color: gray'>
                                                                {$logtype = $cog_note}
                                                            {/if}

                                                            {$logtype}</b><br/><br/>
                                                        </span>

                                                        {if $smarty.session.user_id && $logs[i].user_id eq $smarty.session.user_id}

                                                            <table class="tablefooter">
                                                                <tr>
                                                                    <!--<td class="button" style="width:50%">
                                                                        <a><span class="blue">[{$edit}]</span></a>
                                                                    </td> -->
                                                                    <td class="button" style="width:50%">
                                                                        <a href="./removelog.php?id={$logs[i].id}"><span class="blue"><i>[{$delete}]</i></span></a>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        {/if}

                                                        <div class="opis">{$logs[i].newtext}</div><br/>



                                                    {/section}
                                                    {if $prev_page!=NULL || $next_page!=NULL}<hr/>{/if}
                                                    {if $prev_page!=NULL || $next_page!=NULL}<table class="tablefooter" style="width:87%"><tr>{/if}


                                                            {if $prev_page!=NULL}
                                                                <td class="button" style="width:40%"><a href="./logs.php?wp={$wp_oc}&page={$prev_page}"><<</a></td>
                                                            {else}
                                                                <td style="width:40%"><a></a></td>
                                                                    {/if}
                                                                    {if $prev_page!=NULL || $next_page!=NULL}<td style="width:20%"><a></a></td> {/if}
                                                                    {if $next_page!=NULL}
                                                                <td class="button" style="width:40%"><a href="./logs.php?wp={$wp_oc}&page={$next_page}">>></a></td>
                                                            {else}
                                                                <td style="width:40%"><a></a></td>
                                                                    {/if}

                                                            {if $prev_page!=NULL || $next_page!=NULL}</tr></table>{/if}

                                                    {include file="./tpl/backbutton.inc.tpl"}

                                                    </div>


                                                    {include file="./tpl/footer.inc.tpl"}
