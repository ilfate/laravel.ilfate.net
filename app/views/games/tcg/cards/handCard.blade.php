
<div class="card my-card" data-id="{{{$card['id']}}}">
	<div class="name" >{{{$card['name']}}}({{{$card['id']}}})</div>
    @if ($mode == 'deploy')
	<div class="unit">
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
		<p>
			{{{$card['spell']['config'][\Tcg\Spell::CONFIG_VALUE_TEXT]}}}
		</p>
	</div>
</div>