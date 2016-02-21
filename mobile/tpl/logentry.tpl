{capture name=javascript}
    {literal}
        <script type="text/javascript">

            window.onload = function () {
                wid = document.getElementById("content").offsetWidth;
                document.getElementById("tekst").cols = Math.round(wid * 0.09);
                $uchwyt = document.getElementById('rodzaj').value;
                if ($uchwyt == '4' || $uchwyt == '3' || $uchwyt == '2')
                    hideextra();
            }

            function showextra() {
                var uchwyt = document.getElementById("extra");
                uchwyt.style.display = 'block';
            }

            function hideextra() {
                var uchwyt = document.getElementById("extra");
                uchwyt.style.display = 'none';
            }

        </script>
    {/literal}
{/capture}

{if $smarty.capture.javascript ne ""}{assign var=HeaderContent value="`$HeaderContent``$smarty.capture.javascript`"}{/if}

{$pagename=$logentry_title}

{include file="./tpl/header.inc.tpl"}

<div id='content'>

    {if $error == '1'}
        <center><span class="error">{$no_wp}</span></center>
        {else}

        <div id="formularz" class="big infotitle"><b><a href="./viewcache.php?wp={$wp_oc}">{$cache_name}</a></b></div>
        <hr/><br/>

        {if $error == '2'}
            <center><span class="error">{$wrong_dateformat}</span></center><br/>
            {/if}
            {if $error == '3'}
            <center><span class="error">{$future_date}</span></center><br/>
            {/if}
            {if $error == '4'}
            <center><span class="error">{$hack}</span></center><br/>
            {/if}
            {if $error == '5'}
            <center><span class="error">{$wrong_pass}</span></center><br/>
            {/if}

        <form name="form1" action="#" method="POST">

            <input type="hidden" name="entry" value="true"/>

            <b>{$entry_type}</b><br/>


            <select id="rodzaj" name="rodzaj">
                {if $temp_found eq '0' && $cache_type !='6'}
                    <option onclick="showextra();" value="1" {if $rodz_select == '1'}selected="selected"{/if}>{$found}</option>
                    <option onclick="hideextra();" value="2" {if $rodz_select == '2'}selected="selected"{/if}>{$notfound}</option>
                {elseif $temp_found eq '0' && $cache_type =='6'}
                    <option onclick="showextra();" value="7" {if $rodz_select == '7'}selected="selected"{/if}>{$attended}</option>
                {/if}
                {if $cache_type == '6'}
                    <option onclick="hideextra();" value="8" {if $rodz_select == '8'}selected="selected"{/if}>{$will_attend}</option>
                {/if}
                <option onclick="hideextra();" value="3" {if $rodz_select == '3'}selected="selected"{/if}>{$notes}</option>
                {if $cache_type != '6'}
                    <option onclick="hideextra();" value="5" {if $rodz_select == '5'}selected="selected"{/if}>{$service}</option>
                {/if}
            </select>

            <br/><br/>

            <b>{$entry_date}</b><br/>
            <input class="data" type="text" name="date_d" value="{$date_d}"/> . <input class="data" type="text" name="date_m" value="{$date_m}"/> . <input class="dataY" type="text" name="date_Y" value="{$date_Y}"/><br/><br/>
            <b>{$entry_time}</b><br/>
            <input class="data" type="text" name="date_H" value="{$date_H}"/> . <input class="data" type="text" name="date_i" value="{$date_i}"/><br/><br/>

            <div id="extra">
                {if $temp_found eq '0'}
                    {if $topratingav  eq '1'}
                        <b>{$toprating}</b>
                        <input type="checkbox" name="rekomendacja"/><br/><br/>
                    {/if}
                    <b>{$entry_score}</b><br/>

                    <select name="ocena" id="ocena">
                        <option value="-4">{$no_score}</option>
                        <option value="-3">{$rate0}</option>
                        <option value="-1.5">{$rate1}</option>
                        <option value="0">{$rate2}</option>
                        <option value="1.5">{$rate3}</option>
                        <option value="3">{$rate4}</option>
                    </select>
                    <br/><br/>

                    {if $logpw!=""}
                        <b>{$entry_pass}</b><br/>
                        <input type='text' name='logpw'/><br/><br/>
                    {/if}
                {/if}
            </div>

            <b>{$entry_text}</b><br/>
            <textarea id="tekst" name="tekst" cols="20" rows="5">{$tresc}</textarea><br/><br/>

            <div class='menu'>
                <div class='button'><a href='javascript: document.form1.submit()'>{$add}</a></div>
            </div>
        </form>
        <br/>
    {/if}

    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}
