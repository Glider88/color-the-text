@push('styles')
    <style>
        .article-list-item {
            background-color: #e5e7eb;
        }

        .article-list-item:hover {
            background-color: #c8ffc8;
        }

        .article-list-item.active {
            background-color: powderblue;
        }
    </style>

    <style>
        .loader {
            border: 4px solid #f3f3f3; /* Light grey border */
            border-top: 4px solid #3498db; /* Blue top border for spinning effect */
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite; /* Spin animation */
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endpush

<div class="w-64 bg-white border-r border-gray-200 flex flex-col">
    <div class="p-4 border-b border-gray-200">
        <a href="{{ route('upload') }}"
           class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
            New Article
        </a>
    </div>
    <div class="flex-1 flex-col overflow-y-auto p-2">
        @foreach($articles as $article)
            <div class="w-full h-12 content-center flex">
                @if(! $article->is_completed)
                    <span class="loader"></span>
                @endif

                @isset($currentArticle)
                    @if($currentArticle->id === $article->id)
                        <a href="{{route('read', ['id' => $article->id])}}"
                           class="article-list-item active grow py-2 px-4 rounded-lg mb-2 cursor-pointer"
                        >
                            {{ Str::of($article->title)->limit(12) }}
                        </a>
                    @else
                        <a href="{{route('read', ['id' => $article->id])}}"
                           class="article-list-item grow py-2 px-4 rounded-lg mb-2 cursor-pointer"
                        >
                            {{ Str::of($article->title)->limit(12) }}
                        </a>
                    @endif
                @endisset

                @if(Route::currentRouteName() !== 'upload')
                    <form id="delete-form" method="POST" action="{{ route('delete') }}">
                        @csrf
                        <input name="id" type="hidden" value="{{ $article->id }}">
                        <button type="submit" class="flex-none py-2 px-4 rounded-lg mb-2 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
</div>
