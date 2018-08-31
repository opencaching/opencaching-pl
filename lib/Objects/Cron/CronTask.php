<?php
/**
 * Contains \lib\Objects\Cron\CronTask class definition
 */
namespace lib\Objects\Cron;

/**
 * Represents basic cron task as defined in configuration, before scheduleed and
 * executed
 */
class CronTask
{
    /** Elements of cron string in order of appearance */
    const CRON_FIELDS = [
        CronCommons::MINUTE,
        CronCommons::HOUR,
        CronCommons::DAY,
        CronCommons::MONTH,
        CronCommons::WEEKDAY
    ];

    /**
     * @var string Cron string containing definition when to run the task,
     *      compliant with linux crontable definitions
     */
    protected $cronString;
    /** @var string Name of task to display, should be as short as possible */
    protected $displayName;
    /** @var string Description of task */
    protected $description;
    /** @var integer Maximum historical entries stored in database */
    protected $maxHistory;
    /**
     * @var integer Time in seconds the task is treated as finished/timeouted
     *      after
     */
    protected $ttl;
    /**
     * @var boolean The flag marking concurrent instances are allowed for the
     *      tasks belonging to the same entrypoint
     */
    protected $allowConcurrent;
    /**
     * @var array Contains arrays identified by a cron field where each array
     *      contains true for indexes the execution time is valid for. For
     *      example, if the cron string contain '2-4' in field on day of month
     *      position, there will be $values[CronCommons::DAY][2] = true,
     *      $values[CronCommons::DAY][3] = true and
     *      $values[CronCommons::DAY][4] = true
     */
    protected $values = [];

    /**
     * The constructor is meant to be called mainly from CronConfigurator
     *
     * @param string $cronString the cron string to set
     * @param string $displayName the name to display to set
     * @param string $description the description to set
     * @param int $ttl the time to live to set
     * @param int $maxHistory the maximum history stored to set
     * @param boolean $allowConcurrent true if concurrent tasks of the same kind
     *      are allowed
     */
    public function __construct(
        $cronString,
        $displayName,
        $description,
        $ttl,
        $maxHistory,
        $allowConcurrent
    ) {
        $this->setCronString($cronString);
        $this->setDisplayName($displayName);
        $this->setDescription($description);
        $this->setTtl($ttl);
        $this->setMaxHistory($maxHistory);
        $this->setAllowConcurrent($allowConcurrent);
    }

    /**
     * Gives the cron string
     *
     * @return string the cron string
     */
    public function getCronString()
    {
        return $this->cronString;
    }

    /**
     * Sets the cron string
     *
     * @param string $cronString the cron string to set
     */
    public function setCronString($cronString)
    {
        $this->cronString = $cronString;
    }

    /**
     * Gives the display name
     *
     * @return string the display name
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Sets the display name
     *
     * @param string $displayName the display name to set
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Gives the description
     *
     * @return string the description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description the description to set
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Gives the time to live
     *
     * @return string the time to live
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Sets the time to live
     *
     * @param integer $ttl the time to live to set
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * Gives the maximum history stored
     *
     * @return string the maximum history stored
     */
    public function getMaxHistory()
    {
        return $this->maxHistory;
    }

    /**
     * Sets the maximum history stored
     *
     * @param integer $maxHistory the maximum history stored to set
     */
    public function setMaxHistory($maxHistory)
    {
        $this->maxHistory = $maxHistory;
    }

    /**
     * Gives the allow concurrent flag
     *
     * @return boolean true if concurrent tasks of the same kind are allowed
     */
    public function getAllowConcurrent()
    {
        return $this->allowConcurrent;
    }

    /**
     * Sets the allow concurrent flag
     *
     * @param boolean $allowConcurrent the allow concurrent flag to set
     */
    public function setAllowConcurrent($allowConcurrent)
    {
        $this->allowConcurrent = $allowConcurrent;
    }

    /**
     * Gives the cron time values for given field
     *
     * @param string $name field name
     *
     * @return array the cron time values
     */
    public function getCronValues($name) {
        return
            (in_array($name, self::CRON_FIELDS) && isset($this->values[$name]))
            ? $this->values[$name]
            : []
        ;
    }

    /**
     * Checks if given time value is valid for task execution for given field
     * name
     *
     * @param string $name field name
     * @param integer $value time value
     *
     * @return true if the value for the filed name is valid to execute task,
     *         false otherwise
     */
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

    /**
     * Adds give value as valid for task to execute for given field
     *
     * @param string $name field name
     * @param integer $value time value
     */
    public function addCronValue($name, $value) {
        if (in_array($name, self::CRON_FIELDS)) {
            if (!isset($this->values[$name])) {
                $this->values[$name] = [];
            }
            $this->values[$name][$value] = true;
        }
    }

}
