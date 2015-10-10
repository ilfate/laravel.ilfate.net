@extends('layout.layout')

@section('content')

<div class="page-header">
    <h1>Code <small>Open-source! Feel free to use!</small></h1>
</div>

<div class="row show-grid">
    <div class="col-md-9 col-md-offset-1 code-page-block" >
        <a href="{{ action('CodeController@engine') }}" class="rounded_block_link" data-target=".main-content-well">
            @include('interface.button-block', array('text' => 'My PHP framework', 'background' => '/images/php2.jpg'))
        </a>
    </div>
</div>
<div class="row show-grid">
    <div class="col-md-9 col-md-offset-1 code-page-block" >
        <a href="{{ action('CodeController@stars') }}" class="rounded_block_link" data-target=".main-content-well">
            @include('interface.button-block', array('text' => 'Starred label', 'background' => '/images/js3.jpg'))
        </a>
    </div>
</div>

@stop
