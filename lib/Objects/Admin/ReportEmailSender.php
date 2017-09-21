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
    public static function sendReportWatch(Report $report, User $toUser, $log)
    {
        $subject = tr('admin_reports_mail_subjwatch') . ' #'. $report->getId();
        $subject .= ' - ' . $report->getCache()->getCacheName();
        $subject .= ' (' . $report->getCache()->getWaypointId() . ')';
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_watch.email.html', true);
        $formattedMessage->setVariable('content', $log);
        $formattedMessage->setVariable('cacheWP', $report->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($report->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $report->getCache()->getCacheName());
        $formattedMessage->setVariable('reporturl', Report::getLinkToReport($report->getId()));
        $formattedMessage->setVariable('reportid', $report->getId());
        $formattedMessage->setVariable('reporttype', tr($report->getReportTypeTranslationKey()));
        $formattedMessage->setVariable('reportstatus', tr($report->getReportStatusTranslationKey()));
        $formattedMessage->setVariable('reportleader', $report->getUserLeader()->getUserName());
        $formattedMessage->setVariable('reportsubmitter', $report->getUserSubmit()->getUserName());
        $formattedMessage->setVariable('server', OcConfig::getAbsolute_server_URI());
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
        $subject = tr('admin_reports_mail_subjleader') . ' #'. $report->getId();
        $subject .= ' - ' . $report->getCache()->getCacheName();
        $subject .= ' (' . $report->getCache()->getWaypointId() . ')';
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_newleader.email.html', true);
        $formattedMessage->setVariable('user', $report->getUserChangeStatus()->getUserName());
        $formattedMessage->setVariable('reportid', $report->getId());
        $formattedMessage->setVariable('reporttype', tr($report->getReportTypeTranslationKey()));
        $formattedMessage->setVariable('reporturl', Report::getLinkToReport($report->getId()));
        $formattedMessage->setVariable('server', OcConfig::getAbsolute_server_URI());
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
        $subject = tr('admin_reports_mail_subjlook') . ' #'. $report->getId();
        $subject .= ' - ' . $report->getCache()->getCacheName();
        $subject .= ' (' . $report->getCache()->getWaypointId() . ')';
        $formattedMessage = new EmailFormatter(self::TEMPLATE_PATH . 'report_statuslookhere.email.html', true);
        $formattedMessage->setVariable('user', $report->getUserChangeStatus()->getUserName());
        $formattedMessage->setVariable('reportid', $report->getId());
        $formattedMessage->setVariable('reporttype', tr($report->getReportTypeTranslationKey()));
        $formattedMessage->setVariable('reporturl', Report::getLinkToReport($report->getId()));
        $formattedMessage->setVariable('server', OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable('cacheWP', $report->getCache()->getWaypointId());
        $formattedMessage->setVariable('cacheurl', GeoCacheCommons::GetCacheUrlByWp($report->getCache()->getWaypointId()));
        $formattedMessage->setVariable('cachename', $report->getCache()->getCacheName());
        $formattedMessage->setVariable('reportstatus', tr($report->getReportStatusTranslationKey()));
        $formattedMessage->setVariable('reportleader', $report->getUserLeader()->getUserName());
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
    
}
