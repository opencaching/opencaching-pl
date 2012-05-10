<div class="content2-pagetitle"> 
<img src="tpl/stdstyle/images/blue/opensprawdzacz32x32.png" class="icon32" alt="geocache" title="geocache" align="middle" /> 
OpenSprawdzacz	
</div> 
<div  class="notice" style="height:70px;">Aby dodać swoją skrzynkę do OpenSprawdzacza: W panelu edycji kesza dodaj nowy waypoint "Punkt koncowy" i zaznacz "OpenSprawdzacz"<br>
Nie zapomnij ustawić status waypointa na "niewidoczny".

</div>

{sekcja_1_start}
<div class="content2-container bg-blue02">
<p class="content-title-noshade-size1">
<img src="tpl/stdstyle/images/blue/empty.png" alt="" align="middle" /> 
{{os_komunikat1}}
</p></div>

<p>{formularz}</p>
<div class="content2-container bg-blue02">
<p class="content-title-noshade-size1">
<img src="tpl/stdstyle/images/blue/empty.png" alt="" align="middle" /> 
{{os_komunikat2}}
</p></div>
<br/><br/><br/>
<div class="searchdiv">
<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
 <tr>
  <td><strong>{{cache_type}}</strong></td>
  <td><strong>{{cache_name}}</strong></td>
  <td>{{owner_label}}</td>
  <td><strong>status</strong></td>
  <td>{{os_pr}}</td>
  <td>{{os_sukc}}</td>
 </tr>
 <tr>
  <td colspan="6"><hr></hr></td>
 </tr>
		{keszynki}
 <tr>
  <td colspan="6"><hr></hr></td>
 </tr>
</table>
</div>


<p></p>
{sekcja_1_stop}

{sekcja_2_start}
<div class="searchdiv"> 
<br/> 
<p>{{os_kesz}}:<b> {cachename}</b>  - {wp_oc}, {{os_autor}} <i>{ofner}</i> </p> 
<p>{okienka}</p> 
{sekcja_formularz_opensprawdzacza_start}
<form name="sprawdzeniie" action="opensprawdzacz.php" method="post">
 <input type="hidden" name="cacheid" value="{cacheid}">
 <input type="hidden" name="op_keszynki" value="{wp_oc}">
 {{os_podaj_twoje}}:<br/><br/>
 {{os_stopnie}} N: <input type="text" name="stopnie_N" maxlength="2" size="2" />° 
 {{os_minuty}} N:  <input type="text" name="minuty_N"  maxlength="6" size="5" onkeyup="this.value=this.value.replace( /,/g,'.' );" /><br/><br/>
 {{os_stopnie}} E: <input type="text" name="stopnie_E" maxlength="2" size="2" />° 
 {{os_minuty}} E:  <input type="text" name="minuty_E"  maxlength="6" size="5" onkeyup="this.value=this.value.replace( /,/g,'.' );" /><br/><br/><br/> 
 <button type="submit" name="spr_wsp" value="spr_wsp" style="font-size:14px;width:160px"><b>{{os_sprawdz}}</b></button>
 </form>
{sekcja_formularz_opensprawdzacza_stop}
</br /> </div> 
{sekcja_2_stop}

{sekcja_3_start}
<p>{test1}<br/>{wynik}</p>
<p>{{os_proba}} {licznik_zgadywan} {{os_razy}} {ile_prob} {{os_razy_koniec}}</p>
{sekcja_3_stop}

{sekcja_4_start}
<p><br /><br />
{{os_za_godzine}}
</p>
{sekcja_4_stop}
