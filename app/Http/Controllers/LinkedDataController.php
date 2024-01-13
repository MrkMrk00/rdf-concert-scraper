<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Get;
use Symfony\Component\HttpFoundation\Response;

class LinkedDataController extends Controller
{
    #[Get('/linked', name: 'linked_data')]
    public function getEvents(Request $request): Response
    {
        $params = $request->validate([
            'format' => 'in:rdfxml,turtle,ntriples,nquads,trig,jsonld',
        ]);

        $format = $params['format'] ?? 'turtle';

        $this->createJDBCProperties();

        $descriptorspec = array(
            1 => ['pipe', 'w'],
        );

        $command = [
            base_path('ontop/ontop'),
            'materialize',
            '-m', resource_path('rdf/mapping.ttl'),
            '-p', resource_path('rdf/local.properties'),
            '-f', $format,
        ];

        $proc = proc_open($command, $descriptorspec, $pipes, env_vars: ['ONTOP_LOG_LEVEL' => 'ERROR']);

        $turtle = stream_get_contents($pipes[1]);

        fclose($pipes[1]);
        proc_close($proc);

        return response($turtle, headers: [
            'Content-Type' => $this->getMime($format),
        ]);
    }

    private function createJDBCProperties(): void
    {
        if (file_exists(resource_path('rdf/local.properties'))) {
            return;
        }

        $dbConfig = config('database.connections.'.config('database.default'));

        $properties = "jdbc.url=jdbc:mariadb://{$dbConfig['host']}:{$dbConfig['port']}/{$dbConfig['database']}\n";
        $properties .= "jdbc.user={$dbConfig['username']}\n";
        $properties .= "jdbc.password={$dbConfig['password']}\n";
        $properties .= "jdbc.driver=org.mariadb.jdbc.Driver\n";

        file_put_contents(resource_path('rdf/local.properties'), $properties);
    }

    private function getMime(string $format): string
    {
        return match ($format) {
            'rdfxml' => 'application/rdf+xml',
            'turtle' => 'text/turtle',
            'ntriples' => 'application/n-triples',
            'nquads' => 'application/n-quads',
            'trig' => 'application/trig',
            'jsonld' => 'application/ld+json',
            default => 'text/plain',
        };
    }
}
