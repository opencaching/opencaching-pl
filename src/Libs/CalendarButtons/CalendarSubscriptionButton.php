<?php

namespace src\Libs\CalendarButtons;

class CalendarSubscriptionButton extends BaseCalendarButton
{
    private string $icsFile;

    private bool $subscribe;

    public function __construct(string $name, string $icsFile, bool $subscribe = true)
    {
        parent::__construct($name);
        $this->icsFile = $icsFile;
        $this->subscribe = $subscribe;
    }

    public function render(): string
    {
        return "<add-to-calendar-button 
            id=\"calendar-subscription-button\"
            name=\"{$this->name}\" 
            description=\"{$this->description}\"
            icsFile=\"{$this->icsFile}\"
            " . ($this->subscribe ? 'subscribe' : '') . "
            options=\"{$this->options}\"
            styleLight=\"{$this->styleLight}\"
            styleDark=\"{$this->styleLight}\"
            listStyle=\"{$this->listStyle}\"
            trigger=\"{$this->trigger}\"
            label=\".\"
            language=\"{$this->language}\"
            lightmode=\"bodyScheme\"
            size=\"3\"
            hideTextLabelButton >
        </add-to-calendar-button>
        <style>
            add-to-calendar-button#calendar-subscription-button{
                display: inline-block;
            }
            add-to-calendar-button#calendar-subscription-button::part(atcb-button) {
                height: 22px;
                width: 23px;
                margin: 0 !important;
                padding: 0px !important;
            }
        </style>
        ";

    }
}
