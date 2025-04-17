<?php

namespace src\Models\OcConfig;

final class OcConfig extends ConfigReader
{
    use EmailConfigTrait;
    use GeocacheConfigTrait;
    use I18nConfigTrait;
    use MapConfigTrait;
    use OkapiConfigTrait;
    use PicturesConfigTrait;
    use PrimaAprilisTrait;
    use SiteConfigTrait;
    use UserConfigTrait;
    use GeopathConfigTrait;

    /*
        const OCNODE_GERMANY    = 1;  // Opencaching Germany http://www.opencaching.de OC
        const OCNODE_POLAND     = 2;  // Opencaching Poland http://www.opencaching.pl OP
        const OCNODE_CZECH      = 3;  // Opencaching Czech http://www.opencaching.cz OZ
        const OCNODE_DEVELOPER  = 4;  // Local Development
        const OCNODE_UK         = 6;  // Opencaching Great Britain http://www.opencaching.org.uk OK
        const OCNODE_SWEDEN     = 7;  // Opencaching Sweden http://www.opencaching.se OS =>OC Scandinavia
        const OCNODE_USA        = 10; // Opencaching United States http://www.opencaching.us OU
        const OCNODE_RUSSIA     = 12; // Opencaching Russia http://www.opencaching.org.ru
        const OCNODE_BENELUX    = 14; // Opencaching Nederland https://www.opencaching.nl OB => OC Benelux
        const OCNODE_ROMANIA    = 16; // Opencaching Romania http://www.opencaching.ro OR
    */

    // old-style values - values from new-style config should be accessed through
    // $config[''] etc...

    private string $dbDatetimeFormat = 'Y-m-d H:i:s';

    private string $datetimeFormat = 'Y-m-d H:i';

    private ?string $absolute_server_URI = null;

    private string $dynamicFilesPath;

    private string $googleMapKey;

    private ?string $mapQuestKey;

    private string $dateFormat;

    private string $headerLogo;

    private string $emailHeaderLogo;

    private int $needFindLimit;

    private int $needApproveLimit;

    private int $minimumAge;

    private bool $meritBadgesEnabled;

    private string $dbUser;

    private string $dbPass;

    private string $dbCharset;

    private string $dbAdminUser;

    private string $dbAdminPass;

    private string $dbHost;

    private string $dbName;

    /** Configuration for src\Utils\Lock objects from /config/lock.* files. */
    private ?array $lockConfig = null;

    /** Watchlist configuration from /config/watchlist.* files. */
    private ?array $watchlistConfig = null;

    /** Cache log filter configuration from /config/logfilter.* files. */
    private ?array $logFilterConfig = null;

    /** News configuration from /config/news.* files. */
    private ?array $newsConfig = null;

    /** Guides configuration from /config/guides.* files. */
    private ?array $guidesConfig = null;

    /** Configuration from /config/banner.* files. */
    private ?array $topBannerVideo = null;

    /** Configuration from /config/banner.* files. */
    private ?array $topBannerTxt = null;

    /** Cronjob configuration from /config/cronjobs.* files. */
    private ?array $cronjobsConfig = null;

    /**
     * Private constructor so nobody else can instantiate it.
     */
    protected function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Get the singleton.
     */
    public static function instance(): self
    {
        static $inst = null;

        if ($inst === null) {
            $inst = new self();
        }

        return $inst;
    }

    /**
     * @noinspection PhpUndefinedVariableInspection
     */
    private function loadConfig()
    {
        require self::LEGACY_LOCAL_CONFIG;

        $this->datetimeFormat = $datetimeFormat;
        $this->absolute_server_URI = $absolute_server_URI;
        $this->dynamicFilesPath = $dynbasepath;
        $this->googleMapKey = $googlemap_key;
        $this->mapQuestKey = $config['maps']['mapQuestKey'];
        $this->dateFormat = $dateFormat;
        $this->headerLogo = $config['headerLogo'];
        $this->emailHeaderLogo = !empty($config['emailHeaderLogo']) ? $config['emailHeaderLogo'] : $config['headerLogo'];
        $this->needApproveLimit = $NEED_APPROVE_LIMIT;
        $this->needFindLimit = $NEED_FIND_LIMIT;
        $this->minimumAge = $config['limits']['minimum_age'];
        $this->meritBadgesEnabled = $config['meritBadges'];

        $this->dbHost = $dbserver;
        $this->dbName = $dbname;
        $this->dbUser = $dbusername;
        $this->dbPass = $dbpasswd;
        $this->dbCharset = $dbcharset ?? 'utf8';

        $this->dbAdminUser = $opt['db']['admin_username'] ?? $this->dbUser;
        $this->dbAdminPass = $opt['db']['admin_password'] ?? $this->dbPass;

        if (is_array($config['lock'] ?? null)) {
            $this->lockConfig = $config['lock'];
        }

        if (is_array($config['watchlist'] ?? null)) {
            $this->watchlistConfig = $config['watchlist'];
        }

        if (is_array($config['logfilter'] ?? null)) {
            $this->logFilterConfig = $config['logfilter'];
        }
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    public function getDatetimeFormat(): string
    {
        return $this->datetimeFormat;
    }

    public static function getWikiLinks(): array
    {
        return self::instance()->getLinks()['wiki'];
    }

    public static function getWikiLink(string $wikiLinkKey): string
    {
        return self::getWikiLinks()[$wikiLinkKey];
    }

    public static function getAbsolute_server_URI(): string
    {
        return self::instance()->absolute_server_URI;
    }

    public function getDbDateTimeFormat(): string
    {
        return $this->dbDatetimeFormat;
    }

    public static function getDynFilesPath($trimTrailingSlash = false): string
    {
        $path = self::instance()->getDynamicFilesPath();

        return $trimTrailingSlash ? rtrim($path, '/') : $path;
    }

    public function getDynamicFilesPath(): string
    {
        return $this->dynamicFilesPath;
    }

    public function getGoogleMapKey(): string
    {
        return $this->googleMapKey;
    }

    public function getMapQuestKey(): ?string
    {
        return $this->mapQuestKey;
    }

    public function getMinumumAge(): int
    {
        return $this->minimumAge;
    }

    public function isMeritBadgesEnabled(): bool
    {
        return $this->meritBadgesEnabled;
    }

    public function getDbUser($admin = false): string
    {
        return $admin ? $this->dbAdminUser : $this->dbUser;
    }

    public function getDbPass($admin = false): string
    {
        return $admin ? $this->dbAdminPass : $this->dbPass;
    }

    public function getDbHost(): string
    {
        return $this->dbHost;
    }

    public function getDbName(): string
    {
        return $this->dbName;
    }

    public function getDbCharset(): string
    {
        return $this->dbCharset;
    }

    public static function getHeaderLogo(): string
    {
        return self::instance()->headerLogo;
    }

    public static function getEmailHeaderLogo(): string
    {
        return self::instance()->emailHeaderLogo;
    }

    public static function getNeedFindLimit(): int
    {
        return self::instance()->needFindLimit;
    }

    public static function getNeedApproveLimit(): int
    {
        return self::instance()->needApproveLimit;
    }

    /**
     * @see /config/lock.default.php
     */
    public function getLockConfig(): array
    {
        if (! $this->lockConfig) {
            $this->lockConfig = self::getConfig('lock', 'lock');
        }

        return $this->lockConfig;
    }

    /**
     * @see /config/watchlist.default.php
     */
    public function getWatchlistConfig(): array
    {
        if (! $this->watchlistConfig) {
            $this->watchlistConfig = self::getConfig('watchlist', 'watchlist');
        }

        return $this->watchlistConfig;
    }

    /**
     * @see /config/logfilter.default.php
     */
    public function getLogfilterConfig(): array
    {
        if (! $this->logFilterConfig) {
            $this->logFilterConfig = self::getConfig('logfilter', 'logfilter');
        }

        return $this->logFilterConfig;
    }

    /**
     * @see /config/guides.default.php
     */
    public function getGuidesConfig(): array
    {
        if (! $this->guidesConfig) {
            $this->guidesConfig = self::getConfig('guides', 'guides');
        }

        return $this->guidesConfig;
    }

    /**
     * @see /config/cronjobs.default.php
     */
    public function getCronjobSchedule($job = null)
    {
        if (! $this->cronjobsConfig) {
            $this->cronjobsConfig = self::getConfig('cronjobs', 'cronjobs');
        }

        return $job === null
            ? $this->cronjobsConfig['schedule']
            : $this->cronjobsConfig['schedule'][$job] ?? null;
    }

    /**
     * Returns the value of the key in news-config
     *
     * @see /config/news.default.php for details of possible keys
     */
    public static function getNewsConfig(string $key)
    {
        $instance = self::instance();

        if (! $instance->newsConfig) {
            $instance->newsConfig = self::getConfig('news', 'news');
        }

        return $instance->newsConfig[$key] ?? null;
    }

    /**
     * @see /config/banner.default.php
     *
     * @return string[]
     */
    public function getTopBannerTxt(): array
    {
        if (! $this->topBannerTxt) {
            $this->topBannerTxt = self::getConfig('banner', 'bannerTxt');
        }

        return $this->topBannerTxt;
    }

    /**
     * @see /config/banner.default.php
     *
     * @return string[]
     */
    public function getTopBannerVideo(): array
    {
        if (! $this->topBannerVideo) {
            $this->topBannerVideo = self::getConfig('banner', 'bannerVideo');
        }

        return $this->topBannerVideo;
    }
}
