<blockquote>
    <p>{!! \App\Support\RichText::sanitize($block['text']) !!}</p>
    @if (!empty($block['source']))
        <cite>{{ $block['source'] }}</cite>
    @endif
</blockquote>
