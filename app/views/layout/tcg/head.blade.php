@extends('layout.html')

@section('head')
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{{{ isset($page_title) ? $page_title : 'Ilfate' }}}</title>


    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" >

    <link href="/css/tcg/main.css" rel="stylesheet">
    <link href="/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">

    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon"/>

    <meta content="tcg, card game, The Command Gambit, Web game, game, cards" name="keywords">
    <meta content="Tcg - card game" name="description">
    <meta property="og:site_name" content="Ilfate">
    <meta property="og:title" content="TCG - for The Command: Gambit">
    <meta property="og:image" content="/images/ilfate.png">

</head>
<body>

<script type="text/javascript" src="/js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-additional.js"></script>
<script type="text/javascript" src="/packages/mustache.js"></script>
<script type="text/javascript" src="/packages/autobahn/autobahn.min.js"></script>
<script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>

<script type="text/javascript" src="/js/events.js"></script>

<script type="text/javascript" src="/js/ajax.js"></script>

<script type="text/javascript" src="/js/tcg/tcg.js"></script>
<script type="text/javascript" src="/js/tcg/tcg.units.js"></script>
<script type="text/javascript" src="/js/tcg/tcg.objects.js"></script>
<script type="text/javascript" src="/js/tcg/tcg.order.js"></script>
<script type="text/javascript" src="/js/tcg/tcg.hand.js"></script>
<script type="text/javascript" src="/js/tcg/tcg.spell.js"></script>
<script type="text/javascript" src="/js/tcg/tcg.helpers.js"></script>
<script type="text/javascript" src="/js/tcg/pages.js"></script>



@yield('layout')

<script>
    // (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    // (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    // m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    // })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    // ga('create', 'UA-55905052-1', 'auto');
    // ga('send', 'pageview');

</script>
</body>

@stop