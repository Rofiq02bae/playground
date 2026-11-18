@if (($block['level'] ?? 2) === 3)
    <h3>{{ $block['text'] }}</h3>
@else
    <h2>{{ $block['text'] }}</h2>
@endif
