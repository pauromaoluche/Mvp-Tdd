@extends('layouts.web')
@section('content')
    @livewire('web.auth', ['mode' => $mode])
@endsection
