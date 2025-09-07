@php
    $cfg = app()->get(\MercureConfig::class);
    use App\Models\Article;
@endphp

@push('scripts')
    <script>
        const config = {
            articles: {{ Js::from($articles->map(static fn(Article $a) => ['id' => $a->id, 'is_completed' => $a->is_completed])) }},
            finish_url: "{{ route('finish') }}",
            mercure: {
                url: "{{ $cfg->url }}",
                topic: "{{ $cfg->topicPrefix }}",
            },
        };
    </script>
@endpush

@if(Route::is('upload'))
    @push('scripts')
        @vite(['resources/js/editor.js'])
    @endpush
@else
    @push('scripts')
        @vite(['resources/js/color.js'])
    @endpush
@endif

@if(Route::is('upload'))
    <div class="flex-1 p-6 overflow-auto">
        <form id="editor-form" method="POST" action="{{ route('save') }}">
            @csrf
            <div class="form-group">
                <label for="title">Title: </label>
                <input id="title" name="title" type="text">
            </div>
            <div id="editor" style="height:calc(100vh - 195px);" class="bg-white rounded-lg border border-gray-300 shadow-sm"></div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </form>
    </div>
@else
    <div id="content">
        {!! $currentArticle->content !!}
    </div>
@endif

