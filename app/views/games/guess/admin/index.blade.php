@extends('layout.guess.main-admin')

@section('content')

@if (!empty($series))
    @foreach ($series as $serie)
        <form method="post" class="series-block {{$editAllowed? 'dropzone' : ''}}" action="/GuessSeries/admin/addImage">
            <input type="hidden" name="id" value="{{$serie->id}}" />
            <input type="hidden" class="difficulty-input" name="difficulty" value="1" />
            <h4>
                <a class="{{$serie->active == 0 || !$editAllowed ? 'disabled': ''}}" href="/GuessSeries/admin/series/{{$serie->id}}">
                    {{$serie->name}}
                </a>
            </h4>
            <a onclick="Guess.Game.seriesImagesGenerate({{$serie->id}})">generate</a>

        </form>
    @endforeach
@endif


@stop

@section('sidebar')


<h3>Image difficulty</h3>
<input type="text" class="form-control" value="1" onkeyup="Guess.Game.adminDuffeculty(this)" />

<a class="btn btn-primary" href="/GuessSeries/admin/addSeries">Add a Series</a><br><br>
<a class="btn btn-primary" href="/GuessSeries/admin/liveStream">Live stream</a><br><br>


@stop