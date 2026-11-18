<figure>
    <div class="video-embed">
        <iframe src="{{ $block['embed'] }}" title="{{ $block['caption'] ?? 'Video' }}" loading="lazy" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
    </div>
    @if (!empty($block['caption']))
        <figcaption>{{ $block['caption'] }}</figcaption>
    @endif
</figure>
