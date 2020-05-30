@extends('layout.master')

@section('header')
    @include('header')
@endsection

@section('content')
    @include('form')
    @include('results', ['results' => $data['results']])
    @include('pager', ['recordCounts' => $data['record_counts'], 'displayMax' => $data['display_max'], 'allowedParams' => $data['allowed_params']])
@endsection
