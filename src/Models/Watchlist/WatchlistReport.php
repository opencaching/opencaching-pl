<?php
/**
 * Contains \src\Models\Watchlist\WatchlistReport class definition
 */
namespace src\Models\Watchlist;

use src\Models\OcConfig\OcConfig;
use src\Utils\Text\Formatter;
use src\Utils\Email\EmailFormatter;
use src\Utils\Email\Email;
use src\Utils\Uri\SimpleRouter;

/**
 * Used for preparing and sending email to watcher
 */
class WatchlistReport
{
    /** Default report email contents template path */
    const DEFAULT_TEMPLATE_PATH = __DIR__.'/../../../resources/email/watchlist.email.html';

    /** @var \src\Utils\Email\EmailFormatter template instance to format email */
    private $reportTemplate;
    /** @var string translated static part of email subject  */
    private $watchlistSubject;
    /** @var string translated information about no logs in section */
    private $noLogs;

    /**
     * Inits email formatter from template and translations
     *
     * @param string $templatePath email template path, default is used if null
     */
    public function __construct($templatePath = null)
    {
        $this->reportTemplate = new EmailFormatter(
            $templatePath != null ? $templatePath : self::DEFAULT_TEMPLATE_PATH,
            true
        );
        $this->reportTemplate->setVariable(
            'absolute_server_URI',
            OcConfig::getAbsolute_server_URI()
        );
        $this->reportTemplate->setVariable(
            'emailSign',
            OcConfig::getOcteamEmailsSignature()
        );
        $this->watchlistSubject = tr('watchlist_subject');
        $this->noLogs = tr('watchlist_nologs');
    }

    /**
     * Prepares and sends mail, using \src\Utils\Email\Email class,
     * with watchlist report according to values of watcher attributes
     *
     * @param WatchlistWatcher $watcher
     *
     * @return boolean sending mail operation status
     */
    public function prepareAndSend(WatchlistWatcher $watcher)
    {
        $subject = $this->watchlistSubject
            . ' ' . OcConfig::getSiteName() . ': '
            . Formatter::dateTime(new \DateTime());

        $report = clone $this->reportTemplate;
        $report->addFooterAndHeader($watcher->getUsername());

        if (sizeof($watcher->getOwnerLogs()) > 0) {
            $report->setVariable(
                'ownerlogs',
                rtrim(implode('', $watcher->getOwnerLogs()), "\r\n")
            );
            $report->setVariable('cachesOwnedDisplay', 'block');
        } else {
            $report->setVariable('ownerlogs', $this->noLogs);
            $report->setVariable('cachesOwnedDisplay', 'none');
        }

        if (sizeof($watcher->getWatchLogs()) > 0) {
            $report->setVariable(
                'watchlogs',
                rtrim(implode('', $watcher->getWatchLogs()), "\r\n")
            );
            $report->setVariable('cachesWatchedDisplay', 'block');
        } else {
            $report->setVariable('watchlogs', $this->noLogs);
            $report->setVariable('cachesWatchedDisplay', 'none');
        }
        $report->setVariable('urlNotifySettings', SimpleRouter::getAbsLink('UserProfile', 'notifySettings'));

        $email = new Email();
        $email->addToAddr($watcher->getEmail());
        $email->setReplyToAddr(OcConfig::getEmailAddrNoReply());
        $email->setFromAddr(OcConfig::getEmailAddrNoReply());
        $email->addSubjectPrefix(OcConfig::getEmailSubjectPrefix());
        $email->setSubject($subject);
        $email->setHtmlBody($report->getEmailContent());
        return $email->send();
    }
}
