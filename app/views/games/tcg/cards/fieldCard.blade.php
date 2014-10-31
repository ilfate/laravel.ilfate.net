
<div class="card unit" data-id="{{{$card['id']}}}">
	<div class="name" >{{{$card['name']}}}({{{$card['id']}}})</div>
	<div class="unit">
		<div class="health">
			<i class="fa fa-heart"></i>
			{{{$card['unit']['config'][\Tcg\Unit::CONFIG_VALUE_TOTAL_HEALTH]}}}
		</div>
	</div>
</div>