<table class="content" width="97%">
	<tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{{stats}}" title="{{stats}} {{ranking_by_maintenace}}" align="middle" /><font size="4">  <b>{{statistics}}: {{ranking_by_maintenace}}</b></font></td></tr>
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
<form name="filtrDat" style="display:inline;" action='articles.php?page=s12' method="get">
	<table style="border: solid 1px;">
		<tr>
		<input type="hidden" value="s12" name="page" >
		<td width="100px">{{FiltrYear}}:&nbsp&nbsp<input type="text" name="Rok" value="<?php echo $sRok?>"; style="width:30px; text-align: center"  maxlength="4"></td>
		<td width="110px">{{FiltrMonth}}:&nbsp&nbsp<input type="text" value="<?php echo $sMc?>"  name="Mc" style="width:20px; text-align: center" maxlength="2"></td>		
		<td width="100px"> <button type="submit" name="submit" value="{{search}}" style="font-size:12px;width:100px;"/><b>{{search}}</b></button></td>
		
		</tr>
	</table>
</form> 

{{StatTestVer}}

<hr style="color: black">
<br>
</span>



<?php include ("t12.php"); ?>

</div>

<script type="text/javascript">
TimeTrack( "END", "S12" );
</script>