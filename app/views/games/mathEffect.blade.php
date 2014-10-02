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

<div id="tdMap"></div>
hide
<div id="lean_overlay" class="" title="Game over!">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>All your base is belong to enemy now!</p>

</div>

@stop

@section('sidebar')

<h3>Controls</h3>
Click on <strong>arrows</strong> to give unit command to move<br>
<br>
<h3>Info</h3>
They are trying to overrun your center! Don't let them!

@stop