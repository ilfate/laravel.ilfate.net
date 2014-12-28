@extends('layout.tcg.main')

@section('content')

@if (!$queue)
    @if ($decks)
        <h2>Select a deck</h2>
        @foreach ($decks as $deck)
            <p>
                <a href="/tcg/joinQueue/{{{$deck->id}}}" >{{{$deck->name}}}</a>
            </p>
        @endforeach
    @else
        You have to crete a new deck
    @endif
@else
    <h2>You are in queue</h2>
    Please do not leave the page.

    <script>
        $(document).ready(function() {
            inQueuePage();
        });
    </script>
@endif

@stop

@section('sidebar')

<h3>Info</h3>

@if ($queue)
    <a class="btn btn-primary" href="/tcg/leaveQueue">Leave queue</a><br><br>
@endif
<a class="btn btn-primary" href="/tcg/me">Back to my page</a>
@stop