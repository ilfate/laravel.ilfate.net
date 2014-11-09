

<div class="field">
    @for($y = 0; $y < $field['width']; $y++)
        @for($x = 0; $x < $field['width']; $x++)

            <div class="cell x_{{$x}} y_{{$y}}" data-x="{{$x}}" data-y="{{$y}}">

            </div>
        @endfor
    @endfor
    <div class="units" >
        @if (!empty($field['map']))
            @foreach($field['map'] as $card)
                @include('games.tcg.cards.fieldCard', array(
                'card' => $card,
                'x' => $card['unit']['x'],
                'y' => $card['unit']['y']
                ))
            @endforeach
        @endif
    </div>
</div>