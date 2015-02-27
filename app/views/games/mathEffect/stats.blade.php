@extends('layout.layout')

@section('content')

<div class="hero-unit">
    <h1>MathEffect <small>Defend your base as long as you can!</small></h1>
</div>

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">Top today</div>
  <table class="table">
      <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Turns Survived</th>
            <th>Points</th>
            <th>Units Killed</th>
        </tr>
      </thead>  
      <tbody>
        @forelse ($todayLogs as $key => $log)
            <tr>
                <td>{{{ $key + 1 }}}</td>
                <td>{{{ $log->name }}}</td>
                <td><b>{{{ $log->turnsSurvived }}}</b></td>
                <td>{{{ $log->pointsEarned }}}</td>
                <td>{{{ $log->unitsKilled }}}</td>
            </tr>
        @empty
            <tr>
              <th>No logs today :(</th>
            </tr>
        @endforelse        
      </tbody>
  </table>
</div>

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">Top 10 games</div>
  <table class="table">
      <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Turns Survived</th>
            <th>Points</th>
            <th>Units Killed</th>
        </tr>
      </thead>  
      <tbody>
        @forelse ($topLogs as $key => $log)
            <tr>
                <td>{{{ $key + 1 }}}</td>
                <td>{{{ $log->name }}}</td>
                <td><b>{{{ $log->turnsSurvived }}}</b></td>
                <td>{{{ $log->pointsEarned }}}</td>
                <td>{{{ $log->unitsKilled }}}</td>
            </tr>
        @empty
            <tr>
              <th>No logs today :(</th>
            </tr>
        @endforelse        
      </tbody>
  </table>
</div>

<div class="panel panel-default">
  <div class="panel-body">
    Total games played
  </div>
  <div class="panel-footer">{{{$totalGames}}}</div>
</div>

<div class="panel panel-default">
  <div class="panel-body">
    Average turns survived per game
  </div>
  <div class="panel-footer">{{{$avrTurns}}}</div>
</div>

<div class="panel panel-default">
  <div class="panel-body">
    Number of players
  </div>
  <div class="panel-footer">{{{$users}}}</div>
</div>

@if($userLogs)

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">Your top games</div>
  <table class="table">
      <thead>
        <tr>
            <th>#</th>
            <th>Turns Survived</th>
            <th>Points</th>
            <th>Units Killed</th>
        </tr>
      </thead>  
      <tbody>
        @forelse ($userLogs as $key => $log)
            <tr>
                <td>{{{ $key + 1 }}}</td>
                <td><b>{{{ $log->turnsSurvived }}}</b></td>
                <td>{{{ $log->pointsEarned }}}</td>
                <td>{{{ $log->unitsKilled }}}</td>
            </tr>
        @empty
            No logs today :(
        @endforelse        
      </tbody>
  </table>
</div>

@endif

@stop


@section('sidebar')

<h3>Back to game</h3>
<a href="{{ action('MathEffectController@index') }}" class="btn btn-primary" >Back</a>

@stop
