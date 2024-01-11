<?php

namespace App\RDF\Microdata\Middleware;

use App\Exceptions\MicrodataException;
use App\RDF\Microdata\Context;

class FlattenLocationMiddleware implements TypeMiddleware
{
    public function check(Context $ctx): bool
    {
        $isLocationProp = $ctx->element->getAttribute('itemprop') === 'location';
        $isLocationType = preg_match('/schema\.org\/(Place|PostalAddress|\w*Venue)/', $ctx->element->getAttribute('itemtype'));

        return ($isLocationProp && $ctx->element->hasAttribute('content')) || $isLocationType;
    }

    /**
     * @throws MicrodataException
     */
    public function run(Context $ctx): string
    {
        if ($ctx->element->hasAttribute('content')) {
            return $ctx->element->getAttribute('content');
        }

        if (!($type = $ctx->element->getAttribute('itemtype'))) {
            throw new MicrodataException('Invalid location element');
        }

        $location = '';

        if ($nameProp = $ctx->query('descendant::*[@itemprop="name"]')) {
            if (!empty($nameProp)) {
                if ($nameProp[0]->hasAttribute('content')) {
                    $location .= $nameProp[0]->getAttribute('content') . ' ';
                } else if ($text = trim($nameProp[0]->textContent)) {
                    $location .= $text . ' ';
                }
            }
        }

        if (preg_match('/schema\.org\/(\w*Venue|Place)/', $type)) {
            if ($addressProp = $ctx->query('descendant::*[@itemprop="address"]')) {
                $location .= self::handleAddress($ctx->withElement($addressProp[0]));
            }
        }

        if (str_contains($type, 'schema.org/PostalAddress')) {
            $location .= self::handleAddress($ctx);
        }

        return $location;
    }

    private static function handleAddress(Context $ctx): string
    {
        if ($ctx->element->hasAttribute('content')) {
            return $ctx->element->getAttribute('content');
        }

        $address = [
            self::getOrEmptyString($ctx, 'streetAddress').',',
            self::getOrEmptyString($ctx, 'addressLocality'),
            self::getOrEmptyString($ctx, 'addressRegion'),
            self::getOrEmptyString($ctx, 'postalCode'),
            self::getOrEmptyString($ctx, 'addressCountry'),
        ];

        return trim(preg_replace('/ {2,}/', ' ', implode(' ', $address)));
    }

    public static function getOrEmptyString(Context $ctx, string $prop): string
    {
        if ($prop = $ctx->query('descendant::*[@itemprop="' . $prop . '"]')) {
            if (!empty($prop)) {
                if ($prop[0]->hasAttribute('content')) {
                    return $prop[0]->getAttribute('content');
                } else if ($text = trim($prop[0]->textContent)) {
                    return $text;
                }
            }
        }

        return '';
    }
}
