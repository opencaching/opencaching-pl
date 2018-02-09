<?php
/**
 * Contains \lib\Objects\Watchlist\WatchlistItem class definition
 */
namespace lib\Objects\Watchlist;

use lib\Objects\OcConfig\OcConfig;
use lib\Objects\GeoCache\GeoCacheLogCommons;
use Utils\Text\Formatter;

/**
 * Used for preparing formatted items of watchlist report based on log data
 */
class WatchlistItem
{
    /** Default report item template path */
    const DEFAULT_TEMPLATE_PATH = __DIR__
        . '/../../../tpl/stdstyle/email/watchlist_item.html';
    /** Default logtype color */
    const LOGTYPE_COLOR_DEFAULT = 'black';
    /** Logtype colors array, indexed by logtype value */
    const LOGTYPE_COLORS = array(
        self::LOGTYPE_COLOR_DEFAULT, 'green', 'red', 'black', 'green', 'orange',
        'red', 'green', 'green', 'red', 'green', 'red', 'black'
    );

    /** @var string source template contents used for further completion */
    private $itemTemplate;
    /** @var string translated recommendation text */
    private $recTranslation;
    /** @var string translated OC Team authorities name */
    private $cogUsername;

    /**
     * Inits source string from template as well as
     * translations common for all items
     *
     * @param string $templatePath the path of template to use as a source,
     *     if null the default path is used
     */
    public function __construct($templatePath = null)
    {
        if ($templatePath == null) {
            $templatePath = self::DEFAULT_TEMPLATE_PATH;
        }
        $this->itemTemplate = mb_ereg_replace(
            '{absolute_server_URI}',
            OcConfig::getAbsolute_server_URI(),
            file_get_contents($templatePath)
        );
        $this->recTranslation = tr('recommendation');
        $this->cogUsername = tr('cog_user_name');
    }

    /**
     * Prepares single watchlist item based on given log for use in report
     * sent in email
     *
     * @param WatchlistGeoCacheLog $log the log to prepare item from
     * @param string $srcWatchlistItemText the template text if different from
     *     the default one
     *
     * @return string prepared watchlist item
     */
    public function prepare(
        WatchlistGeoCacheLog $log,
        $srcWatchlistItemText = null
    ) {
        if ($srcWatchlistItemText == null
            || mb_strlen($srcWatchlistItemText) == 0
        ) {
            $srcWatchlistItemText = $this->itemTemplate;
        }

        $text = preg_replace("/<img[^>]+\>/i", "", $log->getLogText());

        $logTypeParams = $this->getLogTypeParams($log->getLogType());
        if (isset($logTypeParams['username'])) {
            $user = $logTypeParams['username'];
        } else {
            $user = $log->getLogger();
        }

        if ($log->isRecommended()
            && $log->getLogType() == GeoCacheLogCommons::LOGTYPE_FOUNDIT
        ) {
            $rcmd = ' + ' . $this->recTranslation;
        } else {
            $rcmd = '';
        }

        $watchlistItemText = mb_ereg_replace(
            '{date}',
            Formatter::dateTime($log->getLogDate()),
            $srcWatchlistItemText
        );
        $watchlistItemText = mb_ereg_replace(
            '{user}',
            $log->getLogger(),
            $watchlistItemText
        );
        $watchlistItemText = mb_ereg_replace(
            '{logtypeColor}',
            $logTypeParams['logtypeColor'],
            $watchlistItemText
        );
        $watchlistItemText = mb_ereg_replace(
            '{logtype}',
            $logTypeParams['logtype'] . $rcmd,
            $watchlistItemText
        );
        $watchlistItemText = mb_ereg_replace(
            '{wp}',
            $log->getCacheWaypoint(),
            $watchlistItemText
        );
        $watchlistItemText = mb_ereg_replace(
            '{cachename}',
            $log->getCacheName(),
            $watchlistItemText
        );
        $watchlistItemText = mb_ereg_replace(
            '{text}',
            $text,
            $watchlistItemText
        );
        return $watchlistItemText;
    }

    /**
     * Prepares a set of parameters corresponding to given type of log
     *
     * @param int $logType the type of log
     *
     * @return string[] prepared parameters
     */
    private function getLogTypeParams($logType)
    {
        $logTypeParams['logtype'] = tr(
            GeoCacheLogCommons::typeTranslationKey($logType)
        );
        if (array_key_exists($logType, self::LOGTYPE_COLORS)) {
            $logTypeParams['logtypeColor'] = self::LOGTYPE_COLORS[$logType];
        } else {
            $logTypeParams['logtypeColor'] = self::LOGTYPE_COLOR_DEFAULT;
        }
        if ($logType == GeoCacheLogCommons::LOGTYPE_ADMINNOTE) {
            $logTypeParams['username'] = $this->cogUsername;
        }
        return $logTypeParams;
    }
}
