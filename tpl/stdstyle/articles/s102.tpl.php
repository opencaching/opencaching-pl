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


<script type="text/javascript">
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


<span class="content-title-noshade" >






<table >

<tr>
<td>
 <form name="FilterDate" style="display:inline; " action='articles.php' method="get">
	<input type="hidden" value="s102" name="page" >
	
	<table style="border-bottom: solid 1px #aaaaaa; border-right: solid 1px #aaaaaa; background: #F0F0F0">
		<tr>
			<td width="70px">{{FiltrYear}}: <input type="text" name="Rok" value="<?php echo $sRok?>"; style="width:30px; text-align: center"  maxlength="4"></td>			
			<td width="80px">{{FiltrMonth}}: <input type="text" value="<?php echo $sMc?>"  name="Mc" style="width:20px; text-align: center" maxlength="2"></td>		
			<td width="70px"> <button type="submit" name="submit" value="{{search}}" style="font-size:12px;width:70px;"/><b>{{Filter}}</b></button></td>
		</tr>
	</table>
</form>
</td>
<td>
<table style="border-bottom: solid 1px #aaaaaa; border-right: solid 1px #aaaaaa; background: #F0F0F0">
<tr>
<td width="270px">

</form>
<form name="FindUser" style="display:inline;" action="" onsubmit="return false;">
&nbsp{{user}}:&nbsp<input type="text" name="User" value=""; style="width:100px; text-align: left">
</form>
&nbsp&nbsp&nbsp<button  name="bFindUsr" style="font-size:12px;width:70px;"; onClick ="GCTFindUser()"  /><b>{{search}}</b></button>

</td>
</tr>
</table>

</td>

<td width="8px">
 
</td>

<td>

<table style="border-bottom: solid 1px #aaaaaa; border-right: solid 1px #aaaaaa; background: #F0F0F0">
<tr>
<td width="220px">
<form name="Position" style="display:inline;" action="" onsubmit="return false;" >
<input type="hidden" value="0" name="RealPosOfTable" >
&nbsp{{my_position}}:&nbsp&nbsp<input type="text" name="Ranking" id="Ranking" style="width:70px; text-align: center; color: black;  font-weight: bold; font-size:12px" readonly>
</form>
<button  name="bGo" style="font-size:12px;width:50px;"; onClick ="GCTGotoPosition()"  /><b>{{go}}</b></button>
</td>
</tr>
</table>


</td>


</tr>
</table>
<br>
<!-- <hr style="color: black"> -->


</span>



<?php include ("t102.php"); ?>

</div>

<script type="text/javascript">
TimeTrack( "END", "S102" );
</script>
