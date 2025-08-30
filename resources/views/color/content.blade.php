@php
    $paragraphs = Str::of($text)->split('/\R/u');
@endphp

<div id="content">
    @foreach ($paragraphs as $paragraph)
        <p>{{ $paragraph }}</p>
    @endforeach
</div>
