@extends('html')

@push('scripts')
    @vite(['resources/js/app.ts'])
@endpush

@push('styles')
    @vite(['resources/css/app.css'])
@endpush

@section('body')
    <div class="bg-gray-50 h-screen flex">
        @include('color.article-nav')

        <div class="flex-1 flex flex-col">
            @include('color.main-content')

            <div class="bg-white border-t border-gray-200 p-3 flex justify-between items-center">
                @include('color.main-content-submit')
                @include('color.model-pick')
            </div>
        </div>
    </div>
@endsection
