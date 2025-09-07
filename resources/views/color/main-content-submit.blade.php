@if(Route::is('upload'))
    <div class="flex justify-start">
        <button type="submit" form="editor-form" class="py-2 px-6 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
            Send
        </button>
    </div>
@else
    <div class="flex justify-start"></div>
@endif
