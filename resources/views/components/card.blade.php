@props([
    'class' => '',
])

<div data-slot="card"
    {{ $attributes->merge([
        'class' => "text-card-foreground flex flex-col gap-6 rounded-xl border border-gray-200 overflow-hidden $class",
    ]) }}>
    {{ $slot }}
</div>
