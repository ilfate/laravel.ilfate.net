

<div class="field">
@for($y = 0; $y < $field['width']; $y++)
    @for($x = 0; $x < $field['width']; $x++)

        <div class="cell">
        @if(isset($field['map'][$x][$y]))
            {{{$field['map'][$x][$y]['unit']['config'][\Tcg\Unit::CONFIG_VALUE_TEXT]}}}
        @endif
        </div>
	@endfor	
@endfor

</div>