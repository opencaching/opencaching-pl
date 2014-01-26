<html>
<head>
</head>

<body>
<div id="idGCB"></div>

<script type="text/javascript">
TimeTrack( "START" );
</script>

<?php
global $debug_page;
//if ( $debug_page )
//  echo "<script type='text/javascript'>TimeTrack( 'DEBUG' );</script>";
?>


<?php
echo "<script type='text/javascript'>";
echo "GCTLoad( 'ChartBar', '', 1 );";
echo "</script>";
?>


<script type="text/javascript">

    var gcb = new GCT( 'idGCB' );
    gcb.addColumn('string', 'UserName' );


</script>

<?php
$sEND = "";
$sDateCondition = "";
$sTypeCondition = "";

global $lang;

require_once('settings.inc.php');
require_once('language.inc.php');
require_once('cookie.class.php');

if ($cookie->is_set('lang'))
    $lang = $cookie->get('lang');

require_once('db.php');



$sUserIDLine = $_REQUEST[ "UserID"];
$sDateFrom = $_REQUEST[ "DF"];
$sDateTo = $_REQUEST[ "DT"];
$sNameOfStat = $_REQUEST[ "stat"];

$nDayInterval = 999;
$sGranulate = "";

if ( $sDateFrom <> ""  and  $sDateTo <> ""  )
{
    $dDateFrom = new DateTime( $sDateFrom );
    $dDateTo = new DateTime( $sDateTo );
    $interval = $dDateFrom->diff( $dDateTo );
    $nDayInterval = $interval->format( '%a' );
}

if ( $nDayInterval < 65 )
{
    $sGranulate = " (week( cl.date) + 1) period ";
}
else if ( $nDayInterval < 367 )
{
    $sGranulate = " month( cl.date) period ";
}
else
{
    $sGranulate = " year( cl.date) period ";
}


//echo $interval->format('%R%a days');
/*echo "<script type='text/javascript'>";
echo "alert( '$sGranulate' );";
echo "</script>";*/


if ( $sDateFrom <> "" )
    $sDateCondition .= "and date >='" .$sDateFrom."'";

if ( $sDateTo <> "" )
    $sDateCondition .= " and date < '".$sDateTo."' ";


if ( $sNameOfStat == "NumberOfFinds" )
    $sTypeCondition = " and  cl.type=1 ";

if ( $sNameOfStat == "MaintenanceOfCaches" )
    $sTypeCondition = " and  cl.type=6 ";


$asUserID = explode(",", $sUserIDLine);


if ( !strlen( $sUserIDLine )  )
    $sEND = tr2('SelectUsers', $lang );

if ( count( $asUserID ) > 30 )
    $sEND = tr2('more30', $lang );


echo "<script type='text/javascript'>";
if ( $sEND <> "" )
{
    echo "alert( '$sEND' );";
    $asUserID = explode(",", "");
}

echo "</script>";


$sCondition = "";
foreach( $asUserID as $sID )
{
    if( strlen( $sCondition ) )
        $sCondition = $sCondition . " or ";

    $sCondition = $sCondition . "cl.user_id = '". $sID."'";
}

if( strlen( $sCondition ) )
{
    $sCondition = " and ( " . $sCondition . " )";
}

$sCondition .= $sDateCondition;


/////////////////

$dbc = new dataBase();

/*$query = "SELECT * FROM cache_logs WHERE deleted=0 ";*/


$query =
"SELECT distinct " . $sGranulate . "
        FROM
        cache_logs cl

        WHERE cl.deleted=0 "

        .$sTypeCondition
        . $sCondition .

         " order by period";



$dbc->multiVariableQuery($query);


$aNrColumn= array();
$i = 0;




echo "<script type='text/javascript'>";


while ( $record = $dbc->dbResultFetch() )
{

    $nPeriod = $record[ 'period' ];



    $aNrColumn[ $nPeriod ] = $i;
    echo "gcb.addColumn('number', '$nPeriod');";

    $i = $i + 1;
}



////////////////////



//echo " var chartOpt = gcb.getChartOption();";
//echo " chartOpt.vAxis.title= '".tr2('NrCaches',$lang)."';";
echo "</script>";

unset( $dbc );
////////////////////////////

foreach( $asUserID as $sID )
{
    $sCondition = " and cl.user_id = '". $sID."'";
    $sCondition .= $sDateCondition;

    $dbc = new dataBase();

    $query =
    "SELECT u.username username, u.user_id user_id,
            " . $sGranulate . ",
            COUNT(*) count

            FROM
            cache_logs cl
            join caches c on c.cache_id = cl.cache_id
            join user u on cl.user_id = u.user_id

            WHERE cl.deleted=0 "

            .$sTypeCondition
            . $sCondition .

            "GROUP BY period";

    $dbc->multiVariableQuery($query);


    echo "<script type='text/javascript'>";


    $nStart = 1;
    while ( $record = $dbc->dbResultFetch() )
    {
        $nPeriod = $record['period'];
        $nVal = $record['count'];

        if ( $nStart == 1 )
        {
            $sUserName = $record[ 'username' ];
            $nUserId = $record[ 'user_id' ];

            echo "
            gcb.addEmptyRow();
            gcb.addToLastRow( 0, '$sUserName' );
            ";

            $nStart = 0;
        }


        $nrCol = $aNrColumn[ $nPeriod ];
        echo "gcb.addToLastRow( $nrCol+1 , $nVal );";
    }

    echo "</script>";

    unset( $dbc );
}
?>


<script type="text/javascript">
    gcb.drawChart( 1 );
</script>

<script type="text/javascript">
TimeTrack( "END", "SB102" );
</script>

</body>

</html>

