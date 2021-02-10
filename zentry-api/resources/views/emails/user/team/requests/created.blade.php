@extends('layouts.email')
@section('content')
    <h2>Hi, {{$request->user()->profile()->displayName()}}</h2>

    <p>You've been invited to the {{$team->name()}} team!</p>
    <p>To accept or reject the invitation, please click on the following link:</p>

    <a href="{{$link}}" class="btn">Request</a>
@endsection
