@extends('color.app')

@section('title', 'Color The Text')

@push('scripts')
    <script>
        const config = {
            route: {
                save: "{{ route('save') }}",
                read: "{{ route('read') }}",
            },
        };
    </script>
    <script>
        // Chat selection
        document.querySelectorAll('.article-list-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.article-list-item').forEach(i => {
                    i.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Dropdown functionality
        const dropdownButton = document.getElementById('dropdownButton');
        const dropdownMenu = document.getElementById('dropdownMenu');

        dropdownButton.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/js/editor.js'])
@endpush

@push('styles')
    <style>
        .ql-editor {
            min-height: 300px;
        }
        .article-list-item.active {
            background-color: #e5e7eb;
        }
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

@section('body')
<div class="bg-gray-50 h-screen flex">
    <!-- Sidebar -->
    <div class="w-64 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <button class="w-full py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                New Article
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-2">
            <div class="article-list-item active py-3 px-4 rounded-lg mb-2 cursor-pointer">
                Чат 1
            </div>
            <div class="article-list-item py-3 px-4 rounded-lg mb-2 cursor-pointer hover:bg-gray-100">
                Чат 2
            </div>
            <div class="article-list-item py-3 px-4 rounded-lg mb-2 cursor-pointer hover:bg-gray-100">
                Чат 3
            </div>
            <div class="article-list-item py-3 px-4 rounded-lg mb-2 cursor-pointer hover:bg-gray-100">
                Чат 4
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <form id="editor-form" method="POST" action="{{ route('save') }}">
            <div class="flex-1 p-6 overflow-auto">
                @csrf
                <div id="editor" style="height:calc(100vh - 160px);" class="bg-white rounded-lg border border-gray-300 shadow-sm"></div>

                @error('content')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </form>

        <!-- Footer -->
        <div class="bg-white border-t border-gray-200 p-3 flex justify-between items-center">
            <div class="flex justify-start">
                <button type="submit" form="editor-form" class="py-2 px-6 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                    Send
                </button>
            </div>
            <div class="relative">
                <button id="dropdownButton" class="flex items-center space-x-1 py-2 px-4 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    <span>Models</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="dropdownMenu" class="dropdown-menu">
                    <div class="py-1">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Gemma</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Qwen</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">DeepSeek</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
