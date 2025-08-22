@extends('color.app')

@section('title', 'Загрузка текста')

@section('script')
    <script>
        (function() {
            console.log('init');
        })();
    </script>
@endsection

@section('style')
    <style></style>
@endsection

@section('body')
    <form method="POST" action="{{ route('read') }}">
        @csrf
        <textarea id="content" name="content" rows="4" cols="50"></textarea>
        <button type="submit">Send</button>
    </form>
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
@endsection
