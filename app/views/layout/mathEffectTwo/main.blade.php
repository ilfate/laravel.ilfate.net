@extends('layout.mathEffectTwo.head')

@section('layout')



<div class="container main">
    <div class="row">
        <div class="col-md-9 game-area">
            @yield('content')
        </div>
        <div class="col-md-3 sidebar-col">
            @yield('sidebar')
        </div>
    </div>
</div>


<input type="hidden" name="_token" id="laravel-token" value="{{ csrf_token() }}">



@stop

