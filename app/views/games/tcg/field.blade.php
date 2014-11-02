

<div class="field">
@for($y = 0; $y < $field['width']; $y++)
    @for($x = 0; $x < $field['width']; $x++)

        <div class="cell x_{{$x}} y_{{$y}}" data-x="{{$x}}" data-y="{{$y}}">
        @if(isset($field['map'][$x][$y]))
            @include('games.tcg.cards.fieldCard', array(
                'card' => $field['map'][$x][$y],
                'isFocus' => isset($cardFocus) && $cardFocus == $field['map'][$x][$y]['id']
            ))
        @endif
        </div>
	@endfor	
@endfor

</div>