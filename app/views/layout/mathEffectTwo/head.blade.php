@extends('layout.html')

@section('head')
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{{{ isset($page_title) ? $page_title : 'Ilfate' }}}</title>


    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" >

    <link rel="stylesheet" href="/css/me2.css" type="text/css" />

 

    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon"/>

    <meta content="MathEffect, game, logic, strategy game, ilfate, ilya rubinchik, ilya_rubinchik, php, web developer" name="keywords">
    <meta content="MathEffect - A logic game! Try to find the best strategy to survive!" name="description">
    <meta property="og:site_name" content="Ilfate">
    <meta property="og:title" content="MathEffect">
    <meta property="og:url" content="http://ilfate.net/MathEffect">
    <meta property="og:image" content="http://ilfate.net/images/game/tdTitle_small.png">
    <meta property="og:description" content="A logic game! Try to find the best strategy to survive!">
    <meta property="og:type" content="game">

</head>
<body>

<script type="text/javascript" src="/js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-additional.js"></script>
<script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>

<script type="text/javascript" src="/js/events.js"></script>
<script type="text/javascript" src="/js/ajax.js"></script>

<script type="text/javascript" src="/js/me2.js"></script>



@yield('layout')



<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-55905052-1', 'auto');
  ga('send', 'pageview');

</script>

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-54ddd47f2fc16fe4" async="async"></script>

</body>

@stop