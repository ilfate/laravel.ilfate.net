
<div class="card my-card">
	<div class="name" >{{{$card['name']}}}({{{$card['id']}}})</div>
	<div class="unit">
		<div class="health">
			<i class="fa fa-heart"></i>
			{{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_TOTAL_HEALTH]}}}
		</div>
		<p>
			{{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_TEXT]}}}
		</p>
	</div>
	<div class="spell">
		<p>
			{{{$card['spell']['config'][\Tcg\Spell::CONFIG_VALUE_TEXT]}}}
		</p>
	</div>
</div>