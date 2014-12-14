@extends('layout.tcg.main')

@section('content')

Your id - {{$player['id']}}<br>
name - {{$player['name']}}

<a class="btn btn-primary" href="/tcg/createDeck">Create new deck</a><br><br>

@if ($decks)
    <h2>My decks</h2>
    @foreach ($decks as $deck)
        <p>
            <a href="/tcg/deck/{{{$deck->id}}}" >{{{$deck->name}}}</a>
        </p>
    @endforeach
@else
    You have to crete a new deck
@endif

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