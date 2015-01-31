@extends('layout.guess.head')

@section('layout')

<div class="container stats-page">
    <div class="row">
        <div class="col-md-3 side-panel">
            @include('games.guess.stats-table', array('table' => $today, 'title' => 'Best today'))
        </div>
        <div class="col-md-6 middle">

                <a class="btn btn-primary back-to-game-button" href="{{action('GuessGameController@index')}}">Back to game</a>

            <div class="middle-panel">
                <div class="hardest-image-title">Hardest image today</div>
                <img class="hardest-image" src="/images/game/guess/{{$hardestPicture}}" />
            </div>
        </div>
        <div class="col-md-3 side-panel">
            @include('games.guess.stats-table', array('table' => $month, 'title' => 'Best this month'))
        </div>

    </div>
</div>

<input type="hidden" name="_token" id="laravel-token" value="{{ csrf_token() }}">



@stop

