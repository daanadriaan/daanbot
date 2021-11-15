@extends('layouts.app')

@section('content')
    <div id="app">
        <navigation></navigation>
        <router-view :key="$route.params.id"></router-view>
    </div>
    <script src="{{ mix('/js/botmaker.js') }}"></script>
@endsection
