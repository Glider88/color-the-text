@push('scripts')
    @vite(['resources/js/models.ts'])
@endpush

@push('styles')
    @vite(['resources/css/models.scss'])
@endpush

<div class="relative">
    <button id="dropdownButton" class="flex items-center space-x-1 py-2 px-4 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
        <span id="currentMenu">Models</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <div id="dropdownMenu" class="dropdown-menu">
        <div class="py-1">
            @foreach($models as $model)
                <button class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ $model }}</button>
            @endforeach
        </div>
    </div>
</div>
