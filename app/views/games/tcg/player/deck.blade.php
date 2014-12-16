@extends('layout.tcg.main')

@section('content')




<a class="btn btn-primary" href="/tcg/changeDeck?deckId={{{$deck->id}}}">Deck settings</a><br><br>

<h2>{{{$deck->name}}}</h2>

@stop

@section('sidebar')

<h3>Info</h3>
<a class="btn btn-primary" href="/tcg/me">Back to my page</a>

@stop