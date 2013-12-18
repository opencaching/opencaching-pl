<table class="content" width="97%">
	<tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{{stats}}" title="{{stats}} {{ranking_by_maintenace}}" align="middle" /><font size="4">  <b>{{statistics}}: {{ranking_by_number_of_finds_new}}</b></font></td></tr>
	<tr><td class="spacer"></td></tr>
</table>

<script type="text/javascript">
TimeTrack( "START" );
</script>

<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>

<?php
global $debug_page; 
if ( $debug_page )
	echo "<script type='text/javascript'>TimeTrack( 'DEBUG' );</script>";  
?>



<div class="searchdiv">

<?php

$sRok = "";
$sMc = "";



if ( !isset( $_REQUEST[ "init" ] ) )
{
	$sRok = $_REQUEST[ "Rok" ];
	$sMc = $_REQUEST[ "Mc" ];
}

if ( ( isset( $_REQUEST[ "init" ] ) or intval($sMc) > 12 or intval($sMc) < 0 or intval($sRok) < 0 )
or ( intval($sMc) != 0 and intval($sRok) == 0 ) )	
{
	$sRok = date( "Y" );
	$sMc = date( "m" );
	
	$_REQUEST[ "Rok" ] = $sRok;
	$_REQUEST[ "Mc" ] = $sMc;
}

?> 


<span class="content-title-noshade" >
<form name="filtrDat" style="display:inline;" action='articles.php' method="get">
	<table style="border: solid 1px;">
		<tr>
		<input type="hidden" value="s102" name="page" >
		<td width="100px">{{FiltrYear}}:&nbsp&nbsp<input type="text" name="Rok" value="<?php echo $sRok?>"; style="width:30px; text-align: center"  maxlength="4"></td>
		<td width="110px">{{FiltrMonth}}:&nbsp&nbsp<input type="text" value="<?php echo $sMc?>"  name="Mc" style="width:20px; text-align: center" maxlength="2"></td>		
		<td width="320px"> <button type="submit" name="submit" value="{{search}}" style="font-size:12px;width:100px;"/><b>{{search}}</b></button></td>
		<td width="190px">Moja pozycja w rankingu:&nbsp&nbsp<input type="text" name="Ranking" id="Ranking" style="width:40px; text-align: center; color:red; font-weight: bold; font-size:15px" readonly></td>
		
		</tr>
	</table>
</form> 

{{StatTestVer}}<br>
Poprzednia wersja znajduje się na stronie: Statystyki (na samym końcu)
<br>
<br>
Kilka słów o możliwościach:<br>
1. stronicowanie, ustawiłem na 10 wpisów na stronie (docelowo będzie na 100) - więc na razie nie widać efektów<br>
2. sortowanie poprzez klikanie na nagłówek kolumny<br>
3. po najechaniu myszką na konkretnego użytkownika pojawia się krótka notka o nim; kolor czcionki zmienia się na dekadencko czarny; po kliknięciu na użytkownika link przeniesie nas do profilu użytkownika (to akurat standard);<br> 
4. wstępne filtrowanie po bieżącym mc i roku<br>
5. można wykasować filtry (oba) i jechać od narodzin ... hmmm OC lub tylko mc i wtedy ... wiadomo <br>
6. klikając na daną pozycję (wszędzie tylko nie na link) można ją podświetlić z szarego na bardziej szary :p<br>
<hr style="color: black">
<br>
</span>



<?php include ("t102.php"); ?>

</div>

<script type="text/javascript">
TimeTrack( "END", "S12" );
</script>
