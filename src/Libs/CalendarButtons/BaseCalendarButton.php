<?php

namespace src\Libs\CalendarButtons;

abstract class BaseCalendarButton implements CalendarButtonInterface
{
    protected string $name;

    protected string $description;

    protected string $options;

    protected string $styleLight;

    protected string $listStyle;

    protected string $trigger;

    protected string $label;

    protected string $lightMode;

    protected string $language;

    public function __construct(
        string $name,
        string $description = '',
        string $options = "'Google','Apple','iCal','Outlook.com','Yahoo'",
        string $styleLight = '--base-font-size-l: 13px !important; --base-font-size-m: 13px !important; --base-font-size-s: 13px !important; --font: OpenSans, sans-serif; --btn-background: #fff; --btn-hover-background: #f5f5f5; --btn-shadow: none; --btn-hover-shadow: none; --btn-active-shadow: none; --btn-padding-x: 11px; --btn-padding-y: 3px; --btn-font-weight: 400;',
        string $listStyle = 'overlay',
        string $trigger = 'click',
        string $label = '',
        string $language = 'en'
    ) {
        $this->name = $name;
        $this->description = str_replace("\n", '[br]', $description);;
        $this->options = $options;
        $this->styleLight = $styleLight;
        $this->listStyle = $listStyle;
        $this->trigger = $trigger;
        $this->label = $label;
        $this->language = $language;
    }

    abstract public function render(): string;
}
