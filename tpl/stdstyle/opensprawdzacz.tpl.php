<?php ?>

<script type="text/javascript">
<!--
    function clearForms()
    {
        var i;
        for (i = 0; (i < document.forms.length); i++) {
            document.forms[i].reset();
        }
    }

    function toggle() {
        var ele = document.getElementById("toggleText");
        var text = document.getElementById("displayText1");
        var text2 = document.getElementById("displayText2");
        var os_tytul = document.getElementById("os_tytul");
        var help_link1 = document.getElementById("help_link1");
        var help_link2 = document.getElementById("help_link2");
        var cialo = document.getElementById("cialo");

        if (ele.style.display == "block")
        {
            ele.style.display = "none";
            // os_tytul.style.display = "block";
            text.innerHTML = "{{os_zobo}}";
            text2.innerHTML = "{{os_zobo}}";
            help_link1.style.display = "block";
            help_link2.style.display = "none";
            cialo.style.display = "block";
        }
        else
        {
            ele.style.display = "block";
            // os_tytul.style.display = "none";
            text.innerHTML = "{{os_powrot}}";
            text2.innerHTML = "{{os_powrot}}";
            help_link1.style.display = "none";
            help_link2.style.display = "block";
            cialo.style.display = "none";
        }
    }
// -->
</script>

<body onLoad="clearForms()" onUnload="clearForms()">

    <div class="content2-pagetitle">
        <img src="tpl/stdstyle/images/blue/opensprawdzacz32x32.png" class="icon32" alt="geocache" title="geocache" align="middle" />
        {{Open_Sprawdzacz}}
    </div>


    {sekcja_1_start}


    <div id="toggleText" style="display: none;" >
        <p>{{os_hlp_01}} <br /><br />
            {{os_hlp_02}} <br /><br />
            <b>{{os_hlp_03}}</b> <br /><br />
            {{os_hlp_04}}<br />
            {{os_hlp_05}} <br />
            {{os_hlp_06}} <br />
            {{os_hlp_07}} <br />
            {{os_hlp_08}} <br />
            {{os_hlp_09}} <br /><br />
            {{os_hlp_10}} <br /><br />
            {{os_hlp_11}}<br />
            {{os_hlp_12}} <br /><br />
            <b>{{os_hlp_13}}</b>
            {{os_hlp_14}} <br /><br />
            {{os_hlp_15}} <br />
            {{os_hlp_16}} <br />
            {{os_hlp_17}} <br /><br />
            {{os_hlp_18}}
        </p>
    </div>

    <div id="help_link1" class="notice" style="height:35px; display: block;">
        {{os_pomoc}}:
        <a id="displayText1" href="javascript:toggle();" class=links href="{os_script}">{{os_zobo}}</a>
    </div>

    <div id="help_link2" style="height:35px; display: none;"><br /><br />
        <a id="displayText2" href="javascript:toggle();" class=links href="{os_script}">{{os_zobo}}</a>
    </div>
    {sekcja_1_stop}

    <div id="cialo" style="display: block;">

        {sekcja_1_start}

        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1">
                <img src="tpl/stdstyle/images/blue/empty.png" alt="" align="middle" />
                {{os_komunikat1}}
            </p>
        </div>

        <p>{formularz}</p>
        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1">
                <img src="tpl/stdstyle/images/blue/empty.png" alt="" align="middle" />
                {{os_komunikat2}}
            </p>
        </div>

        <br/><br/><br/>

        <div class="searchdiv">
            <table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
                <tr>
                    <td><a href="{os_script}?sort=wpt">waypoint</a></td>
                    <td><a href="{os_script}?sort=nazwa">{{cache_name}}</a></td>
                    <td>{{os_typ}}</td>
                    <td>status</td>
                    <td><a href="{os_script}?sort=autor">{{owner_label}}</a></td>
                    <td><a href="{os_script}?sort=szczaly">{{os_pr}}</a></td>
                    <td><a href="{os_script}?sort=sukcesy">{{os_sukc}}</a></td>
                </tr>

                <tr>
                    <td colspan="7"><img src="tpl/stdstyle/images/blue/dot_blue.png" height="1" width="100%"/></td>
                </tr>
                {keszynki}
                <tr>
                    <td colspan="7"><img src="tpl/stdstyle/images/blue/dot_blue.png" height="5" width="100%"/></td>
                </tr>
            </table>
        </div>

        <p></p>
        {sekcja_1_stop}

        {sekcja_2_start}
        <div class="searchdiv">
            <br/>
            <div class="content2-container bg-blue02">
                <p class="content-title-noshade-size1">
                    <img src="tpl/stdstyle/images/blue/opensprawdzacz32x32.png" class="icon32" alt="" />
                    {{os_spr_form}}
                </p>
            </div>

            <p>
            <table width="99%">
                <tr>
                    <td width="40">{ikonka_keszyny}</td>
                    <td valign="top">
                        {wp_oc} <br /><b><a href="viewcache.php?wp={wp_oc}">  {cachename}</a></b>
                    </td>
                    <td> </td>
                    <td align="right">
                        {{os_autor}} <br/><i><a href="viewprofile.php?userid={id_uzyszkodnika}">{ofner}</a></i>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><img src="tpl/stdstyle/images/blue/dot_blue.png" height="1" width="100%"/></td></tr>
            </table>
            </p>

            <p>{okienka}</p>
            {sekcja_formularz_opensprawdzacza_start}
            <form name="sprawdzeniie" action="{os_script}" method="post">
                <input type="hidden" name="cacheid" value="{cacheid}">
                <input type="hidden" name="op_keszynki" value="{wp_oc}">
                {{os_podaj_twoje}}:<br/><br/>
                {{os_stopnie}} N: <input type="text" name="stopnie_N" maxlength="2" size="2" />°
                {{os_minuty}} N:  <input type="text" name="minuty_N"  maxlength="6" size="5" onkeyup="this.value = this.value.replace(/,/g, '.');" /><br/><br/>
                {{os_stopnie}} E: <input type="text" name="stopnie_E" maxlength="3" size="2" />°
                {{os_minuty}} E:  <input type="text" name="minuty_E"  maxlength="6" size="5" onkeyup="this.value = this.value.replace(/,/g, '.');" /><br/><br/><br/>
                <button type="submit" name="spr_wsp" value="spr_wsp" style="font-size:14px;width:160px"><b>{{os_sprawdz}}</b></button>
            </form>

            {sekcja_formularz_opensprawdzacza_stop}
            </br />
        </div>
        {sekcja_2_stop}



        {sekcja_3_start}
        <div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1">
                <img src="tpl/stdstyle/images/blue/empty.png" alt="" align="middle" />
                {{os_wynik}}:
            </p>
        </div>

        <br/>

        <div class="searchdiv">
            <table class="content">
                <tr>
                    <td>{ikonka_yesno}</td>
                    <td class="content">
                        {test1}<br><br>

                        {wynik}
                        {twoje_ws}<br><br>
                        {save_mod_coord}<br>
                        <p><i>{waypoint_desc}</i></p>
                    </td>
                </tr>
            </table>


            <p><br /><br /><br /><br /></p>
            <p>{{os_proba}} {licznik_zgadywan} {{os_razy}} {ile_prob} {{os_razy_srodek}} {ile_czasu} {{os_razy_koniec}}</p>
        </div>
        {sekcja_3_stop}

        {sekcja_4_start}
        <p><br /><br />
            {{os_za_godzine}}
        </p>
        {sekcja_4_stop}

        {sekcja_5_start}
        <p><br /><br />
            {ni_ma_takiego_kesza}
        </p>
        {sekcja_5_stop}

    </div>

