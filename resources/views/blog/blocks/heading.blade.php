@if (($block['level'] ?? 2) === 3)
    <h3>{!! \App\Support\RichText::sanitize($block['text']) !!}</h3>
@else
    <h2>{!! \App\Support\RichText::sanitize($block['text']) !!}</h2>
@endif
