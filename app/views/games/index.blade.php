@extends('layout.layout')

@section('content')

<div class="hero-unit">
    <h1>Games <small>Some of my games</small></h1>
</div>

<div class="row show-grid">
    <div class="col-md-8 col-md-offset-2 code-page-block">
        <a href="{{ action('GuessGameController@index') }}" class="rounded_block_link">
            @include('interface.button-block', array('text' => '', 'background' => '/images/game/GuessSeriesMini.jpg'))
        </a>
    </div>
</div>
<div class="row show-grid">
    <div class="col-md-8 col-md-offset-2 code-page-block">
        <a href="{{ action('MathEffectController@index') }}" class="rounded_block_link">
            @include('interface.button-block', array('text' => 'Math Effect', 'background' => '/images/game/tdTitle_small.png'))
        </a>
    </div>
</div>
<div class="row show-grid">
    <div class="col-md-8 col-md-offset-2 code-page-block">
        <a href="{{ action('GamesController@robotRock') }}" class="rounded_block_link">
            @include('interface.button-block', array('text' => 'Robot Rock', 'background' => '/images/game/Long_robot.jpg'))
        </a>
    </div>
</div>
<div class="row show-grid">
    <div class="col-md-8 col-md-offset-2 code-page-block">
        <a href="{{ action('GamesController@gameTemplate') }}" class="rounded_block_link">
            @include('interface.button-block', array('text' => 'Game template', 'background' => '/images/game/tanks.png'))
        </a>
    </div>
</div>

@stop