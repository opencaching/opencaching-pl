<?php

namespace lib\Objects\Cron;

class CronTask
{
    const CRON_FIELDS = [
        CronCommons::MINUTE,
        CronCommons::HOUR,
        CronCommons::DAY,
        CronCommons::MONTH,
        CronCommons::WEEKDAY
    ];
    
    protected $cronString;
    protected $description;
    protected $maxHistory;
    protected $ttl;
    
    protected $values = [];
    
    public function __construct($cronString, $description, $ttl, $maxHistory)
    {
        $this->setCronString($cronString);
        $this->setDescription($description);
        $this->setTtl($ttl);
        $this->setMaxHistory($maxHistory);
    }
    
    public function getCronString()
    {
        return $this->cronString;
    }
    
    public function setCronString($cronString)
    {
        $this->cronString = $cronString;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function getTtl()
    {
        return $this->ttl;
    }
    
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }
    
    public function getMaxHistory()
    {
        return $this->maxHistory;
    }
    
    public function setMaxHistory($maxHistory)
    {
        $this->maxHistory = $maxHistory;
    }
    
    public function getCronValues($name) {
        return
            (in_array($name, self::CRON_FIELDS) && isset($this->values[$name]))
            ? $this->values[$name]
            : []
        ;
    }
    
    public function hasCronValue($name, $value)
    {
        $result = false;
        $values = $this->getCronValues($name);
        if (!empty($values)) {
            $index = intval($value);
            $result = isset($values[$index]) && $values[$index];
        }
        return $result;
    }
    
    public function addCronValue($name, $value) {
        if (in_array($name, self::CRON_FIELDS)) {
            if (!isset($this->values[$name])) {
                $this->values[$name] = [];
            }
            $this->values[$name][$value] = true;
        }
    }
    
}
