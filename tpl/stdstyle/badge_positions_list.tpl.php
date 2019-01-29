<?php
use Utils\I18n\I18n;
?>

<br>
{head}

<br>
<div>
<table class="Badge-div-table Tables">
    <tr style="vertical-align: top;">
    <td class="Badge-div-oneTable"><div id='idGCTPosition' align = "left"></div></td>
    </tr>
</table>
</div>

<br>


<br>

<script>
<?php echo "GCTLoad( 'ChartTable', '" . I18n::getCurrentLang() . "' );"?>
</script>

<script>
    var gct = new GCT('idGCTPosition');
    gct.addColumn('string', '' ); //
    gct.addColumn('string',  '<?php echo tr("geocache") ?>', 'font-size: 12px; ' ); //1
    gct.addColumn('string', '<?php echo tr("owner") ?>', 'font-size: 12px; ' ); //2
    gct.addColumn('string', '<?php echo tr("merit_badge_gain_date") ?>', 'font-size: 12px; ' ); //3

    gct.addChartOption('sortColumn', 3 ); //Date
    gct.addChartOption('sortAscending', false );
    gct.addChartOption('pageSize', 30);
</script>

<script>
{content}
gct.drawChart();
</script>

<br>


