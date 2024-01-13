<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\RDF\EventParser;
use App\RDF\Microdata\Parser;
use App\RDF\Microdata\Type;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[\Spatie\RouteAttributes\Attributes\Resource(
    resource: 'resources',
    only: ['index', 'destroy', 'create', 'store'],
)]
class ResourcesController extends Controller
{
    public function index(): View
    {
        $resources = Resource::where('id_user', '=', Auth::id())->simplePaginate();

        return $this->page('resources.list', [
            'resources' => $resources,
        ]);
    }

    public function destroy(Resource $resource): Response
    {
        $resource->delete();

        return response()->redirectToRoute('resources.index', status: 303);
    }

    public function create(): View
    {
        return $this->page('resources.add');
    }

    public function store(Request $request): Response
    {
        $src = $request->request->get('src');
        $name = $request->request->get('name');

        if (!$src || !$name) {
            return \response('Nevyplněno', 400);
        }

        $r = new Resource;
        $r->src = $src;
        $r->name = $name;
        $r->id_user = Auth::id();

        $r->save();

        return \response()->redirectToRoute('resources.index');
    }

    #[Get('/resources/{resource}', name: 'resources.show-partial')]
    public function showPartialDetail(Resource $resource, EventParser $events): string
    {
        $parser = new Parser();
//        $parser->registerMiddleware(new FlattenLocationMiddleware());

        $html = \Http::get($resource->src)->body();

        $normalized = array_map(fn (Type $a) => $events->normalize($a), $parser->parse($html));
        foreach ($normalized as $norm) {
            $events->save($norm, $resource->id);
        }

        return response()->json($normalized, options: JSON_PRETTY_PRINT);
    }

    #[Post('/resources/sync', name: 'resources.sync')]
    public function syncAll(EventParser $events): Response
    {
        $parser = new Parser();

        foreach (Resource::all() as $resource) {
            $html = \Http::get($resource->src)->body();

            $normalized = array_map(fn (Type $a) => $events->normalize($a), $parser->parse($html));
            foreach ($normalized as $norm) {
                $events->save($norm, $resource->id);
            }
        }

        return new Response();
    }
}
