@extends('layout.guess.main')

@section('content')


@include('games.guess.game', ['firstQuestion' => $firstQuestion])

@stop

@section('left-content')



@stop

@section('sidebar')

<h3>Info</h3>


<h3>Actions</h3>
<a class="btn btn-primary" href="/tcg/test/clear">Clear And create Battle</a><br><br>
<a class="btn btn-primary" href="/tcg/test/clear?bot=true">Clear And create Game with Bot</a><br><br>
<a class="btn btn-primary" href="/tcg/test/clear?debug=true&bot=true">Clear And create Debug Game with Bot</a><br><br>
<a class="btn btn-primary" href="/tcg/test/clear?situation=true">Create Situation</a><br><br>
<a class="btn btn-primary" href="/tcg/test/clear?situation=true&bot=true">Create Situation with Bot</a><br><br>


@include('games.guess.templates')

@stop