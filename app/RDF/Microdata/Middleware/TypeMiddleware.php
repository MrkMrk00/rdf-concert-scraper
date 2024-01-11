<?php

namespace App\RDF\Microdata\Middleware;

use App\RDF\Microdata\Context;

interface TypeMiddleware
{
    public function check(Context $ctx): bool;

    public function run(Context $ctx): string|array;
}
