<?php

namespace App\RDF\Microdata;

use Illuminate\Contracts\Support\Arrayable;

readonly class Type implements \JsonSerializable, Arrayable
{
    public function __construct(
        public string $type,
        public array $properties,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            '@type' => $this->type,
            ...$this->properties,
        ];
    }

    public function toArray(): array
    {
        return $this->properties;
    }
}
