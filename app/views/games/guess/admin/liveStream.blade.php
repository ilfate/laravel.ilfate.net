@extends('layout.guess.main-admin')

@section('content')

@if (!empty($games))
	@foreach ($games as $game)


        
    <div class="series-block">
        <img class="admin-series-image" src="/images/game/guess/{{$game['url']}}" />
        <span>{{$game['answers']}}</span>
        <span>{{$game['points']}}</span>
    </div>


    @endforeach
@endif


@stop

@section('sidebar')


<h3>Controls</h3>
<a class="btn btn-primary" href="/GuessSeries/admin">Back</a><br><br>


@stop
