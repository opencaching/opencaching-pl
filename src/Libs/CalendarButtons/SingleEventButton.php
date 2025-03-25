<?php

namespace src\Libs\CalendarButtons;

class SingleEventButton extends BaseCalendarButton
{
    private string $startDate;

    private string $timeZone;

    private string $location;

    public function __construct(
        string $name,
        string $description,
        string $startDate,
        string $timeZone,
        string $location,
        string $language
    ) {
        parent::__construct($name, $description);
        $this->startDate = $startDate;
        $this->timeZone = $timeZone;
        $this->location = $location;
        $this->language = $language;
    }

    public function render(): string
    {
        return "<add-to-calendar-button 
            name=\"{$this->name}\" 
            description=\"{$this->description}\"
            startDate=\"{$this->startDate}\"
            timeZone=\"{$this->timeZone}\"
            location=\"{$this->location}\"
            options=\"{$this->options}\"
            styleLight=\"{$this->styleLight}\"
            styleDark=\"{$this->styleLight}\"
            listStyle=\"{$this->listStyle}\"
            trigger=\"{$this->trigger}\"
            label=\"{$this->label}\"
            language=\"{$this->language}\"
            lightmode=\"bodyScheme\"
            size=\"3\">
        </add-to-calendar-button>";
    }
}
