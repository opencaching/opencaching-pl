<?php
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';

$appContainer = lib\Objects\ApplicationContainer::Instance();
if( $appContainer->getLoggedUser() === false){
    $loggedUserId = -9999;
} else {
    $loggedUserId = $appContainer->getLoggedUser()->getUserId();
}

$commentsArr = lib\Controllers\PowerTrailController::getEntryTypes();
$ptOwners = powerTrailBase::getPtOwners($_REQUEST['projectId']);
$paginateCount = powerTrailBase::commentsPaginateCount;
foreach ($ptOwners as $owner) {
    $ownersIdArray[] = $owner['user_id'];
}
$nextSearchStart = $_REQUEST['start'] + $_REQUEST['limit'];

$db = \lib\Database\DataBaseSingleton::Instance();
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
if(count($result) == 0) {
    echo '<p><br /><br />' . tr('pt118') .'</p><br /><br />';
    exit;
}
// build to display
$toDisplay = '<table id="commentsTable" cellspacing="0">';


foreach ($result as $key => $dbEntery) {
    $userActivity = $dbEntery['hidden_count'] + $dbEntery['founds_count'] + $dbEntery['notfounds_count'];
    $logDateTime = explode(' ', $dbEntery['logDateTime']);
    $toDisplay .= '
    <tr>
        <td colspan="3" class="commentHead">
            <span class="CommentDate" id="CommentDate-'.$dbEntery['id'].'">'. $logDateTime[0].'</span><span class="commentTime" id="commentTime-'.$dbEntery['id'].'">'.substr($logDateTime[1],0,-3).'</span><a href="viewprofile.php?userid='.$dbEntery['userId'].'"><b>'.$dbEntery['username'].'</b></a> (<img height="13" src="tpl/stdstyle/images/blue/thunder_ico.png" /><font size="-1">'.$userActivity.'</font>)
            - <span style="color: '.$commentsArr[$dbEntery['commentType']]['color'].';">'. tr($commentsArr[$dbEntery['commentType']]['translate']).'</span>';
    if(isset($loggedUserId)){
        $toDisplay .= '<span class="editDeleteComment">';
        if(($loggedUserId == $dbEntery['userId'] || in_array($loggedUserId, $ownersIdArray))&&$dbEntery['userId']!=-1&&$dbEntery['commentType']!=3&&$dbEntery['commentType']!=4&&$dbEntery['commentType']!=5&&$dbEntery['commentType']!=6) {
            $toDisplay .= '<img src="tpl/stdstyle/images/free_icons/cross.png" /><a href="javascript:void(0);" onclick="deleteComment('.$dbEntery['id'].','.$loggedUserId.')">'.tr('pt130').'</a>';
        }
        if($loggedUserId == $dbEntery['userId']) {
                $toDisplay .= '
                    <img src="tpl/stdstyle/images/free_icons/pencil.png" />
                    <a href="javascript:void(0);" onclick="editComment('.$dbEntery['id'].','.$loggedUserId.')">'.tr('pt145').'</a>';
            }
        $toDisplay .= '</span>';
    }
    $toDisplay .= '
        </td>
    </tr>
    <tr>
        <td class="commentContent" valign="top"><span id="commentId-'.$dbEntery['id'].'" >'.htmlspecialchars_decode(stripslashes($dbEntery['commentText'])).'</span></td>
    </tr><tr><td>&nbsp</td></tr>'
    ;
}
$toDisplay .= '</table>';
$toDisplay .= '<div align="center">';

if ($count > $nextSearchStart || $_REQUEST['start'] > 0) $toDisplay .= '<div style="padding:3px">'.paginate(ceil($count/$paginateCount), $_REQUEST['start']).'</div>';

if ($_REQUEST['start']-$paginateCount < 0 ) {
    $startNew = 0;
} else {
    $startNew = $_REQUEST['start']-$paginateCount;
}
if ($_REQUEST['start'] > 0) {
    $toDisplay .= '<a href="javascript:void(0)" onclick="ajaxGetComments('.$startNew.', '.$paginateCount.');" class="editPtDataButton">'.tr('pt059').'</a>';
}
if ($count > $nextSearchStart) {
    $toDisplay .= ' <a href="javascript:void(0)" onclick="ajaxGetComments('.$nextSearchStart.', '.$paginateCount.');" class="editPtDataButton">'.tr('pt058').'</a>';
}

$toDisplay .= '</div>';

echo $toDisplay;

function paginate($totalPagesCount, $startNow){
    $displayStr = '<br />';
    for ($i=0; $i < $totalPagesCount; $i++) {
        if(ceil($startNow/powerTrailBase::commentsPaginateCount) == $i) $btnStyle = 'currentPaginateButton';
        else $btnStyle = 'paginateButton';
        $displayStr .= '<a href="javascript:void(0)" onclick="ajaxGetComments('.($i*powerTrailBase::commentsPaginateCount).', '.powerTrailBase::commentsPaginateCount.');" class="'.$btnStyle.'">'.($i+1) .'</a>';
    }
return $displayStr;
}
