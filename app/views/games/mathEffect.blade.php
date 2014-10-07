@extends('layout.layout')

@section('additional_css')
<link href="font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
<link href="css/tdGame.css" rel="stylesheet">
@stop

@section('additional_js')
<script type="text/javascript" src="/js/td/td.game.js"></script>
<script type="text/javascript" src="/js/td/td.facet.js"></script>
<script type="text/javascript" src="/js/td/td.map.js"></script>
<script type="text/javascript" src="/js/td/td.map.config.js"></script>
<script type="text/javascript" src="/js/td/td.unit.js"></script>
@stop

@section('content')

<div class="hero-unit">
    <h1>MathEffect <small>Defend your base as long as you can!</small></h1>
</div>

<div id="tdMap"></div>

<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- dialog body -->
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="result-title">
                    Your base belong to enemy now!
                </h2>
                <p id="gameStats" class="result-text">
                    You survived - <span class="result-numbers" id="turnsSurvived"></span> turns!<br>
                    You killed - <span class="result-numbers" id="unitsKilled"></span> units!<br>
                    You earned - <span class="result-numbers" id="pointsEarned"></span> points!<br>
                </p>
            </div>
            <!-- dialog buttons -->
            <div class="modal-footer"><a href="{{ action('GamesController@mathEffect') }}" type="button" class="btn btn-primary">Restart</a></div>
        </div>
    </div>
</div>

@stop

@section('sidebar')

<h3>Controls</h3>
Click on <strong>arrows</strong> to give unit command to move<br>
<br>
<h3>Info</h3>
They are trying to overrun your center! Don't let them!

@stop