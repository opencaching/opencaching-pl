

<div id='idGTC' align = "center"> </div>



<?php
 echo "<script type='text/javascript'>

	var gct = new GCT( 'idGTC' );

	gct.addColumn('number', '".tr('Pos')."', 'text-align: left; ');
	gct.addColumn('number', '".tr('Nr')."', 'text-align: left; ');  
	gct.addColumn('string', '".tr('user')."', 'width:50px; text-align: left; font-weight: bold; ' ); 
 	gct.addColumn('string', '".tr('descriptions')."', 'font-family: curier new; font-style: italic; padding-bottom: 5px; padding-top: 5px;');		
 	gct.addColumn('string', 'UserName' );
	gct.hideColumns( [4] );
			
 </script>";
 
 
 

require_once('./lib/db.php');

$sRok = "";
$sMc = "";

$sDataOd = "";
$sDataDo = "";

$sData_od = "";
$sData_do = "";

$sRD = "";

$sCondition = "";
$nIsCondition = 0;
$nMyRanking = 0;


if ( isset( $_REQUEST[ 'Rok' ]) )
	$sRok =  $_REQUEST[ 'Rok' ];

if ( isset( $_REQUEST[ 'Mc' ]) )
	$sMc =  $_REQUEST[ 'Mc' ];

if ( isset( $_REQUEST[ 'rRD' ]) )
	$sRD =  $_REQUEST[ 'rRD' ];

if ( isset( $_REQUEST[ 'DataOd' ]) )
	$sDataOd =  $_REQUEST[ 'DataOd' ];

if ( isset( $_REQUEST[ 'DataDo' ]) )
	$sDataDo =  $_REQUEST[ 'DataDo' ];


if ( $sRD == "R"   )
{
	if ( $sRok <> "" and $sMc <> "" )
	{
		$sData_od = $sRok.'-'.$sMc.'-'.'01';
		
		$dDate = new DateTime( $sData_od );
		$dDate->add( new DateInterval('P1M') );
		$nIsCondition = 1;
	}
	
	if ( $sRok <> "" and $sMc == "" )
	{
		$sData_od = $sRok.'-01-01';
	
		$dDate = new DateTime( $sData_od );
		$dDate->add( new DateInterval('P1Y') );
		$nIsCondition = 1;
	}
	
	
	if ( $nIsCondition )
	{
		$sData_do = $dDate->format( 'Y-m-d');	
		$sCondition = "and date >='" .$sData_od ."' and date < '".$sData_do."'";	
	}
}
else
{	
	try {
		if ( $sDataOd <> "" )
		{
			$dDate = new DateTime( $sDataOd );
			$sData_od = $dDate->format( 'Y-m-d');
		}
			
		$dDate = new DateTime( $sDataDo );
		$dDate->add( new DateInterval('P1D') );
		$sData_do = $dDate->format( 'Y-m-d');
	
		if ( $sData_od <> "" )
			$sCondition = " and date >='" .$sData_od ."' ";
				 
		if ( $sData_do <> "" )
			$sCondition = $sCondition . " and date < '".$sData_do."' ";
	}
	
	catch (Exception $e)
	{
		$sCondition = "";
		
	}
	
}



$dbc = new dataBase();

$query = 
		"SELECT COUNT(*) count, u.username username, UPPER(u.username) UUN, u.user_id user_id, 
		DATE(u.date_created) date_created, u.description description
		
		FROM 
		cache_logs cl
		join caches c on c.cache_id = cl.cache_id
		join user u on cl.user_id = u.user_id
		
		WHERE cl.deleted=0 AND cl.type=1 "
		
		. $sCondition .		
		
		"GROUP BY u.user_id   		
		ORDER BY count DESC, u.username ASC";

		
$dbc->multiVariableQuery($query);

echo "<script type='text/javascript'>";



$nRanking = 0;
$sOpis = "";
$nOldCount = -1;
$nPos = 0;
$nMyRanking = 0;
$nMyRealPos = 0;





while ( $record = $dbc->dbResultFetch() )
{	
	if ( $record[ "description" ] <> "" )
	{
		$sOpis = $record[ "description" ];
		
		$sOpis = str_replace("\r\n", " ",$sOpis);
		$sOpis = str_replace("\n", " ",$sOpis);
		$sOpis = str_replace("'", "-",$sOpis);
		$sOpis = str_replace("\"", " ",$sOpis);		
	}
	else
		$sOpis = "";
	
	$sOpis = "".$sOpis;
	
	
	$sProfil = "<b>Zarejestrowany od:</b> ".$record[ "date_created" ];
		

	$nCount = $record[ "count" ];
	$sUUN = $record[ "UUN" ];
	$sDateCr = $record[ "date_created" ];
	
	$sUserClass ="";
	if ( $nRanking < 3 )
		$sUserClass = ' class="GCT-link-3"; '; 
	else 
		$sUserClass = ' class="GCT-link"; ';
	
	$sUserProfil = "viewprofile.php?userid=".$record['user_id'];
	
	$sUsername = '<span '.$record[ "username" ].$sUserClass.' onclick="GCTStatsGotoProfil( \\\''.$sUserProfil.'\\\' )"  onmouseover="Tip(\\\''.$sProfil.'\\\')" onmouseout="UnTip()"  >'.$record[ "username" ].'</span>';
	
	
	if ( $nCount != $nOldCount )
	{				
		$nRanking++;
		$nOldCount = $nCount; 
	}
	
	$nPos++;
	
	echo "
			gct.addEmptyRow();
			gct.addToLastRow( 0, $nRanking );
			gct.addToLastRow( 1, $nCount );
			gct.addToLastRow( 2, '$sUsername' );
			gct.addToLastRow( 3, '$sOpis' );
			gct.addToLastRow( 4, '$sUUN' );
		";
	
	if ( $usr['userid'] == $record[ 'user_id'] )
	{
		$nMyRanking = $nRanking;
		$nMyRealPos = $nPos-1;
		//echo " gct.addToLastRow( 3, '<span style=\"color:red\">$sUUN</span>' );";
	}


	
	
}



echo "gct.drawTable();";

echo "document.Position.Ranking.value = '".$nMyRanking." / ".$nRanking."';";
echo "document.Position.RealPosOfTable.value = '".$nMyRealPos."';";
echo "</script>";

?>

