@extends('layout.tcg.main')

@section('content')

Your id - {{$player['id']}}<br>
name - {{$player['name']}}




@stop

@section('sidebar')

<h3>Info</h3>
@if (!$player['auth'])
<a class="btn btn-primary" href="/tcg/register">Registration</a><br><br>
<a class="btn btn-primary" href="/tcg/login">Log In</a><br><br>
@else
<a class="btn btn-primary" href="/tcg/logout">Log out</a><br><br>
@endif

@stop