<?php

namespace App\RDF;

use App\Models\Event;
use App\Models\Location;
use App\RDF\Microdata\Type;

class EventParser
{
    public function normalize(Type $concertEvent): NormalizedConcertEvent
    {
        $event = NormalizedConcertEvent::empty();

        foreach ($concertEvent->properties as $key => $value) {
            if (property_exists($event, $key) && (is_string($value) || is_int($value)) && $key !== 'location') {
                $event->$key = $value;
            }
        }

        $locationProp = match (true) {
            array_key_exists('location', $concertEvent->properties) => 'location',
            array_key_exists('address', $concertEvent->properties) => 'address',
            default => null,
        };

        if ($locationProp) {
            $location = $concertEvent->properties[$locationProp];

            $event->location = match (true) {
                $location instanceof Type && (str_contains($location->type, 'Place') || str_contains($location->type, 'Venue')) =>
                    NormalizedLocation::fromPlace($location),

                $location instanceof Type && str_contains($location->type, 'PostalAddress') =>
                    NormalizedLocation::fromPostalAddress($location),

                is_string($location) => NormalizedLocation::fromAddressLiteral($location),
            };
        }

        return $event;
    }

    public function save(NormalizedConcertEvent $concertEvent, int $resourceId): Event
    {
        $locQuery = Location::query();
        foreach (get_object_vars($concertEvent->location) as $key => $value) {
            if ($value) {
                $locQuery->where($key, '=', $value);
            }
        }

        if (!$locQuery->exists()) {
            $locationId = $locQuery->insertGetId(get_object_vars($concertEvent->location));
        } else {
            $locationId = $locQuery->first()->id;
        }

        $existing = Event
            ::where('id_location', '=', $locationId)
            ->where('id_resource', '=', $resourceId)
            ->where('name', '=', $concertEvent->name)
            ->first();

        if ($existing) {
            $event = $existing;
        } else {
            $event = new Event;
        }

        foreach (get_object_vars($concertEvent) as $key => $value) {
            if (!is_object($value)) {
                $event->$key = $value;
            }
        }

        $concertEvent->startDate = str_replace('CET', 'T', $concertEvent->startDate);

        if ($concertEvent->startDate) {
            $dateTime = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $concertEvent->startDate);

            if (!$dateTime) {
                // hack, ale když to mají oni na těch stránkách špatně...
                $dateTime = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $concertEvent->startDate.':00+02:00');
            }

            if ($dateTime) {
                $event->startDate = $dateTime->format('Y-m-d H:i:s');
            } else {
                $event->startDate = null;
            }
        }

        $event->id_location = $locationId;
        $event->id_resource = $resourceId;

        $event->save();

        return $event;
    }
}
