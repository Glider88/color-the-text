@extends('color.app')

@section('title', 'Article')

@php
    $cfg = app()->get(\MercureConfig::class);
@endphp

@push('scripts')
    <script>
        const config = {
            mercure: {
                url: "{{ $cfg->url }}",
                topic: "{{ $cfg->topic }}",
            },
        };
    </script>
    @vite(['resources/js/color.js'])
@endpush

@section('body')
    <div id="content">
        {!! $content !!}
    </div>
@endsection
