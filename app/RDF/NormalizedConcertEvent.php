<?php

namespace App\RDF;

class NormalizedConcertEvent
{
    public function __construct(
        public NormalizedLocation $location,
        public string $name,
        public string $url,
        public ?string $image,
        public ?string $description,
        public ?string $performer,
        public ?string $startDate,
    ) {
    }

    public static function empty(): self
    {
        return new self(
            location: NormalizedLocation::empty(),
            name: '',
            url: '',
            image: null,
            description: null,
            performer: null,
            startDate: null,
        );
    }
}
