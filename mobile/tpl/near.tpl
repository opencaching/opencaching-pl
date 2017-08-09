{capture name=javascript}
    {literal}
        <script type="text/javascript">

            function round(number, x) {
                var x = (!x ? 2 : x);
                return Math.round(number * Math.pow(10, x)) / Math.pow(10, x);
            }



            window.onload = function () {
                if (navigator.geolocation) {
                    document.getElementById("locate").innerHTML = "<div class='menu'><div class='button'><a href='javascript: nav();'>{/literal}{$localize_me}{literal}</a></div></div>";
                }

            }

            function handle_geolocation_query(position) {
                var n = position.coords.latitude + "";
                var e = position.coords.longitude + "";
                var ntemp = String(n).split(".");
                var etemp = String(e).split(".");
                var nmin = ntemp[1].substring(0, 5);
                while (nmin.length < 5)
                    nmin = nmin + "0";
                var emin = etemp[1].substring(0, 5);
                while (emin.length < 5)
                    emin = emin + "0";
                document.getElementById('Nminuty').value = round(nmin / 1666.66, 3);
                document.getElementById('Eminuty').value = round(emin / 1666.66, 3);
                document.getElementById('Nstopien').value = ntemp[0];
                document.getElementById('Estopien').value = etemp[0];
            }
            function nav() {

                if (navigator.geolocation)
                    navigator.geolocation.getCurrentPosition(handle_geolocation_query);
            }

        </script>
    {/literal}
{/capture}

{if $smarty.capture.javascript ne ""}{assign var=HeaderContent value="`$HeaderContent``$smarty.capture.javascript`"}{/if}

{$pagename=$seek_near}

{include file="./tpl/header.inc.tpl"}

<div id='pagetitle'>{$seek_near2}</div>

<div id="content">

    {if $error eq 1}
        <center><span class="error">{$wrong_cords}</span></center><br/>
        {/if}
        {if $error eq 2}
        <center><span class="error">{$no_city}</span></center><br/>
        {/if}

    <form action="./near.php" method="get" name="form1">

        <b>{$cords}</b><br/><i>(00 ° 00.000')</i><br/><br/>

        <select name="ns" class="dataY">
            <option value="N">N</option>
            <option value="S">S</option>
        </select>&nbsp;

        <input type="text" class="data" id="Nstopien" name="Nstopien" placeholder='00' value='{if isset($smarty.get.Nstopien)}{$smarty.get.Nstopien}{/if}'/>°
        <input type="text" id="Nminuty" name="Nminuty" class="cords" placeholder='00.000' value='{if isset($smarty.get.Nminuty)}{$smarty.get.Nminuty}{/if}'/>'<br/><br/>

        <select name="ew" class="dataY">
            <option value="E">E</option>
            <option value="W">W</option>
        </select>&nbsp;

        <input type="text" class="data" id="Estopien" name="Estopien" placeholder='00' value='{if isset($smarty.get.Estopien)}{$smarty.get.Estopien}{/if}'/>°
        <input type="text" id="Eminuty" name="Eminuty" class="cords" placeholder='00.000' value='{if isset($smarty.get.Eminuty)}{$smarty.get.Eminuty}{/if}'/>'<br/><br/>


        <div id='locate'>

        </div>

        <br/>
        <b>{$radius}</b><br/>

        <select name="radius">
            <option value='1'>1 km</option>
            <option value='3' selected>3 km</option>
            <option value='5'>5 km</option>
            <option value='10'>10 km</option>
            <option value='15'>15 km</option>
            <option value='25'>25 km</option>
        </select><br/><br/>



        <b>{$skip_caches}</b><br/>
        <table style="margin: auto" cellspacing="10">
            {if isset($smarty.session.user_id)}
                <tr><td><input type='checkbox' name='skip_mine' /></td><td>{$skip_mine}</td></tr>
                <tr><td><input type='checkbox' name='skip_found' /></td><td>{$skip_found}</td></tr>
                <tr><td><input type='checkbox' name='skip_ignored' /></td><td>{$skip_ignored}</td></tr>
                    {/if}
            <tr><td><input type='checkbox' name='skip_inactive' checked/></td><td>{$skip_inactive}</td></tr>
        </table>
        <br/>


        <div class='menu'>
            <div class='button'><a href='javascript: document.form1.submit()'>{$seek_button}</a></div>
        </div>

    </form><br/>
    <hr/>
    <form action="./near.php" method="post" name="form2">
        <b>{$city}</b><br/>
        <input type="text"  id="city" name="city" /><br/><br/>
        <b>{$radius}</b><br/>
        <select name="radius">
            <option value='1'>1 km</option>
            <option value='3' selected>3 km</option>
            <option value='5'>5 km</option>
            <option value='10'>10 km</option>
            <option value='15'>15 km</option>
            <option value='25'>25 km</option>
        </select><br/><br/>


        <b>{$skip_caches}</b><br/>
        <table style="margin: auto" cellspacing="10">
            {if isset($smarty.session.user_id)}
                <tr><td><input type='checkbox' name='skip_mine' /></td><td>{$skip_mine}</td></tr>
                <tr><td><input type='checkbox' name='skip_found' /></td><td>{$skip_found}</td></tr>
                <tr><td><input type='checkbox' name='skip_ignored' /></td><td>{$skip_ignored}</td></tr>
                    {/if}
            <tr><td><input type='checkbox' name='skip_inactive' checked/></td><td>{$skip_inactive}</td></tr>
        </table>
        <br/>


        <div class='menu'>
            <div class='button'><a href='javascript: document.form2.submit()'>{$seek_button}</a></div>
        </div>

    </form><br/>


    {include file="./tpl/backbutton.inc.tpl"}

</div>

{include file="./tpl/footer.inc.tpl"}
