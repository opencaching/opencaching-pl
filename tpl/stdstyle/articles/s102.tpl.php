<!-- <script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<link rel="stylesheet" href="tpl/stdstyle/js/jquery_1.9.2_ocTheme/themes/cupertino/jquery.ui.all.css">
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/minified/jquery-ui.min.js"></script>
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.datepick-{language4js}.js"></script> -->


<link href="tpl/stdstyle/js/jquery.1.10.3/css/myCupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script src="tpl/stdstyle/js/jquery.1.10.3/js/jquery-1.9.1.js"></script>
<script src="tpl/stdstyle/js/jquery.1.10.3/js/jquery-ui-1.10.3.custom.js"></script>
<script src="tpl/stdstyle/js/jquery.1.10.3/development-bundle/ui/jquery.datepick-{language4js}.js"></script>
 
<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCT.css" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCTStats.css" />
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type='text/javascript' src="lib/js/GCT.js"></script>
<script type='text/javascript' src="lib/js/GCTStats.js"></script>
<script type='text/javascript' src="lib/js/wz_tooltip.js"></script>

<?php
$sNameOfStat = "";
$sTitleOfStat="";  
if ( isset( $_REQUEST[ "stat" ] ) )
{
	$sNameOfStat = $_REQUEST[ "stat" ];
}

if ( $sNameOfStat == "NumberOfFinds" )
	$sTitleOfStat = " {{ranking_by_number_of_finds_new}} ";

if ( $sNameOfStat == "MaintenanceOfCaches" )
	$sTitleOfStat = " {{ranking_by_maintenace}} ";
?>

<table class="content" width="97%">
	<tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="{{stats}}" title="{{stats}} <?php echo $sTitleOfStat?>" align="middle" /><font size="4">  <b>{{statistics}}: <?php echo $sTitleOfStat?></b></font></td></tr>
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


<!-- content-title-noshade -->
<div class="GCT-div" >

<table width="100%" >
<tr>
	<!-- Begin of Filter -->
	<td>
 		<form name="FilterDate" style="display:inline; " action='articles.php' method="get">
			<input type="hidden" value="s102" name="page" >
			<input type="hidden" value="<?php echo $sNameOfStat?>" name="stat" id = "stat" >
			<input type="hidden" name="DateFrom" id="DateFrom" value="" >
			<input type="hidden" name="DateTo" id="DateTo" value="" >
			<table	class = "GCT-div-table" >
				<tr>
					<td><input type="radio" name="rRD" id="rR" value="R" <?php if ($sRD == "R") echo "checked" ?> ></td>
					<td width="10px">{{FiltrYear}}:</td>
					<td width="64px"> <input type="text" name="Rok" value="<?php echo $sRok?>"; style="width:30px; text-align: center" maxlength="4" onclick="GCTStatsSetRadio( 'Rok' )"></td>			
					<td >{{FiltrMonth}}: <input type="text" value="<?php echo $sMc?>"  name="Mc" style="width:20px; text-align: center" maxlength="2" onclick="GCTStatsSetRadio( 'Rok' )"></td>		
					<td width="90px" rowspan=2; width="70px"  style="text-align: center"> <button type="submit" name="bFilterDate" />{{Filter}}</td>			
				</tr>
				
				<tr>
					<td><input type="radio" name="rRD" id="rD" value="D" <?php if ($sRD == "D") echo "checked" ?>></td>
					<td>{{Dates}}:</td>
					<td colspan=2>		
					<input type="text" id="datepicker" name="DataOd" onclick="GCTStatsSetRadio( 'Data' )" value="<?php echo $sDataOd?>" style="width:60px; text-align: left"  maxlength="10">&nbsp&nbsp-
					<input type="text" id="datepicker1" name="DataDo" onclick="GCTStatsSetRadio( 'Data' )" value="<?php echo $sDataDo?>" style="width:60px; text-align: left"  maxlength="10">
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
						&nbsp&nbsp&nbsp<button type="submit" value={{search}} name="bFindUser" style="font-size:12px;width:70px;"; onClick ="GCTStatsFindUser( document.FindUser.User.value )"  />{{search}}</button>
						
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


<table width="100%" >
<tr>
	<td>				
<!-- 	<table	class = "GCT-div-table" >
			<tr>
				<td width = "150px" align = "center" >
				<form name="ChartHelp" style="display:inline;" action="" onsubmit="return false;" >										
					<button name="bChartHelp" id="bChartHelp"  />Help</button>
				</form>
				</td>
			</tr>
		</table>-->
		
	</td>

	<td>				
		<table class="GCT-div-table" >
			<tr>				
				<td>
				<form name="Details" style="display:inline;" action="" onsubmit="return false;" >
					{{Selected}}:&nbsp&nbsp<input type="text" name="SelectedUser" id="SelectedUser" class="GCT-div-readOnly" style="width:20px" readonly >									
					&nbsp&nbsp&nbsp&nbsp<button name="bDetails" id="bDetails"  />{{chart}}</button>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<span id ="bChartHelp" style="cursor: pointer">?</span>					
				</form>
				</td>
			</tr>
		</table>
		
	</td>
	
	
<!-- Begin of Position   width = "200px" align = "center"-->
	<td align="right">				
		<table	class = "GCT-div-table" >
			<tr>
				<td >
				<form name="Position" style="display:inline;" action="" onsubmit="return false;" >
					<input type="hidden" value="0" name="RealPosOfTable" >
					{{my_position}}:&nbsp&nbsp<input type="text" name="Ranking" id="Ranking" class="GCT-div-readOnly" style="width:70px" readonly >
					&nbsp&nbsp&nbsp&nbsp<button name="bGo" onClick ="GCTStatsGotoPosition(document.Position.RealPosOfTable.value)"  />{{go}}</button>
				</form>
				</td>
			</tr>
		</table>
	</td>
<!-- End of Position -->	
</tr>
</table>


<br>




</div> <!-- End of GCT-div --> 

<?php include ("t102.php"); ?>


</div>

    <script type="text/javascript">
      google.load('visualization', '1', {'packages':['corechart'], 'language': '{language4js}'});      
    </script>


<div  id="dialog"  >
</div>


<div  id="HelpDialog"  >
{{HelpHowToSelect}}
</div>


<script type="text/javascript">
TimeTrack( "END", "S102" );
</script>
