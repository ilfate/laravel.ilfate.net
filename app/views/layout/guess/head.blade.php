@extends('layout.html')

@section('head')
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{{{ isset($page_title) ? $page_title : 'Ilfate' }}}</title>


    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" >
    <link href="/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/guess.css" rel="stylesheet">

    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon"/>

    <meta content="guess series, game, series, quiz, quiz series, guess series game" name="keywords">
    <meta content="Quiz: Guess series game" name="description">
    <meta property="og:site_name" content="GuessSeries">
    <meta property="og:title" content="Guess Series - the game where you can show your knowledge in series">
    <meta property="og:url" content="http://ilfate.net/GuessSeries">
    <meta property="og:image" content="http://ilfate.net/images/game/GuessSeries.jpg">
    <meta property="og:description" content="Play a game where you can show your knowledge in series and compete with other players!">
    <meta property="og:type" content="game">

</head>
<body>

<script type="text/javascript" src="/js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-additional.js"></script>
<script type="text/javascript" src="/js/imagesloaded.pkgd.min.js"></script>
<script type="text/javascript" src="/packages/mustache.js"></script>
<script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>

<script type="text/javascript" src="/js/events.js"></script>
<script type="text/javascript" src="/js/ajax.js"></script>

<script type="text/javascript" src="/js/guess/main.js"></script>


@yield('layout')

<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-55905052-1', 'auto');
    ga('send', 'pageview');

</script>
<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=243940452354382&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
<div class="facebook-like-hidden">
    <div class="fb-like" data-href="http://ilfate.net/GuessSeries" data-layout="box_count" data-action="like" data-show-faces="false" data-share="true"></div>
    <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://ilfate.net/GuessSeries">Tweet</a>

</div>
</body>

@stop
