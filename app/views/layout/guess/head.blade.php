@extends('layout.html')

@section('head')
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{{{ isset($page_title) ? $page_title : 'Ilfate' }}}</title>


    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" >
    <link href="/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/guess.css" rel="stylesheet">

    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon"/>

    <meta content="guess series, game, series, guess series game" name="keywords">
    <meta content="Guess series game" name="description">
    <meta property="og:site_name" content="GuessSeries">
    <meta property="og:title" content="Guess Series game - the game where you can show your knowledges in series">
    <meta property="og:url" content="http://ilfate.net/GuessGame">
    <meta property="og:image" content="http://ilfate.net/images/games/guess.jpg">
    <meta property="og:description" content="Play a game where you can show your knowledges is series and compete with other players!">
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
</body>

@stop