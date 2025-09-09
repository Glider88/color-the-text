@extends('html')

@push('scripts')
    @vite(['resources/js/app.ts'])
@endpush

@push('styles')
    @vite(['resources/css/app.css'])
@endpush

@section('body')
    <div class="bg-gray-50 h-screen flex">
        <x-navigation />

        <div class="flex-1 flex flex-col">
            <div class="flex-1 p-6 overflow-auto">
                <x-editor />
            </div>

            <div class="bg-white border-t border-gray-200 p-3 flex justify-between items-center">
                <x-editor.submit />
                <x-model-pick />
            </div>
        </div>
    </div>
@endsection
