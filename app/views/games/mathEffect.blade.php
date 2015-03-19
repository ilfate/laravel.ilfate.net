@extends('layout.mathEffect.layout')

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
                <p class="result-text">
                    <span class="label label-warning">
                    Try my new game - <a href="http://ilfate.net/GuessSeries">Guess Series</a>
                    </span>
                </p>
                @if (empty($userName))
                <br>
                <p id="MENameFormContainer">
                    <form id="MENameForm" class="ajax result-text" method="post" action="{{ action('MathEffectController@saveName') }}">
                        <input type="text" name="name" />
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="checkKey" id="checkKey" value="{{ $checkKey }}">

                        <button class="btn btn-primary" type="submit">Save my name</button>
                    </form>
                </p>
                @else
                  <input type="hidden" name="checkKey" id="checkKey" value="{{ $checkKey }}" >
                @endif
            </div>

            <!-- dialog buttons -->
            <div class="modal-footer">
                <div class="me-facebook-modal">
                    <div class="addthis_sharing_toolbox" data-url="http://ilfate.net/MathEffect" data-title="MathEffect"></div>
                </div>
                <!-- <div class="me-facebook-modal">
                    <div class="fb-like" data-href="http://ilfate.net/MathEffect" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
                    <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://ilfate.net/MathEffect">Tweet</a>
                </div> -->
                <a href="{{ action('MathEffectController@statistic') }}" class="btn btn-primary modal-leaferboard-button" >Leaderboard</a> 
                <a href="{{ action('MathEffectController@index') }}" type="button" class="btn btn-success">Play again</a>
            </div>
        </div>
    </div>
</div>

<div id="modalHowUnitMove" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- dialog body -->
            <div class="modal-body">
                <a type="button" class="youtube-stop" close" data-dismiss="modal">&times;</a>
                <br>
                <br>
                <iframe width="560" height="315" frameborder="0" data-video="//www.youtube.com/embed/OlJ9VdY9dig" allowfullscreen></iframe>
                {{-- <video id="example_video_1" class="video-js vjs-default-skin"
                  controls preload="auto" width="600" height="600"
                  poster="/images/game/tdTitle.png"
                  data-setup='{"example_option":true}'>
                 <source src="/images/game/MilkyAgileGalapagosdove.webm" type='video/webm' />
                 <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
                </video>
                --}}
            </div>

            <!-- dialog buttons -->
            <div class="modal-footer"><a type="button" class="btn btn-primary youtube-stop" data-dismiss="modal">Close</a></div>
        </div>
    </div>
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
