@php
    $src = $block['src'];
    $url = \Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/']) ? $src : asset('storage/'.$src);
    $full = ($block['size'] ?? 'normal') === 'full';
@endphp
<figure @class(['block-image-full' => $full])>
    <img src="{{ $url }}" alt="{{ $block['caption'] ?? '' }}" loading="lazy" decoding="async">
    @if (!empty($block['caption']))
        <figcaption>{{ $block['caption'] }}</figcaption>
    @endif
</figure>
