@extends('layout.layout')

@section('content')

<div class="page-header">
    <h1>Hello! My name is Ilya Rubinchik <small>and this is my website!</small></h1>
</div>


<div class="row show-grid">
    <div class="col-md-3 col-md-offset-1 main-page-block">
        <a href="{{ action('PageController@cv') }}" class="rounded_block_link">
            @include('interface.button-block', array('text' => 'CV', 'background' => '/images/my/code1_s.jpg'))
        </a>
    </div>
    <div class="col-md-3 col-md-offset-2 main-page-block">
        <a href="{{ action('CodeController@index') }}" class="rounded_block_link">
            @include('interface.button-block', array('text' => 'Code', 'background' => '/images/php.jpg'))
        </a>
    </div>
</div>
<div class="row show-grid">
    <div class="col-md-3 col-md-offset-1 main-page-block">
        <a href="{{ action('GamesController@index') }}" class="rounded_block_link" data-target=".main-content-well">
            @include('interface.button-block', array('text' => 'Game', 'background' => '/images/game/tank1_s.jpg'))
        </a>
    </div>
    <div class="col-md-3 col-md-offset-2 main-page-block" >
        <a href="{{ action('PageController@photo') }}" class="rounded_block_link" data-target=".main-content-well">
            @include('interface.button-block', array('text' => 'Photo', 'background' => '/images/photo2.jpg'))
        </a>
    </div>
</div>

@stop