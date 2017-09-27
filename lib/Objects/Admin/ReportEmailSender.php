<?php
namespace lib\Objects\Admin;

use Utils\Email\EmailFormatter;
use lib\Objects\User\User;
use Utils\Email\Email;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\GeoCache\GeoCacheCommons;

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
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_watch.email.html', true);
        $formattedMessage->setVariable('content', ReportLog::getFormattedLogById($logId));
        $formattedMessage->setVariable('cacheWP', $report->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($report->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $report->getCache()->getCacheName());
        $formattedMessage->setVariable('reporturl', Report::getLinkToReport($report->getId()));
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
        $email->setSubject(ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjwatch'), $report));
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
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_newleader.email.html', true);
        $formattedMessage->setVariable('user', $report->getUserLastChange()->getUserName());
        $formattedMessage->setVariable('reportid', $report->getId());
        $formattedMessage->setVariable('reporttype', tr($report->getReportTypeTranslationKey()));
        $formattedMessage->setVariable('reporturl', Report::getLinkToReport($report->getId()));
        $formattedMessage->setVariable('server', rtrim(OcConfig::getAbsolute_server_URI(), '/'));
        $formattedMessage->setVariable('cacheWP', $report->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($report->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $report->getCache()->getCacheName());
        $formattedMessage->setVariable('reportstatus', tr($report->getReportStatusTranslationKey()));
        $formattedMessage->setVariable('reportsubmitter', $report->getUserSubmit()->getUserName());
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), true);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->setSubject(ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjleader'), $report));
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
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_statuslookhere.email.html', true);
        $formattedMessage->setVariable('user', $report->getUserLastChange()->getUserName());
        $formattedMessage->setVariable('reportid', $report->getId());
        $formattedMessage->setVariable('reporttype', tr($report->getReportTypeTranslationKey()));
        $formattedMessage->setVariable('reporturl', Report::getLinkToReport($report->getId()));
        $formattedMessage->setVariable('reportstatus', tr($report->getReportStatusTranslationKey()));
        $formattedMessage->setVariable('reportleader', $report->getUserLeader()->getUserName());
        $formattedMessage->setVariable('reportsubmitter', $report->getUserSubmit()->getUserName());
        $formattedMessage->setVariable('cacheWP', $report->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($report->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $report->getCache()->getCacheName());
        $formattedMessage->setVariable('server', rtrim(OcConfig::getAbsolute_server_URI(), '/'));
        $formattedMessage->addFooterAndHeader($toUser->getUserName(), true);
        $email = new Email();
        $email->addToAddr($toUser->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->setSubject(ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjlook'), $report));
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
        $formattedMessage->setVariable('reporturl', Report::getLinkToReport($poll->getReport()->getId()));
        $formattedMessage->setVariable('reportstatus', tr($poll->getReport()->getReportStatusTranslationKey()));
        $formattedMessage->setVariable('reportleader', $poll->getReport()->getUserLeader()->getUserName());
        $formattedMessage->setVariable('reportsubmitter', $poll->getReport()->getUserSubmit()->getUserName());
        $formattedMessage->setVariable('cacheWP', $poll->getReport()->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($poll->getReport()->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $poll->getReport()->getCache()->getCacheName());
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
            $email->setSubject(ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjpollrem'), $poll->getReport()));
        } else {
            $email->setSubject(ReportEmailTemplate::processTemplate(tr('admin_reports_mail_subjpoll'), $poll->getReport()));
        }
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
}
