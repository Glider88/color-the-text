@push('scripts')
    @vite(['resources/js/article.ts'])
@endpush

<div id="content">
    {!! $content !!}
</div>
