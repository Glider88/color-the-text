@push('scripts')
    @vite(['resources/js/editor.ts'])
@endpush

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

