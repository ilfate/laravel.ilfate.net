@extends('layout.tcg.head')

@section('layout')



<div class="container main">
    <div class="row">
        <div class="col-md-8">
            @yield('content')
        </div>
        <div class="col-md-4">
            @yield('sidebar')
        </div>
    </div>
</div>


<input type="hidden" name="_token" id="laravel-token" value="{{ csrf_token() }}">



@stop

