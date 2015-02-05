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
            <div class="middle-controls">
                <a class="btn btn-primary col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1" href="{{$reddit}}">Share your opinion about the game on Reddit</a>
                <div class="fb col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
                    <div class="fb-like" data-href="http://ilfate.net/GuessSeries" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 side-panel">
            @include('games.guess.stats-table', array('table' => $month, 'title' => 'Best this month'))
        </div>

    </div>
    <div class="row">
        <div class="free-description col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <br>
            <p>
                Thanks you for playing my game! I hope you enjoyed it!
                Please share the game with everyone who like series! Play it with your friends and share your results!
            </p>
            <br>
            <p>
                <h4>Some statistic:</h4>
                &nbsp;&nbsp;&nbsp;&nbsp;In total the game was played <strong>{{{$total['totalGames']}}}</strong> times by <strong>{{{$total['users']}}}</strong> players. Average of <strong>{{{$total['avrPoints']}}}</strong> points was earned per game. In total <strong>{{{$total['answersTotal']}}}</strong> correct answers was given.

            </p>
            <p>
                Developer`s website: <a href="http://ilfate.net">ilfate.net</a>
            </p>
        </div>
    </div>
</div>

<input type="hidden" name="_token" id="laravel-token" value="{{ csrf_token() }}">



@stop

