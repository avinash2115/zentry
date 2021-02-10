@extends('layouts.email')
@section('content')
    <h2>Hi, {{$team->owner()->profile()->displayName()}}</h2>

    <p>{{$request->user()->profile()->displayName()}} accepted your invitation to the {{$team->name()}}</p>

@endsection
