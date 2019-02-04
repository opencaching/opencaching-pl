<?php
use Utils\I18n\I18n;

?>

<script src="https://www.google.com/jsapi"></script>
<script src="lib/js/GCT.js"></script>
<script src="lib/js/GCT.lang.php"></script>
<script src="tpl/stdstyle/js/search.js"></script>
<script src="tpl/stdstyle/js/okapiGpxFormatterWidget.js?v=3"></script>
<script>
    $(function() {

        /* Bind "OKAPI GPX" links to proper actions. OKAPI GPX Formatter needs
         * a complete list of cache codes before it can be shown. */

        var btnFetchSelected = $("#exportOkapiGPX-selected");
        var btnFetchAll = $("#exportOkapiGPX-all");
        btnFetchSelected.add(btnFetchAll).css({
            "cursor": "pointer"
        });
        btnFetchSelected.on("click", function() {
            $.okapiGpxFormatterWidget.show({
                cacheCodes: GetSelectedCacheCodes()
            });
        });
        btnFetchAll.on("click", function() {
            $.ajax({
                url: "ocpl{queryid}.jsoncodes"
            }).done(function(cacheCodes) {
                $.okapiGpxFormatterWidget.show({
                    cacheCodes: cacheCodes
                });
            });
        });
    });
</script>

<?php
global $hide_coords, $colNameSearch, $NrColSortSearch, $OrderSortSearch, $SearchWithSort, $usr, $selectList, $NrColVisable;

if ( !$SearchWithSort )
{
    if ( $NrColSortSearch != -1 && !isset($_REQUEST["startat"]) )
    {
        echo "<script>
            alert( '".tr('MaxSearchRec')."' );
            </script>; ";
    }
}

echo "<script>";
echo " GCTLoad( 'ChartTable', '". I18n::getCurrentLang() ."' );";
echo "</script>";

$NrColSortToSet = $NrColSortSearch-1;

if ( $NrColSortToSet < 0 )
    $NrColSortSearch = 0;

if ( $NrColSortToSet > 18 )
    $NrColSortSearch = 0;

if ( !$SearchWithSort &&  $NrColSortSearch != -1 )
    $NrColSortSearch = -1;
?>

<script>
    var gct = new GCT( 'idGTC' );

    /* 0 */gct.addColumn('string', "<?php echo $colNameSearch[0]["C"]?>", 'text-align: center; font-size: 12px;');
    /* 1 */gct.addColumn('string', "<?php echo $colNameSearch[1]["C"]?>", 'text-align: center; font-size: 12px;');
    /* 2 */gct.addColumn('string', "<?php echo $colNameSearch[2]["C"]?>", 'font-size: 12px; text-align: left; ');
    /* 3 */gct.addColumn('string', "<?php echo $colNameSearch[3]["C"]?>", 'font-size: 12px; text-align: left; ');
    /* 4 */gct.addColumn('string', "<?php echo $colNameSearch[4]["C"]?>", 'font-size: 12px; text-align: left; ');
    /* 5 */gct.addColumn('string', "<?php echo $colNameSearch[5]["C"]?>", 'font-size: 12px; text-align: center; ');
    /* 6 */gct.addColumn('string', "<?php echo $colNameSearch[6]["C"]?>", 'text-align: left; font-size: 12px;');

    /* 7 */gct.addColumn('number', "<?php echo $colNameSearch[7]["C"]?>", 'font-size: 12px; text-align: center; color:green; ');
    /* 8 */gct.addColumn('number', "<?php echo $colNameSearch[8]["C"]?>", 'font-size: 12px; text-align: center; color:red; ');
    /* 9 */gct.addColumn('number', "<?php echo $colNameSearch[9]["C"]?>", 'font-size: 12px; text-align: center; color:black; ');

    /* 10 */gct.addColumn('number', "<?php echo $colNameSearch[10]["C"]?>", 'font-size: 12px; text-align: center; color:green; font-weight: bold; width: 10px;');
    /* 11 */gct.addColumn('string', "<?php echo $colNameSearch[11]["C"]?>", 'font-size: 12px; text-align: left; width: 90px; ');

    /* 12 */gct.addColumn('string', "<?php echo $colNameSearch[12]["C"]?>", 'font-size: 12px; text-align: left;');
    /* 13 */gct.addColumn('string', "<?php echo $colNameSearch[13]["C"]?>", 'font-size: 12px; text-align: left; ');
    /* 14 */gct.addColumn('string', "<?php echo $colNameSearch[14]["C"]?>", 'font-size: 12px; text-align: left; ');

    /* 15 */gct.addColumn('string', "<?php echo $colNameSearch[15]["C"]?>", 'font-size: 12px; text-align: left; ');
    /* 16 */gct.addColumn('string', "<?php echo $colNameSearch[16]["C"]?>", 'font-size: 12px; text-align: left; ');

    /* 17 */gct.addColumn('string', "<?php echo $colNameSearch[17]["C"]?>", 'font-size: 12px; text-align: left; ');

    /* 18 */gct.addColumn('string', "<?php echo $colNameSearch[18]["C"]?>", 'font-size: 12px; text-align: left; ');
    /* 19 */gct.addColumn('string', "<?php echo $colNameSearch[19]["C"]?>", 'font-size: 12px; text-align: left; ');

    gct.hideColumns( [0, 19] );

    gct.addChartOption('showRowNumber', true );
    gct.addChartOption('width', '780' );

    gct.addVisualOptionVC('headerRow', 'GCT-background-color-white6 GCT-color-black11 GCTalign-center GCT-font-bold GCT-font-size11 ');
    gct.addVisualOptionVC('rowNumberCell', 'GCT-color-none GCT-font-size11 ');
</script>

<?php
    echo "<script>";
    if ($SearchWithSort)
    {
        echo "gct.addChartOption('sortColumn', $NrColSortToSet );";
        echo "gct.addChartOption('sortAscending',"; if ($OrderSortSearch == 'M') echo 'false'; else echo 'true'; echo" );";
        echo "gct.addChartOption('pageSize', 20);";
    }
    else
    {
        echo "gct.addChartOption('showRowNumber', false );";
        echo "gct.addChartOption('sort', 'disable' );";
        echo "gct.addChartOption('page', 'disable' );";
    }
    echo "</script>";
?>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt=""> {{search_results}} {results_count}</div>
<div class="content2-container">
    <p>
        <a href="search.php?queryid={queryid}&amp;showresult=0" class="btn btn-default">{{change_search_criteria}}</a>
        {safelink}<br/>
        {pages}<br/>
    </p>

<div id='idGTC' align = "left" ></div>

<?php if (!$SearchWithSort) echo "<div class='content-title-noshade'><p align='left'>"; ?>
{pages}
<?php if (!$SearchWithSort) { echo "</p></div>"; }
    else {
        echo "<span style='font-size:10px;'>{{pagination_page}}: </span><span id='pageNumber' style='font-size:11px; color:green; font-weight:bold'>1</span>" ; }
?>

<div class="buffer"></div>
{results}

<?php
$selectList = "";
$descCol = tr('Disable');
if ( $NrColSortSearch != -1)
    $selectList.="<option style='color:red' value=-1>$descCol</option>";
else
    $selectList.="<option style='color:red' selected='selected' value=-1>$descCol</option>";

$descCol = tr('Enable');
$selectList.="<optgroup style='color:green' label='".$descCol."'>";

$NrColVisable = 0;

$C1 = fHideColumn( 1, true );
$C2 = fHideColumn( 2, true );
$C3 = fHideColumn( 3, true );
$C4 = fHideColumn( 4, true );
$C5 = fHideColumn( 5, true );
$C6 = fHideColumn( 6, true );
$C7 = fHideColumn( 7, true );
$C8 = fHideColumn( 8, true );
$C9 = fHideColumn( 9, true );
$C10 = fHideColumn( 10, true );
$C11 = fHideColumn( 11, true );
$C12 = fHideColumn( 12, true );
$C13 = fHideColumn( 13, true );
$C14 = fHideColumn( 14, true );
$C15 = fHideColumn( 15, true );
$C16 = fHideColumn( 16, true );
$C17 = fHideColumn( 17, true );
$C18 = fHideColumn( 18, true );
?>

<script>
    gct.drawChart();
    gct.addSelectEvent( EventSelectPosFunction );
    gct.addPageEvent( EventPageFunction );
</script>

<?php
echo "<div class='GCT-div' style='font-size:12px'>
<form name='ExportCaches' id='ExportCaches' style='display:inline;' action='' method='get'>
    <table style='width: 100%;' class = 'GCT-div-table' >
        <tr>
            <td style='color:green;'>{{SelectedPositionExport}}</td>
            <td>
                <span class='content-title-noshade' style='color:green'>{{format_GPX}}</span>:<br/>
                <a class='links' onclick='CacheExport(\"gpx\")' id='exportGPX' title='GPS Exchange Format .gpx'>GPX</a> |
                <a class='links' onclick='CacheExport(\"zip\")' id='exportZIP' title='Garmin ZIP file ({{format_pict}})  .zip'>GARMIN ({{format_pict}})</a> |
                <a class='links' onclick='CacheExport(\"ggz\")' id='exportGGZ' title='Garmin .ggz'>GARMIN GGZ</a> <sup style='color:red;text-shadow: 2px 2px 2px rgba(255, 109, 255, 1);'>Beta!</sup> |
                <a class='links' onclick='CacheExport(\"ggzp\")' id='exportGGZP' title='Garmin ZIP file ({{format_ggz_pict}})  .zip'>GARMIN GGZ ({{format_ggz_pict}})</a> <sup style='color:red;text-shadow: 2px 2px 2px rgba(255, 109, 255, 1);'>Beta!</sup>
                <div>
                    <a class='links' id='exportOkapiGPX-selected'>OKAPI GPX...</a>
                </div>
            </td>
        </tr>

            <tr><td colspan=\"2\">&nbsp;</td></tr>
            <tr class='form-group-sm'>
                <td>{{Selected}}:&nbsp;&nbsp;<input type='text' name='SelectedPos' id='SelectedPos' value='0' class='form-control input40' readonly >&nbsp;&nbsp;{{pos.}}</td>

                <td><span class='content-title-noshade' style='color:green'>{{format_other}}</span>:<br/>
                <a class='links' onclick='CacheExport(\"loc\")' id='exportLOC' title='Waypoint .loc'>LOC</a> |
                <a class='links' onclick='CacheExport(\"kml\")' id='exportKML' title='Google Earth .kml'>KML</a> |
                <a class='links' onclick='CacheExport(\"ov2\")' id='exportOV2' title='TomTom POI .ov2'>OV2</a> |
                <a class='links' onclick='CacheExport(\"ovl\")' id='exportOVL' title='TOP50-Overlay .ovl'>OVL</a> |
                <a class='links' onclick='CacheExport(\"txt\")' id='exportTXT' title='Text .txt'>TXT</a> |
                <a class='links' onclick='CacheExport(\"wpt\")' id='exportWPT' title='Oziexplorer .wptt'>WPT</a> |
                <a class='links' onclick='CacheExport(\"uam\")' id='exportUAM' title='AutoMapa .uam'>UAM</a> |
                <a class='links' onclick='CacheExport(\"xml\")' id='exportXML' title='Xml'>XML</a>
                </td>
           </tr>

        <tr><td colspan=\"2\"><hr></td></tr>

        <tr>
            <td style='color:green;'>{{AllPosExport}}</td>
                     <td>
                       <span class='content-title-noshade' style='color:green'>{{format_GPX}}</span>:<br/>
                <a class=\"links\" href=\"ocplgpx";?>{queryid}<?php echo ".gpx?startat=1&amp;count=max&amp;zip=1\" title=\"GPS Exchange Format .gpx\">GPX</a> |
                <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".zip?startat=1&amp;count=max\" title=\"Garmin ZIP file ({{format_pict}})  .zip\">GARMIN ({{format_pict}})</a> |
                <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".ggz?startat=1&amp;count=max\" title=\"Garmin .ggz\">GARMIN GGZ</a> <sup style='color:red;text-shadow: 2px 2px 2px rgba(255, 109, 255, 1);'>Beta!</sup> |
                <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".zip?startat=1&amp;format=ggz&amp;count=max\" title=\"Garmin ZIP file ({{format_ggz_pict}})  .zip\">GARMIN GGZ ({{format_ggz_pict}})</a> <sup style='color:red;text-shadow: 2px 2px 2px rgba(255, 109, 255, 1);'>Beta!</sup>
                <div>
                    <a class='links' id='exportOkapiGPX-all'>OKAPI GPX...</a>
                </div>
                </td>
              </tr>
             <tr><td colspan=\"2\">&nbsp;</td></tr>
              <tr>
                     <td width=\"270\">
                    ".tr('listing_from_to').":
                     </td>
                            <td><span class='content-title-noshade' style='color:green'>{{format_other}}</span>:<br/>
                <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".loc?startat=1&amp;count=max&amp;zip=1\" title=\"Waypoint .loc\">LOC</a> |
                <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".kml?startat=1&amp;count=max&amp;zip=1\" title=\"Google Earth .kml\">KML</a> |
                <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".ov2?startat=1&amp;count=max&amp;zip=1\" title=\"TomTom POI .ov2\">OV2</a> |
                <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".ovl?startat=1&amp;count=max&amp;zip=1\" title=\"TOP50-Overlay .ovl\">OVL</a> |
                <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".txt?startat=1&amp;count=max&amp;zip=1\" title=\"Text .txt\">TXT</a> |
                <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".wpt?startat=1&amp;count=max&amp;zip=1\" title=\"Oziexplorer .wpt\"> WPT</a> |
                <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".uam?startat=1&amp;count=max&amp;zip=1\" title=\"AutoMapa .uam\">UAM</a> |
                            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".xml?startat=1&amp;count=max&amp;zip=1\" title=\"xml\">XML</a>
                        </td>
           </tr>
    </table>
</form>
</div>";

echo"
<div class=\"buffer\"></div>
<div style='font-size:12px'>
<form name='HideColumns' style='display:inline;' action='search.php' method='get'>
    <input type='hidden' value='";?>{queryid}<?php echo "' name='queryid' >
    <input type='hidden' value='true' name='notinit' >
    <table style='width: 100%;' class = 'GCT-div-table' >
        <tr>
            <td colspan=3 style='color:green;'>{{HideCols}}</td>
        </tr>
        <tr>
            <td><input type='checkbox' name='C1' value='1' "; if($C1 == 1) echo "checked"; echo"> "; echo $colNameSearch[1]["O"]; echo" </td>
            <td><input type='checkbox' name='C2' value='1' "; if($C2 == 1) echo "checked"; echo"> "; echo $colNameSearch[2]["O"]; echo" </td>
            <td><input type='checkbox' name='C3' value='1' "; if($C3 == 1) echo "checked"; echo"> "; echo $colNameSearch[3]["O"]; echo" </td>
        </tr>
        <tr>
            <td><input type='checkbox' name='C4' value='1' "; if($C4 == 1) echo "checked"; echo"> "; echo $colNameSearch[4]["O"]; echo" </td>
            <td><input type='checkbox' name='C5' value='1' "; if($C5 == 1) echo "checked"; echo"> "; echo $colNameSearch[5]["O"]; echo" </td>
            <td><input type='checkbox' name='C6' value='1' "; if($C6 == 1) echo "checked"; echo"> "; echo $colNameSearch[6]["O"]; echo" </td>
        </tr>

        <tr>
            <td><input type='checkbox' name='C7' value='1' "; if($C7 == 1) echo "checked"; echo"> "; echo $colNameSearch[7]["O"]; echo" </td>
            <td><input type='checkbox' name='C8' value='1' "; if($C8 == 1) echo "checked"; echo"> "; echo $colNameSearch[8]["O"]; echo" </td>
            <td><input type='checkbox' name='C9' value='1' "; if($C9 == 1) echo "checked"; echo"> "; echo $colNameSearch[9]["O"]; echo" </td>
        </tr>

        <tr>
            <td><input type='checkbox' name='C10' value='1' "; if($C10 == 1) echo "checked"; echo"> "; echo $colNameSearch[10]["O"]; echo" </td>
            <td><input type='checkbox' name='C11' value='1' "; if($C11 == 1) echo "checked"; echo"> "; echo $colNameSearch[11]["O"]; echo" </td>
            <td><input type='checkbox' name='C12' value='1' "; if($C12 == 1) echo "checked"; echo"> "; echo $colNameSearch[12]["O"]; echo" </td>
        </tr>

        <tr>
            <td><input type='checkbox' name='C13' value='1' "; if($C13 == 1) echo "checked"; echo"> "; echo $colNameSearch[13]["O"]; echo" </td>
            <td><input type='checkbox' name='C14' value='1' "; if($C14 == 1) echo "checked"; echo"> "; echo $colNameSearch[14]["O"]; echo" </td>
            <td><input type='checkbox' name='C15' value='1' "; if($C15 == 1) echo "checked"; echo"> "; echo $colNameSearch[15]["O"]; echo" </td>
         </tr>

        <tr>
            <td><input type='checkbox' name='C16' value='1' "; if($C16 == 1) echo "checked"; echo"> "; echo $colNameSearch[16]["O"]; echo" </td>
            <td><input type='checkbox' name='C17' value='1' "; if($C17 == 1) echo "checked"; echo"> "; echo $colNameSearch[17]["O"]; echo" </td>
            <td><input type='checkbox' name='C18' value='1' "; if($C18 == 1) echo "checked"; echo"> "; echo $colNameSearch[18]["O"]; echo" </td>
         </tr>


        <tr>
            <td colspan=\"3\">&nbsp;</td>
        </tr>
        <tr>
            <td colspan=\"3\" style='color:green;'>{{InteractiveSorting}}</td>
        </tr>

        <tr class='form-group-sm'>
         <td colspan=\"2\">

                <select name='NrColSort' class='form-control input300'>
                    $selectList
                </select>

                &nbsp; <input type='radio' name='OrderSortSearch' value='M'"; if ($OrderSortSearch == 'M') echo "checked='checked'"; echo "/> {{Descending}}
                &nbsp; <input type='radio' name='OrderSortSearch' value='R'"; if ($OrderSortSearch == 'R') echo "checked='checked'"; echo " /> {{Ascending}}

         </td>
         <td class='align-right'> <button type='submit' name='bHideColumns' class='btn btn-primary'>{{save}}</td>
        </tr>
    </table>
</form>
</div>";
?>
  <div class="buffer"></div>
  <p>{{accept_terms_of_use}}</p>
</div>

<script>
    document.getElementById("exportGPX").style.cursor = "pointer";
    document.getElementById("exportZIP").style.cursor = "pointer";
    document.getElementById("exportGGZ").style.cursor = "pointer";
    document.getElementById("exportGGZP").style.cursor = "pointer";
    document.getElementById("exportLOC").style.cursor = "pointer";
    document.getElementById("exportKML").style.cursor = "pointer";
    document.getElementById("exportOV2").style.cursor = "pointer";
    document.getElementById("exportOVL").style.cursor = "pointer";
    document.getElementById("exportTXT").style.cursor = "pointer";
    document.getElementById("exportWPT").style.cursor = "pointer";
    document.getElementById("exportUAM").style.cursor = "pointer";
    document.getElementById("exportXML").style.cursor = "pointer";
</script>
