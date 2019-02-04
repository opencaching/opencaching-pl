<?php
use Utils\I18n\I18n;
?>

<div id='idGTC' align = "center"> </div>
<script>GCTLoad( 'ChartTable', '" . <?=I18n::getCurrentLang()?> . "' );</script>

<script>
    var gct = new GCT('idGTC');

    gct.addColumn('number', "<?php echo tr('Pos') ?>", 'text-align: left; ');
    gct.addColumn('number', "<?php echo tr('Nr') ?>", 'text-align: left; ');
    gct.addColumn('string', "<?php echo tr('user') ?>", 'width:50px; text-align: left; font-weight: bold; ');
    gct.addColumn('string', "<?php echo tr('descriptions') ?>", 'font-family: curier new; font-style: italic; padding-bottom: 5px; padding-top: 5px;');
    gct.addColumn('string', 'UserName');
    gct.addColumn('string', 'UserId');

    gct.hideColumns([4, 5]);
</script>

<?php
echo "<script>";
echo "gct.addChartOption('pagingSymbols', { prev: '" . tr('Prev1') . "', next: '" . tr('Next1') . "' });";
echo "</script>";

use Utils\Database\OcDb;
require_once (__DIR__ . '/ClassPathDictionary.php');
$sRok = "";
$sMc = "";

$sDataOd = "";
$sDataDo = "";

$sData_od = "";
$sData_do = "";
$sJoinWDate = "";
$sJoinWDateWOC = "";

$sRD = "";

$sDateCondition = "";
$sTypeCondition = "";

$nIsCondition = 0;
$nMyRanking = 0;

$nIsCondition = 0;
$nMyRanking = 0;

if (isset($_REQUEST['stat']))
    $sNameOfStat = $_REQUEST['stat'];

if (isset($_REQUEST['Rok']))
    $sRok = $_REQUEST['Rok'];

if (isset($_REQUEST['Mc']))
    $sMc = $_REQUEST['Mc'];

if (isset($_REQUEST['rRD']))
    $sRD = $_REQUEST['rRD'];

if (isset($_REQUEST['DataOd']))
    $sDataOd = $_REQUEST['DataOd'];

if (isset($_REQUEST['DataDo']))
    $sDataDo = $_REQUEST['DataDo'];


if ($sRD == "R") {
    if ($sRok <> "" and $sMc <> "") {
        $sData_od = $sRok . '-' . $sMc . '-' . '01';

        $dDate = new DateTime($sData_od);
        $dDate->add(new DateInterval('P1M'));
        $nIsCondition = 1;
    }

    if ($sRok <> "" and $sMc == "") {
        $sData_od = $sRok . '-01-01';

        $dDate = new DateTime($sData_od);
        $dDate->add(new DateInterval('P1Y'));
        $nIsCondition = 1;
    }


    if ($nIsCondition) {
        $sData_do = $dDate->format('Y-m-d');
        $sDateCondition = " " . $sJoinWDate . "date >='" . $sData_od . "' and " . $sJoinWDate . "date < '" . $sData_do . "' ";
    }
} else {
    try {
        if ($sDataOd <> "") {
            $dDate = new DateTime($sDataOd);
            $sData_od = $dDate->format('Y-m-d');
        }

        $dDate = new DateTime($sDataDo);
        $dDate->add(new DateInterval('P1D'));
        $sData_do = $dDate->format('Y-m-d');

        if ($sData_od <> "")
            $sDateCondition = " " . $sJoinWDate . "date >='" . $sData_od . "' ";

        if ($sData_do <> "") {
            if ($sDateCondition != "")
                $sDateCondition = $sDateCondition . " and ";

            $sDateCondition = $sDateCondition . " " . $sJoinWDate . "date < '" . $sData_do . "' ";
        }
    } catch (Exception $e) {
        $sDateCondition = "";
    }
}


$dbc = OcDb::instance();

if ($sNameOfStat == "MaintenanceOfCaches") {
    if ($sDateCondition != "")
        $sDateCondition = " and " . $sDateCondition;

    $query = "SELECT COUNT(*) count, u.username username, UPPER(u.username) UUN, u.user_id user_id,
        DATE(u.date_created) date_created, u.description description

        FROM
        cache_logs cl
        join caches c on c.cache_id = cl.cache_id
        join user u on cl.user_id = u.user_id

        WHERE cl.deleted=0  and  cl.type=6 and c.user_id <> cl.user_id "
            . $sDateCondition .
            "GROUP BY u.user_id
        ORDER BY count DESC, u.username ASC";

}else if ($sNameOfStat == "NumberOfFinds") {

    if ($sDateCondition != "")
        $sDateCondition = " WHERE " . $sDateCondition;


    $query = "SELECT f.c count, f.user_id, u.username username, UPPER(u.username) UUN,
                        DATE(u.date_created) date_created, u.description description

    FROM (

    SELECT cl.user_id, sum( cl.number ) c
    FROM user_finds cl "
            . $sDateCondition .
            "GROUP BY 1
    ORDER BY c DESC
    ) AS f
    JOIN user u ON f.user_id = u.user_id";
} else {
    //strange $sNameOfStat
    exit;
}

$s = $dbc->multiVariableQuery($query);

echo "<script>";


$nRanking = 0;
$sOpis = "";
$nOldCount = -1;
$nPos = 0;
$nMyRanking = 0;
$nMyRealPos = 0;

while ($record = $dbc->dbResultFetch($s)) {
    if ($record["description"] <> "") {
        $sOpis = $record["description"];

        $sOpis = str_replace("\r\n", " ", $sOpis);
        $sOpis = str_replace("\n", " ", $sOpis);
        $sOpis = str_replace("'", "-", $sOpis);
        $sOpis = str_replace("\"", " ", $sOpis);

    } else
        $sOpis = "";

    $sOpis = "" . $sOpis;


    $sProfil = "<b>" . tr('registered_since_label') . "</b> " . $record["date_created"];


    $nCount = $record["count"];
    $sUUN = htmlspecialchars($record["UUN"]);
    $sUserID = $record["user_id"];
    $sDateCr = $record["date_created"];

    if ($nCount != $nOldCount) {
        $nRanking++;
        $nOldCount = $nCount;
    }

    $sUserClass = "";
    if ($nRanking <= 3)
        $sUserClass = ' class="GCT-link-3"; ';
    else
        $sUserClass = ' class="GCT-link"; ';

    $sUserProfil = "viewprofile.php?userid=" . $record['user_id'];
    $sUsername = '<span ' . $record["username"] . $sUserClass . '  onclick="GCTStatsGotoProfil( \\\'' . $sUserProfil . '\\\' )"  onmouseover="Tip(\\\'' . $sProfil . '\\\')" onmouseout="UnTip()"  >' . $record["username"] . '</span><a name="' . $sUUN . '"></a>';
    $sUsername = str_replace("'", "&#39", $sUsername);

    $nPos++;

    echo "
            gct.addEmptyRow();
            gct.addToLastRow( 0, $nRanking );
            gct.addToLastRow( 1, $nCount );
            gct.addToLastRow( 2, '$sUsername' );
            gct.addToLastRow( 3, '$sOpis' );
            gct.addToLastRow( 4, \"$sUUN\" );
            gct.addToLastRow( 5, '$sUserID' );
        ";

    if ($usr['userid'] == $record['user_id']) {
        $nMyRanking = $nRanking;
        $nMyRealPos = $nPos - 1;
    }
}



echo "gct.drawChart();";
echo "gct.addSelectEvent( GCTEventSelectFunction );";

echo "document.Details.SelectedUser.value = '0';";
echo "document.Position.Ranking.value = '" . $nMyRanking . " / " . $nRanking . "';";
echo "document.Position.RealPosOfTable.value = '" . $nMyRealPos . "';";
echo "document.FilterDate.DateFrom.value = '" . $sData_od . "';";
echo "document.FilterDate.DateTo.value = '" . $sData_do . "';";


echo "</script>";

unset($dbc);
