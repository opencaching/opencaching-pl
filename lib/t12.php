<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type="text/javascript" src="lib/js/GCT.js"></script>
<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>

<div id='idGTC' ></div>

<?php
 echo "<script type='text/javascript'>

	var gct = new GCT( 'idGTC' );

	gct.addColumn('number', '".tr('ranking')."', 'width:60px; text-align: left;');
	gct.addColumn('number', '".tr('caches')."', 'width:60px; text-align: left;');
	gct.addColumn('string', '".tr('user')."' );	    	
   	    	
</script>";

 

require_once('./lib/db.php');

$sRok = "";
$sMc = "";
$sCondition = "";

if ( isset( $_REQUEST[ 'Rok' ]) )
	$sRok =  $_REQUEST[ 'Rok' ];

if ( isset( $_REQUEST[ 'Mc' ]) )
	$sMc =  $_REQUEST[ 'Mc' ];



if ( $sRok <> "" and $sMc <> "" )
{
	$sData_od = $sRok.'-'.$sMc.'-'.'01';
	
	$dDate = new DateTime( $sData_od );
	$dDate->add( new DateInterval('P1M') );
	
	$sData_do = $dDate->format( 'Y-m-d');
	
	$sCondition = "and date >='" .$sData_od ."' and date < '".$sData_do."'"; 
}



$dbc = new dataBase();
$query = 
		"SELECT COUNT(*) count, u.username username, u.user_id user_id, 
		u.date_created date_created, u.description description
		
		FROM 
		cache_logs cl
		join caches c on c.cache_id = cl.cache_id
		join user u on cl.user_id = u.user_id
		
		WHERE cl.deleted=0 AND  cl.type=6 and c.user_id <> cl.user_id "
		
		. $sCondition .
		
		"GROUP BY u.user_id   
		
		ORDER BY count DESC, u.username ASC";

		
$dbc->multiVariableQuery($query);

echo "<script type='text/javascript'>";

$nRanking = 0;
$sOpis = "";
$nOldCount = -1;


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
		$sOpis = "Niestety, brak opisu <img src=lib/tinymce/plugins/emotions/images/smiley-surprised.gif />";
	
	
	$sProfil = "<b>Zarejestrowany od:</b> ".$record[ "date_created" ]
		 ." <br><b>Opis: </b> ".$sOpis;

	$nCount = $record[ "count" ];
	$sUsername = '<a href="viewprofile.php?userid='.$record["user_id"].'" onmouseover="Tip(\\\''.$sProfil.'\\\')" onmouseout="UnTip()"  >'.$record[ "username" ].'</a>';

	
	if ( $nCount != $nOldCount )
	{				
		$nRanking++;
		$nOldCount = $nCount; 
	}

	echo "
	gct.addEmptyRow();
	gct.addToLastRow( 0, $nRanking );
	gct.addToLastRow( 1, $nOldCount );
	gct.addToLastRow( 2, '$sUsername' );
	";
	
	
}

echo "gct.drawTable();";
echo "</script>";




/* Ex aequo
 * 
 * $nRanking = 0;
$sOpis = "";
$sLUsername = "";
$nOldCount = -1;


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
		$sOpis = "Niestety, brak opisu <img src=lib/tinymce/plugins/emotions/images/smiley-surprised.gif />";
	
	
	$sProfil = "<b>Zarejestrowany od:</b> ".$record[ "date_created" ]
		 ." <br><b>Opis: </b> ".$sOpis;

	$nCount = $record[ "count" ];
	$sUsername = '<a href="viewprofile.php?userid='.$record["user_id"].'" onmouseover="Tip(\\\''.$sProfil.'\\\')" onmouseout="UnTip()"  >'.$record[ "username" ].'</a>';
	
	if ($nOldCount == -1 )
		$nOldCount = $nCount;
	
	if ( $nCount != $nOldCount )
	{				
		$nRanking++;
		
		echo "
		gct.addEmptyRow();
		gct.addToLastRow( 0, $nRanking );
		gct.addToLastRow( 1, $nOldCount );
		gct.addToLastRow( 2, '$sLUsername' );
		";
		
		$sLUsername = $sUsername;				
		$nOldCount = $nCount; 
	}
	else
	{
		if ( $sLUsername <> "" )
			$sLUsername .= ", " ;
		
		$sLUsername .= $sUsername;
	} 
	
}

if ( $nOldCount != -1 )
{
	$nRanking++;
	
	echo "
	gct.addEmptyRow();
	gct.addToLastRow( 0, $nRanking );
	gct.addToLastRow( 1, $nOldCount );
	gct.addToLastRow( 2, '$sLUsername' );
	";
}
*/

?>


 
 
 <!-- 
 <script type="text/javascript">

	var gct = new GCT( 'idGTC' );
    	    	
   	gct.addColumn('string', 'Names', 'width:700px; text-align: left;');    	
</script>

<?php

echo "
<script type='text/javascript'>
  	gct.addEmptyRow();
  	gct.addToLastRow( 0, 'Jacek');
    	
  	gct.addEmptyRow();
  	gct.addToLastRow( 0, 'Kasia');
</script>"	
		
?>


<script type="text/javascript">  	
   	gct.drawTable();
        
 </script>	
  -->