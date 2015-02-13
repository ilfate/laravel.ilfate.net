@extends('layout.mathEffect.head')

@section('layout')

@include('menu')

<div class="container main">
    <div class="row">
        <div class="col-md-8">
            <div class="main-content-well well well-small ">
                @yield('content')
            </div>
        </div>
        <div class="col-md-1">
            
                @yield('order')
            
        </div>
        <div class="col-md-3">
            @include('sidebar')
        </div>
    </div>
</div>

<input type="hidden" name="_token" id="laravel-token" value="{{ csrf_token() }}">

@stop

