@push('styles')
    @vite(['resources/css/navigation.scss'])
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
                @if (! $article->is_completed)
                    <span id="loader-article-{{ $article->id }}" class="loader"></span>
                @endif

                <a href="{{ route('read', ['id' => $article->id]) }}"
                   class="article-list-item {{ $isSelected($article->id) ? 'active': '' }} grow py-2 px-4 rounded-lg mb-2 cursor-pointer"
                >
                    {{ $fixedTitle($article->title) }}
                </a>

                @if ($attributes->has('with-delete'))
                    <x-navigation.delete article-id="{{ $article->id }}"/>
                @endif
            </div>
        @endforeach
    </div>
</div>
