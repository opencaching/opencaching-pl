<?php
use Utils\Database\OcDb;

require_once(__DIR__.'/common.inc.php');

?>
<html>
    <head>
    </head>

    <body>
        <div id="idGCL"></div>
<script>GCTLoad( 'ChartLine', '', 1 );</script>

        <script>
            var gcl = new GCT('idGCL');
            gcl.addColumn('date', 'Date');
        </script>

        <?php
        $sEND = "";
        $sDateCondition = "";
        $sTypeCondition = "";

        $sUserIDLine = $_REQUEST["UserID"];
        $sDateFrom = $_REQUEST["DF"];
        $sDateTo = $_REQUEST["DT"];
        $sNameOfStat = $_REQUEST["stat"];



        if ($sDateFrom <> "")
            $sDateCondition .= "and date >='" . $sDateFrom . "'";

        if ($sDateTo <> "")
            $sDateCondition .= " and date < '" . $sDateTo . "' ";


        if ($sNameOfStat == "NumberOfFinds")
            $sTypeCondition = " and  cl.type=1 ";

        if ($sNameOfStat == "MaintenanceOfCaches")
            $sTypeCondition = " and  cl.type=6 ";


        $asUserID = explode(",", $sUserIDLine);


        if (!strlen($sUserIDLine))
            $sEND = tr('SelectUsers');

        if (count($asUserID) > 10)
            $sEND = tr('more10');

        echo "<script>";
        if ($sEND <> "") {
            echo "alert( '$sEND' );";
            $asUserID = explode(",", "");
        }
        echo "</script>";


        $sCondition = "";

        $aNrColumn = array();

        foreach ($asUserID as $sID) {
            if (strlen($sCondition))
                $sCondition = $sCondition . " or ";

            $sCondition = $sCondition . "cl.user_id = '" . $sID . "'";
        }

        if (strlen($sCondition)) {
            $sConditionUser = " ( " . $sCondition . " )";
            $sCondition = " and ( " . $sCondition . " )";
        }

        $sCondition .= $sDateCondition;

/////////////////

        $dbc = OcDb::instance();

        $query = "SELECT user_id, username FROM user cl where " . $sConditionUser;
        $s = $dbc->multiVariableQuery($query);

        $aUserName = array();

        while ($record = $dbc->dbResultFetch($s)) {
            $sID = $record['user_id'];
            $aUserName[$sID] = $record['username'];
        }

////////////////////



        echo "<script>";

        $i = 0;
        foreach ($asUserID as $sID) {
            $sName = $aUserName[$sID];
            $sName = str_replace("'", "`", $sName);
            echo "gcl.addColumn('number', '$sName');";
            $aNrColumn[$sID] = $i;
            $i++;
        }


//echo "gcl.addChartOption('vAxis', { title: 'Ilość keszy' } );";
        echo " var chartOpt = gcl.getChartOption();";
        echo " chartOpt.vAxis.title= '" . tr('NrCaches') . "';";
        echo "</script>";

////////////////////////////


        $dbc = OcDb::instance();

        $query = "SELECT year( cl.date) year, month( cl.date ) month, day( cl.date ) day,
         u.username username, u.user_id user_id,
        COUNT(*) count

        FROM
        cache_logs cl
        join caches c on c.cache_id = cl.cache_id
        join user u on cl.user_id = u.user_id

        WHERE cl.deleted=0 "
                . $sTypeCondition
                . $sCondition .
                "GROUP BY year, month, day, user_id
        order by year, month, day   ";

        $s = $dbc->multiVariableQuery($query);

        $nCount = array();

        foreach ($asUserID as $sID) {
            $anCount[$sID] = 0;
        }

        echo "<script>";

        while ($record = $dbc->dbResultFetch($s)) {
            $nYear = $record['year'];
            $nMonth = $record['month'] - 1;
            $nDay = $record['day'];

            $sNewDate = "new Date( $nYear, $nMonth, $nDay )";
            $sUserName = $record['username'];
            $nUserId = $record['user_id'];

            $anCount[$nUserId] += $record['count'];


            echo "
            gcl.addEmptyRow();
            gcl.addToLastRow( 0, $sNewDate );
        ";


            $nrCol = $aNrColumn[$nUserId];
            $val = $anCount[$nUserId];
            echo "gcl.addToLastRow( $nrCol+1 , $val );";
        }

        echo "</script>";

        unset($dbc);
        ?>


        <script>
            gcl.drawChart(1);
        </script>
    </body>
</html>
