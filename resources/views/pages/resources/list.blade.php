@extends('base')

@section('title') Zdroje @endsection

@php
    /** @var \Illuminate\Contracts\Pagination\Paginator $resources */
@endphp

@section('body')
    <div hx-boost="true" class="flex flex-col items-center p-8 gap-4 bg-white mx-auto max-w-xl rounded-2xl shadow-2xl">
        <h2 class="font-bold text-2xl border-b">Zdroje</h2>

        <div class="flex flex-row justify-end max-w-xl w-full">
            <x-Button
                onclick="document.getElementById('add-dialog').showModal();"
                class="bg-primary text-white"
            >
                <strong>+</strong>&nbsp;Přidat
            </x-Button>
        </div>

        <div class="rounded-2xl w-full shadow overflow-x-hidden overflow-y-scroll max-w-xl max-h-[50%]">
            <table class="w-full h-full">
                <thead>
                <tr class="bg-slate-200 py-4">
                    <th class="border-r px-2 py-4">ID</th>
                    <th class="border-r">Název</th>
                    <th>URL</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($resources->items() as $resource)
                    <tr class="bg-white hover:brightness-90 border-t">
                        <td class="text-center py-2 font-bold">{{ $resource->id }}</td>
                        <td class="text-center py-2">{{ $resource->name }}</td>
                        <td class="text-center py-2"><a class="underline text-blue-700" target="_blank" href="{{ $resource->src }}">{{ $resource->src }}</a></td>
                        <td class="flex flex-row gap-3 p-2">
                            <button
                                hx-confirm="Opravdu chceš smazat zdroj {{ $resource->name }}?"
                                hx-delete="{{ route('resources.destroy', ['resource' => $resource->id]) }}"
                                hx-vals='{@hxCsrf}'
                                hx-target="body"
                                type="button"
                                class="w-full h-full py-2"
                                title="Smazat"
                            >
                                <x-icon.TrashCan height="2em" width="1.2em" fill="red" />
                            </button>
                            <button
                                hx-get="{{ route('resources.show-partial', ['resource' => $resource]) }}"
                                hx-swap="innerHTML"
                                hx-target="#resource-detail"
                                type="button"
                                class="w-full h-full py-2"
                                title="Zobrazit data ze stránky"
                            >
                                <x-icon.ArrowRight height="2em" width="1.2em" />
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if($resources->isEmpty())
                <div class="flex flex-row w-full justify-center font-bold text-xl py-2">Nic :(</div>
            @endif
        </div>

        <div class="rounded-2xl shadow w-full max-w-xl p-4 flex flex-row justify-between">
            <x-Button
                class="bg-blue-600 text-white"
                hx-post="{{ route('resources.sync') }}"
                hx-swap="none"
            >Synchronizovat vše</x-Button>

            <select
                aria-label="Stáhnout RDF dump"
                class="rounded-xl bg-blue-600 text-white px-4 py-2 shadow-md hover:brightness-95 transition-all text-lg"
                id="dump-download">
                <option value="" selected>Stáhnout RDF dump</option>
                <optgroup label="Vyberte formát:">
                    <option value="rdfxml">RDF/XML</option>
                    <option value="turtle">Turtle</option>
                    <option value="ntriples">N-Triples</option>
                    <option value="nquads">N-Quads</option>
                    <option value="trig">TriG</option>
                    <option value="jsonld">JSON-LD</option>
                </optgroup>
            </select>
            <script type="text/javascript">
                document.getElementById('dump-download').addEventListener('change', function (e) {
                    const format = e.target.value;
                    if (format) {
                        const href = '{{ route('linked_dump') }}?format=' + format;

                        window.open(href, '_blank');
                    }

                    e.target.value = '';
                });
            </script>
        </div>

        <div class="rounded-2xl shadow w-full max-w-xl p-4">
            <div class="flex flex-row justify-between items-center">
                <h3 class="font-bold text-xl">Data z vybraného zdroje:</h3>
                <x-icon.TrashCan height="2em" width="1.2em" fill="red" id="delete-resource-detail" class="cursor-pointer" />
            </div>
            <pre id="resource-detail" class="break-all text-sm overflow-scroll max-h-[50vh]"></pre>
        </div>

        <script type="text/javascript">
            document.getElementById('delete-resource-detail').addEventListener('click', function () {
                document.getElementById('resource-detail').innerHTML = '';
            });
        </script>
    </div>

    <dialog
        hx-boost="true"
        id="add-dialog"
        class="gap-3 max-w-md w-full rounded-2xl shadow overflow-hidden"
    >
        <div class="flex flex-col w-full p-4">
            <div class="w-full flex flex-row justify-between items-center">
                <h3 class="font-bold text-xl">Přidej nový zdroj</h3>
                <x-Button class="bg-red-500 text-white" onclick="document.getElementById('add-dialog').close();">X</x-Button>
            </div>
            <hr class="border-t my-2"/>
            <form
                action="{{ route('resources.store') }}"
                hx-vals='{@hxCsrf}'
                method="post"
                class="flex flex-col gap-2"
            >
                <label for="add-resource__name" class="font-bold">Název<x-RequiredStar /></label>
                <x-Input id="add-resource__name" type="text" name="name" placeholder="Jazz Dock" required />

                <label for="add-resource__src" class="font-bold">Adresa<x-RequiredStar /></label>
                <x-Input id="add-resource__src" type="url" name="src" placeholder="https://www.jazzdock.cz/cs/program" required />

                <div class="flex flex-row justify-end w-full">
                    <x-Button type="submit" class="bg-primary text-white">Přidat</x-Button>
                </div>
            </form>
        </div>
    </dialog>
@endsection
