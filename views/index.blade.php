@extends('layout.master')

@section('header')
    @include('header')
@endsection

@section('content')
    {{--index.phpのフォームだけクッキーの検索条件を適用する--}}
    @include('form', ['cookie' => $_COOKIE])
@endsection
