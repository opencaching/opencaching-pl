<?php
use Utils\Uri\OcCookie;
use Utils\I18n\I18n;
?>
<link rel="stylesheet" type="text/css" media="screen,projection" href="/tpl/stdstyle/css/GCT.css" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="/tpl/stdstyle/css/GCTStats.css" />
<script src='https://www.google.com/jsapi'></script>
<script src="/lib/js/GCT.js"></script>
<script src="/lib/js/GCT.lang.php"></script>
<script src="/lib/js/GCTStats.js"></script>
<script src="/lib/js/wz_tooltip.js"></script>

<script>
  $( function() {
    $.datepicker.setDefaults($.datepicker.regional["<?=I18n::getCurrentLang()?>"]);
  } );
</script>

<?php
$sNameOfStat = "";
$sTitleOfStat = "";
if (isset($_REQUEST["stat"])) {
    $sNameOfStat = $_REQUEST["stat"];
}

if ($sNameOfStat == "NumberOfFinds")
    $sTitleOfStat = " {{ranking_by_number_of_finds_new}} ";
else if ($sNameOfStat == "MaintenanceOfCaches")
    $sTitleOfStat = " {{ranking_by_maintenace}} ";
else
    $sTitleOfStat = " Ranking ";
?>

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="">
    {{statistics}}: <?php echo $sTitleOfStat ?>
  </div>

<div class="searchdiv">

    <?php

    $sRok = "";
    $sMc = "";
    $sDataOd = "";
    $sDataDo = "";
    $sRD = "R";
    $sNameOfStat = $_REQUEST['stat'];
    $sNameOfStatCookieEmptyDate = $sNameOfStat . "EmptyDate";
    $sIsEmptDate = "";

    if (isset($_REQUEST["init"])) {
        $sIsEmptDate = OcCookie::get($sNameOfStatCookieEmptyDate);
    }


    if (!isset($_REQUEST["init"])) {
        $sRok = $_REQUEST["Rok"];
        $sMc = $_REQUEST["Mc"];

        $sDataOd = $_REQUEST["DataOd"];
        $sDataDo = $_REQUEST["DataDo"];

        $sRD = $_REQUEST["rRD"];


        if ($sRD == "R" && $sRok == "" && $sMc == ""){
            OcCookie::set($sNameOfStatCookieEmptyDate, "Yes", true);
        }else{
            OcCookie::set($sNameOfStatCookieEmptyDate, "No", true);
        }

    }

    if (( isset($_REQUEST["init"]) or intval($sMc) > 12 or intval($sMc) < 0 or intval($sRok) < 0 )
            or ( intval($sMc) != 0 and intval($sRok) == 0 )) {
        if ($sIsEmptDate != "Yes") {
            $sRok = date("Y");
            $sMc = date("m");
        }

        $_REQUEST["Rok"] = $sRok;
        $_REQUEST["Mc"] = $sMc;

        $_REQUEST["DataOd"] = $sDataOd;
        $_REQUEST["DataDo"] = $sDataDo;

        $_REQUEST["rRD"] = $sRD;
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
                        <input type="hidden" value="<?php echo $sNameOfStat ?>" name="stat" id = "stat" >
                        <input type="hidden" name="DateFrom" id="DateFrom" value="" >
                        <input type="hidden" name="DateTo" id="DateTo" value="" >

                        <table  class = "GCT-div-table" >
                            <tr>
                                <td><input type="radio" name="rRD" id="rR" value="R" <?php if ($sRD == "R") echo "checked" ?> ></td>
                                <td width="10px">{{FiltrYear}}:</td>
                                <td width="64px"> <input type="text" name="Rok" value="<?php echo $sRok ?>"; style="width:30px; text-align: center" maxlength="4" onclick="GCTStatsSetRadio('Rok')"></td>
                                <td >{{FiltrMonth}}: <input type="text" value="<?php echo $sMc ?>"  name="Mc" style="width:20px; text-align: center" maxlength="2" onclick="GCTStatsSetRadio('Rok')"></td>
                                <td width="90px" rowspan=2; width="70px"  style="text-align: center"> <button type="submit" name="bFilterDate" />{{Filter}}</td>
                            </tr>

                            <tr>
                                <td><input type="radio" name="rRD" id="rD" value="D" <?php if ($sRD == "D") echo "checked" ?>></td>
                                <td>{{Dates}}:</td>
                                <td colspan=2>
                                    <input type="text" id="datepicker" name="DataOd" onclick="GCTStatsSetRadio('Data')" value="<?php echo $sDataOd ?>" style="width:60px; text-align: left"  maxlength="10">&nbsp;&nbsp;-
                                    <input type="text" id="datepicker1" name="DataDo" onclick="GCTStatsSetRadio('Data')" value="<?php echo $sDataDo ?>" style="width:60px; text-align: left"  maxlength="10">
                                </td>
                            </tr>

                        </table>
                    </form>
                </td>
                <!-- END of Filter -->

                <!-- Begin of User -->
                <td align="right">
                    <?php $view->callSubTpl('/articles/userfilter'); ?>
                </td>
                <!-- End of User -->
            </tr>
        </table>

        <hr style="color: black">
        <br>
        <?php $view->callSubTpl('/articles/mypositionandcharts'); ?>
        <br>

    </div> <!-- End of GCT-div -->

    <?php include ('t102.php'); /* blah! this is called in in common_tpl_funcs.php context*/ ?>


</div>

<script>
    google.load('visualization', '1', {'packages': ['corechart'], 'language': '{language4js}'});
</script>


<div  id="dialogLine"  >
</div>

<div  id="dialogBar"  >
</div>


<div  id="HelpDialog"  >
    {{HelpHowToSelect}}
</div>

</div>