
<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCT.css" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCTStats.css" />
<script src='https://www.google.com/jsapi'></script>
<script src="lib/js/GCT.js"></script>
<script src="lib/js/GCT.lang.php"></script>
<script src="lib/js/GCTStats.js"></script>
<script src="lib/js/wz_tooltip.js"></script>


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/TitledCache.png" class="icon32" alt="" title="" align="middle" />&nbsp;<?php global $titled_cache_period_prefix; $ntitled_cache = $titled_cache_period_prefix.'_titled_caches'; echo tr($ntitled_cache); ?></div>

<br>
<br>

<div id='idGTC' align = "center"> </div>


<script>;
<?php echo "GCTLoad( 'ChartTable', '" . $lang . "' );";?>
</script>


<script>
    var gct = new GCT('idGTC');
    gct.addColumn('string', '' ); //
    gct.addColumn('string',  '<?php echo tr("geocache") ?>', 'font-size: 12px; ' ); //1
    gct.addColumn('string', '<?php echo tr("region") ?>', 'font-size: 12px; ' ); //2
    gct.addColumn('string', '<?php echo tr("owner") ?>', 'font-size: 12px; ' ); //3
    gct.addColumn('string', '<?php echo tr("titled_cache_date") ?>', 'font-size: 12px; ' ); //4

    gct.addChartOption('showRowNumber', true );

    gct.addChartOption('sortColumn', 4 ); //Data
    gct.addChartOption('sortAscending', false );
    gct.addChartOption('pageSize', 20);

    gct.addVisualOptionVC('rowNumberCell', 'GCT-color-none GCT-font-size11 ');
</script>

<script>
{contentTable}
gct.drawChart();
</script>

