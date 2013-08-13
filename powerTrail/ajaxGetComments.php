<?php
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/../lib/common.inc.php';
require_once __DIR__.'/powerTrailBase.php';
$commentsArr = powerTrailBase::getPowerTrailComments();

$nextSearchStart = $_REQUEST['start'] + $_REQUEST['limit'];

$db = new dataBase(false);
$q = 'SELECT count(*) AS `count` FROM  `PowerTrail_comments` WHERE  `PowerTrailId` =:1 AND `deleted` = 0 ';
$db->multiVariableQuery($q, $_REQUEST['projectId']);
$count = $db->dbResultFetch();
$count = $count['count'];

$query = 'SELECT * FROM  `PowerTrail_comments`, `user` WHERE  `PowerTrailId` =:variable1 AND `deleted` = 0 AND `PowerTrail_comments`.`userId` = `user`.`user_id` ORDER BY  `logDateTime` DESC LIMIT :variable2 , :variable3   '  ;
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
$toDisplay = '<table id="commentsTable" cellspacing="0">';
foreach ($result as $key => $dbEntery) {
	$userActivity = $dbEntery['hidden_count'] +	$dbEntery['log_notes_count'] + $dbEntery['founds_count'] + $dbEntery['notfounds_count'];
	$toDisplay .= '
	<tr>
		<td colspan="3" class="commentHead">
			<span class="CommentDate">'. substr($dbEntery['logDateTime'],0,-8).'</span><b>'.$dbEntery['username'].'</b> (<img height="13" src="tpl/stdstyle/images/blue/thunder_ico.png" /><font size="-1">'.$userActivity.'</font>)
			- <span style="color: '.$commentsArr[$dbEntery['commentType']]['color'].';">'. tr($commentsArr[$dbEntery['commentType']]['translate']).'</span>
		</td>
	</tr>
	<tr>
		<td class="commentContent" valign="top">'.htmlspecialchars_decode($dbEntery['commentText']).'</td>
	</tr><tr><td>&nbsp</td></tr>'
	
	;
}
$toDisplay .= '</table>';

if ($_REQUEST['start']-20 < 0 ) $startNew = 0; else $startNew = $_REQUEST['start']-20;
if ($_REQUEST['start'] > 0) $toDisplay .= '<a href="javascript:void(0)" onclick="ajaxGetComments('.$startNew.', 20);" class="editPtDataButton">'.tr('pt059').'</a>';
if ($count > $nextSearchStart) $toDisplay .= '<a href="javascript:void(0)" onclick="ajaxGetComments('.$nextSearchStart.', 20);" class="editPtDataButton">'.tr('pt058').'</a>';

echo $toDisplay;

?>