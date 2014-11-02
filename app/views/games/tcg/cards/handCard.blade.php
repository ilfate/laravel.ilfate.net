
<div class="card my-card" data-id="{{{$card['id']}}}">
	<div class="name" >{{{$card['name']}}}({{{$card['id']}}})</div>
    @if ($mode == 'deploy')
	<div class="unit">
		<div class="health">
			<i class="fa fa-heart"></i>
			{{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_TOTAL_HEALTH]}}}
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