<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<link rel="stylesheet" href="tpl/stdstyle/js/jquery_1.9.2_ocTheme/themes/cupertino/jquery.ui.all.css">
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/minified/jquery-ui.min.js"></script>
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.datepick-{language4js}.js"></script>
 
 
<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCT.css" />
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type='text/javascript' src="lib/js/GCT.js"></script>
<script type='text/javascript' src="lib/js/wz_tooltip.js"></script>



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
//if ( $debug_page )
//	echo "<script type='text/javascript'>TimeTrack( 'DEBUG' );</script>";  
?>

{{StatTestVer}}<br>
{{PrevVersion}}



<div class="searchdiv">


<?php

$sRok = "";
$sMc = "";
$sDataOd = "";
$sDataDo = "";
$sRD = "R";



if ( !isset( $_REQUEST[ "init" ] ) )
{
	$sRok = $_REQUEST[ "Rok" ];
	$sMc = $_REQUEST[ "Mc" ];
	
	$sDataOd = $_REQUEST[ "DataOd" ];
	$sDataDo = $_REQUEST[ "DataDo" ];
	
	$sRD = $_REQUEST[ "rRD" ];	
}

if ( ( isset( $_REQUEST[ "init" ] ) or intval($sMc) > 12 or intval($sMc) < 0 or intval($sRok) < 0 )
or ( intval($sMc) != 0 and intval($sRok) == 0 ) )	
{
	$sRok = date( "Y" );
	$sMc = date( "m" );
	
	$_REQUEST[ "Rok" ] = $sRok;
	$_REQUEST[ "Mc" ] = $sMc;
	
	$_REQUEST[ "DataOd" ] = $sDataOd;
	$_REQUEST[ "DataDo" ] = $sDataDo;
		
	$_REQUEST[ "rRD" ] = $sRD;
}

?> 



<script type="text/javascript">


function GCTSetRadio(name )
{	
	var radio;
		
	if ( name == "Rok" ) 
		radio = document.getElementById("rR");
	else
		radio = document.getElementById("rD");
	
	radio.checked= true;
}


function GCTGotoProfil( link )
{
	window.location.href = link;
}


function GCTGotoPosition()
{
	var myPos = parseInt( document.Position.RealPosOfTable.value );
	
	if ( myPos != 0 )
	{
		gct.setAsSelected( myPos );	
		gct.goToPosition( myPos, 1 );
	}
}

function GCTFindUser()
{
	var user = document.FindUser.User.value;
	user = user.toUpperCase();	
	var userMax = user + 'z';
	
	var nrRowsArray = gct.getFilteredRows( [{column: 4, minValue: user, maxValue: userMax}] );


	if ( nrRowsArray.length == 1 )
	{
		/*var v = gct.getValue( nrRowsArray[0], 1 );
		v = '<span style="color: red">' + v + '</span>';
		alert( v );
		gct.modifyValue( nrRowsArray[0], 1, v );*/
		
		gct.setAsSelected( nrRowsArray[0] );
		gct.goToPosition( nrRowsArray[0], 1  );
	} 
	else
		gct.showRows( nrRowsArray, 1 );
	
}


</script>


<!-- content-title-noshade -->
<div class="< GCT-div" >

<table width="100%" >
<tr>
	<!-- Begin of Filter -->
	<td>
 		<form name="FilterDate" style="display:inline; " action='articles.php' method="get">
			<input type="hidden" value="s102" name="page" >
			<table	class = "GCT-div-table" >
				<tr>
					<td><input type="radio" name="rRD" id="rR" value="R" <?php if ($sRD == "R") echo "checked" ?> ></td>
					<td width="10px">{{FiltrYear}}:</td>
					<td width="64px"> <input type="text" name="Rok" value="<?php echo $sRok?>"; style="width:30px; text-align: center" maxlength="4" onclick="GCTSetRadio( 'Rok' )"></td>			
					<td >{{FiltrMonth}}: <input type="text" value="<?php echo $sMc?>"  name="Mc" style="width:20px; text-align: center" maxlength="2" onclick="GCTSetRadio( 'Rok' )"></td>		
					<td width="90px" rowspan=2; width="70px"  style="text-align: center"> <button type="submit" name="bFilterDate" />{{Filter}}</td>			
				</tr>
				
				<tr>
					<td><input type="radio" name="rRD" id="rD" value="D" <?php if ($sRD == "D") echo "checked" ?>></td>
					<td>{{Dates}}:</td>
					<td colspan=2>		
					<input type="text" id="datepicker" name="DataOd" onclick="GCTSetRadio( 'Data' )" value="<?php echo $sDataOd?>" style="width:60px; text-align: left"  maxlength="10">&nbsp&nbsp-
					<input type="text" id="datepicker1" name="DataDo" onclick="GCTSetRadio( 'Data' )" value="<?php echo $sDataDo?>" style="width:60px; text-align: left"  maxlength="10">
					</td>									
				</tr>
		
			</table>
 		</form>
	</td>
	<!-- END of Filter -->

	<!-- EMPTY -->
	<!-- <td width="124px"> </td> -->

	<!-- Begin of User -->
	<td align="right">	
		<table	class = "GCT-div-table" >
			<tr >
				<td >
					<form name="FindUser" style="display:inline;" action="" onsubmit="return false;">
						{{user}}:&nbsp&nbsp<input type="text" name="User" value=""; style="width:100px; text-align: left; ">
						&nbsp&nbsp&nbsp<button type="submit" value={{search}} name="bFindUser" style="font-size:12px;width:70px;"; onClick ="GCTFindUser()"  />{{search}}</button>
					</form>
				</td>
			</tr>
		</table>
	</td>
<!-- End of User -->
</tr>
</table>



<hr style="color: black">
<br>
<!-- Begin of Position -->
<table width="100%" >
<tr>	 	
	<td align="right">				
		<table	class = "GCT-div-table" >
			<tr>
				<td >
				<form name="Position" style="display:inline;" action="" onsubmit="return false;" >
					<input type="hidden" value="0" name="RealPosOfTable" >
					{{my_position}}:&nbsp&nbsp<input type="text" name="Ranking" id="Ranking" style="width:70px; text-align: center; color: black;  font-weight: bold; font-size:12px; background-color: #FAFAFA;" readonly >
					&nbsp&nbsp&nbsp&nbsp<button name="bGo" onClick ="GCTGotoPosition()"  />{{go}}</button>
				</form>
				</td>
			</tr>
		</table>
	</td>
	
</tr>
</table>
<!-- End of Position -->
<br>


</div> <!-- End of GCT-div --> 

<?php include ("t102.php"); ?>


</div>

<script type="text/javascript">
TimeTrack( "END", "S102" );
</script>
