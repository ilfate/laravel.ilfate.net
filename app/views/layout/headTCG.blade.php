@extends('layout.html')

@section('head')
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{{{ isset($page_title) ? $page_title : 'Ilfate' }}}</title>


    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" >

    <link href="css/tcg/main.css" rel="stylesheet">
    <link href="font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">

    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon"/>

    <meta content="ilfate ilya rubinchik ilya_rubinchik php web developer" name="keywords">
    <meta content="Rubinchik Ilya`s personal site" name="description">
    <meta property="og:site_name" content="Ilfate">
    <meta property="og:title" content="Ilfate - Rubinchik Ilya. Personal Site.">
    <meta property="og:image" content="/images/ilfate.png">

</head>
<body>

<script type="text/javascript" src="/js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>

<script type="text/javascript" src="/js/events.js"></script>

<script type="text/javascript" src="/js/index.js"></script>
<script type="text/javascript" src="/js/ajax.js"></script>
<script type="text/javascript" src="/js/modal.js"></script>
<script type="text/javascript" src="/js/form.js"></script>
<script type="text/javascript" src="/js/pages.js"></script>

<script type="text/javascript" src="/js/tcg/tcg.js"></script>
<script type="text/javascript" src="/js/tcg/tcg.units.js"></script>

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