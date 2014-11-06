
<div class="card my-card" data-id="{{{$card['id']}}}" data-spelltype="{{{$card['spell']['config'][\Tcg\Spell::CONFIG_VALUE_TYPE]}}}">
    @if ($mode == 'deploy')
	<div class="unit">
		<div class="name" >{{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_NAME]}}}({{{$card['id']}}})</div>
		<div class="health-total">
			<i class="fa fa-heart"></i>
            <span class="value" >
			    {{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_TOTAL_HEALTH]}}}
            </span>
		</div>
        <div class="attack">
            <i class="fa fa-gavel"></i>
            <span class="value">
			    {{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_ATTACK][0]}}}-
                {{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_ATTACK][1]}}}
            </span>
        </div>
		<p>
			{{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_TEXT]}}}
		</p>
	</div>
    @endif
	<div class="spell">
		<a class="cast btn btn-warning btn-xs" style="display:none">Cast</a>
		<div class="name" >{{{$card['spell']['config'][\Tcg\Spell::CONFIG_VALUE_NAME]}}}({{{$card['id']}}})</div>
		<p>
			{{{$card['spell']['config'][\Tcg\Spell::CONFIG_VALUE_TEXT]}}}
		</p>
	</div>
</div>