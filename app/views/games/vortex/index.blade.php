@extends('layout.vortex.head')

@section('layout')

<div class="map-area">
	<div class="map">
		<div class="cells" style="width: {{$mapSize * 5}}em; height: {{$mapSize * 5}}em">
			@for ($y = 0; $y < $mapSize; $y++)
				@for ($x = 0; $x < $mapSize; $x++)
					<div class="cell x-{{$x}} y-{{$y}} status-accessible" data-x="{{$x}}" data-y="{{$y}}" style="width:{{100/$mapSize}}%; height:{{100/$mapSize}}%">
					</div>
				@endfor
			@endfor
		</div>
	</div>
</div>

<div class="target"></div>

<script>
    $(document).ready(function() {
    	Vortex.Game.init();
    });
</script>


@stop
