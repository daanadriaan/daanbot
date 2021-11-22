@extends('layouts.app')

@section('content')
    <div id="app">
        <navigation></navigation>
        <router-view :key="$route.params.id"></router-view>
    </div>
    <script src="/js/tinymce/tinymce.min.js"></script>
    <script src="{{ mix('/js/botmaker.js') }}"></script>
@endsection
