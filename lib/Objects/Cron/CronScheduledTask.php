<?php

namespace lib\Objects\Cron;

final class CronScheduledTask extends CronTask
{
    private $uuid;
    private $scheduledTime;
    private $startTime;
    private $endTime;
    private $result;
    private $output;
    private $failed;
    private $errorMsg;
    
    public function __construct(CronTask $cronTask = null)
    {
        if (!empty($cronTask)) {
            $this->setDescription($cronTask->getDescription());
            $this->setMaxHistory($cronTask->getMaxHistory());
            $this->setTtl($cronTask->getTtl());
        
            foreach(self::CRON_FIELDS as $name) {
                $this->values[$name] = $cronTask->getCronValues($name);
            }
        }
    }
    
    public function getUuid()
    {
        return $this->uuid;
    }
    
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }
    
    public function getScheduledTime()
    {
        return $this->scheduledTime;
    }
    
    public function setScheduledTime($scheduledTime)
    {
        $this->scheduledTime = $scheduledTime;
    }
    
    public function getStartTime()
    {
        return $this->startTime;
    }
    
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }
    
    public function getEndTime()
    {
        return $this->endTime;
    }
    
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }
    
    public function getResult()
    {
        return $this->result;
    }
    
    public function setResult($result)
    {
        $this->result = $result;
    }
    
    public function getOutput()
    {
        return $this->output;
    }
    
    public function setOutput($output)
    {
        $this->output = $output;
    }
    
    public function getFailed()
    {
        return $this->failed;
    }
    
    public function setFailed($failed)
    {
        $this->failed = $failed;
    }
    
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
    
    public function setErrorMsg($errorMsg)
    {
        $this->errorMsg = $errorMsg;
    }
}