

<div class="field">
@for($x = 0; $x < $field['width']; $x++)
	@for($y = 0; $y < $field['width']; $y++)
	<div class="cell">
	@if(isset($field['map'][$x][$y]))
		{{{$field['map'][$x][$y]['unit']['config'][\Tcg\Unit::CONFIG_VALUE_TEXT]}}}
	@endif
	</div>
	@endfor	
@endfor

</div>