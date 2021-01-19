@extends('layouts.email')
@section('content')
    <h2>Hi, {{$team->owner()->profile()->displayName()}}</h2>

    <p>{{$request->user()->profile()->displayName()}} rejected your invitation to the {{$team->name()}}</p>
@endsection

