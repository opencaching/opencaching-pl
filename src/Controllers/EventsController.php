<?php

namespace src\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use DateTime;
use DateTimeZone;
use okapi\core\Okapi;
use okapi\Facade;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use src\Controllers\Core\CoreController;
use src\Utils\Cache\OcMemCache;
use src\Utils\Uri\SimpleRouter;

class EventsController extends CoreController
{
    private const CACHE_KEY = __CLASS__ . ':incomingEvents';

    private const CACHE_TTL = 3600;

    public function isCallableFromRouter(string $actionName): bool
    {
        return true;
    }

    public function incomingEvents(): void
    {
        $calendarData = OcMemCache::getOrCreate(self::CACHE_KEY, self::CACHE_TTL, function () {
            $events = $this->fetchEvents();

            return $this->generateCalendar($events)->get();
        });

        $this->outputCalendar($calendarData);
    }

    private function fetchEvents()
    {
        $params = [
            'search_method' => 'services/caches/search/all',
            'search_params' => json_encode([
                'type' => 'Event',
                'status' => 'Available|Temporarily unavailable|Archived',
                'order_by' => '-date_hidden',
                'limit' => 30,
            ]),
            'retr_method' => 'services/caches/geocaches',
            'retr_params' => json_encode([
                'fields' => 'code|name|location|url|short_description|description|date_hidden',
            ]),
            'wrap' => true,
            'langpref' => 'pl',
        ];

        $data = Facade::service_call('services/caches/shortcuts/search_and_retrieve', null, $params);

        return $data;
    }

    private function generateCalendar(array $events): Calendar
    {
        $calendarName = tr('events') . ' ' . Okapi::get_oc_installation_code();
        $calendar = Calendar::create($calendarName)
            ->name($calendarName)
            ->description($calendarName)
            ->refreshInterval(60 * 12);

        foreach ($events['results'] as $event) {
            $calendar->event($this->createEvent($event));
        }

        return $calendar;
    }

    private function createEvent(array $event): Event
    {
        $eventDate = new DateTime(strip_tags($event['date_hidden']));
        $startDate = DateTime::createFromFormat('Y-m-d', $eventDate->format('Y-m-d'), new DateTimeZone('UTC'));

        [$lat, $lon] = explode('|', strip_tags($event['location']));

        $eventName = strip_tags('[' . Okapi::get_oc_installation_code() . '] ' . $event['code'] . ' ' . $event['name']);
        $eventUrl = $event['url'];
        $description = "<a href=\"{$eventUrl}\">{$eventName}</a>\n\n" . strip_tags($event['description']);

        return Event::create()
            ->uniqueIdentifier($event['code'])
            ->name($eventName)
            ->startsAt($startDate)
            ->fullDay()
            ->description($description)
            ->url($eventUrl)
            ->address(trim($lat) . ',' . trim($lon))
            ->coordinates(trim($lat), trim($lon));
    }

    private function outputCalendar(string $calendarData): void
    {
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="ocpl-events.ics"');
        echo $calendarData;
    }

    public static function getICSFileURL(): string
    {
        return SimpleRouter::getAbsLink(EventsController::class, 'incomingEvents');
    }

    public function index()
    {
    }
}
