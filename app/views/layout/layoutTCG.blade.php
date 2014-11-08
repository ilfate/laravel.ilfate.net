@extends('layout.headTCG')

@section('layout')



<div class="container main">
    <div class="row">
        <div class="col-md-1">
            @yield('left-content')
        </div>
        <div class="col-md-7">
            @yield('content')
        </div>
        <div class="col-md-1">
            @yield('right-content')
        </div>
        <div class="col-md-3">
            @yield('sidebar')
        </div>
    </div>
</div>


<input type="hidden" name="_token" id="laravel-token" value="{{ csrf_token() }}">



@stop

