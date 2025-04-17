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
        string $language,
        string $label
    ) {
        parent::__construct($name, $description);
        $this->startDate = $startDate;
        $this->timeZone = $timeZone;
        $this->location = $location;
        $this->language = $language;
        $this->label = $label;
    }

    public function render(): string
    {
        return "<div id=\"add-to-calendar-button\">
            <img src=\"images/free_icons/date_go.png\" class=\"icon16\" alt=\"\">
            <strong style=\"text-decoration: underline; cursor: pointer\">{$this->label}</strong>
        </div>
        <script type=\"application/javascript\">
            const config = {
                name: \"{$this->name}\", 
                description: \"{$this->description}\",
                startDate: \"{$this->startDate}\",
                timeZone: \"{$this->timeZone}\",
                location: \"{$this->location}\",
                options: [{$this->options}],
                trigger: \"{$this->trigger}\",
                label: \"{$this->label}\",
                language: \"{$this->language}\"
            };
            const button = document.getElementById('add-to-calendar-button');
            if (button) {
                button.addEventListener('click', () => atcb_action(config, button));
            }
        </script>";
    }
}
