<?php

use src\Models\OcConfig\OcConfig;
use src\Utils\Debug\Debug;
use src\Models\ApplicationContainer;

class sendEmail
{
    public static function emailOwners($ptId, $commentType, $commentDateTime, $commentText, $action, $commentOwnerId = false, $delReason = '') {
        /*ugly, but we need this values from common.inc.php*/
        global $absolute_server_URI, $siteDateFormat, $siteDateTimeFormat;
        $siteDateFormat = 'Y-m-d';
        $siteDateTimeFormat = 'Y-m-d H:i';

        $owners = powerTrailBase::getPtOwners($ptId);
        $commentTypes = src\Controllers\PowerTrailController::getEntryTypes();
        $ptDbRow = powerTrailBase::getPtDbRow($ptId);

        //remove images
        $commentText = preg_replace("/<img[^>]+\>/i", "", $commentText);

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8 ' . "\r\n";
        $headers .= "From: ".OcConfig::getSiteName()." <" . OcConfig::getEmailAddrNoReply() . ">\r\n";
        $headers .= "Reply-To: " . OcConfig::getEmailAddrNoReply() . "\r\n";

        $mailbody = file_get_contents(dirname(__FILE__) . '/commentEmail.html');

        switch ($action) {
            case 'delComment' :
                $actionDesc = sprintf(tr('pt131'), $ptDbRow['name']);
                $subject = $actionDesc;
                $mailbody = mb_ereg_replace('{commentAction}', tr('pt132'), $mailbody);
                $mailbody = mb_ereg_replace('{actionDesc}', $actionDesc, $mailbody);
                $mailbody = mb_ereg_replace('{delReason}', $delReason, $mailbody);
                $mailbody = mb_ereg_replace('{pt153}', tr('pt153'), $mailbody);
                break;

            case 'restoreComment' :
                $actionDesc = sprintf(tr('pt136'), $ptDbRow['name']);
                $subject = $actionDesc;
                $mailbody = mb_ereg_replace('{commentAction}', tr('pt137'), $mailbody);
                $mailbody = mb_ereg_replace('{actionDesc}', $actionDesc, $mailbody);
                $mailbody = mb_ereg_replace('{delReason}', '', $mailbody);
                $mailbody = mb_ereg_replace('{pt153}', '', $mailbody);
                break;

            case 'newComment' :
                $subject = tr('pt128') . ' ' . $ptDbRow['name'];
                $mailbody = mb_ereg_replace('{commentAction}', tr('pt127'), $mailbody);
                $mailbody = mb_ereg_replace('{actionDesc}', tr('pt128'), $mailbody);
                $mailbody = mb_ereg_replace('{delReason}', '', $mailbody);
                $mailbody = mb_ereg_replace('{pt153}', '', $mailbody);
                break;

            case 'editComment' :
                $subject = tr('pt146') . ' ' . $ptDbRow['name'];
                $mailbody = mb_ereg_replace('{commentAction}', tr('pt147'), $mailbody);
                $mailbody = mb_ereg_replace('{actionDesc}', tr('pt146'), $mailbody);
                $mailbody = mb_ereg_replace('{delReason}', '', $mailbody);
                $mailbody = mb_ereg_replace('{pt153}', '', $mailbody);
                break;
        }

        $user = ApplicationContainer::GetAuthorizedUser();
        if ($user) {
            $userId = $user->getUserId();
            $username = $user->getUserName();
        } else {
            $userId = -1;
            $username = 'SYSTEM';
        }

        $mailbody = mb_ereg_replace('{oclogo}', OcConfig::getHeaderLogo(), $mailbody);
        $mailbody = mb_ereg_replace('{mail_auto_generated}', tr('mail_auto_generated'), $mailbody);
        $mailbody = mb_ereg_replace('{commentDateTime}', date($siteDateTimeFormat, strtotime($commentDateTime)), $mailbody);
        $mailbody = mb_ereg_replace('{userId}', $userId, $mailbody);
        $mailbody = mb_ereg_replace('{userName}', $username, $mailbody);
        $mailbody = mb_ereg_replace('{absolute_server_URI}', $absolute_server_URI, $mailbody);
        if (isset($commentTypes[$commentType]['translate']))
            $mailbody = mb_ereg_replace('{commentType}', tr($commentTypes[$commentType]['translate']), $mailbody);
        else
            $mailbody = mb_ereg_replace('{commentType}', '&nbsp;', $mailbody);
        $mailbody = mb_ereg_replace('{ptName}', $ptDbRow['name'], $mailbody);
        $mailbody = mb_ereg_replace('{ptId}', $ptId, $mailbody);
        $mailbody = mb_ereg_replace('{pt133}', tr('pt133'), $mailbody);
        $mailbody = mb_ereg_replace('{pt134}', tr('pt134'), $mailbody);
        $mailbody = mb_ereg_replace('{commentText}', htmlspecialchars_decode(stripslashes($commentText)), $mailbody);
        $mailbody = mb_ereg_replace('{addingCommentDateTime}', date($siteDateTimeFormat), $mailbody);

        $doNotSendEmailToCommentAuthor = false;
        foreach ($owners as $owner) {
            $to = $owner['email'];
            if($owner['power_trail_email'] == 1){
                //TODO: Email class should be used here in future...
                if(! mb_send_mail($to, $subject, $mailbody, $headers)){
                    Debug::errorLog('Mail sending failure: to:'.$to);
                }
            }
            if ($commentOwnerId && $commentOwnerId == $owner["user_id"]) {
                $doNotSendEmailToCommentAuthor = true;
            }
        }
        if ($commentOwnerId && !$doNotSendEmailToCommentAuthor) {
            $userDetails = powerTrailBase::getUserDetails($commentOwnerId);
            if($userDetails['power_trail_email']) {
                if(! mb_send_mail($userDetails['email'], $subject, $mailbody, $headers)){
                    Debug::errorLog('Mail sending failure: to:'.$userDetails['email']);
                }
            }
        }
        //for debug only
        //mb_send_mail('lza@tlen.pl', $subject, $mailbody, $headers);
    }
}
