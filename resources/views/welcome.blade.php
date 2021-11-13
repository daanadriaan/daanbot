@extends('layouts.app')

@section('content')
    <div id="daanbot">
        <daanbot></daanbot>
    </div>
    <script src="{{ mix('/js/daanbot.js') }}"></script>
@endsection
