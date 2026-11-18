@props(['label' => null, 'title', 'description' => null, 'align' => 'left'])

<div class="{{ $align === 'center' ? 'text-center mx-auto' : '' }} max-w-2xl">
    @if ($label)
        <p class="section-label">{{ $label }}</p>
    @endif
    <h2 class="section-title mt-2">{{ $title }}</h2>
    @if ($description)
        <p class="mt-3 text-sm leading-relaxed text-stone-600">{{ $description }}</p>
    @endif
</div>
