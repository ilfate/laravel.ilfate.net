@extends('layout.guess.main')

@section('content')


@include('games.guess.game', ['firstQuestion' => $firstQuestion])

@stop

@section('left-content')



@stop

@section('sidebar')




@include('games.guess.templates')

@stop