@extends('layout.guess.main')

@section('content')


@include('games.guess.game', ['firstQuestion' => $firstQuestion])

@stop

@section('left-content')



@stop

@section('sidebar')

<div class="row">
    <div class="ability-container col-md-12 col-sm-4">
        <div class="btn btn-info ability ability-1 " data-id="1">
            <div class="first-50">50</div>
            <div class="diagonal-line-50"></div>
            <div class="second-50">50</div>
        </div>
    </div>
    <div class="ability-container col-md-12 col-sm-4">
        <div class="btn btn-info ability ability-2" data-id="2">
            Skip
        </div>
    </div>
    <div class="ability-container col-md-12 col-sm-4">
        <div class="btn btn-info ability ability-3" data-id="3">
            <i class="fa fa-refresh"></i>
        </div>
    </div>
</div>

@include('games.guess.templates')

@stop