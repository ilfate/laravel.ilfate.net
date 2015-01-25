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
    <meta property="og:site_name" content="Guess series game">
    <meta property="og:title" content="Guess series game - the game where you can show your knowlage in series">
    <meta property="og:image" content="/images/ilfate.png">

</head>
<body>

<script type="text/javascript" src="/js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/js/preloadjs-0.2.0.min.js"></script>
<script type="text/javascript" src="/packages/mustache.js"></script>
<script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/packages/dropzone.js"></script>

<script type="text/javascript" src="/js/events.js"></script>
<script type="text/javascript" src="/js/ajax.js"></script>

<script type="text/javascript" src="/js/guess/main.js"></script>


@yield('layout')

</body>

@stop