@extends('layout.layout')

@section('additional_css')
<script type="text/javascript" src="/js/easeljs-0.5.0.min.js"></script>
<script type="text/javascript" src="/js/preloadjs-0.2.0.min.js"></script>
@stop

@section('additional_js')
<script type="text/javascript" src="/js/canvasActions.js"></script>
@stop

@section('content')

<canvas id="demoCanvas" width="576" height="576">
    alternate content
</canvas>


<script >
    $(document).ready(function(){
        CanvasActions.init();
    });
</script>

@stop

@section('sidebar')

<h3>Controls</h3>
<strong>W,A,S,D</strong> - move<br>
<strong>E</strong> - destroy wall<br>
<br>
<h3>Info</h3>
This is not actually a game. This is just result of my experiments during studing new Canvas framework (Createjs)

@stop