@if (!empty($block['ordered']))
    <ol>
        @foreach ($block['items'] as $item)<li>{{ $item }}</li>@endforeach
    </ol>
@else
    <ul>
        @foreach ($block['items'] as $item)<li>{{ $item }}</li>@endforeach
    </ul>
@endif
