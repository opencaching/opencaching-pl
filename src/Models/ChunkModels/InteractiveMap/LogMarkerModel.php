<?php
namespace src\Models\ChunkModels\InteractiveMap;

use src\Models\GeoCache\GeoCacheLog;
use src\Models\User\User;
use src\Utils\Text\Formatter;

/**
 * This is map marker which has log information in infowindow
 * Cache properties are inherited from CacheMarkerModel
 */
class LogMarkerModel extends CacheMarkerModel
{
    public $logLink = null; // if there is no link there is no log :)
    public $logText;
    public $logIcon;
    public $logType;
    public $logTypeName;
    public $logUserName;
    public $logDate;

    public static function fromGeoCacheLogFactory(
        GeoCacheLog $log, User $user = null
    ) {
        $marker = new self();
        $marker->importDataFromGeoCacheLog($log, $user);
        return $marker;
    }

    protected function importDataFromGeoCacheLog(
        GeoCacheLog $log, User $user = null
    ) {
        parent::importDataFromGeoCache($log->getGeoCache(), $user);

        $this->id = $log->getId();
        $this->logLink = $log->getLogUrl();
        $text = strip_tags($log->getText(),'<br><p>');
        $textLen = mb_strlen($text);
        if ($textLen > 200) {
            $text = mb_strcut($text, 0, 200);
            // do not leave open tags on truncate
            $text = preg_replace('/\<[^\>]*$/', '', $text);
            $text .= '...';
        }
        $this->logText = $text;
        $this->logIcon = $log->getLogIcon();
        $this->logType = $log->getType();
        $this->logTypeName = tr(
            GeoCacheLog::typeTranslationKey($log->getType())
        );
        $this->logUserName = $log->getUser()->getUserName();
        $this->logDate = Formatter::date($log->getDateCreated());
    }

    /**
     * Check if all necessary data is set in this marker class
     * @return boolean
     */
    public function checkMarkerData()
    {
        return parent::checkMarkerData()
        && isset($this->logLink)
        && isset($this->logText)
        && isset($this->logIcon)
        && isset($this->logType)
        && isset($this->logTypeName)
        && isset($this->logUserName)
        && isset($this->logDate)
        ;
    }
}
