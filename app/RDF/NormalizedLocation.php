<?php

namespace App\RDF;

use App\RDF\Microdata\Type;

class NormalizedLocation
{
    public function __construct(
        public ?string $addressCountry,
        public ?string $addressLocality,
        public ?string $addressRegion,
        public ?string $postalCode,
        public ?string $streetAddress,
        public ?string $name,
        public ?string $url,
    ) {
    }

    public static function empty(): self
    {
        return new self(
            addressCountry: null,
            addressLocality: null,
            addressRegion: null,
            postalCode: null,
            streetAddress: null,
            name: null,
            url: null,
        );
    }

    public static function fromPlace(Type $place): self
    {
        if (!str_contains($place->type, 'Place') && !str_contains($place->type, 'Venue')) {
            throw new \InvalidArgumentException('Type must be Place');
        }

        $name = $place->properties['name'] ?? null;
        $url = $place->properties['url'] ?? null;

        $address = $place->properties['address'] ?? null;

        if (is_string($address)) {
            $norm = self::fromAddressLiteral($address);
        } else if ($address instanceof Type) {
            $norm = self::fromPostalAddress($address);
        } else {
            $norm = self::empty();
        }

        $norm->name = $name;
        $norm->url = $url;

        return $norm;
    }

    public static function fromPostalAddress(Type $postalAddress): self
    {
        if (!str_contains($postalAddress->type, 'PostalAddress')) {
            throw new \InvalidArgumentException('Type must be PostalAddress');
        }

        $norm = self::empty();
        foreach ($postalAddress->properties as $key => $value) {
            if (property_exists($norm, $key)) {
                $norm->$key = $value;
            }
        }

        return $norm;
    }

    public static function fromAddressLiteral(string $address): self
    {
        $norm = self::empty();
        $norm->addressLocality = $address;

        return $norm;
    }
}
