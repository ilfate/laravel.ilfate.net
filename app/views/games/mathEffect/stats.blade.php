@extends('layout.layout')

@section('content')

<div class="hero-unit">
    <h1>MathEffect <small>Defend your base as long as you can!</small></h1>
</div>

@forelse ($logs as $log)
    @if ($log['name'])
        {{{ $log['name'] }}}
    @else
        {{{ $log['ip'] }}}
    @endif
    ---&gt;&nbsp;
    {{{ $log['turnsSurvived'] }}},&nbsp;&nbsp;
    {{{ $log['pointsEarned'] }}},&nbsp;&nbsp;
    {{{ $log['unitsKilled'] }}}<br>
@empty
    No logs today :(
@endforelse

@stop

