
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

<script type='text/javascript'>
<?php echo "GCTLoad( 'ChartTable', '" . $lang . "' );"?>
</script>

<script type='text/javascript'>
    var gct = new GCT('idGCTPosition');
    gct.addColumn('string', '' ); //
    gct.addColumn('string',  '<?php echo tr("geocache") ?>', 'font-size: 12px; ' ); //1
    gct.addColumn('string', '<?php echo tr("owner") ?>', 'font-size: 12px; ' ); //2
    gct.addColumn('string', '<?php echo tr("titled_cache_date") ?>', 'font-size: 12px; ' ); //3

    gct.addChartOption('sortColumn', 3 ); //Data
    gct.addChartOption('sortAscending', false );
    gct.addChartOption('pageSize', 30);
</script>

<script type='text/javascript'>
{content}
gct.drawChart();
</script>

<br>


