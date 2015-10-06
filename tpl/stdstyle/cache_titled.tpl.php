<link href="tpl/stdstyle/js/jquery.1.10.3/css/myCupertino/jquery-ui-1.10.3.custom.css" rel="stylesheet">
<script src="tpl/stdstyle/js/jquery.1.10.3/js/jquery-1.9.1.js"></script>
<script src="tpl/stdstyle/js/jquery.1.10.3/js/jquery-ui-1.10.3.custom.js"></script>
<script src="tpl/stdstyle/js/jquery.1.10.3/development-bundle/ui/jquery.datepick-{language4js}.js"></script>

<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCT.css" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="tpl/stdstyle/css/GCTStats.css" />
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type='text/javascript' src="lib/js/GCT.js"></script>
<script type='text/javascript' src="lib/js/GCTStats.js"></script>
<script type='text/javascript' src="lib/js/wz_tooltip.js"></script>


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/TitledCache.png" class="icon32" alt="Wyszukiwanie" title="Suchergebnis" align="middle" />&nbsp;<?php global $titled_cache_period_prefix; $ntitled_cache = $titled_cache_period_prefix.'_titled_caches'; echo tr($ntitled_cache); ?></div>

<br>
<br>

<div id='idGTC' align = "center"> </div>


<script type='text/javascript'>;
<?php echo "GCTLoad( 'ChartTable', '" . $lang . "' );";?>
</script>


<script type='text/javascript'>
    var gct = new GCT('idGTC');
    gct.addColumn('string', '' ); //
    gct.addColumn('string',  '<?php echo tr("geocache") ?>', 'font-size: 12px; ' ); //1
    gct.addColumn('string', '<?php echo tr("region") ?>', 'font-size: 12px; ' ); //2
    gct.addColumn('string', '<?php echo tr("owner") ?>', 'font-size: 12px; ' ); //3
    gct.addColumn('string', '<?php echo tr("titled_cache_date") ?>', 'font-size: 12px; ' ); //4

    gct.addChartOption('showRowNumber', true );

    gct.addChartOption('sortColumn', 4 ); //Data
    gct.addChartOption('sortAscending', true );
    gct.addChartOption('pageSize', 20);

    gct.addVisualOptionVC('rowNumberCell', 'GCT-color-none GCT-font-size11 ');
</script>

<script type='text/javascript'>
{contentTable}
gct.drawChart();
</script>

