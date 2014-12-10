<?php

namespace lib\Medals;

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
        include __dir__ . '/../settings.inc.php';
        $this->medalsModuleSwitchedOn = $config['medalsModuleSwitchedOn'];
        $this->datetimeFormat = $datetimeFormat;
        $this->ocNodeId = $oc_nodeid;
//       dd($config, $oc_nodeid);
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

}
