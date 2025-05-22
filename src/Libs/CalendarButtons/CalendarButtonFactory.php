<?php

namespace src\Libs\CalendarButtons;

use InvalidArgumentException;

class CalendarButtonFactory
{
    public static function createButton(string $type, array $params): CalendarButtonInterface
    {
        switch ($type) {
            case 'single_event':
                return new SingleEventButton(
                    $params['name'],
                    $params['description'],
                    $params['startDate'],
                    $params['timeZone'],
                    $params['location'],
                    $params['language'] ?? 'en',
                    $params['label']
                );
            case 'subscription':
                return new CalendarSubscriptionButton(
                    $params['name'],
                    $params['icsFile'],
                    $params['subscribe'] ?? true,
                    $params['language'] ?? 'en'
                );
            default:
                throw new InvalidArgumentException("Unknown button type: {$type}");
        }
    }
}
