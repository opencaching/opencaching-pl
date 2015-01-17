<?php

namespace lib\Objects\OcConfig;

/**
 * Description of ocConfig
 *
 * @author Andrzej Łza Wozniak
 */
final class OcConfig
{

    private $medalsModuleSwitchedOn = false;
    private $dbDatetimeFormat = 'Y-m-d H:i:s';
    private $datetimeFormat = 'Y-m-d H:i';
    private $ocNodeId = null;
    private $absolute_server_URI = null;
    private $octeamEmailsSignature = null;
    private $octeamEmailAddress;
    private $siteName;

    /**
     * Call this method to get singleton
     * @return ocConfig
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new ocConfig();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instance it
     */
    private function __construct()
    {
        $this->loadConfig();
    }

    private function loadConfig()
    {
        include __DIR__ . '/../../settings.inc.php';
        $this->medalsModuleSwitchedOn = $config['medalsModuleSwitchedOn'];
        $this->datetimeFormat = $datetimeFormat;
        $this->ocNodeId = $oc_nodeid;
        $this->absolute_server_URI = $absolute_server_URI;
        $this->octeamEmailsSignature = $octeamEmailsSignature;
        $this->octeamEmailAddress = $octeam_email;
        $this->siteName = $site_name;

    }

    public function getAbsolute_server_URI()
    {
        return $this->absolute_server_URI;
    }
    
    public function getOcteamEmailsSignature()
    {
        return $this->octeamEmailsSignature;
    }

    public function getOcNodeId()
    {
        return $this->ocNodeId;
    }

    public function getMedalsModuleSwitchOn()
    {
        return $this->medalsModuleSwitchedOn;
    }

    public function getDbDateTimeFormat()
    {
        return $this->dbDatetimeFormat;
    }
    

    public function getSiteName()
    {
        return $this->siteName;
    }

    public function getOcteamEmailAddress()
    {
        return $this->octeamEmailAddress;
    }

}
