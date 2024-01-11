<button {{ $attributes->merge(['class' => 'px-4 py-2 rounded-xl text-lg shadow hover:brightness-90 transition-all', 'type' => 'button']) }}>
    {{ $slot }}
</button>
