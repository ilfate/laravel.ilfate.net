@extends('layout.layout')

@section('additional_css')
<link href="font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
<link href="css/tdGame.css" rel="stylesheet">
<link href="/packages/video-js/video-js.css" rel="stylesheet">
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
    <h1>JamNov <small>Here we are playing!</small></h1>
</div>



@stop

@section('sidebar')

<h3>Info</h3>
This is a game created on November Berlin Game Jam

@stop