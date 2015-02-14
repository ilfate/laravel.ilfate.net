@extends('layout.mathEffectTwo.main')


@section('content')

<div class="hero-unit">
    <h1>MathEffect <small>Defend your base as long as you can!</small></h1>
</div>

<div class="me2">
    <div class="field"></div>
    <div class="roads"></div>
    <div class="units"></div>
    <div class="enemies"></div>
</div>



@stop

@section('sidebar')

<h3>Controls</h3>
Click on <strong>arrows</strong> to give unit command to move<br>
<h3>Info</h3>
<button id="modalHowUnitMoveButton" class="btn btn-primary" >How to play</button>
<h3>Rules</h3>
<ul>
    <li>All moving units get +1 power every turn.</li>
    <li>Your unit on base cell get +1 power every turn.</li>
    <li>When your base is empty new unit with 1 power will be created for you.</li>
    <li>All standing units will lose power. Longer they stay, faster they lose power. (except unit on base)</li>
    <li>If you command unit to move, it will not stop until it hit the wall. </li>
</ul>
<h3>Game Leaderboard</h3>
<a href="{{ action('MathEffectController@statistic') }}" class="btn btn-primary" >Statistics</a>

<h3>Share with the world</h3>
If you like my game, you can help just by sharing the link with someone who may also like it! Thanks!
@stop
