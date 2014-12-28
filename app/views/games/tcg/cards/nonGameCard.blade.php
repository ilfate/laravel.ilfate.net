
<div class="card-container">
<div class="non-game-card card id-{{$card['cardId']}} {{$mode == 'info'?'info-card':''}} {{!empty($selected)?'focus':''}}" data-id="{{$card['cardId']}}">
    @if ($mode == 'form')
    <input type="radio" value="{{$card['cardId']}}" name="cardId" class="hidden" {{!empty($selected)?'checked="checked"':''}} />
    @endif
    <div class="image" style="background-image:url('/images/game/tcg/125/{{$card['image']}}')"></div>
	<div class="info-unit">
        <div class="health-total">
            <i class="fa fa-heart"></i>
            <span class="value" >
			    {{$card['unit']['totalHealth']}}
            </span>
        </div>
        @if (!empty($card['unit']['armor']))
			<span class="armor">
	            <i class="fa fa-shield"></i>
	            <span class="value">
	            {{$card['unit']['armor']}}
	            </span>
	        </span>
        @endif
        <div class="attack">
            <i class="fa fa-gavel"></i>
            <span class="value">
			    {{$card['unit']['attack'][0]}}-
                {{$card['unit']['attack'][1]}}
            </span>
        </div>
    </div>
    <div class="clear"></div>
    <div class="info-unit">
		<div class="name" >{{{$card['unit']['name']}}}</div>

		<p>
			{{{$card['unit']['text']}}}
		</p>
	</div>
    
	<div class="spell">
		<a class="cast btn btn-warning btn-xs" style="display:none">Cast</a>
		<div class="name" >{{{$card['spell']['name']}}}</div>
		<p>
			{{{$card['spell']['text']}}}
		</p>
	</div>
</div>
</div>
