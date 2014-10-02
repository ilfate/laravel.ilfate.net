@extends('layout.html')

@section('head')
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{{{ isset($page_title) ? $page_title : 'Ilfate' }}}</title>


    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css" type="text/css" media="screen" >

    <link rel="stylesheet" href="/css/main.css" type="text/css" />
    <link rel="stylesheet" href="/css/game.css" type="text/css" />

    @yield('additional_css')

    <!--  <link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap.min.css">
      <link rel="stylesheet" href="css/bootstrap-image-gallery.min.css">-->

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
<script type="text/javascript" src="packages/leanModal.v1.1/jquery.leanModal.min.js"></script>

<!--  <script type="text/javascript" src="/js/bootstrap-image-gallery.min.js"></script>
  <script src="http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js"></script>-->

<script type="text/javascript" src="/js/events.js"></script>

<script type="text/javascript" src="/js/index.js"></script>
<script type="text/javascript" src="/js/ajax.js"></script>
<script type="text/javascript" src="/js/modal.js"></script>
<script type="text/javascript" src="/js/form.js"></script>
<script type="text/javascript" src="/js/pages.js"></script>

@yield('additional_js')

@yield('layout')


</body>

@stop