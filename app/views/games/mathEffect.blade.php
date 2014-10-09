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

<script src="/packages/video-js/video.js"></script>
<script>
  videojs.options.flash.swf = "/packages/video-js/video-js.swf"
</script>
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
                @if (empty($userName))
                <br>
                <p>
                    <form class="result-text" method="post" action="{{ action('MathEffectController@saveName') }}">
                        <input type="text" name="name" />
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <button class="btn btn-primary" type="submit">Save my name</button>
                    </form>
                </p>
                @endif
            </div>

            <!-- dialog buttons -->
            <div class="modal-footer"><a href="{{ action('MathEffectController@index') }}" type="button" class="btn btn-primary">Restart</a></div>
        </div>
    </div>
</div>

<div id="modalHowUnitMove" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- dialog body -->
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="result-title">
                    This is how you control units and spawn new.
                </h2>
                <video id="example_video_1" class="video-js vjs-default-skin"
                  controls preload="auto" width="600" height="600"
                  poster="/images/game/tdTitle.png"
                  data-setup='{"example_option":true}'>
                 <source src="/images/game/MilkyAgileGalapagosdove.webm" type='video/webm' />
                 <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
                </video>
            </div>

            <!-- dialog buttons -->
            <div class="modal-footer"><a type="button" class="btn btn-primary">Close</a></div>
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

<button id="modalHowUnitMoveButton" class="btn btn-primary" >How to control units</button>

@stop