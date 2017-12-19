<?php
/**
 * Purpose of this class: provide simple interface
 * for sending email messages from the code
 *
 * This class is interface to Email class.
 *
 */

namespace Utils\Email;

use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\User\User;
use lib\Objects\User\UserMessage;
use Utils\Gis\Gis;

class EmailSender
{
    /**
     * This method provide simple interface to send email messages
     * to OC Technical Admins (RT) if unexpected error occurs.
     *
     * @param string $message
     */
    public static function adminOnErrorMessage(
        $message,
        $spamDomain=OcSpamDomain::GENERIC_ERRORS)
    {

        //first check if sending email is allowed
        if(!OcSpamDomain::isEmailAllowed($spamDomain)){
            //skip email
            return;
        }

        //ok, mail allowed - build it
        $email = new Email();

        $email->addToAddr( OcConfig::getTechAdminsEmailAddr());
        $email->setReplyToAddr( OcConfig::getTechAdminsEmailAddr());
        $email->setFromAddr( OcConfig::getNoreplyEmailAddress());

        $email->addSubjectPrefix("OC Admin Email");
        $email->setSubject('Error in domain: '.$spamDomain); //TODO
        $email->setBody($message);

        if( ! $email->send() ){
            trigger_error(__METHOD__.": Email sending failed!", E_USER_NOTICE);
        }
    }

    /**
     * @return string a default path of directory containing email templates in current style
     */
    public static function getDefaultTemplatePath()
    {
        global $config;
        return __DIR__ . '/../../tpl/'.$config['style'].'/email/';
    }

    /**
     * @param $emailTemplateFile
     * @param $username
     * @param $country
     * @param $code
     * @param $newUserEmailAddress
     * @param $uuid
     */
    public static function sendActivationMessage($emailTemplateFile, $username, $country, $code, $newUserEmailAddress, $uuid) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
        $formattedMessage->setVariable("registermail01", tr("registermail01"));
        $formattedMessage->setVariable("useractivationmail1", tr("useractivationmail1"));
        $formattedMessage->setVariable("useractivationmail2", tr("useractivationmail2"));
        $formattedMessage->setVariable("useractivationmail3", tr("useractivationmail3"));
        $formattedMessage->setVariable("useractivationmail4", tr("useractivationmail4"));
        $formattedMessage->setVariable("useractivationmail5", tr("useractivationmail5"));
        $formattedMessage->setVariable("useractivationmail6", tr("useractivationmail6"));
        $formattedMessage->setVariable("user", $username);
        $formattedMessage->setVariable("code", $code);
        $formattedMessage->setVariable("country", $country);
        $formattedMessage->setVariable("email", $newUserEmailAddress);
        $formattedMessage->setVariable("useruid", $uuid);
        $formattedMessage->setVariable("server", OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable("sitename", OcConfig::getSiteName());

        $formattedMessage->addFooterAndHeader($username);

        $email = new Email();
        $email->addToAddr($newUserEmailAddress);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('register_email_subject'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * @param $emailTemplateFile
     * @param $username
     * @param $userEmailAddress
     */
    public static function sendPostActivationMail($emailTemplateFile, $username, $userEmailAddress) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
        $formattedMessage->setVariable("server", OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable("registermail01", tr("registermail01"));
        $formattedMessage->setVariable("postactivationmail01", tr("postactivationmail01"));
        $formattedMessage->setVariable("postactivationmail02", tr("postactivationmail02"));
        $formattedMessage->setVariable("postactivationmail03", tr("postactivationmail03"));
        $formattedMessage->setVariable("user", $username);

        $wikiLinks = OcConfig::getWikiLinks();

        $formattedMessage->setVariable("wikiaddress", $wikiLinks['forBeginers']);
        $formattedMessage->setVariable("sitename", OcConfig::getSiteName());

        $needAproveLimit = OcConfig::getNeedAproveLimit();
        $needFindLimit = OcConfig::getNeedFindLimit();

        if ($needAproveLimit > 0) {
            $formattedMessage->setVariable("postactivationmail05", tr('postactivationmail05'));
            $formattedMessage->setVariable("NEED_APPROVE_LIMIT", $needAproveLimit);
        } else {
            $formattedMessage->setVariable("postactivationmail05", "");
        }

        if ($needFindLimit > 0) {
            $formattedMessage->setVariable("postactivationmail04", tr('postactivationmail04'));
            $formattedMessage->setVariable("NEED_FIND_LIMIT", $needFindLimit);
        } else {
            $formattedMessage->setVariable("postactivationmail04", "");
        }

        $formattedMessage->addFooterAndHeader($username);

        $email = new Email();
        $email->addToAddr($userEmailAddress);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('post_activation_email_subject'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    public static function sendRemoveLogNotification($emailTemplateFile, GeoCacheLog $log, User $loggedUser)
    {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
        $formattedMessage->setVariable("log_owner", $log->getUser()->getUserName());
        $formattedMessage->setVariable("waypointId", $log->getGeoCache()->getWaypointId());
        $formattedMessage->setVariable("serviceUrl", OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable("logRemover", $loggedUser->getUserName());
        $formattedMessage->setVariable("logRemoverId", $loggedUser->getUserId());
        $formattedMessage->setVariable("cache_name", $log->getGeoCache()->getCacheName());
        $formattedMessage->setVariable("log_entry", $log->getText());
        $formattedMessage->setVariable("removedLog_01", tr('removedLog_01'));
        $formattedMessage->setVariable("removedLog_02", tr('removedLog_02'));

        $formattedMessage->addFooterAndHeader($log->getUser()->getUserName());

        $email = new Email();
        $email->addToAddr($log->getUser()->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('removed_log_title'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    public static function sendNotifyOfOcTeamCommentToCache(GeoCache $cache, User $admin, $message)
    {
        $emailTemplateFile = __DIR__ . '/../../tpl/stdstyle/email/octeam_comment.email.html';

        $formattedMessage = new EmailFormatter($emailTemplateFile);
        $formattedMessage->setVariable("ocTeamComment_01", tr("ocTeamComment_01"));
        $formattedMessage->setVariable("ocTeamComment_02", tr("ocTeamComment_02"));
        $formattedMessage->setVariable("ocTeamComment_03", tr("ocTeamComment_03"));
        $formattedMessage->setVariable("ocTeamComment_04", tr("ocTeamComment_04"));
        $formattedMessage->setVariable("ocTeamComment_05", tr("ocTeamComment_05"));
        $formattedMessage->setVariable("waypointId", $cache->getWaypointId());
        $formattedMessage->setVariable("cachename", $cache->getCacheName());
        $formattedMessage->setVariable("octeam_comment", $message);
        $formattedMessage->setVariable("adminName", $admin->getUserName());
        $formattedMessage->setVariable("adminId", $admin->getUserId());
        $formattedMessage->setVariable("server", OcConfig::getAbsolute_server_URI());

        $formattedMessage->addFooterAndHeader($cache->getOwner()->getUserName(), false);

        $email = new Email();
        $email->addToAddr($cache->getOwner()->getEmail());
        $email->setReplyToAddr(OcConfig::getCogEmailAddress());
        $email->setFromAddr(OcConfig::getCogEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('octeam_comment_subject'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();

        //Send copy to COG
        $emailCOG = new Email();
        $emailCOG->addToAddr(OcConfig::getCogEmailAddress());
        $emailCOG->setReplyToAddr(OcConfig::getCogEmailAddress());
        $emailCOG->setFromAddr(OcConfig::getCogEmailAddress());
        $emailCOG->addSubjectPrefix(OcConfig::getMailSubjectPrefixForReviewers());
        $emailCOG->setSubject(tr('octeam_comment_subject_copy').' '.$admin->getUserName());
        $emailCOG->setBody($formattedMessage->getEmailContent(), true);
        $emailCOG->send();
    }

    public static function sendAdoptionOffer($emailTemplateFile, $cacheName, $newOwnerUserName,
        $oldOwnerUserName, $userEmail) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
        $formattedMessage->setVariable("adopt01", tr("adopt_26"));
        $formattedMessage->setVariable("userName", '<b>'.$oldOwnerUserName.'</b>');
        $formattedMessage->setVariable("cacheName", '<b>'.$cacheName.'</b>');

        $formattedMessage->addFooterAndHeader($newOwnerUserName);

        $email = new Email();
        $email->addToAddr($userEmail);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('adopt_25'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    public static function sendAdoptionSuccessMessage($emailTemplateFile, $cacheName, $newOwnerUserName,
        $oldOwnerUserName, $oldOwnerEmail) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
        $formattedMessage->setVariable("adopt01", tr("adopt_31"));
        $formattedMessage->setVariable("userName", '<b>'.$newOwnerUserName.'</b>');
        $formattedMessage->setVariable("cacheName", '<b>'.$cacheName.'</b>');

        $formattedMessage->addFooterAndHeader($oldOwnerUserName);

        $email = new Email();
        $email->addToAddr($oldOwnerEmail);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('adopt_18'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    public static function sendAdoptionRefusedMessage($emailTemplateFile, $cacheName, $newOwnerUserName,
        $oldOwnerUserName, $oldOwnerEmail) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
        $formattedMessage->setVariable("adopt01", tr("adopt_29"));
        $formattedMessage->setVariable("userName", '<b>'.$newOwnerUserName.'</b>');
        $formattedMessage->setVariable("cacheName", '<b>'.$cacheName.'</b>');

        $formattedMessage->addFooterAndHeader($oldOwnerUserName);

        $email = new Email();
        $email->addToAddr($oldOwnerEmail);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('adopt_28'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Sends user2user message (used by mailTo)
     * Returns true on success;
     *
     * @param UserMessage $msg
     * @return boolean
     */
    public static function sendUser2UserMessage(User $from, User $to, $subject, $text, $attachSenderAddress)
    {

        // add additional prefix to subject
        $subject = tr('mailto_emailFrom').' '.$from->getUserName().': '.$subject;

        // prepare message text
        if($attachSenderAddress){
            $userMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/user2user/messageWithSenderEmail.email.html', true);
        }else{
            $userMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/user2user/messageWithoutSenderEmail.email.html', true);
        }

        $userMessage->setVariable('toUsername', $to->getUserName());
        $userMessage->setVariable('fromUsername', $from->getUserName());
        $userMessage->setVariable('fromEmail', $from->getEmail());
        $userMessage->setVariable('absoluteServerURI', OcConfig::getAbsolute_server_URI());
        $userMessage->setVariable('fromUserid', $from->getUserId());
        $userMessage->setVariable('text', nl2br($text));
        $userMessage->addFooterAndHeader($to->getUserName(), !$attachSenderAddress);


        // prepare copy for sender
        $senderCopy = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/user2user/messageCopyForSender.email.html', true);

        if(preg_match('/(?:<body[^>]*>)(.*)<\/body>/isU', $userMessage->getEmailContent(), $matches)) {
            $bodyOfMessage = $matches[1];
        }else{
            $bodyOfMessage = '';
        }

        $senderCopy->setVariable('text', $bodyOfMessage);
        $senderCopy->setVariable('toUsername', $to->getUserName());
        $senderCopy->addFooterAndHeader($from->getUserName());



        $noReplyAddress = OcConfig::getNoreplyEmailAddress();

        //send email to Recipient
        $email = new Email();
        $email->addToAddr($to->getEmail());
        if($attachSenderAddress){
            $email->setReplyToAddr($from->getEmail());
            $email->setFromAddr($from->getEmail());
        }else{
            $email->setReplyToAddr($noReplyAddress);
            $email->setFromAddr($noReplyAddress);
        }

        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject($subject);
        $email->setBody($userMessage->getEmailContent(), true);
        $result = $email->send();

        if(!$result){
            error_log(__METHOD__.': Mail sending failure to: '.$to->getEmail());
            return $result;
        }else{

            //send copy of email to sender
            $email = new Email();
            $email->addToAddr($from->getEmail());
            $email->setReplyToAddr($noReplyAddress);
            $email->setFromAddr($noReplyAddress);
            $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
            $email->setSubject($subject);
            $email->setBody($senderCopy->getEmailContent(), true);

            if(!$email->send()){
                error_log(__METHOD__.': Sender copy sending failure to: '.$from->getEmail());
            }
        }

        return $result;
    }

    public static function sendNotifyAboutNewCacheToOcTeam($emailTemplateFile, User $owner, $newCacheName, $newCacheId,
                                                           $region, $country) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
        $formattedMessage->setVariable("ocTeamNewCache_01", tr("ocTeamNewCache_01"));
        $formattedMessage->setVariable("ocTeamNewCache_02", tr("ocTeamNewCache_02"));
        $formattedMessage->setVariable("ocTeamNewCache_03", tr("ocTeamNewCache_03"));
        $formattedMessage->setVariable("ocTeamNewCache_04", tr("ocTeamNewCache_04"));
        $formattedMessage->setVariable("ocTeamNewCache_05", tr("ocTeamNewCache_05"));
        $formattedMessage->setVariable("ocTeamNewCache_06", tr("ocTeamNewCache_06"));
        $formattedMessage->setVariable("server", OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable("userid", $owner->getUserId());
        $formattedMessage->setVariable("username", $owner->getUserName());
        $formattedMessage->setVariable("cacheid", $newCacheId);
        $formattedMessage->setVariable("cachename", $newCacheName);

        $formattedMessage->addFooterAndHeader(OcConfig::getMailSubjectPrefixForReviewers());

        $email = new Email();
        $email->addToAddr(OcConfig::getCogEmailAddress());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForReviewers());

        if (isset($region) && isset($country)) {
            $email->setSubject(tr('ocTeamNewCache_sub').": ".$country." -> ".$region);
        } else {
            $email->setSubject(tr('ocTeamNewCache_sub').": ".tr('dummy_outside'));
        }

        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    public static function sendNewCacheNotify($emailTemplatePath, $notifiesList, User $user)
    {
        if (count($notifiesList) > 1) {
            $pluralSuffix = '_pl';
        } else {
            $pluralSuffix = '';
        }

        $content = '';
        foreach ($notifiesList as $item) {
            $line = file_get_contents($emailTemplatePath . 'newcache_notify_item.html');
            $line = mb_ereg_replace('{absolute_server_URI}', OcConfig::getAbsolute_server_URI(), $line);
            $line = mb_ereg_replace('{cache_type}', tr($item->getCache()->getCacheTypeTranslationKey()), $line);
            $line = mb_ereg_replace('{cache_type_icon}', Geocache::CacheIconByType($item->getCache()->getCacheType(), $item->getCache()->getStatus()), $line);
            $line = mb_ereg_replace('{cache_wp}', $item->getCache()->getWaypointId(), $line);
            $line = mb_ereg_replace('{cache_name}', $item->getCache()->getCacheName(), $line);
            $line = mb_ereg_replace('{cache_size}', tr($item->getCache()->getSizeTranslationKey()), $line);
            $line = mb_ereg_replace('{cache_direction}', Gis::bearing2Text(Gis::calcBearingBetween($user->getHomeCoordinates(), $item->getCache()->getCoordinates()), true), $line);
            $line = mb_ereg_replace('{cache_distance}', round(Gis::distanceBetween($user->getHomeCoordinates(), $item->getCache()->getCoordinates())), $line);
            $line = mb_ereg_replace('{cache_unit}', 'km', $line);
            $line = mb_ereg_replace('{cache_diff_icon}', $item->getCache()->getDifficultyIcon(), $line);
            $line = mb_ereg_replace('{cache_diff}', $item->getCache()->getDifficulty() / 2, $line);
            $line = mb_ereg_replace('{cache_ter_icon}', $item->getCache()->getTerrainIcon(), $line);
            $line = mb_ereg_replace('{cache_ter}', $item->getCache()->getTerrain() / 2, $line);
            $line = mb_ereg_replace('{cache_author_id}', $item->getCache()->getOwnerId(), $line);
            $line = mb_ereg_replace('{cache_author}', $item->getCache()->getOwner()->getUserName(), $line);
            $line = mb_ereg_replace('{cache_author_activity}', tr('user_activity01'), $line);
            $line = mb_ereg_replace('{cache_author_activity2}', tr('user_activity02'), $line);
            $line = mb_ereg_replace('{cache_author_found}', $item->getCache()->getOwner()->getFoundGeocachesCount(), $line);
            $line = mb_ereg_replace('{cache_author_dnf}', $item->getCache()->getOwner()->getNotFoundGeocachesCount(), $line);
            $line = mb_ereg_replace('{cache_author_hidden}', $item->getCache()->getOwner()->getHiddenGeocachesCount(), $line);
            $line = mb_ereg_replace('{cache_author_total}', $item->getCache()->getOwner()->getFoundGeocachesCount() + $item->getCache()->getOwner()->getNotFoundGeocachesCount() + $item->getCache()->getOwner()->getHiddenGeocachesCount(), $line);
            $line = mb_ereg_replace('{cache_date}', $item->getCache()->getDatePlaced()->format(OcConfig::instance()->getDateFormat()), $line);
            $content .= $line;
        }

        $subject = tr('notify_subject' . $pluralSuffix);
        $subject = mb_ereg_replace('{site_name}', OcConfig::getSiteName(), $subject);

        $formattedMessage = new EmailFormatter($emailTemplatePath . 'newcache_notify.email.html', true);
        $formattedMessage->addFooterAndHeader($user->getUserName());
        $formattedMessage->setVariable('intro', tr('notify_intro' . $pluralSuffix));
        $formattedMessage->setVariable('absolute_server_URI', OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable('content', $content);

        $email = new Email();
        $email->addToAddr($user->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject($subject);
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Prepares and sends mail with watchlist report according to values of parameters
     *
     * @param string $username the name of user to send the mail to
     * @param string $usermail the email address to send to
     * @param string $ownerLogs the logs regarding the user own caches
     * @param string $watchLogs the logs regarding caches watched by user
     * @param string $emailTemplatePath the directory path where to find the mail template, if null default is used
     *
     * @return int sending mail operation status
     */
    public static function sendWatchlistMail($username, $usermail, $ownerLogs, $watchLogs, $emailTemplatePath = null)
    {
        $subject = tr('runwatch03') . ' ' . OcConfig::getSiteName() . ': ' . date('Y-m-d H:i:s');

        $formattedMessage = new EmailFormatter(($emailTemplatePath != null ? $emailTemplatePath : self::getDefaultTemplatePath()) . 'watchlist.email.html', true);
        $formattedMessage->addFooterAndHeader($username);
        $formattedMessage->setVariable('absolute_server_URI', OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable('runwatch01', tr('runwatch01'));
        $formattedMessage->setVariable('runwatch02', tr('runwatch02'));
        $formattedMessage->setVariable('runwatch03', tr('runwatch03'));
        $formattedMessage->setVariable('runwatch04', tr('runwatch04'));
        $formattedMessage->setVariable('runwatch05', tr('runwatch05'));
        $formattedMessage->setVariable('runwatch06', tr('runwatch06'));
        $formattedMessage->setVariable('runwatch07', tr('runwatch07'));
        $formattedMessage->setVariable('runwatch08', tr('runwatch08'));
        $formattedMessage->setVariable('runwatch09', tr('runwatch09'));
        $formattedMessage->setVariable('runwatch10', tr('runwatch10'));
        $formattedMessage->setVariable('runwatch11', tr('runwatch11'));
        $formattedMessage->setVariable('runwatch12', tr('runwatch12'));
        $formattedMessage->setVariable('runwatch13', tr('runwatch13'));
        $formattedMessage->setVariable('runwatch14', tr('runwatch14'));
        $formattedMessage->setVariable('emailSign', OcConfig::getOcteamEmailsSignature());

        if ($ownerLogs != '') {
            $logtexts = $ownerLogs;

            while ((mb_substr($logtexts, -1) == "\n") || (mb_substr($logtexts, -1) == "\r")) {
                $logtexts = mb_substr($logtexts, 0, mb_strlen($logtexts) - 1);
            }
            $formattedMessage->setVariable('ownerlogs', $logtexts);
            $formattedMessage->setVariable('cachesOwnedDisplay', 'block');
        } else {
            $formattedMessage->setVariable('ownerlogs', tr('runwatch15'));
            $formattedMessage->setVariable('cachesOwnedDisplay', 'none');
        }

        if ($watchLogs != '') {
            $logtexts = $watchLogs;

            while ((mb_substr($logtexts, -1) == "\n") || (mb_substr($logtexts, -1) == "\r")) {
                $logtexts = mb_substr($logtexts, 0, mb_strlen($logtexts) - 1);
            }
            $formattedMessage->setVariable('watchlogs', $logtexts);
            $formattedMessage->setVariable('cachesWatchedDisplay', 'block');
        } else {
            $formattedMessage->setVariable('watchlogs', tr('runwatch15'));
            $formattedMessage->setVariable('cachesWatchedDisplay', 'none');
        }

        $email = new Email();
        $email->addToAddr($usermail);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject($subject);
        $email->setBody($formattedMessage->getEmailContent(), true);
        return $email->send();
    }

    /**
     * @return string the source text of template used for watchlist item presentation
     */
    public static function prepareWatchlistItemSrc($emailTemplatePath = null)
    {
        return file_get_contents(($emailTemplatePath != null ? $emailTemplatePath : self::getDefaultTemplatePath()) . 'watchlist_item.html');
    }

    /**
     * Prepares single watchlist item for use in report sent in email
     *
     * @param string $logdate date of the cache log
     * @param string $logger the user who entered log to the cache
     * @param int $logtype the type of entered log
     * @param string $wp the waypoint code of the cache
     * @param string $cachename the name of the cache
     * @param string $logtext the text entered by user
     * @param int $recommended if not zero the user has recommended the cache
     * @param string $srcWatchListItemText the source text of template used for watchlist item, if null default will be used
     *
     * @return string prepared watchlist item
     */
    public static function prepareWatchlistItem($logdate, $logger, $logtype, $wp, $cachename, $logtext, $recommended, $srcWatchlistItemText = null)
    {
        if ($srcWatchlistItemText == null or mb_strlen($srcWatchlistItemText) == 0) {
            $srcWatchlistItemText = self::prepareWatchlistItemSrc();
        }

        $text = preg_replace("/<img[^>]+\>/i", "", $logtext);

        $logtypeParams = self::getWatchlistLogtypeParams($logtype);
        if (isset($logtypeParams['username'])) {
            $user = $logtypeParams['username'];
        } else {
            $user = $logger;
        }

        if ($recommended != 0 && $logtype == 1) {
            $rcmd = ' + ' . tr('recommendation');
        } else {
            $rcmd = '';
        }

        $watchlistItemText = mb_ereg_replace('{date}', date('Y-m-d H:i', strtotime($logdate)), $srcWatchlistItemText);
        $watchlistItemText = mb_ereg_replace('{user}', $user, $watchlistItemText);
        $watchlistItemText = mb_ereg_replace('{logtypeColor}', $logtypeParams['logtypeColor'], $watchlistItemText);
        $watchlistItemText = mb_ereg_replace('{logtype}', $logtypeParams['logtype'] . $rcmd, $watchlistItemText);
        $watchlistItemText = mb_ereg_replace('{wp}', $wp, $watchlistItemText);
        $watchlistItemText = mb_ereg_replace('{cachename}', $cachename, $watchlistItemText);
        $watchlistItemText = mb_ereg_replace('{text}', $text, $watchlistItemText);
        return $watchlistItemText;
    }

    /**
     * Prepares a set of parameters corresponding to the given type of log
     *
     * @param int $logType the type of log
     *
     * @return array prepared parameters
     */
    private static function getWatchlistLogtypeParams($logType)
    {
        global $COGname;

        switch ($logType) {
            case '1':
                $logtypeParams['logtype'] = tr('logType1');
                $logtypeParams['logtypeColor'] = 'green';
                break;
            case '2':
                $logtypeParams['logtype'] = tr('logType2');
                $logtypeParams['logtypeColor'] = 'red';
                break;
            case '3':
                $logtypeParams['logtype'] = tr('logType3');
                $logtypeParams['logtypeColor'] = 'black';
                break;
            case '4':
                $logtypeParams['logtype'] = tr('logType4');
                $logtypeParams['logtypeColor'] = 'green';
                break;
            case '5':
                $logtypeParams['logtype'] = tr('logType5');
                $logtypeParams['logtypeColor'] = 'orange';
                break;
            case '6':
                $logtypeParams['logtype'] = tr('logType6');
                $logtypeParams['logtypeColor'] = 'red';
                break;
            case '7':
                $logtypeParams['logtype'] = tr('logType7');
                $logtypeParams['logtypeColor'] = 'green';
                break;
            case '8':
                $logtypeParams['logtype'] = tr('logType8');
                $logtypeParams['logtypeColor'] = 'green';
                break;
            case '9':
                $logtypeParams['logtype'] = tr('logType9');
                $logtypeParams['logtypeColor'] = 'red';
                break;
            case '10':
                $logtypeParams['logtype'] = tr('logType10');
                $logtypeParams['logtypeColor'] = 'green';
                break;
            case '11':
                $logtypeParams['logtype'] = tr('logType11');
                $logtypeParams['logtypeColor'] = 'red';
                break;
            case '12':
                $logtypeParams['logtype'] = tr('logType12');
                $logtypeParams['username'] = $COGname;
                $logtypeParams['logtypeColor'] = 'black';
                break;
            default:
                $logtypeParams['logtype'] = "";
                $logtypeParams['logtypeColor'] = 'black';
        }
        return $logtypeParams;
    }
}
