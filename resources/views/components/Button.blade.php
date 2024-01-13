<button
    {{ $attributes->merge(['class' => 'px-4 py-2 rounded-xl text-lg shadow hover:brightness-90 transition-all', 'type' => 'button']) }}
    @if($attributes->hasAny(['hx-get', 'hx-post', 'hx-delete'])) hx-vals='{@hxCsrf}' @endif
>
    {{ $slot }}
</button>
