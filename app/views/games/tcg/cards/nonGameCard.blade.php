

<div class="non-game-card id_{{$card['id']}}" data-id="{{$card['id']}}">
    @if ($mode == 'form')
    <input type="radio" value="{{$card['id']}}" name="cardId" class="hidden" />
    @endif
	<div class="unit">
		<div class="name" >{{{$card['unit']['name']}}}</div>
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
