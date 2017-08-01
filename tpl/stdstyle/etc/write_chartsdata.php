<?php
/*
 * This file should be run once a day with cron. Best term to do this is a few minutes after midnight.
 */

$rootpath = __DIR__ . '/../../../';
require_once($rootpath . 'lib/common.inc.php');

/*
 * Generate static data for charts in statistics page
 */

require_once($rootpath . 'graphs/stats-charts.php');

$content = "<?php\n";
$content .= "tpl_set_var('cachetype_chart_data', '" . genChartDataCacheTypes() . "');\n";
$content .= "tpl_set_var('cachesfound_chart_data', '" . genChartDataCachesFound() . "');\n";

$output_file = fopen($dynstylepath . "charts_data.inc.php", 'w');
fwrite($output_file, $content);
fclose($output_file);