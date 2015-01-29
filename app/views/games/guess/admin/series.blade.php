@extends('layout.guess.main-admin')

@section('content')

@if (!empty($images))
	@foreach ($images as $difficulty => $imagesGroup)
	<div class="clear"></div>
	<form method="post" class="series-difficulty-block dropzone" action="/GuessSeries/admin/addImage">
        <input type="hidden" name="id" value="{{$seriesId}}" />
        <input type="hidden" class="difficulty-input" name="difficulty" value="{{$difficulty}}" />

        
		<h1>{{$difficulty}}</h1>
	    @foreach ($imagesGroup as $image)
	    	<div class="series-block">
	        	<img class="admin-series-image" src="/images/game/guess/{{$image['url']}}" />
	            <a href="/GuessSeries/admin/deleteImage/{{$image['id']}}">Delete</a>
	        </div>
	    @endforeach
        <div class="clear"></div>
	</form>
    @endforeach
@endif


@stop

@section('sidebar')


<h3>Controls</h3>
<a class="btn btn-primary" href="/GuessSeries/admin">Back</a><br><br>
<a class="btn btn-primary" href="/GuessSeries/admin/series/toggle/{{{$seriesId}}}">Toggle Active</a><br><br>


@stop
