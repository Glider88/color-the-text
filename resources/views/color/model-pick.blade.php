@push('scripts')
    <script>
        @if(Route::is('upload'))
            const dropdownButton = document.getElementById('dropdownButton')
            const dropdownMenu = document.getElementById('dropdownMenu')
            const currentMenu = document.getElementById('currentMenu')

            const menuItems = dropdownMenu.getElementsByTagName('button')
            for (let menuItem of menuItems) {
                menuItem.addEventListener('click', function(e) {
                    e.stopPropagation()
                    dropdownMenu.classList.toggle('show')
                    currentMenu.textContent = menuItem.textContent
                    currentMenu.value = menuItem.textContent
                })
            }

            dropdownButton.addEventListener('click', function(e) {
                e.stopPropagation()
                dropdownMenu.classList.toggle('show')
            });

            document.addEventListener('click', function(e) {
                if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show')
                }
            });
        @endif
    </script>
@endpush

@push('styles')
    <style>
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            bottom: 100%;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            /*z-index: 10;*/
            min-width: 200px;
        }
        .dropdown-menu.show {
            display: block;
        }
    </style>
@endpush

@if(Route::is('upload'))
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
@else
    <div class="relative">
        <button id="dropdownButton"
                class="flex items-center space-x-1 py-2 px-4 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
            <span id="currentMenu">{{ $currentArticle->model }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>
@endif
