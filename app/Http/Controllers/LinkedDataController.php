<?php

namespace App\Http\Controllers;

use Spatie\RouteAttributes\Attributes\Get;

class LinkedDataController extends Controller
{
    #[Get('/linked', name: 'linked_data')]
    public function getEvents()
    {
        $descriptorspec = array(
            1 => ['pipe', 'w'],
        );

        $proc = proc_open(
            [base_path('ontop/ontop'), 'materialize', '-m', base_path('ontop/mapping.ttl'), '-p', base_path('ontop/local.properties'), '-f', 'ntriples'],
            $descriptorspec,
            $pipes,
            env_vars: ['ONTOP_LOG_LEVEL' => 'ERROR'],
        );

        $turtle = stream_get_contents($pipes[1]);

        fclose($pipes[1]);
        proc_close($proc);

        return response($turtle, headers: [
            'Content-Type' => 'application/n-triples',
        ]);
    }
}
