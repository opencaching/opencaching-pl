<?php

use src\Controllers\PowerTrailController;
use src\Models\ApplicationContainer;
use src\Utils\Database\OcDb;

require_once __DIR__ . '/../lib/common.inc.php';

if (!isset($_REQUEST['projectId'])) {
    http_response_code(403);
    echo "Unknown PT";
    exit;
}

$start = intval($_REQUEST['start'] ?? -1);
$limit = intval($_REQUEST['limit'] ?? -1);
if ($limit < 0 || $start < 0) {
    http_response_code(403);
    exit;
}

$user = ApplicationContainer::GetAuthorizedUser();
if (!$user) {
    $loggedUserId = null;
    $ocTeamUser = false;
} else {
    $loggedUserId = $user->getUserId();
    $ocTeamUser = $user->hasOcTeamRole();
}

$commentsArr = PowerTrailController::getEntryTypes();
$ptOwners = powerTrailBase::getPtOwners($_REQUEST['projectId']);

$paginateCount = powerTrailBase::commentsPaginateCount;
$ownersIdArray = [];
foreach ($ptOwners as $owner) {
    $ownersIdArray[] = $owner['user_id'];
}
$nextSearchStart = $start + $limit;

$db = OcDb::instance();
$q = 'SELECT count(*) AS `count` FROM  `PowerTrail_comments`
    WHERE  `PowerTrailId` =:1 AND `deleted` = 0 ';
$s = $db->multiVariableQuery($q, $_REQUEST['projectId']);
$count = $db->dbResultFetchOneRowOnly($s);
$count = $count['count'];

$query = 'SELECT * FROM  `PowerTrail_comments`, `user`
          WHERE  `PowerTrailId` =:variable1
            AND (`deleted` = 0 OR :variable4)
            AND `PowerTrail_comments`.`userId` = `user`.`user_id`
          ORDER BY  `logDateTime` DESC
          LIMIT :variable2 , :variable3   ';

$params['variable1']['value'] = (integer)$_REQUEST['projectId'];
$params['variable1']['data_type'] = 'integer';
$params['variable2']['value'] = $start;
$params['variable2']['data_type'] = 'integer';
$params['variable3']['value'] = $limit;
$params['variable3']['data_type'] = 'integer';
$params['variable4']['value'] = $ocTeamUser;
$params['variable4']['data_type'] = 'boolean';
$s = $db->paramQuery($query, $params);
$result = $db->dbResultFetchAll($s);

if (count($result) == 0) {
    echo '<p><br><br>' . tr('pt118') . '</p><br><br>';
    exit;
}
// build to display
$toDisplay = '<table id="commentsTable" cellspacing="0">';

foreach ($result as $key => $dbEntry) {
    $userActivity = $dbEntry['hidden_count'] + $dbEntry['founds_count'] + $dbEntry['notfounds_count'];

    $logDateTime = explode(' ', $dbEntry['logDateTime']);

    if (!array_key_exists($dbEntry['commentType'], $commentsArr)) {
        // skip unknown comments type entries
        continue;
    }

    $strikethrough = ($dbEntry['deleted'] ? 'style="text-decoration: line-through"' : '');

    $toDisplay .= '
    <tr>
        <td colspan="3" class="commentHead" ' . $strikethrough . '>
            <span class="CommentDate" id="CommentDate-' . $dbEntry['id'] . '">' . $logDateTime[0] . '</span>
            <span class="commentTime" id="commentTime-' . $dbEntry['id'] . '">' . substr($logDateTime[1], 0, -3) . '</span>
                <a href="viewprofile.php?userid=' . $dbEntry['userId'] . '"><b>' . $dbEntry['username'] . '</b></a>
                (<img height="13" src="/images/blue/thunder_ico.png" alt=""><font size="-1">' . $userActivity . '</font>)
            - <span style="color: ' . $commentsArr[$dbEntry['commentType']]['color'] . ';">' . tr($commentsArr[$dbEntry['commentType']]['translate']) . '</span>';

    if (!is_null($loggedUserId)) {
        $toDisplay .= '<span class="editDeleteComment">';

        if ($dbEntry['deleted']) {
            if ($ocTeamUser) {
                $toDisplay .= '&nbsp;<img src="images/free_icons/accept.png" alt=""> <a href="javascript:void(0);" onclick="restoreComment(' . $dbEntry['id'] . ',' . $loggedUserId . ', true)">' . tr('restore') . '</a>';
            }
        } else {
            if (($loggedUserId == $dbEntry['userId'] || in_array($loggedUserId, $ownersIdArray))
                && $dbEntry['userId'] != -1
                && !in_array($dbEntry['commentType'], [3, 4, 5, 6])
            ) {
                $toDisplay .= '&nbsp;<img src="images/free_icons/cross.png" alt=""> <a href="javascript:void(0);" onclick="deleteComment(' . $dbEntry['id'] . ',' . $loggedUserId . ', false)">' . tr('pt130') . '</a>';
            }
            if ($loggedUserId == $dbEntry['userId']) {
                $toDisplay .= '
                    &nbsp;<img src="images/free_icons/pencil.png" alt="">
                    <a href="javascript:void(0);" onclick="editComment(' . $dbEntry['id'] . ',' . $loggedUserId . ')">' . tr('pt145') . '</a>';
            }
        }
        $toDisplay .= '</span>';
    }
    $toDisplay .= '
        </td>
    </tr>
    <tr>
        <td class="commentContent" valign="top"><span id="commentId-' . $dbEntry['id'] . '" ' . $strikethrough . '>' . htmlspecialchars_decode(stripslashes($dbEntry['commentText'])) . '</span></td>
    </tr><tr><td>&nbsp;</td></tr>';
}
$toDisplay .= '</table>';
$toDisplay .= '<div align="center">';

if ($count > $nextSearchStart || $start > 0) $toDisplay .= '<div style="padding:3px">' . paginate(ceil($count / $paginateCount), $start) . '</div>';

if ($start - $paginateCount < 0) {
    $startNew = 0;
} else {
    $startNew = $start - $paginateCount;
}
if ($start > 0) {
    $toDisplay .= '<a href="javascript:void(0)" onclick="ajaxGetComments(' . $startNew . ', ' . $paginateCount . ')" class="editPtDataButton">' . tr('pt059') . '</a>';
}
if ($count > $nextSearchStart) {
    $toDisplay .= ' <a href="javascript:void(0)" onclick="ajaxGetComments(' . $nextSearchStart . ', ' . $paginateCount . ')" class="editPtDataButton">' . tr('pt058') . '</a>';
}

$toDisplay .= '</div>';

echo $toDisplay;

function paginate($totalPagesCount, $startNow): string
{
    $displayStr = '<br>';
    for ($i = 0; $i < $totalPagesCount; $i++) {
        if (ceil($startNow / powerTrailBase::commentsPaginateCount) == $i) $btnStyle = 'currentPaginateButton';
        else $btnStyle = 'paginateButton';
        $displayStr .= '<a href="javascript:void(0)" onclick="ajaxGetComments(' . ($i * powerTrailBase::commentsPaginateCount) . ', ' . powerTrailBase::commentsPaginateCount . ')" class="' . $btnStyle . '">' . ($i + 1) . '</a>';
    }
    return $displayStr;
}
