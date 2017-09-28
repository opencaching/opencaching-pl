<?php
namespace lib\Objects\Admin;

use Utils\Email\Email;
use Utils\Email\EmailFormatter;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\GeoCacheCommons;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\User\User;
use lib\Objects\User\UserCommons;

class ReportEmailSender
{

    const TEMPLATE_PATH = __DIR__ . '/../../../tpl/stdstyle/email/admin/reports/';

    /**
     * Sends e-mail for user who watch report
     *
     * @param Report $report
     * @param User $toUser
     * @param string $log
     */
    public static function sendReportWatch(Report $report, User $toUser, $logId)
    {
        $subject = ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjwatch'), $report);
        $subject = '[R#' . $report->getId() . '] ' . $subject;
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_watch.email.html', true);
        $formattedMessage->setVariable('content', ReportLog::getFormattedLogById($logId));
        $formattedMessage->setVariable('cacheWP', $report->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($report->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $report->getCache()->getCacheName());
        $formattedMessage->setVariable('cacheregion', $report->getCache()->getCacheLocationObj()->getLocationDesc(' &gt; '));
        $formattedMessage->setVariable('reporturl', $report->getLinkToReport());
        $formattedMessage->setVariable('reportid', $report->getId());
        $formattedMessage->setVariable('reporttype', tr($report->getReportTypeTranslationKey()));
        $formattedMessage->setVariable('reportstatus', tr($report->getReportStatusTranslationKey()));
        $formattedMessage->setVariable('reportleader', $report->getUserLeader()->getUserName());
        $formattedMessage->setVariable('reportsubmitter', $report->getUserSubmit()->getUserName());
        $formattedMessage->setVariable('server', rtrim(OcConfig::getAbsolute_server_URI(), '/'));
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), true);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForReviewers());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Sends e-mail to new report leader
     *
     * @param Report $report
     * @param User $toUser
     */
    public static function sendReportNewLeader(Report $report, User $toUser)
    {
        $subject = ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjleader'), $report);
        $subject = '[R#' . $report->getId() . '] ' . $subject;
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_newleader.email.html', true);
        $formattedMessage->setVariable('user', $report->getUserLastChange()->getUserName());
        $formattedMessage->setVariable('reportid', $report->getId());
        $formattedMessage->setVariable('reporttype', tr($report->getReportTypeTranslationKey()));
        $formattedMessage->setVariable('reporturl', $report->getLinkToReport());
        $formattedMessage->setVariable('server', rtrim(OcConfig::getAbsolute_server_URI(), '/'));
        $formattedMessage->setVariable('cacheWP', $report->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($report->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $report->getCache()->getCacheName());
        $formattedMessage->setVariable('cacheregion', $report->getCache()->getCacheLocationObj()->getLocationDesc(' &gt; '));
        $formattedMessage->setVariable('reportstatus', tr($report->getReportStatusTranslationKey()));
        $formattedMessage->setVariable('reportsubmitter', $report->getUserSubmit()->getUserName());
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), true);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForReviewers());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Sends e-mail about change report status to "Look here!"
     *
     * @param Report $report
     * @param User $toUser
     */
    public static function sendReportLookHere(Report $report, User $toUser)
    {
        $subject = ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjlook'), $report);
        $subject = '[R#' . $report->getId() . '] ' . $subject;
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_statuslookhere.email.html', true);
        $formattedMessage->setVariable('user', $report->getUserLastChange()->getUserName());
        $formattedMessage->setVariable('reportid', $report->getId());
        $formattedMessage->setVariable('reporttype', tr($report->getReportTypeTranslationKey()));
        $formattedMessage->setVariable('reporturl', $report->getLinkToReport());
        $formattedMessage->setVariable('reportstatus', tr($report->getReportStatusTranslationKey()));
        $formattedMessage->setVariable('reportleader', $report->getUserLeader()->getUserName());
        $formattedMessage->setVariable('reportsubmitter', $report->getUserSubmit()->getUserName());
        $formattedMessage->setVariable('cacheWP', $report->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($report->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $report->getCache()->getCacheName());
        $formattedMessage->setVariable('cacheregion', $report->getCache()->getCacheLocationObj()->getLocationDesc(' &gt; '));
        $formattedMessage->setVariable('server', rtrim(OcConfig::getAbsolute_server_URI(), '/'));
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), true);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForReviewers());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Sends e-mail about add new poll in report
     * $remind = true => send pool reminder
     *
     * @param ReportPoll $poll
     * @param User $toUser
     * @param boolean $remind
     */
    public static function sendNewPoll(ReportPoll $poll, User $toUser, $remind = false)
    {
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_newpoll.email.html', true);
        $formattedMessage->setVariable('user', $poll->getReport()->getUserLastChange()->getUserName());
        $formattedMessage->setVariable('pollquestion', $poll->getQuestion());
        $formattedMessage->setVariable('ans1', $poll->getAns1());
        $formattedMessage->setVariable('ans2', $poll->getAns2());
        $formattedMessage->setVariable('ans3txt', ($poll->getAns3() !== null) ? tr('admin_reports_lbl_ans') . ' 3: ' . $poll->getAns3() . '<br>&nbsp;' : '');
        $formattedMessage->setVariable('date_start', $poll->getDateStart()->format(OcConfig::instance()->getDatetimeFormat()));
        $formattedMessage->setVariable('date_end', $poll->getDateEnd()->format(OcConfig::instance()->getDatetimeFormat()));
        $formattedMessage->setVariable('reportid', $poll->getReport()->getId());
        $formattedMessage->setVariable('reporttype', tr($poll->getReport()->getReportTypeTranslationKey()));
        $formattedMessage->setVariable('reporturl', $poll->getReport()->getLinkToReport());
        $formattedMessage->setVariable('reportstatus', tr($poll->getReport()->getReportStatusTranslationKey()));
        $formattedMessage->setVariable('reportleader', $poll->getReport()->getUserLeader()->getUserName());
        $formattedMessage->setVariable('reportsubmitter', $poll->getReport()->getUserSubmit()->getUserName());
        $formattedMessage->setVariable('cacheWP', $poll->getReport()->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($poll->getReport()->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $poll->getReport()->getCache()->getCacheName());
        $formattedMessage->setVariable('cacheregion', $poll->getReport()->getCache()->getCacheLocationObj()->getLocationDesc(' &gt; '));
        $formattedMessage->setVariable('server', rtrim(OcConfig::getAbsolute_server_URI(), '/'));
        if ($remind) {
            $header = tr('admin_reports_mail_txtpollrem');
        } else {
            $header = tr('admin_reports_mail_txtpoll');
        }
        $formattedMessage->setVariable('header', ReportEmailTemplate::processTemplate($header, $poll->getReport()));
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), true);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        if ($remind) {
            $subject = ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjpollrem'), $poll->getReport());
        } else {
            $subject = ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjpoll'), $poll->getReport());
        }
        $subject = '[R#' . $poll->getReport()->getId() . '] ' . $subject;
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForReviewers());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Send e-mail from OC Team member to cacheowner or report submitter
     *
     * @param Report $report
     * @param User $toUser
     * @param string $content
     */
    public static function sendMailToUser(Report $report, User $toUser, $content)
    {
        $subject = ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjuser'), $report);
        $subject = '[R#' . $report->getId() . '] ' . $subject;
        $intro = ReportEmailTemplate::processTemplate(tr('admin_reports_mail_txtuser'), $report);
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_touser.email.html', true);
        $formattedMessage->setVariable('intro', $intro);
        $formattedMessage->setVariable('content', $content);
        $formattedMessage->setVariable('server', rtrim(OcConfig::getAbsolute_server_URI(), '/'));
        $formattedMessage->setVariable('cacheWP', $report->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($report->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $report->getCache()->getCacheName());
        $formattedMessage->setVariable('reportleader', $report->getUserLeader()->getUserName());
        $formattedMessage->setVariable('reportsubmitter', $report->getUserSubmit()->getUserName());
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), false);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getCogEmailAddress());
        $email->setFromAddr(OcConfig::getCogEmailAddress());
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Send e-mail to cacheowner about report cache to him
     *
     * @param User $toUser
     * @param User $submitter
     * @param GeoCache $cache
     * @param string $content
     * @param int $reason
     * @param boolean $publishMail
     */
    public static function sendReport2COMail2CO(User $toUser, User $submitter, GeoCache $cache, $content, $reason, $publishMail)
    {
        $now = new \DateTime('now');
        $server = rtrim(OcConfig::getAbsolute_server_URI(), '/');
        $subject = tr('reports_user_mail_subj1');
        $subject = mb_ereg_replace('{user}', $submitter->getUserName(), $subject);
        $subject = mb_ereg_replace('{cachename}', $cache->getCacheName(), $subject);
        $intro = tr('reports_user_mail_intro1');
        $intro = mb_ereg_replace('{date}', $now->format(OcConfig::instance()->getDatetimeFormat()), $intro);
        $intro = mb_ereg_replace('{user}', '<strong>' . $submitter->getUserName() . '</strong>', $intro);
        $intro = mb_ereg_replace('{cachelink}', '<a href="' . $server . $cache->getCacheUrl() . '">' . $cache->getCacheName() . '</a>', $intro);
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'newreport_2U_mail2CO.email.html', true);
        $formattedMessage->setVariable('reason', tr(ReportCommons::reportTypeTranslationKey($reason)));
        $formattedMessage->setVariable('intro', $intro);
        $formattedMessage->setVariable('content', $content);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        if ($publishMail) {
            $formattedMessage->setVariable('reply', tr('mailto_respByEmail') . '<br><a href="' . $server . '/mailto.php?userid=' . $submitter->getUserId() . '">' . tr('reports_user_mail_send') . '</a>');
            $formattedMessage->addFooterAndHeader($toUser->getUserName(), false);
            $email->setReplyToAddr($submitter->getEmail());
            $email->setFromAddr($submitter->getEmail());
        } else {
            $formattedMessage->setVariable('reply', tr('mailto_respByOc') . '<br><a href="' . $server . '/mailto.php?userid=' . $submitter->getUserId() . '">' . tr('reports_user_mail_send') . '</a>');
            $formattedMessage->addFooterAndHeader($toUser->getUserName(), true);
            $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
            $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        }
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Send e-mail to submiter report (report is send to user)
     *
     * @param User $toUser
     * @param GeoCache $cache
     * @param string $content
     * @param int $reason
     */
    public static function sendReport2COMail2S(User $toUser, GeoCache $cache, $content, $reason)
    {
        $now = new \DateTime('now');
        $server = rtrim(OcConfig::getAbsolute_server_URI(), '/');
        $subject = tr('reports_user_mail_subj2');
        $subject = mb_ereg_replace('{user}', $cache->getOwner()->getUserName(), $subject);
        $subject = mb_ereg_replace('{cachename}', $cache->getCacheName(), $subject);
        $intro = tr('reports_user_mail_intro2');
        $intro = mb_ereg_replace('{date}', $now->format(OcConfig::instance()->getDatetimeFormat()), $intro);
        $intro = mb_ereg_replace('{user}', '<strong>' . $cache->getOwner()->getUserName() . '</strong>', $intro);
        $intro = mb_ereg_replace('{cachelink}', '<a href="' . $server . $cache->getCacheUrl() . '">' . $cache->getCacheName() . '</a>', $intro);
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'newreport_2U_mail2U.email.html', true);
        $formattedMessage->setVariable('reason', tr(ReportCommons::reportTypeTranslationKey($reason)));
        $formattedMessage->setVariable('intro', $intro);
        $formattedMessage->setVariable('content', $content);
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), true);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Send e-mail to cacheowner about report his cache to OC Team
     *
     * @param User $toUser
     * @param Report $report
     */
    public static function sendReport2OCTMail2CO(User $toUser, Report $report)
    {
        $server = rtrim(OcConfig::getAbsolute_server_URI(), '/');
        $subject = tr('reports_user_mail_subj3');
        $subject = mb_ereg_replace('{usersubmit}', $report->getUserSubmit()->getUserName(), $subject);
        $subject = mb_ereg_replace('{cachename}', $report->getCache()->getCacheName(), $subject);
        $subject = '[R#' . $report->getId() . '] ' . $subject;
        $intro = tr('reports_user_mail_intro3');
        $intro = mb_ereg_replace('{date}', $report->getDateSubmit()->format(OcConfig::instance()->getDatetimeFormat()), $intro);
        $intro = mb_ereg_replace('{reportid}', $report->getId(), $intro);
        $intro = mb_ereg_replace('{usersubmit}', '<a href ="' . $server . UserCommons::GetUserProfileUrl($report->getUserIdSubmit()) . '">' . $report->getUserSubmit()->getUserName() . '</a>', $intro);
        $intro = mb_ereg_replace('{cachelink}', '<a href="' . $server . $report->getCache()->getCacheUrl() . '">' . $report->getCache()->getCacheName() . '</a>', $intro);
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'newreport_2OCT_mail2U.email.html', true);
        $formattedMessage->setVariable('intro', $intro);
        $formattedMessage->setVariable('reason', tr(ReportCommons::reportTypeTranslationKey($report->getType())));
        $formattedMessage->setVariable('content', $report->getContent());
        if ($report->getSecretLink() === null) {
            $formattedMessage->setVariable('secretlink', '');
        } else {
            $formattedMessage->setVariable('secretlink', '&nbsp;<br>' . tr('admin_reports_mail_link') . ' <a href="' . $server . $report->getSecretLink() . '">' . $server . $report->getSecretLink() . '</a>');
        }
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), false);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getCogEmailAddress());
        $email->setFromAddr(OcConfig::getCogEmailAddress());
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Send e-mail to submitter (report is send to OC Team)
     *
     * @param User $toUser
     * @param Report $report
     */
    public static function sendReport2OCTMail2S(User $toUser, Report $report)
    {
        $server = rtrim(OcConfig::getAbsolute_server_URI(), '/');
        $subject = tr('reports_user_mail_subj4');
        $subject = mb_ereg_replace('{cachename}', $report->getCache()->getCacheName(), $subject);
        $subject = '[R#' . $report->getId() . '] ' . $subject;
        $intro = tr('reports_user_mail_intro4');
        $intro = mb_ereg_replace('{date}', $report->getDateSubmit()->format(OcConfig::instance()->getDatetimeFormat()), $intro);
        $intro = mb_ereg_replace('{reportid}', $report->getId(), $intro);
        $intro = mb_ereg_replace('{cacheowner}', '<a href ="' . $server . UserCommons::GetUserProfileUrl($report->getCache()->getOwnerId()) . '">' . $report->getCache()
            ->getOwner()
            ->getUserName() . '</a>', $intro);
        $intro = mb_ereg_replace('{cachelink}', '<a href="' . $server . $report->getCache()->getCacheUrl() . '">' . $report->getCache()->getCacheName() . '</a>', $intro);
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'newreport_2OCT_mail2U.email.html', true);
        $formattedMessage->setVariable('intro', $intro);
        $formattedMessage->setVariable('reason', tr(ReportCommons::reportTypeTranslationKey($report->getType())));
        $formattedMessage->setVariable('content', $report->getContent());
        if ($report->getSecretLink() === null) {
            $formattedMessage->setVariable('secretlink', '');
        } else {
            $formattedMessage->setVariable('secretlink', '&nbsp;<br>' . tr('admin_reports_mail_link') . ' <a href="' . $server . $report->getSecretLink() . '">' . $server . $report->getSecretLink() . '</a>');
        }
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), false);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getCogEmailAddress());
        $email->setFromAddr(OcConfig::getCogEmailAddress());
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Send info about new report to OC Team member
     *
     * @param User $toUser
     * @param Report $report
     */
    public static function sendReport2OCTMail2OCTeam(User $toUser, Report $report)
    {
        $server = rtrim(OcConfig::getAbsolute_server_URI(), '/');
        $subject = tr('reports_user_mail_subj5');
        $subject = mb_ereg_replace('{cacheregion}', $report->getCache()
            ->getCacheLocationObj()
            ->getLocationDesc('>'), $subject);
        $subject = mb_ereg_replace('{user}', $report->getUserSubmit()->getUserName(), $subject);
        $subject = mb_ereg_replace('{cachename}', $report->getCache()->getCacheName(), $subject);
        $subject = '[R#' . $report->getId() . '] ' . $subject;
        $intro = tr('reports_user_mail_intro5');
        $intro = mb_ereg_replace('{date}', $report->getDateSubmit()->format(OcConfig::instance()->getDatetimeFormat()), $intro);
        $intro = mb_ereg_replace('{reportid}', '<a = href="' . $server . $report->getLinkToReport() . '">' . $report->getId() . '</a>', $intro);
        $intro = mb_ereg_replace('{usersubmit}', '<a href="' . $server . UserCommons::GetUserProfileUrl($report->getUserIdSubmit()) . '">' . $report->getUserSubmit()->getUserName() . '</a>', $intro);
        $intro = mb_ereg_replace('{cacheowner}', '<a href ="' . $server . UserCommons::GetUserProfileUrl($report->getCache()->getOwnerId()) . '">' . $report->getCache()
            ->getOwner()
            ->getUserName() . '</a>', $intro);
        $intro = mb_ereg_replace('{cachelink}', '<a href="' . $server . $report->getCache()->getCacheUrl() . '">' . $report->getCache()->getCacheName() . '</a>', $intro);
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'newreport_2OCT_mail2OCT.email.html', true);
        $formattedMessage->setVariable('intro', $intro);
        $formattedMessage->setVariable('reason', '<a href="' . $server . $report->getLinkToReport() . '">' . tr(ReportCommons::reportTypeTranslationKey($report->getType())) . '</a>');
        $formattedMessage->setVariable('content', $report->getContent());
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), false);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getCogEmailAddress());
        $email->setFromAddr(OcConfig::getCogEmailAddress());
        $email->setSubject($subject);
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForReviewers());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }
}
