<?php
session_start();
require_once __DIR__.'/../lib/db.php';
// require_once __DIR__.'/powerTrailController.php';
// $ptAPI = new powerTrailBase;

// $projectId = $_REQUEST['projectId'];
// $limit = $_REQUEST['limit'];
// $start = $_REQUEST['start'];

$query = 'SELECT * FROM  `PowerTrail_comments`, `user` WHERE  `PowerTrailId` =:variable1 AND `deleted` = 0 AND `PowerTrail_comments`.`userId` = `user`.`user_id` ORDER BY  `logDateTime` ASC LIMIT :variable2 , :variable3   '  ;
$db = new dataBase(false);
// $db->multiVariableQuery($query, $projectId, $start, $limit);
$params['variable1']['value'] = (integer) $_REQUEST['projectId'];
$params['variable1']['data_type'] = 'integer';
$params['variable2']['value'] = (integer) $_REQUEST['start'];;
$params['variable2']['data_type'] = 'integer';
$params['variable3']['value'] = (integer) $_REQUEST['limit'];;
$params['variable3']['data_type'] = 'integer';
$db->paramQuery($query, $params); // multiVariableQuery($query, $projectId, 0, 8);
$result = $db->dbResultFetchAll();
// print_r($result);

// build to display
$toDisplay = '<table>';
foreach ($result as $key => $dbEntery) {
	$userActivity = $dbEntery['hidden_count'] +	$dbEntery['log_notes_count'] + $dbEntery['founds_count'] + $dbEntery['notfounds_count'];
	$toDisplay .= '<tr>
	<td valign="top"><b>'.$dbEntery['username'].'</b> ('.$userActivity.')<td>
	<td>'.htmlspecialchars_decode($dbEntery['commentText']).'</td>'.
	
	'</tr>'
	;
}
$toDisplay .= '</table>';
echo $toDisplay;

?>