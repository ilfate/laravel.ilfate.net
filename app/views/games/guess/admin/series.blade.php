@extends('layout.guess.main-admin')

@section('content')

@if (!empty($images))
	@foreach ($images as $difficulty => $imagesGroup)
	<div class="clear"></div>
	<h1>{{$difficulty}}</h1>
	    @foreach ($imagesGroup as $image)
	        <form method="post" class="ajax series-block" action="/GuessSeries/admin/imageUpdate">
	        	<img class="admin-series-image" src="/images/game/guess/{{$image['url']}}" />
	            <input type="hidden" class="difficulty-input" name="difficulty" value="1" />
	            <a href="/GuessSeries/admin/deleteImage/{{$image['id']}}">Delete</a>
	        </form>
	    @endforeach
    @endforeach
@endif


@stop

@section('sidebar')


<h3>Controls</h3>
<a class="btn btn-primary" href="/GuessSeries/admin">Back</a><br><br>
<a class="btn btn-primary" href="/GuessSeries/admin/series/toggle?id={{{$seriesId}}}">Disable</a><br><br>
<a class="btn btn-primary" href="/GuessSeries/admin/series/toggle?id={{{$seriesId}}}">Enable</a><br><br>


@stop
